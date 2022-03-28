<?php

namespace App\Listeners;

use App\Events\CheckOrder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Setting;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class AddOrder implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CheckOrder  $event
     * @return void
     */
    public function handle(CheckOrder $event)
    {
       try{
           $ids = $event->ids;
           \Log::info("====================START:: Add Order Listener :: ". $ids['user_id'] . " :: ". $ids['entity_id'] ." ====================");
           \Log::info($ids['action']);
           $order = Order::where('user_id', $ids['user_id'])->where('order_id', $ids['order_id'])->first();
           $this->getOrders($ids['user_id'], $order, $ids['action']);
       }catch( \Exception $e ){
           \Log::info("====================ERROR:: Add Order Listener ====================");
           \Log::info($e->getMessage());
       }
    }

    public function getOrders($user, $order, $action)
    {
        try {
            \Log::info('============================== START:: getOrders ==============================');
            logger('----------------- ACTION:: '. $action .' ---------------');
            $shop = User::where('id', $user)->first();
            logger(json_encode($shop));    
            $credit = ( env('SHOPIFY_BILLING_ENABLED') ) ? $this->SubscriberCredit($shop->name, 'get', $order) : 1;
            logger('================= CREDIT:: '. $credit .' ==================');
            logger($credit > 0);
            if( $credit > 0 ){
                $parameter['fields'] = 'id,name,billing_address,cart_token,customer,customer_locale,financial_status,line_items,order_number,processed_at,shipping_address,source_name,total_price,created_at,client_details,discount_codes,gateway,landing_site,app_id,fulfillment_status,processing_method,referring_site,token,note_attributes,cancelled_at';
                $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order->order_id.'.json';

                $sh_order = $shop->api()->rest('GET', $endPoint, $parameter);
               
                if (!$sh_order->errors) {
                    $sh_order = $sh_order->body->order;

                    $parameter['fields'] = 'id,order_id,kind,gateway,status,message,authorization,parent_id,processed_at,device_id,error_code,currency,amount,payment_details';
                    $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$sh_order->id.'/transactions.json';
                    $transactions_json = $shop->api()->rest('GET', $endPoint);
                    if (!$transactions_json->errors) {
                        $transactions = $transactions_json->body->transactions;
                    }

                    $parameter['fields'] = 'id,order_id,checkout_id,score,source,recommendation,merchant_message';
                    $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$sh_order->id.'/risks.json';
                    $risks_json = $shop->api()->rest('GET', $endPoint, $parameter);
                    if (!$risks_json->errors) {
                        $risks = $risks_json->body->risks;
                    }

                    $tid = '';

                    if( $action == 'create' ){
                        if( !empty($sh_order->note_attributes) ){
                            foreach ($sh_order->note_attributes as $key=>$val){
                                if( $val->name == 'cybertonica tid' ){
                                    $tid = $val->value;
                                }
                            }
                        }
                    }else{
                        $td_order = Order::where('order_id', $sh_order->id)->where('user_id', $user)->first();
                        if( $td_order ){
                            $tid = $td_order['cybertonica_tid'];
                        }
                    }

                    logger('tid :: ' . $tid);
                    $res = $this->testCybertonicaApi($shop, $sh_order, $risks, $transactions, $tid, $action, $order);

                    // if( $res != null || $res != ''){
                     if( $res ){
                        $ress = json_decode($res);
                        $setting = Setting::where('user_id', $shop->id)->where('key', 'risk_score_range')->first();
                        $custom_range = json_decode($setting->value);

//                        // this is temparoary code after update api endpoint work perfactly it will be removed
//                        if( $action == 'update' &&  $sh_order->cancelled_at){
//                            $orderDetail = OrderDetail::where('')
//                        }

                         $score = 0;
                         $order_status = '';
                        if(@$ress->score){
                            logger($ress->score);
                            $score = $ress->score;
                            $order_status = 'Approved';
                            if( $score <= $custom_range[0] ){
                                $risk = 'Low';
                            }elseif ( $score <= $custom_range[1] ){
                                $risk = 'Medium';
                            }else{
                                $risk = 'High';
                                $order_status = 'Pending';
                            }
                        }else{
                            $risk = 'Low';
                        }
                         logger($order_status);

                         $comment = (@$ress->comments[0]) ? json_decode($ress->comments[0]) : '';
                         if( $action == 'create' ){
                             $order->response = $res;
                             $order->cybertonica_risk_score = $score;
                             $order->update_id = (@$ress) ? $ress->id : '';;
                             $order->cybertonica_risk = $risk;
                             $order->status = 1;
                             $order->cybertonica_tid = $tid;
                             $order->cybertonica_status = 'Merchant Review';
                             $order->alert_message = (@$comment->AlertMessage) ? $comment->AlertMessage : '';
                             $order->cybertonica_customer_verification = 'None';
                             $order->save();

                             // add note attribute in shopify
                             $this->addNoteAttrInShopify($shop,$sh_order, $order->order_id, 'Merchant Review', $risk, $score, 'None', $order_status);
                         }


                        // add customer and order details in database

                        $this->addOrderInLocal($shop, $order->order_id, $order_status);

// update credit
//                        $this->SubscriberCredit($shop->name, 'update');

                        // send mail if risk score is high then order risk score
                        $setting = Setting::where('user_id', $shop->id)->where('key', 'manage_email_notification')->first();
                        if( $setting->value == 1 ){
//                            $setting = Setting::where('user_id', $shop->id)->where('key', 'risk_score')->first();
                            if( $score >= $custom_range[1] ){
                                $parameter['fields'] = 'id,email';
                                $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json';
                                $result = $shop->api()->rest('GET', $endPoint, $parameter);
                                if( !$result->errors ){
                                    if( !$result->errors ){
                                        $email = $result->body->shop->email;
//                                        $email = "ruchita.crawlapps@gmail.com";
                                    }
                                    $data = [
                                        'order' => 'https://' . $shop->name . '/admin/orders/' . $order->order_id,
                                        'msg' => 'Your Order number ' . $sh_order->name . ' risk score('. $score .') is high then your default risk score('.$custom_range[1] . ')',
                                    ];
                                    Mail::send('Mail.orderRisk', $data, function ($message) use ($email) {
                                        $message->to($email, '');
                                        $message->subject("Cybertonica Order Risk");
                                    });
                                }
                            }
                        }
                    }
                }else{
                     logger(json_encode($sh_order));
                     $order->error_message = json_encode($sh_order);
                    $order->save();
                }
            }
            \Log::info('============================== END:: getOrders ==============================');
        } catch (\Exception $e) {
            \Log::info('============================== ERROR:: getOrders ==============================');
            \Log::info($e);
        }
    }

    public function SubscriberCredit($shopName, $action, $order){
        try{
             \Log::info('============================== START:: '. $action .' Credit ==============================');

             if( $action == 'get' ){
                $postData = [
                    'username' => 'zeshan',
                    'password' => '123',
                    'customerID' => $shopName
                ];
             }else{
                $postData = [
                    'username' => 'zeshan',
                    'password' => '123',
                    'customerID' => $shopName,
                    'creditsDeducted' => 1
                ];
             }
            $end = ( $action == 'get' ) ? 'getSubscriberCredit' : 'updateSubscriberCredit';
            $endPoint = config('const.DASHBOARD_ENDPOINT') . $end;
            \Log::info($endPoint);
            $result = Http::withHeaders([
                'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            ])->post($endPoint, $postData);

//            logger($result->json());
            if( $action == 'get' ){
                if( $result->successful() ){
                     $res = $result->json();
                     if( $res['IsSuccessful'] ){
                         $credit = ( @$res['Data']['AvailableCredit'] ) ? $res['Data']['AvailableCredit'] : -1;
                     }else{
                         $order->error_message = json_encode($res);
                         $order->save();
                         $credit = -1;
                     }
                }else{
                    $order->error_message = json_encode($result->json());
                    $order->save();
                    return -1;
                }
                return $credit;
            }
             \Log::info('============================== END:: '. $action .' Credit ==============================');
        } catch (\Exception $e) {
            \Log::info('============================== ERROR:: '. $action .' Credit ==============================');
            \Log::info($e);
        }
    }

    public function addNoteAttrInShopify($user,$order, $order_id, $cybertonica_status, $cybertonica_risk, $cybertonica_risk_score, $cybertonica_customer_verification, $order_status){
        try{
            \Log::info('=====================START:: addNoteAttrInShopify===================');
            $shop = $user;
            $ns8 = ['Cybertonica Risk' => $cybertonica_risk, 'Cybertonica Risk Score' => $cybertonica_risk_score];
            $note_attrs = [];
            if( !empty($order->note_attributes) ){
                foreach ($order->note_attributes as $key=>$val){
                    if( $val->name != 'cybertonica tid' ){
                        $attr['name'] = $val->name;
                        $attr['value'] = $val->value;
                        array_push($note_attrs, $attr);
                    }
                }
            }


            foreach($ns8 as $key=>$val){
                $attr['name'] = $key;
                $attr['value'] = $val;
                array_push($note_attrs, $attr);
            }

            $nt = [
                'order' => [
                    'id' => $order_id,
                    'note_attributes' => $note_attrs,
                    'financial_status' => ( $order->cancelled_at ) ? $order->financial_status : $order_status
                ]
            ];
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order_id.'.json';
            $shop->api()->rest('PUT', $endPoint, $nt);
        }catch(\Exception $e){
            \Log::info('=====================ERROR:: addNoteAttrInShopify===================');
            \Log::info($e);
        }
    }

    public function addOrderInLocal( $user, $order_id ,$order_status){
        try{
            $shop = $user;
            \Log::info('============================== START:: addOrderInLocal ==============================');
            $parameter['fields'] = 'id,billing_address,customer,financial_status,name,shipping_address,total_price,created_at,gateway,currency,confirmed,cancelled_at,cancel_reason,location_id,device_id,note_attributes,contact_email,payment_details';
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order_id.'.json';
            $result = $shop->api()->rest('GET', $endPoint, $parameter);

            $sh_order = $result->body->order;
            if (!$result->errors) {
                $bill_add = [];
                $ship_add = [];
                $bill_lat_long = ',';
                $shipp_lat_long = ',';
                if (!empty($sh_order->billing_address)) {
                    $bill_add = $this->getAddress($sh_order->billing_address);
                    $bill_lat_long =  $sh_order->billing_address->latitude . ',' . $sh_order->billing_address->longitude;
                }
                if (!empty($sh_order->shipping_address)) {
                    $ship_add = $this->getAddress($sh_order->shipping_address);
                    $shipp_lat_long =  $sh_order->shipping_address->latitude . ',' . $sh_order->shipping_address->longitude;
                }

                $init_order = Order::where('order_id', $sh_order->id)->where('user_id', $user->id)->first();
                $order = OrderDetail::where('order_id', $sh_order->id)->where('user_id', $user->id)->first();

                if( $order_status == '' ){
                    $order_status = ( $order ) ? $order['order_status'] : 'Pending';
                }
                $order = ($order) ? $order : new OrderDetail;
                $order->db_order_id = $init_order->id;
                $order->user_id = $user->id;
                $order->order_id = $sh_order->id;
                $order->customer_id = (@$sh_order->customer) ? $sh_order->customer->id : '';
                $order->order_name = $sh_order->name;
                $order->order_status = ($sh_order->cancelled_at) ? 'Cancelled' : $order_status;
                $order->billing_address = implode(',', $bill_add);
                $order->billing_lat_long = $bill_lat_long;
                $order->shipping_address = implode(',', $ship_add);
                $order->shipping_lat_long = $shipp_lat_long;
                $order->total_price = $sh_order->total_price;
                $order->currency = $sh_order->currency;
                $order->financial_status = $sh_order->financial_status;
                $order->order_confirmed = $sh_order->confirmed;
                $order->cancelled_at = $sh_order->cancelled_at;
                $order->cancel_reason = $sh_order->cancel_reason;
                $order->location_id = $sh_order->location_id;
                $order->device_id = $sh_order->device_id;
                $order->note_attributes = (@$sh_order->note_attributes) ? json_encode($sh_order->note_attributes) : '';
                $order->contact_email = $sh_order->contact_email;
                $order->gateway = $sh_order->gateway;
                $order->payment_details = (@$sh_order->payment_details) ? json_encode($sh_order->payment_details) : '';
                $order->save();

                if( @$sh_order->customer ){
                    $cust = $sh_order->customer;

                    $query = ' {
                          customer(id: "gid://shopify/Customer/'.$cust->id.'") {
                                displayName
                                image {
                                  src
                                }
                              }
                        }';
                    $parameter = [];
                    $result = $shop->api()->graph($query, $parameter);
                    if( !$result->errors ){
                        $glcust = $result->body->customer;
                        $avtar = (@$glcust->image->src) ? $glcust->image->src : '';
                        $name = $glcust->displayName;
                    }else{
                        $name = ( @$cust->first_name ) ? $cust->first_name : '';
                        $name = ( @$cust->last_name ) ? $name . ' ' . $cust->last_name : $name;
                        $avtar = '';
                    }

                    $customer = Customer::where('user_id', $user->id)->where('customer_id', $cust->id)->first();
                    $customer = ($customer) ? $customer : new Customer;
                    $customer->user_id = $user->id;
                    $customer->customer_id = $cust->id;
                    $customer->name = $name;
                    $customer->avtar = $avtar;
                    $customer->phone = $cust->phone;
                    $customer->is_email_verified = $cust->verified_email;
                    $customer->order_count = $cust->orders_count;
                    $customer->customer_since = date("Y-m-d H:i:s", strtotime($cust->created_at));
                    $customer->save();
                }
            }

        }catch( \Exception $e ){
            \Log::info('============================== ERROR:: addOrderInLocal ==============================');
            \Log::info($e);
            \Log::info($e->getLine());
        }
    }

    public function testCybertonicaApi($shop, $data, $risks, $transactions, $tid, $action, $db_order)
    {
        try {
            \Log::info('============================== START:: testCybertonicaApi ==============================');
            $pythndata = [];

            $src_parent = '';
            $tr = [];
            $TransactionType = '';
            $TransactionStatus = '';
            if (!empty($transactions)) {
                foreach ( $transactions as $tk=>$tv ){
                    if( $tv->message == 'Transaction approved' ){
                        $tr = $tv;
                    }
                    $tv->amount = (int) $tv->amount;
                }
                if( empty( $tr ) ){
                    $tr = $transactions[0];
                }
                if (!empty($tr->payment_details)) {
                    $src_parent = $tr->payment_details->credit_card_bin;
                }

                $TransactionType = ( @$tr->kind ) ? $tr->kind : '';
                $TransactionStatus = ( @$tr->status ) ? $tr->status : '';
            }

            if( $action == 'create' ) {

                $parameter['fields'] = 'id,name,shop_owner';
                $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json';

                $sh_shop = $shop->api()->rest('GET', $endPoint, $parameter);
                if (!$sh_shop->errors) {
                    $sh_shop = $sh_shop->body->shop;
                }

                if( !$shop->app_id ){
                    $shop->app_id = $data->app_id;
                    $shop->save();
                }

                $dst_parent = '';
                if( $shop->is_passed_app_id && $shop->app_id != $data->app_id){
                    $dst_parent =  $data->app_id;
                } elseif ( !$shop->is_passed_app_id ){
                    $dst_parent =  $data->app_id;
                }
                if( !$shop->is_passed_app_id && $dst_parent != '' ) {
                    $shop->is_passed_app_id = true;
                    $shop->save();
                }

                $bill_add = [];
                $ship_add = [];
                if (!empty($data->billing_address)) {
                    $bill_add = $this->getAddress($data->billing_address);
                }
                if (!empty($data->shipping_address)) {
                    $ship_add = $this->getAddress($data->shipping_address);
                }

                logger(json_encode($data));    
                $zipcode = '';
                $email = '';
                $phone = '';
                $fullName = '';
                if (@$data->customer) {
                    $e = ( @$data->email ) ? $data->email : '';
                    $email = (@$data->customer->email) ? $data->customer->email: $e;
                    $phone = (@$data->customer->phone) ? $data->customer->phone : '';
                    if (!empty(@$data->customer->default_address)) {
                        $df = $data->customer->default_address;
                        $zipcode = $df->zip;
                        $phone = ($phone == '') ? $df->phone : $phone;
                        $fullName = ($df->first_name == '') ? $df->last_name : $df->first_name.' '.$df->last_name;
                    }
                }
                $cart = [];
                if (!empty(@$data->line_items)) {
                    $li = @$data->line_items;
                    foreach ($li as $lk => $lv) {
                        $c['name'] = $lv->title;
                        $c['qty'] = $lv->quantity;
                        $c['price'] = $lv->price;
                        $cart[$lk] = $c;
                    }
                }

                $amount = '';
                $currency = '';
                $exp = 0;
                if (!empty($transactions)) {
                    $tr = end($transactions);
                    $currency = $tr->currency;
                }

                if ($data->total_price != '') {
                    $str = $data->total_price;
                    $amount = str_replace(".", "", $str);
                    if (strpos($str, '.')) {
                        $exp = strlen(substr($str, stripos($str, '.') + 1));
                    }
                }
                // pass total price in amount convert float in

                $pythndata = array(
                    'extid' => (@$data->customer) ? $data->customer->id . '_' . $data->id : $data->id,
                    'billing_address' => implode(',', $bill_add),
                    'src_id' => $data->cart_token,
                    'src_client_id' => (@$data->customer) ? $data->customer->id : '',
                    'dst_client_id' => (@$sh_shop->shop_owner && @$sh_shop->name) ? $sh_shop->name . '-' .$sh_shop->shop_owner : '',
                    'dst_partner' => $data->gateway,
                    'src_partner' => 'Shopify',
                    'dst_id' => (@$sh_shop->id) ? $sh_shop->id : '',
                    'customer' => (@$data->customer) ? $data->customer : '',
                    'Customer Locale' => $data->customer_locale,
                    'Financial Status' => $data->financial_status,
                    'Line Items' => (!empty($data->line_items)) ? $data->line_items : '',
                    'Order Number' => $data->order_number,
                    't' => $data->processed_at,
                    'shipping_address' => implode(',', $ship_add),
                    'Source Name' => $data->source_name,
                    'Total Price' => $data->total_price,
                    'User Id' => @($data->customer) ? $data->customer->id : '',
                    'Client Details' => (!empty($data->client_details)) ? $data->client_details : '',
                    'Discount Codes' => (!empty($data->discount_codes)) ? $data->discount_codes : '',
                    'Gateway' => $data->gateway,
                    'Landing Site' => $data->landing_site,
                    'Processing Method' => $data->processing_method,
                    'Referring Sited' => $data->referring_site,
                    'Token' => $data->token,
                    'Transaction Properties' => (!empty($transactions)) ? $transactions : '',
                    'Order Risk Properties' => (!empty($risks)) ? $risks : '',
                    'tid' => $tid,
                    'timezone' => 'UTC',
                    'ip' => (!empty($data->client_details)) ? $data->client_details->browser_ip : '',
                    'amount' => (int)$amount,
                    'exp' => $exp,
                    'zip_code' => $zipcode,
                    'cart' => $cart,
                    'email' => $email,
                    'phonenumber' => $phone,
                    'fullname' => $fullName,
                    'TransactionType' => $TransactionType
                );
                if ($dst_parent !== '') {
                    $pythndata['dst_parent'] = $dst_parent;
                }
                if ($src_parent !== '') {
                    $pythndata['src_parent'] = $src_parent;
                }
                if ($currency !== '') {
                    $pythndata['currency'] = $currency;
                }
                logger("================== RISKS::  ================");
                if ((!empty($risks))) {
                    $rk = end($risks);
                    $pythndata['ext_fraud_score'] = $rk->score;
                }
                logger('AMOUNT :: ' . $amount);
                logger(json_encode($pythndata));
            }else{
//                $query =  '{
//                    order(id: "gid://shopify/Order/'. $data->id .'") {
//                        FulfillmentStatus
//                      }
//                    }';
//                $result = $shop->api()->graph($query, []);
//                logger(json_encode($result));
                $pythndata = [
                    'extid' =>  (@$data->customer) ? $data->customer->id . '_' . $data->id : $data->id,
                    'status' => $TransactionStatus,
                    't' => $data->processed_at,
                ];
            }
//            logger("================== " . json_encode($pythndata, JSON_PRETTY_PRINT) ." ================");
            $channel = config('const.CYBERTONICA_ORDER_SUBCHANNEL');
            $result = $this->addToCybertocnica($pythndata, $channel, $action);
            \Log::info('Cybertonica api response');
            \Log::info($result);
            if ($result === null || $result === '') {
                $db_order->error_message = json_encode($result);
                $db_order->save();
                return "Somethings Goes Wrong";
            } else {
                return $result;
            }
            \Log::info('============================== END:: testCybertonicaApi ==============================');
        } catch (\Exception $e) {
            \Log::info('============================== ERROR:: testCybertonicaApi ==============================');
            \Log::info($e);
        }
    }

    private function addToCybertocnica($data, $channel, $action)
    {
        try {
            \Log::info('============================== START:: addToCybertocnica ==============================');
            $key = config('const.CYBERTONICA_API_KEY');

            $raw = json_encode($data);
            $sign = base64_encode(hash_hmac('sha1', $raw, $key, true));
            return $this->getResult($sign, $channel, $data, $action);
        } catch (\Exception $th) {
            \Log::info('============================== ERROR:: addToCybertocnica ==============================');
            \Log::info($th);
            return null;
        }
    }

    public function getResult($sign, $channel, $data, $action){
        logger('====================' . config('const.CYBERTONICA_ORDER_SUBCHANNEL') . '===================');
        $result = '';
        if( $action == 'create' ){

            $endPoint = config('const.CYBERTONICA_PYTHON_ORDER_ENDPOINT') . 'order?subChannel='. config('const.CYBERTONICA_ORDER_SUBCHANNEL');
            logger($endPoint);

            $result = Http::withHeaders([
                'X-Af-Team' => config('const.CYBERTONICA_API_USER'),
                'X-Af-Signature' => $sign,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post($endPoint, $data);

        }elseif ( $action == 'update' ) {

//            $data['status'] = ($data['status'] == '') ? 'SHOPIFY' : $data['status'];
//            $endPoint = 'https://shopify.cybertonica.com:7499/api/v2.2/events/order/' . $data['extid'] . '?status=' . $data['status'];
//
//            $postField =  '{ "status": "ok", "code": "0001", "comment": "generator comment", "t": "'. $data['t'] .'"}';
//            logger($endPoint);
//            logger($postField);
//            $curl = curl_init();
//            $t = (int)(time() * 1000);
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => $endPoint,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => "",
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => "PUT",
//                // CURLOPT_POSTFIELDS => '{"is_authed": 0, "code": "pm_code_here", "comment": "pm_comment", "t": 1600337491652}',
//                CURLOPT_POSTFIELDS => $postField,
//                CURLOPT_HTTPHEADER => array(
//                    "X-Af-Team: " . config('const.CYBERTONICA_API_USER'),
//                    "X-Af-Signature: N4H3uAR51uMnDAJv/sw5vLIShjg=",
//                    "Content-Type: application/json"
//                ),
//            ));
//            $result = curl_exec($curl);
//            $info = curl_getinfo($curl);
//            curl_close($curl);
//            logger($info['http_code']);

        }

        return $result;
    }

    public function getAddress($add){
        $arr = [];
//        ( $add->name ) ? array_push($arr, $add->name) : '';
        ( $add->address1 ) ? array_push($arr, $add->address1) : '';
        ( $add->address2 ) ? array_push($arr, $add->address2) : '';
        ( $add->city ) ? array_push($arr, $add->city) : '';
        ( $add->city ) ? array_push($arr, $add->city) : '';
        ( $add->province ) ? array_push($arr, $add->province) : '';
        ( $add->country ) ? array_push($arr, $add->country) : '';
        ( $add->company ) ? array_push($arr, $add->company) : '';
        ( $add->phone ) ? array_push($arr, $add->phone) : '';
        ( $add->latitude ) ? array_push($arr, $add->latitude) : '';
        ( $add->longitude ) ? array_push($arr, $add->longitude) : '';
        ( $add->country_code ) ? array_push($arr, $add->country_code) : '';
        ( $add->province_code ) ? array_push($arr, $add->province_code) : '';

        return $arr;
    }
}
