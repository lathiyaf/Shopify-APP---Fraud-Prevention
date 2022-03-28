<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\Ss_contract;
use App\Models\SsActivityLog;
use App\Models\SsContractLineItem;
use App\Models\SsDeletedProduct;
use App\Models\SsEmail;
use App\Models\SsEvents;
use App\Models\SsFailedPayment;
use App\Models\SsOrder;
use App\Models\SsPlanGroup;
use App\Models\SsPlanGroupVariant;
use App\Models\SsSetting;
use App\Models\SsTwilio;
use App\Models\SsTwilioBlacklist;
use App\Models\SsWebhook;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Contracts\Commands\Shop as IShopCommand;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
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
    public function handle(
        IShopCommand $shopCommand,
        IShopQuery $shopQuery,
        CancelCurrentPlan $cancelCurrentPlanAction
    ): bool {
        logger('=============== AppUninstalledJob =============');
//        $domain = $this->shopDomain->toNative();
        // Get the shop
        $user = $shopQuery->getByDomain($this->shopDomain);

        $shopId = $user->getId();

        // Cancel the current plan
        $cancelCurrentPlanAction($shopId);

        // Purge shop of token, plan, etc.
        $shopCommand->clean($shopId);

        // Soft delete the shop.
        $shopCommand->softDelete($shopId);

        return true;
    }
}
