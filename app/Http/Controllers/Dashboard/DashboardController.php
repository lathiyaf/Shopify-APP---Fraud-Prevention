<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CybertonicaUser;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Response;

class DashboardController extends Controller
{
    public function appBladeindex(Request $request){
        try{
            $current_user = $this->getUser();
            $id = @$request->id ? $request->id : 0;
            return view('layouts.app', compact('id', 'current_user'));
        }catch( \Exception $e ){
            dd($e);
        }
    }
    public function index(Request $request)
    {
        try {
            $shop = Auth::user();
            $data = $this->getDashboardData($request->days);
            return response::json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response::json(['data' => $e->getMessage()], 422);
        }
    }

    public function getDashboardData($day){
        try {
            $user = $this->getUser();

            $endPoint = config('const.DASHBOARD_ENDPOINT').'getTIDData';
            $result = Http::withHeaders([
                'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            ])->post($endPoint, [
//                "username" => 'Cybertonica11',
//                "password" => 's@PXQ-60wS@84k#1$u',
                "username" => $user->username,
                "password" => $user->password,
                "days" => $day
            ]);

            $res = ($result->successful()) ? json_decode($result->getBody())->Data : [];

            $returnres['data'] = [];
            $returnres['isSuccessfull'] = false;
            $data = [];
            if (!empty($res)) {
                $data['live'] = $this->getLiveData($res->LiveData);
                $data['Visitors'] = $this->getVisitorsData($res->SiteVisitors, $day);

                $data['Threats'] = $this->getThreatsData($res->ThreatData, $day);
                $data['Pages'] = $this->getPagesData($res->PagesData, $day);
                $data['ThreatOrigin'] = $this->getThreatsOrigin($res->ThreatsOrigin, $day);
                $data['ThreatClassification'] = $this->getThreatClassification($res->ThreatClassification);
                $data['TopMaliciousIP'] = (count($res->TopMaliciousIP) > 10) ? array_slice($res->TopMaliciousIP, 0, 10) : $res->TopMaliciousIP;
                $data['TopAttackersIP'] = (count($res->TopAttackersIP) > 10) ? array_slice($res->TopAttackersIP, 0, 10) : $res->TopAttackersIP;
                $data['TopSuspiciousIP'] = (count($res->TopSuspiciousIP) > 10) ? array_slice($res->TopSuspiciousIP, 0, 10) : $res->TopSuspiciousIP;
                $returnres['isSuccessfull'] = true;
            }
            $returnres['data'] = $data;
            return $returnres;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function filterChart(Request $request)
    {
        try {
            $data = [];
//            $data['live'] = $this->getLiveData();
            $data['visitor'] = $this->getVisitorsData($request->date, $request->days);
            $data['threat'] = $this->getThreatsData($request->date, $request->days);
            $data['page'] = $this->getPagesData($request->date, $request->days);

            return response::json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response::json(['data' => $e->getMessage()], 422);
        }
    }

    public function getVisitorsData($res, $day)
    {
        try {
            $data['total'] = 0;
            if (!empty($res)) {
                foreach ($res as $key => $val) {
                    $index = strpos($val->Date, 'T');
                    $data['labels'][] = substr($val->Date, 0, $index);
                    $data['data'][] = $val->Visitor;
                    $data['total'] = $data['total'] + $val->Visitor;
                }
                if( count($data['data']) < $day ){
                    $less = $day -  count($data['data']);
                    for( $i=0; $i<$less; $i++ ){
                        $data['data'][] = 0;
                    }
                }
            } else {
                $data['labels'] = [];
                $data['data'] = [];
            }
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getThreatsData($res, $day)
    {
        try {
            $data['total'] = 0;
            if (!empty($res)) {
                foreach ($res as $key => $val) {
                    $data['data'][] = $val->Threats;
                    $data['total'] = $data['total'] + $val->Threats;
                }
                if( count($data['data']) < $day ){
                    $less = $day -  count($data['data']);
                    for( $i=0; $i<$less; $i++ ){
                        $data['data'][] = 0;
                    }
                }
            } else {
                $data['labels'][] = '';
                $data['data'] = [];
            }
            $data['labels'] = [];
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getPagesData($res, $day)
    {
        try {
            $data['total'] = 0;
            if (!empty($res)) {
                foreach ($res as $key => $val) {
                    $data['data'][] = $val->Views;
                    $data['total'] = $data['total'] + $val->Views;
                }
                if( count($data['data']) < $day ){
                    $less = $day -  count($data['data']);
                    for( $i=0; $i<$less; $i++ ){
                        $data['data'][] = 0;
                    }
                }
            } else {
                $data['data'] = [];
            }
            $data['org_data'] = $res;
            $data['labels'] = [];
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getThreatsOrigin($res, $day)
    {
        try {
            if (!empty($res)) {
                foreach ($res as $key => $val) {
                    $data['code'][substr($val->ISO3Code, 0, 2)] = $val->Threats;
                    $data['selected'][] = substr($val->ISO3Code, 0, 2);
                }
            } else {
                $data['code'] = [];
                $data['selected'] = [];
            }

            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getTopMaliciousIPs($res)
    {
        try {
            if (count($res) > 10) {
                $res = array_slice($res, 0, 10);
            }
            return $res;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getTopAttackersIP($res)
    {
        try {
            if (count($res) > 10) {
                $res = array_slice($res, 0, 10);
            }
            return $res;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getTopSuspiciousIP($res)
    {
        try {

            if (count($res) > 10) {
                $res = array_slice($res, 0, 10);
            }
            return $res;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getThreatClassification($res)
    {
        try {
                if (!empty($res)) {
                    foreach ($res as $key => $val) {
                        $r[$val->Threat] = $val->Count;
                    }
                    arsort($r);
                } else {
                    $r = [];
                }
            return $r;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getLiveData($res)
    {
        try {
            $data['live_users'] = (!empty($res)) ? $res->Users : 0;
            $data['live_threats'] = (!empty($res)) ? $res->Threats : 0;
            $data['high_risk_threats'] = (!empty($res)) ? $res->HighRiskThreats : 0;
            $data['page_views'] = (!empty($res)) ? $res->PageViews : 0;

            if (!empty($res)) {
                $pu = $res->PercentageVisitorToday;
                $sign = (substr($pu, 0, 1) == '-') ? '-' : '+';
                $text = ($sign == '-') ? 'loss' : 'growth';
                $figure = ($sign == '-') ? round(str_replace('-', '', $pu)) : round($pu);
                $data['perUser'] = $figure.'% visitors '.$text;

                $pu = $res->PercentageThreatsToday;
                $sign = (substr($pu, 0, 1) == '-') ? '-' : '+';
                $text = ($sign == '-') ? 'decrease' : 'increase';
                $figure = ($sign == '-') ? round(str_replace('-', '', $pu)) : round($pu);
                $data['perThreats'] = $figure.'% '.$text.' in threats';

                $pu = $res->PercentageHighRiskThreatsToday;
                $sign = (substr($pu, 0, 1) == '-') ? '-' : '+';
                $text = ($sign == '-') ? 'decreased' : 'increased';
                $figure = ($sign == '-') ? round(str_replace('-', '', $pu)) : round($pu);
                $data['perHighThreats'] = $figure.'% '.$text.' in threats';

                $pu = $res->PercentagePageViewsToday;
                $sign = (substr($pu, 0, 1) == '-') ? '-' : '+';
                $text = ($sign == '-') ? 'decreased' : 'increased';
                $figure = ($sign == '-') ? round(str_replace('-', '', $pu)) : round($pu);
                $data['perPageView'] = $figure.'% '.$text.' page views';
            }
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getHighRiskThreats($dates)
    {
        try {
            $dates = explode(',', $dates);

            $endPoint = config('const.DASHBOARD_ENDPOINT').'getTopAttackersIP';
            $result = Http::withHeaders([
                'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            ])->post($endPoint, [
                "username" => "sam1",
                "password" => "sam121",
//                'start' => $dates[0] . ' 00:00:00',
//                'end' => $dates[1] . ' 00:00:00'
                "start" => "2020-04-01 00:00:00",
                "end" => "2020-07-01 00:00:00"
            ]);

            if ($result->successful()) {
                $data = json_decode($result->getBody())->Data;
                if (count($data) > 7) {
                    $data = array_slice($data, 0, 7);
                }
                foreach ($data as $key => $val) {
                    $res['data'][] = $val->Count;
                }
            } else {
                $res = [];
            }
            return $res;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getPlan(Request $request)
    {
        try {
            $shop = \Auth::user();
            $user = $this->getUser();
            if( $request->t == 'b' ){
                $endPoint = config('const.DASHBOARD_ENDPOINT').'getPageViews';
                $result = Http::withHeaders([
                    'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
                ])->post($endPoint, [
                    "days"=> 21,
                    "username" => $user->username,
                    "password" => $user->password,
                ]);
                $res = ($result->successful()) ? json_decode($result->getBody())->Data : [];
                $dates = ',';
                $data['plan'] = $this->getPlanD();
                $data['trial'] = $this->getTrialD();
                $data['pageView'] = $this->getPagesData($res, 21);

                $charge = Charge::select('activated_on')->where('user_id', $shop->id)->where('status', 'ACTIVE')->first();
                $data['plan']['start_dt'] = date("F d, Y", strtotime($charge['activated_on']));
                $data['plan']['end_dt'] = date('F d, Y', strtotime($charge['activated_on']. ' + 30 days'));
            }else{
                $data['plan']['plan_id'] = $shop->plan_id;
            }
            return response::json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response::json(['data' => $e->getMessage()], 422);
        }
    }
    public function getPlanD()
    {
        try {
            $shop = \Auth::user();
            $user = $this->getUser();
            $plan = Plan::select('name')->where('id', $shop->plan_id)->first();
            $data['plan_id'] = $shop->plan_id;
            $data['plan_name'] = $plan['name'];

            $endPoint = config('const.DASHBOARD_ENDPOINT').'getSubscriberCredit';

            $result = Http::withHeaders([
                'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            ])->post($endPoint, [
                "username" => $user->username,
                "password" => $user->password,
                'customerID' => $shop->name,
            ]);

            $data['total_credit'] = 0;
            $data['available_credit'] = 0;

            if ($result->successful()) {
                $subscription = $result->json();
                if ($subscription['IsSuccessful']) {
                    $data['total_credit'] = $subscription['Data']['TotalCredit'];
                    $data['available_credit'] = $subscription['Data']['AvailableCredit'];
                }
            }
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function getUser()
    {
        $user = Auth::user();
        $cyber_user = CybertonicaUser::select('username', 'password', 'created_at', 'email', 'storeID')->where('user_id', $user->id)->first();
        return $cyber_user;
    }

    public function getTrial(){
        try{
            return response::json(['data' => $this->getTrialD()], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }
    public function getTrialD(){
        try{
            $shop = \Auth::user();
            $charge = Charge::where('user_id', $shop->id)->where('status', 'ACTIVE')->where('trial_ends_on', '>=', date('Y-m-d H:i:s'))->first();

            if( $charge ){
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $charge['trial_ends_on']);
//                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $charge['activated_on']);
                $from = date('Y-m-d H:s:i');

                $data['trial_days'] = $to->diffInDays($from);
                $data['is_show'] = true;
            }else{
                $data['trial_days'] = 0;
                $data['is_show'] = false;
            }
            return $data;
        }catch( \Exception $e ){
            dd($e);
        }
    }
}
