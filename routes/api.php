<?php

use Illuminate\Http\Request;

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
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


/**
*Routes added by Hitesh Start
**/ 
Route::post('mailinglist/add', 'Api\v1\MailinglistController@add');
/**
*Routes added by Hitesh Ends
**/ 



Route::get('scrape/queue', 'Products\ScrapeController@getUrlFromQueue');
Route::get('scrape/process', 'Products\ScrapeController@processDataFromScraper');

Route::get('messages/{thread}', 'InstagramController@getThread');
Route::post('messages/{thread}', 'InstagramController@replyToThread');
Route::post('sync-product', 'ScrapController@syncGnbProducts');
Route::post('scrap-products/add', 'ScrapController@syncProductsFromNodeApp');
Route::post('add-product-entries', 'ScrapController@addProductEntries');
Route::post('add-product-images', 'ScrapController@getProductsForImages');
Route::post('save-product-images', 'ScrapController@saveImagesToProducts');
Route::post('save-product-images2', 'ScrapController@saveImagesToProducts2');
Route::post('save-supplier', 'ScrapController@saveSupplier');
Route::get('hashtags', 'HashtagController@sendHashtagsApi');
Route::get('crop', 'ProductController@giveImage');
Route::post('link/image-crop', 'ProductController@saveImage');
Route::post('instagram/create', 'AccountController@createAccount');
Route::resource('stat', 'ScrapStatisticsController');

Route::get('crop/amends', 'ProductCropperController@giveAmends');
Route::post('crop/amends', 'ProductCropperController@saveAmends');

Route::get('products/auto-rejected', 'ScrapController@getProductsToScrape');
Route::post('products/auto-rejected', 'ScrapController@saveScrapedProduct');

Route::get('products/get-products-to-scrape', 'ScrapController@getProductsToScrape');
Route::post('products/save-scraped-product', 'ScrapController@saveScrapedProduct');

Route::post('twilio-conference','TwilioController@outgoingCallConference');
Route::post('twilio-conference-mute','TwilioController@muteConferenceNumber');
Route::post('twilio-conference-hold','TwilioController@holdConferenceNUmber');
Route::post('twilio-conference-remove','TwilioController@removeConferenceNumber');
//Route::get('products/new-supplier', 'ScrapController@getFromNewSupplier');
Route::get('products/new-supplier', 'ScrapController@getProductsToScrape');
Route::post('products/new-supplier', 'ScrapController@saveFromNewSupplier');

Route::get('broken-link-details', 'BrokenLinkCheckerController@getBrokenLinkDetails');

Route::get('products/enhance', 'Products\ProductEnhancementController@index');
Route::post('products/enhance', 'Products\ProductEnhancementController@store');
Route::post('users/updatePermission', 'PermissionController@updatePermission');
Route::post('userLogs', 'UserLogController@store');
Route::post('scrape/process-product-links', 'ScrapController@processProductLinks');
Route::post('values-as-per-user', 'DocumentController@getDataByUserType')->name('getDataByUserType');
Route::post('values-as-per-category', 'ResourceImgController@getSubCategoryByCategory')->name('imageResourceSubcategory');
Route::post('get-customers', 'QuickSellController@getCustomers')->name('getCustomers');

Route::get('product-template', 'ProductTemplatesController@apiIndex');
Route::post('product-template', 'ProductTemplatesController@apiSave');


Route::get('{client}/{numberFrom}/get-im','InstantMessagingController@getMessage');
Route::post('{client}/{numberFrom}/webhook','InstantMessagingController@processWebhook');
Route::get('{client}/{numberFrom}/im-status-update','InstantMessagingController@updatePhoneStatus');
Route::post('{client}/{numberFrom}/social-message','FacebookController@storeMessages');

//Competitor Facebook
Route::get('{client}/{numberFrom}/competitor','FacebookController@competitor');

Route::post('{client}/{numberFrom}/competitor','FacebookController@saveCompetitor');

//Scrapped facebook users
Route::post('facebook/scrape-user','FacebookController@apiPost');


Route::get('duty/v1/get-currencies', 'SimplyDutyCurrencyController@sendCurrencyJson');
Route::get('duty/v1/get-countries', 'SimplyDutyCountryController@sendCountryJson');
Route::post('duty/v1/calculate', 'SimplyDutyCalculationController@calculate');

// INSTAGRAM
Route::post('instagram/post', 'InstagramPostsController@apiPost');

Route::get('instagram/send-account/{token}', 'InstagramPostsController@sendAccount');
Route::get('instagram/get-comments-list/{username}', 'InstagramPostsController@getComments');
Route::post('instagram/comment-sent', 'InstagramPostsController@commentSent');
Route::get('instagram/get-hashtag-list','InstagramPostsController@getHashtagList');

//Giving All Brands with Reference
Route::get('brands','BrandController@brandReference');


// SUPPLIERS
Route::post('supplier/brands-raw', 'SupplierController@apiBrandsRaw');

