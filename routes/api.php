<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Logging;
use App\Http\Controllers\Products;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\scrapperPhyhon;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScrapController;
use App\Http\Controllers\TwilioController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\Api\v1\ReferaFriend;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\QuickSellController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UpdateLogController;
use App\Http\Controllers\LaravelLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\WeTransferController;
use App\Http\Controllers\InfluencersController;
use App\Http\Controllers\ResourceImgController;
use App\Http\Controllers\SearchQueueController;
use App\Http\Controllers\FacebookPostController;
use App\Http\Controllers\GitHubActionController;
use App\Http\Controllers\GoogleSearchController;
use App\Http\Controllers\MissingBrandController;
use App\Http\Controllers\Api\v1\TicketController;
use App\Http\Controllers\SocialWebhookController;
use App\Http\Controllers\Api\v1\BuyBackController;
use App\Http\Controllers\InstagramPostsController;
use App\Http\Controllers\MagentoCareersController;
use App\Http\Controllers\MagentoProblemController;
use App\Http\Controllers\ProductCropperController;
use App\Http\Controllers\Api\v1\GiftCardController;
use App\Http\Controllers\GoogleAffiliateController;
use App\Http\Controllers\ScrapStatisticsController;
use App\Http\Controllers\Shopify\ShopifyController;
use App\Http\Controllers\Api\v1\AffiliateController;
use App\Http\Controllers\InstantMessagingController;
use App\Http\Controllers\PriceComparisionController;
use App\Http\Controllers\ProductTemplatesController;
use App\Http\Controllers\BrokenLinkCheckerController;
use App\Http\Controllers\Github\RepositoryController;
use App\Http\Controllers\SimplyDutyCountryController;
use App\Http\Controllers\Api\v1\BrandReviewController;
use App\Http\Controllers\SimplyDutyCurrencyController;
use Modules\ChatBot\Http\Controllers\MessageController;
use App\Http\Controllers\Api\v1\GoogleScrapperController;
use App\Http\Controllers\SimplyDutyCalculationController;
use App\Http\Controllers\NodeScrapperCategoryMapController;
use App\Http\Controllers\MagentoCustomerReferenceController;
use App\Http\Controllers\Api\v1\PushFcmNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('mailinglist/add', [Api\v1\MailinglistController::class, 'add']);
Route::post('fetch-credit-balance', [CustomerController::class, 'fetchCreditBalance']);
Route::post('deduct-credit', [CustomerController::class, 'deductCredit']);
Route::post('add-env', [EnvController::class, 'addEnv'])->name('add-env');
Route::post('edit-env', [EnvController::class, 'editEnv'])->name('edit-env');

Route::post('add-credit', [CustomerController::class, 'addCredit']);

Route::post('customer/add_customer_data', [CustomerController::class, 'add_customer_data']); //Purpose : Add Customer Data - DEVTASK-19932

Route::post('scrape/queue', [Products\ScrapeController::class, 'getUrlFromQueue']);
Route::get('scrape/process', [Products\ScrapeController::class, 'processDataFromScraper']);
Route::post('scrape/send-screenshot', [ScrapController::class, 'sendScreenshot']);
Route::post('scrape/send-position', [ScrapController::class, 'sendPosition']);

Route::get('messages/{thread}', [InstagramController::class, 'getThread']);
Route::post('messages/{thread}', [InstagramController::class, 'replyToThread']);
Route::post('sync-product', [ScrapController::class, 'syncGnbProducts']); // This function is not found in controller
Route::post('scrap-products/add', [ScrapController::class, 'syncProductsFromNodeApp']);
Route::post('add-product-entries', [ScrapController::class, 'addProductEntries']); // This function is not found in controller
Route::post('add-product-images', [ScrapController::class, 'getProductsForImages']); // This function is not found in controller
Route::post('save-product-images', [ScrapController::class, 'saveImagesToProducts']); // This function is not found in controller
Route::post('save-product-images2', [ScrapController::class, 'saveImagesToProducts2']); // This function is not found in controller
Route::post('save-supplier', [ScrapController::class, 'saveSupplier']);
Route::get('hashtags', [HashtagController::class, 'sendHashtagsApi']);
Route::get('crop', [ProductController::class, 'giveImage']);
Route::post('link/image-crop', [ProductController::class, 'saveImage']);
Route::post('instagram/create', [AccountController::class, 'createAccount']);
Route::resource('stat', ScrapStatisticsController::class);

Route::get('crop/amends', [ProductCropperController::class, 'giveAmends']);
Route::post('crop/amends', [ProductCropperController::class, 'saveAmends']);

