<?php namespace App\Jobs;

use App\Events\CheckOrder;
use App\Models\Order;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;
use Response;

class OrdersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param  string  $shopDomain  The shop's myshopify domain
     * @param  stdClass  $data  The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::Info("=============== Order Create Webhook ==================");
        try {
            return response()->json(['data' => 'success'], 200);
//            $shopDomain = $this->shopDomain->toNative();
//            $user = User::select('id')->where('name', $shopDomain)->first();
//            \Log::info(json_encode($user));
//             $user_id = ($user) ? $user->id : '';
//
//             $order = new Order;
//             $order->user_id = $user_id;
//             $order->order_id = $this->data->id;
//             $order->status = 0;
//             $order->order_created_at = date("Y-m-d H:i:s", strtotime($this->data->created_at));
//             $order->save();
//
//            event(new CheckOrder($order->id, $user_id, $this->data->id));
        } catch (\Exception $e) {
            \Log::Info('=============== ERROR:: Order Create ==================');
            \Log::Info(json_encode($e));
        }
    }
}
