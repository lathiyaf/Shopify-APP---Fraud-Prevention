<?php

namespace App\Http\Controllers\Threat;

use App\Http\Controllers\Controller;
use App\Models\CybertonicaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Response;

class ThreatController extends Controller
{
    public function index( Request $request ){
        try{
            $res = $this->getThreatData($request->data);
            return response::json(['data' => $res], 200);
        }catch( \Exception $e ){
            return response::json(['data' => $e->getMessage()], 422);
        }
    }

    public function getThreatData($data){
        try {
            $user = $this->getUser();
            $postData = [
                "username" => $user->username,
                "password" => $user->password,
                "start" => $data['start_date'],
                "end" => $data['end_date'],
                "type" => ($data['threat_type'] == 'all' ) ? '' : $data['threat_type'],
                "platform" => ($data['platform'] == 'all' ) ? '' : $data['platform'],
                "ipaddress" => '',
                "severity" => ($data['threat_severity'] == 'all' ) ? '' : $data['threat_severity'],
            ];
            $endPoint = config('const.DASHBOARD_ENDPOINT').'getallThreats';
            $result = Http::withHeaders([
                'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
            ])->post($endPoint, $postData);

            $res = ($result->successful()) ? json_decode($result->getBody())->Data->Data : [];
            return $res;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getUser()
    {
        $user = Auth::user();
        $cyber_user = CybertonicaUser::select('username', 'password')->where('user_id', $user->id)->first();
        return $cyber_user;
    }
}