Route::get('products/auto-rejected', [ScrapController::class, 'getProductsToScrape']);
Route::post('products/auto-rejected', [ScrapController::class, 'saveScrapedProduct']); // This function is not found in controller

Route::get('products/get-products-to-scrape', [ScrapController::class, 'getProductsToScrape']); // This function is also call for other route
Route::post('products/save-scraped-product', [ScrapController::class, 'saveScrapedProduct']); // This function is not found in controller

Route::post('twilio-conference', [TwilioController::class, 'outgoingCallConference']);
Route::post('twilio-conference-mute', [TwilioController::class, 'muteConferenceNumber']);
Route::post('twilio-conference-hold', [TwilioController::class, 'holdConferenceNUmber']);
Route::post('twilio-conference-remove', [TwilioController::class, 'removeConferenceNumber']);
Route::get('products/new-supplier', [ScrapController::class, 'getProductsToScrape']); // This function is also call for other route
Route::post('products/new-supplier', [ScrapController::class, 'saveFromNewSupplier']);

Route::get('broken-link-details', [BrokenLinkCheckerController::class, 'getBrokenLinkDetails']);

Route::get('products/enhance', [Products\ProductEnhancementController::class, 'index']);
Route::post('products/enhance', [Products\ProductEnhancementController::class, 'store']);
Route::post('users/updatePermission', [PermissionController::class, 'updatePermission']);
Route::post('userLogs', [UserLogController::class, 'store']);
Route::post('scrape/process-product-links', [ScrapController::class, 'processProductLinks']);
Route::post('scrape/process-product-links-by-brand', [ScrapController::class, 'processProductLinksByBrand']);
Route::post('values-as-per-user', [DocumentController::class, 'getDataByUserType'])->name('getDataByUserType');
Route::post('values-as-per-category', [ResourceImgController::class, 'getSubCategoryByCategory'])->name('imageResourceSubcategory');
Route::post('get-customers', [QuickSellController::class, 'getCustomers'])->name('getCustomers'); // This function is not found in controller

Route::get('product-template', [ProductTemplatesController::class, 'apiIndex']);
Route::post('product-template', [ProductTemplatesController::class, 'apiSave']);
Route::post('new-product-template', [ProductTemplatesController::class, 'NewApiSave']);

Route::get('{client}/{numberFrom}/get-im', [InstantMessagingController::class, 'getMessage']);
Route::post('{client}/{numberFrom}/webhook', [InstantMessagingController::class, 'processWebhook']);
Route::get('{client}/{numberFrom}/im-status-update', [InstantMessagingController::class, 'updatePhoneStatus']);

Route::post('{client}/{numberFrom}/social-message', [FacebookController::class, 'storeMessages']); // This function is not found in controller

//Competitor Facebook
Route::get('{client}/{numberFrom}/competitor', [FacebookController::class, 'competitor']); // This function is not found in controller

Route::post('{client}/{numberFrom}/competitor', [FacebookController::class, 'saveCompetitor']); // This function is not found in controller

//Scrapped facebook users
Route::post('facebook/scrape-user', [FacebookController::class, 'apiPost']);

Route::post('facebook/post', [FacebookController::class, 'facebookPost']);

Route::get('duty/v1/get-currencies', [SimplyDutyCurrencyController::class, 'sendCurrencyJson']);
Route::get('duty/v1/get-countries', [SimplyDutyCountryController::class, 'sendCountryJson']);
Route::post('duty/v1/calculate', [SimplyDutyCalculationController::class, 'calculate']);

// INSTAGRAM
Route::post('instagram/post', [InstagramPostsController::class, 'apiPost']);

Route::get('instagram/send-account/{token}', [InstagramPostsController::class, 'sendAccount']);
Route::get('instagram/get-comments-list/{username}', [InstagramPostsController::class, 'getComments']);
Route::post('instagram/comment-sent', [InstagramPostsController::class, 'commentSent']);
Route::get('instagram/get-hashtag-list', [InstagramPostsController::class, 'getHashtagList']);

//Get all the instagram accounts attached to keywords
Route::get('instagram/accounts', [InfluencersController::class, 'getKeywordsWithAccount']);

//Giving All Brands with Reference
Route::get('brands', [BrandController::class, 'brandReference']);

// SUPPLIERS
Route::post('supplier/brands-raw', [SupplierController::class, 'apiBrandsRaw']);

//Google search
Route::get('google/keywords', [GoogleSearchController::class, 'getKeywordsApi']);
Route::post('google/search-results', [GoogleSearchController::class, 'apiPost']);

