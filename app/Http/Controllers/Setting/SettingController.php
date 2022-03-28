<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Response;

class SettingController extends Controller
{
    public function index( Request $request ){
        try{
            $shop = \Auth::user();
            $timezones = TimeZone::select('timezone')->get()->toArray();
            $settings = Setting::select('key', 'value')->where('user_id', $shop->id)->get()->toArray();

            foreach ( $settings as $key=>$val ){
                if( $val['key'] == 'risk_score' ){
                    $data['setting'][$val['key']] = (int)$val['value'];
                }elseif ( $val['key'] == 'risk_score_range' ){
                    $data['setting'][$val['key']] = json_decode($val['value']);
                } elseif($val['key'] == 'manage_email_notification'){
                    $data['setting'][$val['key']] = (int)$val['value'];
                }else{
                    $data['setting'][$val['key']] = $val['value'];
                }
            }
            $data['timezone'] = $timezones;

            return response::json(['data' => $data], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }

    public function store( Request $request ){
        try{
            $shop = \Auth::user();
            $data = $request->data;
            foreach ( $data as $key=>$val ){
                $setting = Setting::where('user_id', $shop->id)->where('key', $key)->first();
                if( $key == 'risk_score' ){
                    $setting->value = (int)$val;
                }elseif ( $key == 'risk_score_range' ){
                    $setting->value = json_encode($val);
                } else{
                    $setting->value = $val;
                }
                $setting->save();
            }
            return response::json(['data' => $data], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }
}