//Google search
Route::get('google/keywords', 'GoogleSearchController@getKeywordsApi');
Route::post('google/search-results', 'GoogleSearchController@apiPost');

//Wetransfer
Route::get('wetransfer', 'WeTransferController@getLink');
Route::post('wetransfer-file-store', 'WeTransferController@storeFile');

//Google affiliate search
Route::get('google/affiliate/keywords', 'GoogleAffiliateController@getKeywordsApi');
Route::post('google/affiliate/search-results', 'GoogleAffiliateController@apiPost');

Route::get('scraper/next','ScrapController@sendScrapDetails');
Route::post('scraper/endtime','ScrapController@recieveScrapDetails');

Route::get('search/{type}', 'SearchQueueController@index');
Route::post('search/{type}', 'SearchQueueController@upload_content');

//Magneto Customer Reference Store
Route::post('magento/customer-reference','MagentoCustomerReferenceController@store');

Route::post('node/restart-script','ScrapController@restartNode');

Route::post('local/instagram-post','InstagramPostsController@saveFromLocal');

Route::get('local/instagram-user-post','InstagramPostsController@getUserForLocal');

Route::post('node/get-status','ScrapController@getStatus');

Route::prefix('v1')->group(function () {
    Route::prefix('product')->group(function () {
        Route::prefix('{sku}')->group(function () {
            Route::get('price', '\App\Http\Controllers\Api\v1\ProductController@price');
        });
    });

    Route::prefix('account')->group(function () {
        Route::post('create', '\App\Http\Controllers\Api\v1\AccountController@create');
    });
});

// Scraper ready api
Route::post('scraper/ready','ScrapController@scraperReady');
Route::post('scraper/completed','ScrapController@scraperCompleted');
Route::get('scraper/need-to-start','ScrapController@needToStart');
Route::get('scraper-needed-products','ScrapController@scraperNeeded');

Route::post('shopify/customer/create','\App\Http\Controllers\Shopify\ShopifyController@setShopifyCustomers');
Route::post('shopify/order/create','\App\Http\Controllers\Shopify\ShopifyController@setShopifyOrders');

Route::get('price_comparision/{type}','PriceComparisionController@index');
Route::post('price_comparision/store','PriceComparisionController@storeComparision');
//order details api for a customer
Route::get('customer/order-details','OrderController@customerOrderDetails');

//refer a friend api
Route::post('friend/referral/create','\App\Http\Controllers\Api\v1\ReferaFriend@store');
Route::post('price_comparision/details','PriceComparisionController@sendDetails');
//Ticket api
Route::post('ticket/create','\App\Http\Controllers\Api\v1\TicketController@store');
Route::post('ticket/send','\App\Http\Controllers\Api\v1\TicketController@sendTicketsToCustomers');

Route::post('facebook/post/status','\App\Http\Controllers\FacebookPostController@setPostStatus');
Route::post('facebook/account','\App\Http\Controllers\FacebookPostController@getPost');

//gift cards api
Route::post('giftcards/add','\App\Http\Controllers\Api\v1\GiftCardController@store');
Route::get('giftcards/check-giftcard-coupon-amount','\App\Http\Controllers\Api\v1\GiftCardController@checkGiftcardCouponAmount');

Route::post('facebook/post/status','\App\Http\Controllers\FacebookPostController@setPostStatus');
Route::post('facebook/account','\App\Http\Controllers\FacebookPostController@getPost');

//gift cards api
Route::post('giftcards/add','\App\Http\Controllers\Api\v1\GiftCardController@store');
Route::get('giftcards/check-giftcard-coupon-amount','\App\Http\Controllers\Api\v1\GiftCardController@checkGiftcardCouponAmount');

//Affiliate Api
Route::post('affiliate/add','\App\Http\Controllers\Api\v1\AffiliateController@store');
Route::post('influencer/add','\App\Http\Controllers\Api\v1\AffiliateController@store');
//buyback cards api
Route::get('orders/products','\App\Http\Controllers\Api\v1\BuyBackController@checkProductsForBuyback');
Route::post('return-exchange-buyback/create','\App\Http\Controllers\Api\v1\BuyBackController@store');

//Push Notification Api
Route::post('notification/create','\App\Http\Controllers\Api\v1\PushFcmNotificationController@create');

//Saving Not Found Brand
Route::get('missing-brand/save','MissingBrandController@saveMissingBrand');

//Store data into the laravel_logs
Route::post('laravel-logs/save','LaravelLogController@saveNewLogData');




Route::post('templates/create/webhook','TemplatesController@createWebhook');
Route::post('product/templates/update/webhook','ProductTemplatesController@updateWebhook')->name('api.product.update.webhook');

//check for order cancellation
Route::post('order/check-cancellation','\App\Http\Controllers\Api\v1\ProductController@checkCancellation');

Route::post('magento/order-create','MagentoCustomerReferenceController@createOrder');



