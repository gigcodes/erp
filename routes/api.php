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

Route::get('duty/v1/get-currencies', 'SimplyDutyCurrencyController@sendCurrencyJson');
Route::get('duty/v1/get-countries', 'SimplyDutyCountryController@sendCountryJson');
Route::post('duty/v1/calculate', 'SimplyDutyCalculationController@calculate');

// INSTAGRAM
Route::post('instagram/post', 'InstagramPostsController@apiPost');

Route::get('instagram/send-account/{token}', 'InstagramPostsController@sendAccount');
Route::post('{username}/{password}/get-comments', 'InstagramPostsController@getComments');
Route::post('{username}/{password}/send-comment', 'InstagramPostsController@commentSent');
Route::get('instagram/get-hashtag-list','InstagramPostsController@getHashtagList');

// SUPPLIERS
Route::post('supplier/brands-raw', 'SupplierController@apiBrandsRaw');

//Google search
Route::get('google/keywords', 'GoogleSearchController@getKeywordsApi');
Route::post('google/search-results', 'GoogleSearchController@apiPost');

Route::get('scraper/next','ScrapController@sendScrapDetails');
Route::post('scraper/endtime','ScrapController@recieveScrapDetails');

Route::get('search/{type}', 'SearchQueueController@index');
Route::post('search/{type}', 'SearchQueueController@upload_content');

