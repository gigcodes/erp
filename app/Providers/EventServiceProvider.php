<?php

namespace App\Providers;

use App\Brand;
use App\Category;
use App\Email;
use App\Observers\BrandObserver;
use App\Observers\EmailObserver;
use App\Observers\MediaObserver;
use App\Observers\ScrappedCategoryMappingObserver;
use App\Observers\ScrappedProductCategoryMappingObserver;
use App\ScrapedProducts;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use \Plank\Mediable\Media;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event'                 => [
            'App\Listeners\EventListener',
        ],

        'Illuminate\Auth\Events\Login'     => [
            'App\Listeners\LogSuccessfulLoginListener',
        ],

        'Illuminate\Auth\Events\Logout'    => [
            'App\Listeners\LogSuccessfulLogoutListener',
        ],

        'App\Events\OrderCreated'          => [
            'App\Listeners\CreateOrderCashFlow',
        ],

        'App\Events\OrderUpdated'          => [
            'App\Listeners\UpdateOrderCashFlow',
        ],

        'App\Events\RefundCreated'         => [
            'App\Listeners\CreateRefundCashFlow',
        ],

        'App\Events\RefundDispatched'      => [
            'App\Listeners\UpdateRefundCashFlow',
        ],

        'App\Events\CaseBilled'            => [
            'App\Listeners\CreateCaseCashFlow',
        ],

        'App\Events\CaseBillPaid'          => [
            'App\Listeners\UpdateCaseCashFlow',
        ],

        'App\Events\ProformaConfirmed'     => [
            'App\Listeners\CreatePurchaseCashFlow',
        ],

        'App\Events\VendorPaymentCreated'  => [
            'App\Listeners\VendorPaymentCashFlow',
        ],

        'App\Events\CaseReceivableCreated' => [
            'App\Listeners\CreateCaseReceivableCashFlow',
        ],

        'App\Events\BloggerPaymentCreated' => [
            'App\Listeners\CreateBloggerCashFlow',
        ],

        'App\Events\VoucherApproved'       => [
            'App\Listeners\CreateVoucherCashFlow',
        ],

        'App\Events\PaymentReceiptCreated' => [
            'App\Listeners\CreatePaymentReceiptCashflow',
        ],

        'App\Events\PaymentReceiptUpdated' => [
            'App\Listeners\UpdatePaymentReceiptCashflow',
        ],

        'App\Events\PaymentCreated' => [
            'App\Listeners\CreatePaymentCashflow',
        ],

        'App\Events\PaymentUpdated' => [
            'App\Listeners\UpdatePaymentCashflow',
        ],

        'App\Events\PurchaseCreated' => [
            'App\Listeners\CreatePurchaseCashflow',
        ],

        'App\Events\PurchaseUpdated' => [
            'App\Listeners\UpdatePurchaseCashflow',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Brand::observe(BrandObserver::class);
        Email::observe(EmailObserver::class);
        Media::observe(MediaObserver::class);

        Category::observe(ScrappedCategoryMappingObserver::class);

        ScrapedProducts::observe(ScrappedProductCategoryMappingObserver::class);
        //
    }
}