//Wetransfer
Route::get('wetransfer', [WeTransferController::class, 'getLink']);
Route::post('wetransfer-file-store', [WeTransferController::class, 'storeFile']);

//Google affiliate search
Route::get('google/affiliate/keywords', [GoogleAffiliateController::class, 'getKeywordsApi']);
Route::post('google/affiliate/search-results', [GoogleAffiliateController::class, 'apiPost']);

Route::get('scraper/next', [ScrapController::class, 'sendScrapDetails']);
Route::post('scraper/endtime', [ScrapController::class, 'recieveScrapDetails']);

Route::get('search/{type}', [SearchQueueController::class, 'index']);
Route::post('search/{type}', [SearchQueueController::class, 'upload_content']);
//Google Developer API

//Magneto Customer Reference Store
Route::post('magento/customer-reference', [MagentoCustomerReferenceController::class, 'store']);
Route::post('product-live-status', [Logging\LogListMagentoController::class, 'updateLiveProductCheck']);

Route::post('node/restart-script', [ScrapController::class, 'restartNode']);

Route::post('node/update-script', [ScrapController::class, 'updateNode']);

Route::post('node/kill-script', [ScrapController::class, 'killNode']);

Route::post('local/instagram-post', [InstagramPostsController::class, 'saveFromLocal']);

Route::get('local/instagram-user-post', [InstagramPostsController::class, 'getUserForLocal']);

Route::post('node/get-status', [ScrapController::class, 'getStatus']);
Route::get('node/get-log', [ScrapController::class, 'getLatestLog'])->name('scraper.get.log.list');

Route::group([
    'prefix' => 'v1',
], function () {
    Route::group([
        'prefix' => 'product',
    ], function () {
        Route::group([
            'prefix' => '{sku}',
        ], function () {
            Route::get('price', [Api\v1\ProductController::class, 'price']);
        });
    });

    Route::group([
        'prefix' => 'account',
    ], function () {
        Route::post('create', [Api\v1\AccountController::class, 'create']);
    });
});

// Scraper ready api
Route::post('scraper/ready', [ScrapController::class, 'scraperReady']);
Route::post('scraper/completed', [ScrapController::class, 'scraperCompleted']);
Route::get('scraper/need-to-start', [ScrapController::class, 'needToStart']);
Route::get('scraper/update-restart-time', [ScrapController::class, 'updateRestartTime']);
Route::get('scraper/auto-restart', [ScrapController::class, 'needToAutoRestart']);
Route::get('scraper-needed-products', [ScrapController::class, 'scraperNeeded']);

Route::post('shopify/customer/create', [ShopifyController::class, 'setShopifyCustomers']);
Route::post('shopify/order/create', [ShopifyController::class, 'setShopifyOrders']);

Route::get('price_comparision/{type}', [PriceComparisionController::class, 'index']);
Route::post('price_comparision/store', [PriceComparisionController::class, 'storeComparision']);

//order details api for a customer
Route::get('customer/order-details', [OrderController::class, 'customerOrderDetails']);

//refer a friend api
Route::post('friend/referral/create', [ReferaFriend::class, 'store']);
Route::post('price_comparision/details', [PriceComparisionController::class, 'sendDetails']);

//Ticket api
Route::post('ticket/create', [TicketController::class, 'store']);
Route::post('store_reviews', [Api\v1\CustomerController::class, 'storeReviews']);
Route::get('all-reviews', [Api\v1\CustomerController::class, 'allReviews']);
Route::post('ticket/send', [TicketController::class, 'sendTicketsToCustomers']);

Route::post('facebook/post/status', [FacebookPostController::class, 'setPostStatus']);
Route::post('facebook/account', [FacebookPostController::class, 'getPost']);

//gift cards api
Route::post('giftcards/add', [GiftCardController::class, 'store']);
Route::get('giftcards/check-giftcard-coupon-amount', [GiftCardController::class, 'checkGiftcardCouponAmount']);

Route::post('facebook/post/status', [FacebookPostController::class, 'setPostStatus']); // this route is decleared above
Route::post('facebook/account', [FacebookPostController::class, 'getPost']); // this route is decleared above

//gift cards api
Route::post('giftcards/add', [GiftCardController::class, 'store']); // this route is decleared above
Route::get('giftcards/check-giftcard-coupon-amount', [GiftCardController::class, 'checkGiftcardCouponAmount']); // this route is decleared above

//Affiliate Api
Route::post('affiliate/add', [AffiliateController::class, 'store']);
Route::post('influencer/add', [AffiliateController::class, 'store']);

