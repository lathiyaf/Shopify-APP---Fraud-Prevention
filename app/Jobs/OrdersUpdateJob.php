<?php namespace App\Jobs;

use App\Events\CheckOrder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;
use Response;
class OrdersUpdateJob implements ShouldQueue
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
     * @param string   $shopDomain The shop's myshopify domain
     * @param stdClass $data    The webhook data (JSON decoded)
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
        try{
            \Log::info( '=============== START:: Order Update Webhook ==============');
            $shop_domain = $this->shopDomain->toNative();
            $shop = User::where('name', $shop_domain)->first();

            if( $shop->plan_id ){
                $sh_order = $this->data;
                $order = Order::where('order_id', $sh_order->id)->where('user_id', $shop->id)->first();

                if( !$order ){
                    $order = new Order;
                    $order->user_id = $shop->id;
                    $order->order_id = $this->data->id;
                    $order->status = 0;
                    $order->order_created_at = date("Y-m-d H:i:s", strtotime($this->data->created_at));
                    $order->save();
                    $action = 'create';
                }else{
                    $action = 'update';
                }
                event(new CheckOrder($order->id, $shop->id, $this->data->id, $action));
            }
           
            return response()->json(['data' => 'success'], 200);
        }catch( \Exception $e ){
            \Log::info( '=============== ERROR:: Order Update Webhook ==============');
            \Log::info( json_encode($e) );
        }
        // Access domain name as $this->shopDomain->toNative()
    }
}
