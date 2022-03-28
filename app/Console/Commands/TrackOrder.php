<?php

namespace App\Console\Commands;

use App\Exports\OrderTrackingCSVExport;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Traits\GraphQLTrait;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class TrackOrder extends Command
{
    use GraphQLTrait;

    public $user_id = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is run to tack order and generate csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \Log::info('============================== START:: TRACK ORDER SCHEDULING ==============================');

            $users = User::all();

            if (count($users) > 0) {
                foreach ($users as $userk => $userv) {
                    $orders = Order::where('user_id', $userv->id)->where('status', 0)->get();
                    if (count($orders) > 0) {
                        foreach ($orders as $orderkey => $orderval) {
                            $this->getOrders($userv, $orderval);
                        }
                    }
                }
            }

            \Log::info('============================== END:: TRACK ORDER SCHEDULING ==============================');
        } catch (\Exception $e) {
            \Log::info('============================== ERROR:: TRACK ORDER SCHEDULING ==============================');
            \Log::info($e);
        }
    }

    public function getOrders($user, $order)
    {
        try {
            \Log::info('============================== START:: getOrders ==============================');

            $shop = $user;
            $parameter['fields'] = 'id,billing_address,cart_token,customer,customer_locale,financial_status,line_items,order_number,processed_at,shipping_address,source_name,total_price,created_at,client_details,discount_codes,gateway,landing_site,app_id,processing_method,referring_site,token,note_attributes';
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order->order_id.'.json';

            $sh_order = $shop->api()->rest('GET', $endPoint, $parameter);
            if (!$sh_order->errors) {
                $sh_order = $sh_order->body->order;
//                \Log::info('ORDER_ID :: '.$sh_order->id);
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
                $res = $this->testCybertonicaApi($sh_order, $risks, $transactions);
                $ress = json_decode($res);
                $tid = '';
                \Log::info('note attributes');
                \Log::info(json_encode($sh_order->note_attributes));
                if( !empty($sh_order->note_attributes) ){
                    foreach ($sh_order->note_attributes as $key=>$val){
                        if( $val->name == 'cybertonica tid' ){
                            $tid = $val->value;
                        }
                    }
                }
                $order->response = $res;
//                $order->status = 1;
                $order->cybertonica_tid = $tid;
                $order->cybertonica_status = 'Merchant Review';
                $order->cybertonica_risk = 'Medium';
                $order->cybertonica_risk_score = $ress->score;
                $order->cybertonica_customer_verification = 'None';
                $order->save();

                // add note attribute in shopify
                $this->addNoteAttrInShopify($shop,$sh_order, $order->order_id, 'Merchant Review', 'Medium', $ress->score, 'None');
                $this->addOrderInLocal($shop, $order->order_id);
            }
            \Log::info('============================== END:: getOrders ==============================');
        } catch (\Exception $e) {
            \Log::info('============================== ERROR:: getOrders ==============================');
            \Log::info($e);
        }
    }
    public function addOrderInLocal( $user, $order_id ){
        try{
            $shop = $user;
            \Log::info('============================== START:: addOrderInLocal ==============================');
            \Log::info(json_encode($shop));
            $parameter['fields'] = 'id,billing_address,customer,financial_status,name,shipping_address,total_price,created_at,gateway,currency,confirmed,cancelled_at,cancel_reason,location_id,device_id,note_attributes,contact_email,payment_details';
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order_id.'.json';

            $result = $shop->api()->rest('GET', $endPoint, $parameter);
            $sh_order = $result->body->order;
            if (!$result->errors) {
                $bill_add = [];
                $ship_add = [];
                if (!empty($sh_order->billing_address)) {
                    $bill_add = $this->getAddress($sh_order->billing_address);
                }
                if (!empty($sh_order->shipping_address)) {
                    $ship_add = $this->getAddress($sh_order->shipping_address);
                }

                $init_order = Order::where('order_id', $sh_order->id)->where('user_id', $user->id)->first();
                $order = OrderDetail::where('order_id', $sh_order->id)->where('user_id', $user->id)->first();
                $order->user_id = $shop->id;
                $order->db_order_id = $init_order->id;
                $order->order_id = $sh_order->id;
                $order->customer_id = (@$sh_order->customer) ? $sh_order->customer->id : '';
                $order->order_name = $sh_order->name;
                $order->billing_address = implode(',', $bill_add);
                $order->shipping_address = implode(',', $ship_add);
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
//                $order->payment_details = (@$sh_order->payment_details) ? json_encode($sh_order->payment_details) : '';
                $order->save();

                if( @$sh_order->customer ){
                    $cust = $sh_order->customer;

                    $name = ( @$cust->first_name ) ? $cust->first_name : '';
                    $name = ( @$cust->last_name ) ? $cust->last_name : $name;
                    $customer = Customer::where('user_id', $user->id)->where('customer_id', $cust->id)->first();
                    $customer = ($customer) ? $customer : new Customer;
                    $customer->user_id = $user->id;
                    $customer->customer_id = $cust->id;
                    $customer->name = $name;
                    $customer->is_email_verified = $cust->verified_email;
                    $customer->order_count = $cust->orders_count;
                    $customer->customer_since = date("Y-m-d H:i:s", strtotime($cust->created_at));
                    $customer->save();
                }
            }

        }catch( \Exception $e ){
            \Log::info('============================== ERROR:: addOrderInLocal ==============================');
            \Log::info($e);
        }
    }
    public function addNoteAttrInShopify($user,$order, $order_id, $ns8_status, $ns8_risk, $ns8_eq8_score, $ns8_customer_verification){
        try{
            \Log::info('=====================START:: addNoteAttrInShopify===================');
            $shop = $user;
            $ns8 = ['NS8 Status' => $ns8_status, 'NS8 Order Risk' => $ns8_risk, 'NS8 EQ8 Score' => $ns8_eq8_score, 'NS8 Customer Verification' => $ns8_customer_verification];
            $note_attrs = [];
            if( !empty($order->note_attributes) ){
                foreach ($order->note_attributes as $key=>$val){
                    $attr['name'] = $val->name;
                    $attr['value'] = $val->value;
                    array_push($note_attrs, $attr);
                }
            }
            \Log::info(json_encode($note_attrs));

            foreach($ns8 as $key=>$val){
                $attr['name'] = $key;
                $attr['value'] = $val;
                array_push($note_attrs, $attr);
            }

            $nt = [
                'order' => [
                    'id' => $order_id,
                    'note_attributes' => $note_attrs
                ]
            ];
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/orders/'.$order_id.'.json';
            $shop->api()->rest('PUT', $endPoint, $nt);
        }catch(\Exception $e){
            \Log::info('=====================ERROR:: addNoteAttrInShopify===================');
            \Log::info($e);
        }
    }
    public function testCybertonicaApi($data, $risks, $transactions)
    {
        try {
            \Log::info('============================== START:: testCybertonicaApi ==============================');
            $bill_add = [];
            $ship_add = [];
            if (!empty($data->billing_address)) {
                $bill_add = $this->getAddress($data->billing_address);
            }
            if (!empty($data->shipping_address)) {
                $ship_add = $this->getAddress($data->shipping_address);
            }
            $data = array(
                'App Id' => $data->app_id,
                'Id' => $data->id,
                'Billing Address' => implode(',', $bill_add),
                'Cart Token' => $data->cart_token,
                'customer' => (@$data->customer) ? json_encode($data->customer) : '',
                'Customer Locale' => $data->customer_locale,
                'Financial Status' => $data->financial_status,
                'Line Items' => (!empty($data->line_items)) ? json_encode($data->line_items) : '',
                'Order Number' => $data->order_number,
                'Processed At' => $data->processed_at,
                'Shipping Address' => implode(',', $ship_add),
                'Source Name' => $data->source_name,
                'Total Price' => $data->total_price,
                'User Id' => @($data->customer) ? $data->customer->id : '',
                'Client Details' => (!empty($data->client_details)) ? json_encode($data->client_details) : '',
                'Discount Codes' => (!empty($data->discount_codes)) ? json_encode($data->discount_codes) : '',
                'Gateway' => $data->gateway,
                'Landing Site' => $data->landing_site,
                'Processing Method' => $data->processing_method,
                'Referring Sited' => $data->referring_site,
                'Token' => $data->token,
                'Transaction Properties' => (!empty($transactions)) ? json_encode($transactions) : '',
                'Order Risk Properties' => (!empty($risks)) ? json_encode($risks) : '',
//                'tid' => $tid
            );

            $channel = 'shopify_test';
            $result = $this->addToCybertocnica($data, $channel);
//            \Log::info('Cybertonica api response');
//            \Log::info($result);
            if ($result === null) {
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

    private function addToCybertocnica($data, $channel)
    {
        try {
            \Log::info('============================== START:: addToCybertocnica ==============================');
            $key = env('CYBERTONICA_API_KEY'); //m1yH2s6yhaCqgjm

            $raw = json_encode($data);
            $sign = base64_encode(hash_hmac('sha1', $raw, $key, true));

            $result = Http::withHeaders([
                'X-Af-Team' => 'shopify_test',
                'X-Af-Signature' => $sign,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post('https://test.cybertonica.com:7499/api/v2.2/events/'. urlencode($channel) . '?subChannel=default', $data);


            \Log::info($result);


//            $raw = json_encode($data);
////            \Log::info($raw);
//            $sign = base64_encode(hash_hmac('sha1', $raw, $key, true));
//            // start Curl //
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, 'https://test.cybertonica.com:7499/api/v2.2/events/' . urlencode($channel) . '?subChannel=default');
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
//            // Create header
//            $headers = array();
//            $headers[] = 'X-Af-Team: shopify_test';
//            $headers[] = 'X-Af-Signature: ' . $sign;
//            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            $result = curl_exec($ch);
//
////             print result for debug
//
//            $error = curl_errno($ch);
//            if (curl_errno($ch)) {
//                echo 'Error:' . curl_error($ch);
//                $info = curl_getinfo($ch);
//                \Log::info($info);
//                return null;
//            }
//            curl_close($ch);
            return $result;
        } catch (\Exception $th) {
            \Log::info('============================== ERROR:: addToCybertocnica ==============================');
            \Log::info($th);
            return null;
        }
    }
    public function getAddress($add){
        $arr = [];
        ( $add->name ) ? array_push($arr, $add->name) : '';
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
//    public function getOrders($user){
//        try{
//            \Log::info('============================== START:: getOrders ==============================');
//
//            $shop = $user;
//            $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/shop.json';
//            $parameter['field'] = 'iana_timezone';
//            $shop_json = $shop->api()->rest('GET', $endPoint, $parameter);
//
//            if( !$shop_json->errors ) {
//                $timezone = $shop_json->body->shop->iana_timezone;
//                date_default_timezone_set($timezone);
//                $date = date("Y-m-d");
////                $time = date('H:i', strtotime('-1 hour'));
//                $time = date('H:i', strtotime('-10 minutes'));
//                $min = $date.'T'.$time;
//
//                $parameter['created_at_min'] = $min;
//                $parameter['fields'] = 'id,billing_address,cart_token,customer,customer_locale,financial_status,line_items,order_number,processed_at,shipping_address,source_name,total_price,created_at,client_details,discount_codes,gateway,landing_site,app_id,processing_method,referring_site,token';
//                $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/orders.json';
//
//                $order_count = $shop->api()->rest('GET', 'admin/orders/count.json', $parameter);
//                $total_page = number_format(ceil($order_count->body->count / 250), 0);
//
//                if ($total_page > 0){
//                    for ($i = 1; $i <= $total_page; $i++) {
//                        $sh_orders = $shop->api()->rest('GET', $endPoint, $parameter);
//                        if (!$sh_orders->errors) {
//                            $orders = $sh_orders->body->orders;
//                            if (count($orders) > 0 && is_array($orders)) {
//                                foreach ($orders as $okey => $oval) {
//                                    \Log::info('ORDER_ID :: ' . $oval->id);
//                                    $parameter['fields'] = 'id,order_id,kind,gateway,status,message,authorization,parent_id,processed_at,device_id,error_code,currency,amount,payment_details';
//                                    $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/orders/'. $oval->id .'/transactions.json';
//                                    $transactions_json = $shop->api()->rest('GET', $endPoint);
//                                    if( !$transactions_json->errors ) {
//                                        $transactions = $transactions_json->body->transactions;
//                                    }
//
//                                    $parameter['fields'] = 'id,order_id,checkout_id,score,source,recommendation,merchant_message';
//                                    $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/orders/'. $oval->id .'/risks.json';
//                                    $risks_json = $shop->a$user = $this->getUser();pi()->rest('GET', $endPoint, $parameter);
//                                    if( !$shop_json->errors ) {
//                                        $risks = $risks_json->body->risks;
//                                    }
//
//                                    $storeName = str_replace('.myshopify.com', '',$user->name);
//                                    $fileName = $oval->id.'.csv';
//                                    $filepath = '/upload/'.$storeName.'/'.$fileName;
//                                    $res = Excel::store(new OrderTrackingCSVExport($oval, $risks, $transactions), $fileName, 'public');
//                                    \Storage::disk('sftp')->put($filepath,
//                                        fopen(storage_path('app/public/').$fileName, 'r+'));
//                                    $path = \Storage::path('public/' .$fileName);
//                                    unlink($path);
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//            \Log::info('============================== END:: getOrders ==============================');
//        }catch ( \Exception $e ){
//            \Log::info('============================== ERROR:: getOrders ==============================');
//            \Log::info($e);
//        }
//    }

}
