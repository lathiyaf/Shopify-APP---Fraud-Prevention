<?php

namespace App\Http\Controllers\Test;

use App\Exports\MetafieldsCSVExports;
use App\Exports\OrderTrackingCSVExport;
use App\Http\Controllers\Controller;
use App\Models\CybertonicaUser;
use App\Models\Order;
use App\Models\Setting;
use App\Traits\GraphQLTrait;
use http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\CheckOrder;

class TestController extends Controller
{
    use GraphQLTrait;

    public function index()
    {
        try {
            $orders = Order::where('user_id', 29)->where('status', 0)->get();
            foreach ($orders as $key => $value) {
                event(new CheckOrder($value->id, 29, $value->order_id, 'create'));
            }
            // event(new CheckOrder(877, 29, '2917501272216', 'create'));
            dd('1111');

            // $postData = [
            //     'username' => 'zeshan',
            //     'password' => '123',
            //     'customerID' => 'fisherwallace.myshopify.com'
            // ];
            // $endPoint = config('const.DASHBOARD_ENDPOINT') . 'getSubscriberCredit';
            // $result = Http::withHeaders([
            //     'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            // ])->post($endPoint, $postData);
            // $res = $result->json();

            // dd($res);

//             $shop = Auth::user();
//             $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->where('is_success', 1)->first();

//             if( !$cybertonica_user ){
//                 $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json';
//                 $parameter['fields'] = "id,email,name,shop_owner,domain";
//                 $shop_data = $shop->api()->rest('GET', $endPoint, $parameter);
//                 if (!$shop_data->errors) {
//                     $shop_data = $shop_data->body->shop;
//                     $name = explode(' ', $shop_data->shop_owner);

//                     $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->first();

//                     $password = ($cybertonica_user) ?  $cybertonica_user->password : $this->generatePassword();

//                     $cybertonica_user = ($cybertonica_user) ? $cybertonica_user : new CybertonicaUser;

//                     $cybertonica_user->user_id = $shop->id;
//                     $cybertonica_user->username = $shop_data->name;
//                     $cybertonica_user->password = $password;
//                     $cybertonica_user->role = 'client';
//                     $cybertonica_user->storeID = $shop_data->id;
//                     $cybertonica_user->firstname = (@$name[0]) ? $name[0] : '';
//                     $cybertonica_user->lastname = (@$name[1]) ? $name[1] : '';
//                     $cybertonica_user->email = $shop_data->email;
//                     $cybertonica_user->teams = json_encode([1, 2, 3]);
//                     $cybertonica_user->save();

//                     $is_cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->where('is_success', 1)->first();

// //                    dd($is_cybertonica_user);
//                     if( !$is_cybertonica_user ){
//                         $this->addCyberData($shop_data, $shop_data->name, $name, $password);
//                     }
//                 }
//             }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function addCyberData($shop_data, $unm, $name, $password){
        try{

            $res['success'] = '';
            $res['unm'] = '';
            $endPoint = 'http://'. env('CYBERTONICA_SERVER') .'/reportingserver/client/CreateNewUser';
            $data = [
                "username" => $unm,
                "password" => $password,
                "role" => 'client',
                "storeid" => $shop_data->id,
                "firstname" => (@$name[0]) ? $name[0] : '',
                "lastname" => (@$name[1]) ? $name[1] : '',
                "email" => 'abc@gmail.com',
                "teams" => [1, 2, 3],
                "domain" => $shop_data->domain
            ];
            $result = Http::withHeaders([
                'Authorization' => env('CYBERTONICA_AUTHORIZATION'),
            ])->asJson()->post($endPoint, $data);

            if( $result->successful() ){
                $cyberData = json_decode($result->getBody());
                if($cyberData->IsSuccessful){
                    $this->updateUser($unm);
                }else if( $cyberData->Message == 'User already exists' ){
                    $this->addCyberData($shop_data, $unm . 1, $name, $password);
                }
            }
            return $res;
        }catch(\Exception $e){
            dd($e);
        }
    }

    public function updateUser($unm){
        try{
            $shop = Auth::user();
            $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->first();
            $cybertonica_user->username = $unm;
            $cybertonica_user->is_success = 1;
            $cybertonica_user->save();
        }catch(\Exception $e){
            dd($e);
        }
    }
}
