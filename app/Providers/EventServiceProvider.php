<?php

namespace App\Providers;

use App\Brand;
use App\Email;
use App\Category;
use App\ChatMessage;
use App\ScrapedProducts;
use Plank\Mediable\Media;
use App\Observers\BrandObserver;
use App\Observers\EmailObserver;
use App\Observers\MediaObserver;
use Illuminate\Support\Facades\Event;
use App\Observers\ChatMessageObserver;
use App\Observers\ChatMessageIndexObserver;
use App\Observers\ScrappedCategoryMappingObserver;
use App\Observers\ScrappedProductCategoryMappingObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        'Illuminate\Auth\Events\Login' => [
            \App\Listeners\LogSuccessfulLoginListener::class,
        ],

        'Illuminate\Auth\Events\Logout' => [
            \App\Listeners\LogSuccessfulLogoutListener::class,
        ],

        \App\Events\OrderCreated::class => [
            \App\Listeners\CreateOrderCashFlow::class,
        ],

        \App\Events\OrderUpdated::class => [
            \App\Listeners\UpdateOrderCashFlow::class,
        ],

        \App\Events\RefundCreated::class => [
            \App\Listeners\CreateRefundCashFlow::class,
        ],

        \App\Events\RefundDispatched::class => [
            \App\Listeners\UpdateRefundCashFlow::class,
        ],

        \App\Events\CaseBilled::class => [
            \App\Listeners\CreateCaseCashFlow::class,
        ],

        \App\Events\CaseBillPaid::class => [
            \App\Listeners\UpdateCaseCashFlow::class,
        ],

        \App\Events\ProformaConfirmed::class => [
            \App\Listeners\CreatePurchaseCashFlow::class,
        ],

        \App\Events\VendorPaymentCreated::class => [
            \App\Listeners\VendorPaymentCashFlow::class,
        ],

        \App\Events\CaseReceivableCreated::class => [
            \App\Listeners\CreateCaseReceivableCashFlow::class,
        ],

        \App\Events\BloggerPaymentCreated::class => [
            \App\Listeners\CreateBloggerCashFlow::class,
        ],

        \App\Events\VoucherApproved::class => [
            \App\Listeners\CreateVoucherCashFlow::class,
        ],

        \App\Events\PaymentReceiptCreated::class => [
            \App\Listeners\CreatePaymentReceiptCashflow::class,
        ],

        \App\Events\PaymentReceiptUpdated::class => [
            \App\Listeners\UpdatePaymentReceiptCashflow::class,
        ],

        \App\Events\PaymentCreated::class => [
            \App\Listeners\CreatePaymentCashflow::class,
        ],

        \App\Events\PaymentUpdated::class => [
            \App\Listeners\UpdatePaymentCashflow::class,
        ],

        'App\Events\PurchaseCreated' => [
            'App\Listeners\CreatePurchaseCashflow',
        ],

        'App\Events\PurchaseUpdated' => [
            'App\Listeners\UpdatePurchaseCashflow',
        ],

        \App\Events\CashFlowCreated::class => [
            \App\Listeners\CreateCurrencyCashFlow::class,
        ],

        \App\Events\CashFlowUpdated::class => [
            \App\Listeners\UpdateCurrencyCashFlow::class,
        ],
        \App\Events\MonetaryAccountCreated::class => [
            \App\Listeners\MonetaryAccountHistoryCreate::class,
        ],

        \App\Events\MonetaryAccountUpdated::class => [
            \App\Listeners\MonetaryAccountHistoryUpdate::class,
        ],
        \App\Events\SendgridEventCreated::class => [
            \App\Listeners\SendgridEventCreatedListner::class,
        ],
        'Illuminate\Mail\Events\MessageSent' => [
            \App\Events\MessageIdTranscript::class,
        ],
        'Illuminate\Mail\Events\MessageSending' => [
            \App\Listeners\AddSignatureToMail::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\YouTube\YouTubeExtendSocialite::class . '@handle',
        ],
        'App\Events\AppointmentFound' => [
            'App\Listeners\AppointmentNotify',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Brand::observe(BrandObserver::class);
        Email::observe(EmailObserver::class);
        Media::observe(MediaObserver::class);
        ChatMessage::observe(ChatMessageObserver::class);
        ChatMessage::observe(ChatMessageIndexObserver::class);
        Category::observe(ScrappedCategoryMappingObserver::class);
        ScrapedProducts::observe(ScrappedProductCategoryMappingObserver::class);
    }
}
