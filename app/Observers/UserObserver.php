<?php

namespace App\Observers;
use App\User;
use App\Models\CybertonicaUser;
use Osiset\ShopifyApp\Storage\Models\Plan;
use Illuminate\Support\Facades\Http;

class UserObserver
{
    /**
     * @param  User  $user
     */
    public function saving(User $user)
    {
    	$old_plan = $user->getOriginal('plan_id');
        $err_msg = $user->getOriginal('error_message');
    	$new_plan = $user->plan_id;

    	if( $old_plan != $new_plan ){
    		$customerID = $user->name;
    		$plan = Plan::select('name')->where('id', $new_plan)->first();
    		// $cybertonicaUser = CybertonicaUser::where('user_id', $user->id)->where('is_success', 1)->first();
    		// dd($cybertonicaUser);

    		$action = ( $old_plan == '' || is_null($old_plan)) ? 'new' : 'update';
            ($plan) ? $this->cybertonica($plan->name, $customerID, $action, $user->id, $new_plan) : '';
    	}
    }

    public function cybertonica($planName, $customerID, $action, $user_id, $new_plan){
        $end = ( $action == 'new' ) ? 'createNewSubscription' : 'ChangeSubscriberPackage';
        $endPoint = config('const.DASHBOARD_ENDPOINT') . $end;

        \Log::info($endPoint);
        \Log::info($planName);
        $result = Http::withHeaders([
            'Authorization' => config('const.CYBERTONICA_AUTHORIZATION'),
        ])->post($endPoint, [
            'username' => 'zeshan',
            'password' => '123',
            'customerID' => $customerID,
            'package' => $planName
        ]);
        \Log::info($result->json());

        if( $result->successful() ){
            $data = $result->json();
            if( !$data['IsSuccessful'] ){
                $user = User::find($user_id);
                $user->error_message = json_encode($data);
                $user->save();
            }
        }else{
            $user = User::find($user_id);
            $user->error_message = json_encode($result->json());
            $user->save();
        }
    }
}
