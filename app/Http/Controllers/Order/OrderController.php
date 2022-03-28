<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Response;

class OrderController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index( Request $request ){
        try{
            $shop = \Auth::user();
            $order_id = $request->order_id;

            $order = Order::with('OrderDetails')->where('order_id', $order_id)->where('user_id', $shop->id)->first();

            if( $order &&  $order->OrderDetails){
                $order->response = json_decode($order['response']);
                $risk_factor = [];
                if( @$order->response->comments && !empty($order->response->comments)){
                    $cmt = (array) json_decode($order->response->comments[0]);
                    $risk_factor = (@$cmt['risk factors']->Rule->factors) ? json_decode($cmt['risk factors']->Rule->factors) : [];
                }
                $order['risk_factor'] = $risk_factor;
                $order_details = $order->OrderDetails;
                $customer = Customer::where('customer_id', $order_details->customer_id)->where('user_id', $shop->id)->first();

                $order->last_orders = OrderDetail::select('order_name', 'db_order_id')->with('Order')->where('customer_id', $order_details->customer_id)->where('order_id', '!=', $order_id)->where('user_id', $shop->id)->limit(5)->orderBy('created_at', 'desc')->get()->toArray();

                $order->order_created_at = date("y-m-d H:i:s", strtotime($order->order_created_at));

                if( @$order->customer && $order->customer != null ){
                    $order->customer = $customer;
                    $order->customer->avtar = ($order->customer->avtar == '') ? '/images/no-photo.jpg' : $order->customer->avtar;
                    $order->customer->customer_since = date("Y-m-d", strtotime($order->customer->customer_since));
                    $order->customer->is_email_verified = ( $order->customer->is_email_verified == 1) ? 'YES' : 'NO';
                }else{
                    $customer['avtar'] = '/images/no-photo.jpg';
                    $customer['customer_since'] = '';
                    $customer['is_email_verified'] = '';
                    $order['customer'] = $customer;
                }


                $bill_lat = explode(',', $order_details->billing_lat_long);
                $shipp_lat = explode(',', $order_details->shipping_lat_long);

                $order->makers = [
                    // billing
                    [ 'position'=>
                        [ 'lat'=> (double)$bill_lat[0], 'lng'=> (double)$bill_lat[1] ],
                        'icon'=>
                            ['url' => env('APP_URL') . '/images/Group-49-1.png'],
//                     ['url' => 'https://i.ibb.co/74vHb4B/Group-49-1.png'],
                    ],
                    // shipping
                    [ 'position'=>
                        [ 'lat'=> (double)$shipp_lat[0], 'lng'=> (double)$shipp_lat[1] ],
                        'icon'=>
                            ['url' => env('APP_URL') . '/images/Group-49.png'],
//                        ['url' => 'https://i.ibb.co/gTM4pvK/Group-49.png'],
                    ],
                    // device
//                 [ 'position'=>
//                    [ 'lat'=> (double)21.5984569, 'lng'=> (double)72.236598 ],
//                    'icon'=>
//                        ['url' => env('APP_URL') . '/images/Group-49-(2).png'],
////                        ['url' => 'https://i.ibb.co/108j9Gf/Group-49-2.png'],
//                 ]
                ];
                $centerM['lat'] = ($bill_lat[0] == '') ? (double)'21.170240' : (double)$bill_lat[0];
                $centerM['lng'] = ($bill_lat[1] == '') ? (double)'72.831062' : (double)$bill_lat[1];
                $order['center'] = $centerM;

            }else{
                $order = [];
            }

            return response::json(['data' => $order], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }

//    public function orderList(){
//        try{
//            $shop = \Auth::user();
//            $order = Order::with('OrderDetails')->where('user_id', $shop->id)->where('status', 1)->orderBy('order_created_at', 'desc')->get()->toArray();
//
//
//            foreach ( $order as $key=>$val ){
//                if( !empty($val['order_details']) ) {
//                    $order_details = $val['order_details'];
//
//                    $customer = ($order_details) ? Customer::where('customer_id',
//                        $order_details['customer_id'])->where('user_id', $shop->id)->first() : [];
//
//                    $val['order_created_at'] = date("y-m-d H:i:s", strtotime($val['order_created_at']));
//                    $val['customer'] = $customer;
//
//                    if (!empty($customer)) {
//                        $val['customer']['customer_since'] = date("Y-m-d", strtotime($val['customer']['customer_since']));
//                        $val['customer']['is_email_verified'] = (@$val['customer']['s_email_verified'] == 1) ? 'YES' : 'NO';
//                    }
//                }else{
//                    unset( $order[$key] );
//                }
//            }
//            return response::json(['data' => $order], 200);
//        }catch( \Exception $e ){
//            return response::json(['data' => $e->getMessage()], 422);
//        }
//    }

    /**
     * @return mixed
     */
//    public function orderList(){
//        try{
//            $shop = \Auth::user();
//
//
//            $order = Order::with('OrderDetails')->where('user_id', $shop->id)->where('status', 1)->orderBy('order_created_at', 'desc')->get()->toArray();
//
//            $res = [];
//            foreach ( $order as $key=>$val ){
//                if( !empty($val['order_details']) ) {
//                    $order_details = $val['order_details'];
//
//                    $res[$key]['order_id'] = $val['order_id'];
//                    $res[$key]['order_name'] = $order_details['order_name'];
//                    $res[$key]['order_created_at'] = date("y-m-d H:i:s", strtotime($val['order_created_at']));
//                    $res[$key]['order_status'] = $order_details['order_status'];
//                    $res[$key]['cybertonica_risk'] = $val['cybertonica_risk'];
//                    $res[$key]['cybertonica_risk_score'] = $val['cybertonica_risk_score'];
//                    $res[$key]['currency'] = $order_details['currency'];
//                    $res[$key]['total_price'] = $order_details['total_price'];
//                }else{
//                    unset( $order[$key] );
//                }
//            }
//            return response::json(['data' => $res], 200);
//        }catch( \Exception $e ){
//            return response::json(['data' => $e->getMessage()], 422);
//        }
//    }
    public function orderList(Request $request){
        try{
            $sort = ($request->sort != null) ?  explode('|', $request->sort) : null;
            $shop = \Auth::user();
            $s = $request->s;
            $res = [];
            $st = ( $sort[0] == 'cybertonica_risk' || $sort[0] == 'cybertonica_risk_score' || $sort[0] == 'order_created_at') ? 'orders' : 'order_details';
            $order = Order::join('order_details', 'orders.id', '=', 'order_details.db_order_id') ->where(function ($query) use ($s) {
                $query->where('cybertonica_risk', 'LIKE', '%'.$s.'%')->orWhere('order_created_at', 'LIKE', '%'.$s.'%')->orWhere('cybertonica_risk_score', 'LIKE', '%'.$s.'%')->orWhere('order_status', 'LIKE', '%'.$s.'%')->orWhere('order_name', 'LIKE', '%'.$s.'%')->orWhere('total_price', 'LIKE', '%'.$s.'%');
            })->orderBy($st . '.'.$sort[0], $sort[1])->where('orders.user_id', $shop->id)->where('orders.status', 1)->paginate($request->per_page);
            if( $order ){
                $res = $order->map(function ($name) {
                    return [
                        'order_id' => $name->order_id,
                        'order_name' => $name->order_name,
                        'order_created_at' => date("y-m-d H:i:s", strtotime($name->order_created_at)),
                        'order_status' => $name->order_status,
                        'cybertonica_risk' => $name->cybertonica_risk,
                        'cybertonica_risk_score' => $name->cybertonica_risk_score,
                        'currency' => $name->currency,
                        'total_price' => $name->total_price,
                    ];
                })->toArray();
            }

            // $order = Order::with('OrderDetails')->where('user_id', $shop->id)->where('status', 1)->when(($sort != null ), function( $q, $sort ){
            //                                        return $q->orderBy($sort[0], $sort[1])
            //                                    })->paginate($request->per_page);
            // dd($order);




            // $data['order'] = $res;
            // $data['prev_page_url'] = $order->previousPageUrl();
            // $data['next_page_url'] = $order->nextPageUrl();
            // $data['last_page'] = $order->lastPage();
            // $data['current_page'] = $order->currentPage();
            // $data['per_page'] = $order->perPage();
            // $data['total'] = $order->total();
            // $data['from'] = $order->firstItem();
            // $data['to'] = $order->lastItem();
            //  $order = Order::with('OrderDetails')->where('user_id', $shop->id)->where('status', 1)->orderBy('order_created_at', 'desc')->->paginate($request->per_page);
            // foreach ( $order as $key=>$val ){
            //     if( !empty($val['order_details']) ) {
            //         $order_details = $val['order_details'];

            //         $res[$key]['order_id'] = $val['order_id'];
            //         $res[$key]['order_name'] = $order_details['order_name'];
            //         $res[$key]['order_created_at'] = date("y-m-d H:i:s", strtotime($val['order_created_at']));
            //         $res[$key]['order_status'] = $order_details['order_status'];
            //         $res[$key]['cybertonica_risk'] = $val['cybertonica_risk'];
            //         $res[$key]['cybertonica_risk_score'] = $val['cybertonica_risk_score'];
            //         $res[$key]['currency'] = $order_details['currency'];
            //         $res[$key]['total_price'] = $order_details['total_price'];
            //     }else{
            //         unset( $order[$key] );
            //     }
            // }
            return response::json([
                'data' => array_filter($res),
                'prev_page_url' => $order->previousPageUrl(),
                'next_page_url' => $order->nextPageUrl(),
                'last_page' => $order->lastPage(),
                'current_page' => $order->currentPage(),
                'per_page' => $order->perPage(),
                'total' => $order->total(),
                'from' => $order->firstItem(),
                'to' => $order->lastItem()
            ], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function changeStatus(Request $request){
        try{
            $shop = \Auth::user();
            $order_id = $request->order_id;
            $order = OrderDetail::where('order_id', $order_id)->where('user_id', $shop->id)->first();

            if($request->status == 'approve' ){
                $d = [
                    "order"=>[
                        "id"=> $order_id,
                        "financial_status"=> "paid"
                      ]
                ];
                $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/orders/'. $order_id .'.json';
                $response = $shop->api()->rest('PUT', $endPoint, $d);
                if( !$response->errors ){
                    $order->order_status = 'Approved';
                    $order->save();
                    $data['msg'] = 'Order Approved Successfully!';

                }else{
                    $data['msg'] = 'Order not approved!';
                }
            }elseif ( $request->status == 'cancel' ){
                $sh_order = [
                    "amount"=> $order->total_price,
                    "currency"=> $order->currency
                ];

                $endPoint = '/admin/orders/'. $order_id .'/cancel.json';
                // $endPoint = '/admin/orders/3640596136096/cancel.json';
                $response = $shop->api()->rest('POST', $endPoint, $sh_order);
                if( !$response->errors ){
                    $order->order_status = 'Cancelled';
                    $order->cancelled_at = date('Y-m-d H:i:s');
                    $order->cancel_reason = 'other';
                    $order->save();
                    $data['msg'] = 'Order Cancelled Successfully!';
                }else{
                    $data['msg'] = (@$response->body) ? $response->body : 'Order not cancelled!';
                }
            }
            $data['order_id'] = $order_id;
            return response::json(['data' => $data], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }
}
