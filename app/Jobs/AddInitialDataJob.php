<?php

namespace App\Jobs;

use App\Models\CybertonicaUser;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AddInitialDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('-----------------------AFTER AUTHENTICATION JOB-----------------------');

        try {
            $shop = Auth::user();

            \Log::info('--------------------- add snippet --------------------');
            $type = 'add';

            $parameter['role'] = 'main';
            $result = $shop->api()->rest('GET', '/admin/api/2020-01/themes.json',$parameter);

            // add value in setting table
            $keys = ['timezone' => 'Asia/Kolkata', 'risk_score' => 200, 'risk_score_range' => json_encode([200,600]), 'manage_email_notification' => 0];

            foreach ($keys as $key=>$val){
                $setting = Setting::where('user_id', $shop->id)->where('key', $key)->first();
                $setting = ( $setting ) ? $setting : new Setting;
                $setting->user_id = $shop->id;
                $setting->key = ($setting->key) ? $setting->key : $key;
                $setting->value = ($setting->value) ? $setting->value : $val;
                $setting->save();
            }

            $theme_id = $result->body->themes[0]->id;
            \Log::info('Theme id :: ' . $theme_id);
            if($type == 'add') {
                $value = <<<EOF
            {%- if content_for_header contains "order-tra-list" -%}
            <script src="https://pxl.cybertonica.com/js/v2/beacon.min.js"></script>
            <script>
                if (typeof window.jQuery == 'undefined') {
                    var script = document.createElement('script');
                    script.type = "text/javascript";
                    script.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js";
                    document.getElementsByTagName('head')[0].prepend(script);
                  }

                window.addEventListener("load", function() {
                    var tid = undefined;
                    if (typeof AFCYBERTONICA !== "undefined") {
                        tid = AFCYBERTONICA.init('{{ shop.domain }}', undefined, "https://pxl.cybertonica.com");
                        localStorage.setItem("cybertonica_tid", tid);
                    }
                });
            </script>
            {% endif %}
EOF;
            }
            $parameter['asset']['key'] = 'snippets/order-tracking.liquid';
            $parameter['asset']['value'] = $value;
            $asset = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id . '/assets.json',$parameter);
            \Log::info('--------------- snippet --------------------');

            $this->updateThemeLiquid($theme_id, 'order-tracking.liquid', $shop);

            $this->RegisterCybertonica();

        } catch (\Exception $e) {
            \Log::info('-----------------------ERROR :: AFTER AUTHENTICATION JOB-----------------------');
            \Log::info($e);
        }
    }
    public function updateThemeLiquid($theme_id, $snippet_name, $shop)
    {
        try {
            \Log::info('-----------------------updateThemeLiquid-----------------------');
            $asset = $shop->api()->rest('GET', 'admin/themes/'.$theme_id.'/assets.json',
                ["asset[key]" => 'layout/theme.liquid']);
            if (@$asset->body->asset) {
                $asset = $asset->body->asset->value;

                $search_string = "</head>";

                // add before </head>
                if (!strpos($asset, "{% include '$snippet_name' %}")) {
                    $asset = str_replace('</head>', "{% include '$snippet_name' %}</head>", $asset);
                }

                $parameter['asset']['key'] = 'layout/theme.liquid';
                $parameter['asset']['value'] = $asset;
                $result = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id.'/assets.json', $parameter);
                \Log::info(json_encode($result));
            }
        } catch (\Exception $e) {
            \Log::info('------------ERROR :: updateThemeLiquid--------------');
            logger($e);
        }

    }

    /**
     *
     */
    public function RegisterCybertonica(){
        \Log::info('-----------------------START :: RegisterCybertonica -----------------------');
        try {
            $shop = Auth::user();

            $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->where('is_success', 1)->first();

            if( !$cybertonica_user ){
                $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json';
                $parameter['fields'] = "id,email,name,shop_owner,domain";
                $shop_data = $shop->api()->rest('GET', $endPoint, $parameter);
                if (!$shop_data->errors) {
                    $shop_data = $shop_data->body->shop;
                    $name = explode(' ', $shop_data->shop_owner);

                    $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->first();

                    $password = ($cybertonica_user) ?  $cybertonica_user->password : $this->generatePassword();

                    $cybertonica_user = ($cybertonica_user) ? $cybertonica_user : new CybertonicaUser;

                    $cybertonica_user->user_id = $shop->id;
                    $cybertonica_user->username = $shop_data->name;
                    $cybertonica_user->password = $password;
                    $cybertonica_user->role = 'client';
                    $cybertonica_user->storeID = $shop_data->id;
                    $cybertonica_user->firstname = (@$name[0]) ? $name[0] : '';
                    $cybertonica_user->lastname = (@$name[1]) ? $name[1] : '';
                    $cybertonica_user->email = $shop_data->email;
                    $cybertonica_user->teams = json_encode([1, 2, 3]);
                    $cybertonica_user->save();

                    $is_cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->where('is_success', 1)->first();

                    ( !$is_cybertonica_user ) ? $this->addCyberData($shop_data, $shop_data->name, $name, $password) : '';
                }
            }
        } catch (\Exception $e) {
            logger('-----------------------ERROR :: RegisterCybertonica-----------------------');
            logger(json_encode($e));
        }
    }

    /**
     * @param $shop_data
     * @param $unm
     * @param $name
     * @param $password
     */
    public function addCyberData($shop_data, $unm, $name, $password){
        try{
            $endPoint = config('const.DASHBOARD_ENDPOINT').'CreateNewUser';
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
        }catch(\Exception $e){
            logger('-----------------------ERROR :: addCyberData-----------------------');
            logger(json_encode($e));
        }
    }

    /**
     * @param $unm
     */
    public function updateUser($unm){
        try{
            $shop = Auth::user();
            $cybertonica_user = CybertonicaUser::where('user_id', $shop->id)->first();
            $cybertonica_user->username = $unm;
            $cybertonica_user->is_success = 1;
            $cybertonica_user->save();
        }catch(\Exception $e){
            logger('-----------------------ERROR :: updateUser-----------------------');
            logger(json_encode($e));
        }
    }

    /**
     * @return string
     */
    public function generatePassword()
    {
        $lc = range('a', 'z');
        $uc = range('A', 'Z');
        $digits =  range(0,10);
        $spe_char = ['$', '#', '@', '!', '.', '-','$', '#', '@', '!', '.', '-' ];

        shuffle($lc);
        shuffle($uc);
        shuffle($digits);
        shuffle($spe_char);

        $LC = array_slice($lc,5,4);
        $UC = array_slice($uc,5,4);
        $SPE_CHAR = array_slice($spe_char,3,5);
        $DIGITS = array_slice($digits,5,5);

        $SPE_LC = array_merge($LC, $SPE_CHAR);
        $DIG_UC = array_merge( $UC, $DIGITS);

        shuffle($SPE_LC);
        shuffle($DIG_UC);

        $characters = array_merge( $SPE_LC, $DIG_UC );
        shuffle($characters);
        $org = implode('',$characters);
        return $org;
    }
}