//buyback cards api
Route::get('orders/products', [BuyBackController::class, 'checkProductsForBuyback']);
Route::post('return-exchange-buyback/create', [BuyBackController::class, 'store']);

//Push Notification Api
Route::post('notification/create', [PushFcmNotificationController::class, 'create']);
Route::post('notification/update-lang', [PushFcmNotificationController::class, 'updateLang']);

//Saving Not Found Brand
Route::get('missing-brand/save', [MissingBrandController::class, 'saveMissingBrand']);
// Scraper info
Route::get('{supplierName}/supplier-list', [SupplierController::class, 'supplierList']);

//Store data into the laravel_logs
Route::post('laravel-logs/save', [LaravelLogController::class, 'saveNewLogData']);

Route::post('templates/create/webhook', [TemplatesController::class, 'createWebhook']);
Route::post('product/templates/update/webhook', [ProductTemplatesController::class, 'updateWebhook'])->name('api.product.update.webhook');

//check for order cancellation
Route::post('order/check-cancellation', [Api\v1\ProductController::class, 'checkCancellation']);
Route::post('order/check-return', [Api\v1\ProductController::class, 'checkReturn']);
Route::post('order/check-category-is-eligibility', [Api\v1\ProductController::class, 'checkCategoryIsEligibility']);
Route::post('wishlist/create', [Api\v1\ProductController::class, 'wishList']);
Route::post('wishlist/remove', [Api\v1\ProductController::class, 'wishListRemove']);
Route::post('github/gettoken', [RepositoryController::class, 'addGithubTokenHistory']);

Route::post('magento/order-create', [MagentoCustomerReferenceController::class, 'createOrder']);

Route::post('scraper-images-save', [scrapperPhyhon::class, 'imageSave']);

//New API for trust pilot reviews
Route::get('review/get', [BrandReviewController::class, 'getAllBrandReview']);
Route::post('review/scrap', [BrandReviewController::class, 'storeReview']);
Route::post('google-scrapper-data', [GoogleScrapperController::class, 'extractedData']);

//Out Of Stock Subscribe
Route::post('out-of-stock-subscription', [Api\v1\OutOfStockSubscribeController::class, 'Subscribe']);
Route::any('get-order-stat', [Api\v1\OutOfStockSubscribeController::class, 'getOrderState']);
Route::post('customer/add_cart_data', [Api\v1\CustomerController::class, 'add_cart_data']);

// Social Webhook
Route::get('social/webhook', [SocialWebhookController::class, 'verifyWebhook']);
Route::get('social/webhookfbtoken', [SocialWebhookController::class, 'webhookfbtoken']);
Route::post('social/webhook', [SocialWebhookController::class, 'webhook']);
Route::post('social/fbtoken', [SocialWebhookController::class, 'fbtoken']);

//Sync Transaction with order
Route::post('order/sync-transaction', [OrderController::class, 'syncTransaction']);

Route::post('updateLog', [UpdateLogController::class, 'store']);

Route::post('login', [Api\v1\Auth\LoginController::class, 'login']);
Route::post('register', [Api\v1\Auth\LoginController::class, 'register']);
Route::middleware('api')->prefix('auth')->group(function ($router) {
    Route::post('logout', [Api\v1\Auth\LoginController::class, 'logout']);
    Route::post('refresh', [Api\v1\Auth\LoginController::class, 'refresh']);
    Route::post('me', [Api\v1\Auth\LoginController::class, 'me']);
});
Route::middleware('custom.api.auth')->group(function () {
    Route::get('/chatbot/messages', [MessageController::class, 'messagesJson']);
    Route::get('/email/{email?}', [EmailController::class, 'emailJson']);
    Route::get('/todolist', [TodoListController::class, 'indexJson']);
    Route::post('/todolist/update', [TodoListController::class, 'updateJson']);
    Route::delete('/todolist/delete/{id}', [TodoListController::class, 'destroyJson']);
});

Route::post('users/add-system-ip-from-email', [UserController::class, 'addSystemIpFromEmail']);

Route::post('/github-action', [GitHubActionController::class, 'store']);

Route::post('/magento-problem', [MagentoProblemController::class, 'store']);
Route::get('magento_modules/listing-careers', [MagentoCareersController::class, 'listingApi'])->name('magento_module_listing_careers_listing_api');

Route::post('scrapper-category-map', [NodeScrapperCategoryMapController::class, 'store']);
Route::post('get-scrapper-category-map', [NodeScrapperCategoryMapController::class, 'getRecord']);
