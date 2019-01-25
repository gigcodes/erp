<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('/productselection/list','ProductSelectionController@sList')->name('productselection.list');
Route::get('/productsearcher/list','ProductSearcherController@sList')->name('productsearcher.list');
// adding chat contro

	Route::get('/message', 'MessageController@index')->name('message');
	Route::post('/message', 'MessageController@store')->name('message.store');
	Route::post('/message/{message}', 'MessageController@update')->name('message.update');
	Route::post('/message/{id}/removeImage', 'MessageController@removeImage')->name('message.removeImage');
	Route::get('/chat/getnew', 'ChatController@checkfornew')->name('checkfornew');
	Route::get('/chat/updatenew', 'ChatController@updatefornew')->name('updatefornew');
	//Route::resource('/chat','ChatController@getmessages');
	Route::get('users/check/logins', 'UserController@checkUserLogins')->name('users.check.logins');

Route::group(['middleware'  => ['auth'] ], function (){

	Route::resource('roles','RoleController');
	Route::get('users/logins', 'UserController@login')->name('users.login.index');
	Route::resource('users','UserController');
	Route::resource('products','ProductController');
	Route::post('products/{id}/archive','ProductController@archive')->name('products.archive');
	Route::post('products/{id}/restore','ProductController@restore')->name('products.restore');
	Route::resource('productselection','ProductSelectionController');
	Route::resource('productattribute','ProductAttributeController');
	Route::resource('productsearcher','ProductSearcherController');
	Route::resource('productimagecropper','ProductCropperController');
	Route::resource('productsupervisor','ProductSupervisorController');
	Route::resource('productlister','ProductListerController');
	Route::resource('productapprover','ProductApproverController');
	Route::post('productinventory/import', 'ProductInventoryController@import')->name('productinventory.import');
	Route::resource('productinventory','ProductInventoryController');
	Route::resource('sales','SaleController');
	Route::resource('stock','StockController');
	Route::delete('stock/{id}/permanentDelete', 'StockController@permanentDelete')->name('stock.permanentDelete');
//	Route::resource('activity','ActivityConroller');
	Route::resource('brand','BrandController');
	Route::resource('reply','ReplyController');
	Route::post('reply/category/store', 'ReplyController@categoryStore')->name('reply.category.store');

	Route::resource('settings','SettingController');
	Route::resource('category','CategoryController');
	Route::resource('benchmark','BenchmarkController');

	// adding lead routes
	Route::get('leads/imageGrid', 'LeadsController@imageGrid')->name('leads.image.grid');
	Route::resource('leads','LeadsController');
	Route::post('leads/{id}/changestatus', 'LeadsController@updateStatus');
	Route::delete('leads/permanentDelete/{leads}','LeadsController@permanentDelete')->name('leads.permanentDelete');
	Route::resource('chat','ChatController');
//	Route::resource('task','TaskController');

	// Instruction
	Route::resource('instruction','InstructionController');
	Route::post('instruction/complete', 'InstructionController@complete')->name('instruction.complete');
	Route::post('instruction/pending', 'InstructionController@pending')->name('instruction.pending');
	Route::get('instruction/complete/alert', 'InstructionController@completeAlert')->name('instruction.complete.alert');


	Route::post('order/{id}/changestatus', 'OrderController@updateStatus');
	Route::delete('order/permanentDelete/{order}','OrderController@permanentDelete')->name('order.permanentDelete');
	Route::get('order/products/list', 'OrderController@products')->name('order.products');
	Route::get('order/missed-calls', 'OrderController@missedCalls')->name('order.missed-calls');
    Route::resource('order','OrderController');

	Route::post('order/status/store','OrderReportController@statusStore')->name('status.store');
	Route::post('order/report/store','OrderReportController@store')->name('status.report.store');

	Route::resource('task','TaskModuleController');
	Route::resource('task_category','TaskCategoryController');
	Route::get('/', 'TaskModuleController@index')->name('home');

});

Route::middleware('auth')->group(function (){

	Route::get('/notifications' , 'NotificaitonContoller@index')->name('notifications');
	Route::get('/notificaitonsJson','NotificaitonContoller@json')->name('notificationJson');
	Route::get('/salesNotificaitonsJson','NotificaitonContoller@salesJson')->name('salesNotificationJson');
	Route::post('/notificationMarkRead/{notificaion}','NotificaitonContoller@markRead')->name('notificationMarkRead');

	Route::post('/productsupervisor/approve/{product}','ProductSupervisorController@approve')->name('productsupervisor.approve');
	Route::post('/productsupervisor/reject/{product}','ProductSupervisorController@reject')->name('productsupervisor.reject');
	Route::post('/productlister/isUploaded/{product}','ProductListerController@isUploaded')->name('productlister.isuploaded');
	Route::post('/productapprover/isFinal/{product}','ProductApproverController@isFinal')->name('productapprover.isfinal');

	Route::get('/productinventory/in/stock','ProductInventoryController@instock')->name('productinventory.instock');
	Route::post('/productinventory/stock/{product}','ProductInventoryController@stock')->name('productinventory.stock');

	Route::get('category','CategoryController@manageCategory')->name('category');
	Route::post('add-category','CategoryController@addCategory')->name('add.category');
	Route::post('category/{category}/edit','CategoryController@edit')->name('category.edit');
	Route::post('category/remove','CategoryController@remove')->name('category.remove');

	Route::get('productSearch/','SaleController@searchProduct');
	Route::post('productSearch/','SaleController@searchProduct');

	Route::get('activity/','ActivityConroller@showActivity')->name('activity');
	Route::get('graph/','ActivityConroller@showGraph')->name('graph');
	Route::get('graph/user','ActivityConroller@showUserGraph')->name('graph_user');

	Route::get('search/','SearchController@search')->name('search');
	Route::get('pending/{roletype}','SearchController@getPendingProducts')->name('pending');

//	Route::post('productAttachToSale/{sale}/{product_id}','SaleController@attachProduct');
//	Route::get('productSelectionGrid/{sale}','SaleController@selectionGrid')->name('productSelectionGrid');

	//Attach Products
	Route::get('attachProducts/{model_type}/{model_id}','ProductController@attachProducts')->name('attachProducts');
	Route::post('attachProductToModel/{model_type}/{model_id}/{product_id}','ProductController@attachProductToModel')->name('attachProductToModel');
	Route::post('deleteOrderProduct/{order_product}','OrderController@deleteOrderProduct')->name('deleteOrderProduct');

	Route::get('attachImages/{model_type}/{model_id?}/{status?}/{assigned_user?}','ProductController@attachImages')->name('attachImages');
	Route::post('download', 'MessageController@downloadImages')->name('download.images');

	Route::get('quickSell', 'QuickSellController@index')->name('quicksell.index');
	Route::post('quickSell', 'QuickSellController@store')->name('quicksell.store');
	Route::post('quickSell/{id}/edit', 'QuickSellController@update')->name('quicksell.update');

	// Customers
	Route::get('customers', 'CustomerController@index')->name('customer.index');
	Route::get('customers-load', 'CustomerController@load')->name('customer.load');
	Route::post('customer/merge', 'CustomerController@merge')->name('customer.merge');
	Route::get('customer/create', 'CustomerController@create')->name('customer.create');
	Route::post('customer/create', 'CustomerController@store')->name('customer.store');
	Route::get('customer/{id}', 'CustomerController@show')->name('customer.show');
	Route::get('customer/{id}/edit', 'CustomerController@edit')->name('customer.edit');
	Route::post('customer/{id}/edit', 'CustomerController@update')->name('customer.update');
	Route::delete('customer/{id}/destroy', 'CustomerController@destroy')->name('customer.destroy');


	Route::get('purchases', 'PurchaseController@index')->name('purchase.index');
	Route::get('purchase/{id}', 'PurchaseController@show')->name('purchase.show');
	Route::get('purchase/{id}/edit', 'PurchaseController@edit')->name('purchase.edit');
	Route::post('purchase/{id}/changestatus', 'PurchaseController@updateStatus');
	Route::post('purchase/{id}/saveBill', 'PurchaseController@saveBill');
	Route::delete('purchase/{id}/delete', 'PurchaseController@destroy')->name('purchase.destroy');
	Route::delete('purchase/{id}/permanentDelete', 'PurchaseController@permanentDelete')->name('purchase.permanentDelete');
	Route::get('purchaseGrid', 'PurchaseController@purchaseGrid')->name('purchase.grid');
	Route::post('purchaseGrid', 'PurchaseController@store')->name('purchase.store');
	Route::get('purchase/product/{id}', 'PurchaseController@productShow')->name('purchase.product.show');
	Route::post('purchase/product/{id}', 'PurchaseController@updatePercentage')->name('purchase.product.percentage');

	//Comments
	Route::post('doComment','CommentController@store')->name('doComment');
	Route::post('deleteComment/{comment}','CommentController@destroy')->name('deleteComment');
	Route::get('message/updatestatus','MessageController@updatestatus')->name('message.updatestatus');
	Route::get('message/loadmore','MessageController@loadmore')->name('message.loadmore');

	//Push Notifications new
	Route::get('/new-notifications','PushNotificationController@index')->name('pushNotification.index');
	Route::get('/pushNotifications','PushNotificationController@getJson')->name('pushNotifications');
	Route::post('/pushNotificationMarkRead/{push_notification}','PushNotificationController@markRead')->name('pushNotificationMarkRead');
	Route::post('/pushNotificationMarkReadReminder/{push_notification}','PushNotificationController@markReadReminder')->name('pushNotificationMarkReadReminder');
	Route::post('/pushNotification/status/{push_notification}','PushNotificationController@changeStatus')->name('pushNotificationStatus');

	Route::post('dailyActivity/store','DailyActivityController@store')->name('dailyActivity.store');
	Route::get('dailyActivity/get','DailyActivityController@get')->name('dailyActivity.get');

	// Complete the task
	Route::get('/task/complete/{taskid}','TaskModuleController@complete')->name('task.complete');
	Route::get('/statutory-task/complete/{taskid}','TaskModuleController@statutoryComplete')->name('task.statutory.complete');
	Route::post('/task/addremark','TaskModuleController@addRemark')->name('task.addRemark');
	Route::get('tasks/getremark','TaskModuleController@getremark')->name('task.getremark');
	Route::get('tasks/gettaskremark','TaskModuleController@getTaskRemark')->name('task.gettaskremark');

	Route::post('tasks/deleteTask','TaskModuleController@deleteTask');
	Route::post('tasks/{id}/delete','TaskModuleController@archiveTask')->name('task.archive');
//	Route::get('task/completeStatutory/{satutory_task}','TaskModuleController@completeStatutory');
	Route::post('task/deleteStatutoryTask','TaskModuleController@deleteStatutoryTask');

	Route::post('task/export','TaskModuleController@exportTask')->name('task.export');
	Route::post('/task/addRemarkStatutory','TaskModuleController@addRemark')->name('task.addRemarkStatutory');

	// Social Media Image Module
	Route::get('images/grid', 'ImageController@index')->name('image.grid');
	Route::post('images/grid', 'ImageController@store')->name('image.grid.store');
	Route::post('images/grid/attachImage', 'ImageController@attachImage')->name('image.grid.attach');
	Route::get('images/grid/approvedImages', 'ImageController@approved')->name('image.grid.approved');
	Route::get('images/grid/finalApproval', 'ImageController@final')->name('image.grid.final.approval');
	Route::get('images/grid/{id}', 'ImageController@show')->name('image.grid.show');
	Route::get('images/grid/{id}/edit', 'ImageController@edit')->name('image.grid.edit');
	Route::post('images/grid/{id}/edit', 'ImageController@update')->name('image.grid.update');
	Route::delete('images/grid/{id}/delete', 'ImageController@destroy')->name('image.grid.delete');
	Route::post('images/grid/{id}/approveImage', 'ImageController@approveImage')->name('image.grid.approveImage');
	Route::get('images/grid/{id}/download', 'ImageController@download')->name('image.grid.download');
	Route::post('images/grid/make/set', 'ImageController@set')->name('image.grid.set');
	Route::post('images/grid/make/set/download', 'ImageController@setDownload')->name('image.grid.set.download');

  Route::post('leads/save-leave-message', 'LeadsController@saveLeaveMessage')->name('leads.message.save');

	// Development
	Route::get('development', 'DevelopmentController@index')->name('development.index');
	Route::post('development/create', 'DevelopmentController@store')->name('development.store');
	Route::post('development/{id}/edit', 'DevelopmentController@update')->name('development.update');
	Route::delete('development/{id}/destroy', 'DevelopmentController@destroy')->name('development.destroy');

	Route::get('development/issue/list', 'DevelopmentController@issueIndex')->name('development.issue.index');
	Route::get('development/issue/create', 'DevelopmentController@issueCreate')->name('development.issue.create');
	Route::post('development/issue/create', 'DevelopmentController@issueStore')->name('development.issue.store');
	Route::post('development/{id}/assignIssue', 'DevelopmentController@issueAssign')->name('development.issue.assign');
	Route::delete('development/{id}/issueDestroy', 'DevelopmentController@issueDestroy')->name('development.issue.destroy');

	Route::post('development/module/create', 'DevelopmentController@moduleStore')->name('development.module.store');
	Route::delete('development/module/{id}/destroy', 'DevelopmentController@moduleDestroy')->name('development.module.destroy');
	Route::post('development/{id}/assignModule', 'DevelopmentController@moduleAssign')->name('development.module.assign');

	Route::post('development/comment/create', 'DevelopmentController@commentStore')->name('development.comment.store');
	Route::post('development/{id}/awaiting/response', 'DevelopmentController@awaitingResponse')->name('development.comment.awaiting.response');

	Route::post('development/cost/store', 'DevelopmentController@costStore')->name('development.cost.store');

	/*Routes For Social */

	Route::any('social/get-post/page','SocialController@pagePost')->name('social.get-post.page');

		// post creating routes define's here

	Route::get('social/post/page','SocialController@index')->name('social.post.page');
	Route::post('social/post/page/create','SocialController@createPost')->name('social.post.page.create');

Route::get('twilio/token', 'TwilioController@createToken');
Route::post('twilio/ivr', 'TwilioController@ivr');
Route::post('twilio/gatherAction', 'TwilioController@gatherAction');
Route::post('twilio/incoming', 'TwilioController@incomingCall');
Route::post('twilio/outgoing', 'TwilioController@outgoingCall');
Route::get('twilio/getLeadByNumber', 'TwilioController@getLeadByNumber');
Route::post('twilio/recordingStatusCallback', 'TwilioController@recordingStatusCallback');
Route::post('twilio/handleDialCallStatus', 'TwilioController@handleDialCallStatus');
      // Route::post(
      //       '/twilio/store-recording', [
      //           'as' => 'store-recording',
      //           'uses' => 'TwilioController@storeRecording'
      //       ]
      //   );
  Route::post('twilio/storerecording','TwilioController@storeRecording');
    Route::post('twilio/storetranscript','TwilioController@storetranscript');

 Route::get(
            '/twilio/hangup', [
                'as' => 'hangup',
                'uses' => 'TwilioController@showHangup'
            ]
        );


		// Ad reports routes

	Route::get('social/ad/report','SocialController@report')->name('social.report');
	Route::post('social/ad/report/paginate','SocialController@paginateReport')->name('social.report.paginate');
	Route::get('social/ad/report/{ad_id}/{status}/','SocialController@changeAdStatus')->name('social.report.ad.status');

		// end to ad reports routes

		// AdCreative reports routes

	Route::get('social/adcreative/report','SocialController@adCreativereport')->name('social.adCreative.report');
	Route::post('social/adcreative/report/paginate','SocialController@adCreativepaginateReport')->name('social.adCreative.paginate');


		// end to ad reports routes


	// Creating Ad Campaign Routes defines here


	Route::get('social/ad/campaign/create','SocialController@createCampaign')->name('social.ad.campaign.create');

	Route::post('social/ad/campaign/store','SocialController@storeCampaign')->name('social.ad.campaign.store');



	// Creating Adset Routes define here

	Route::get('social/ad/adset/create','SocialController@createAdset')->name('social.ad.adset.create');
	Route::post('social/ad/adset/store','SocialController@storeAdset')->name('social.ad.adset.store');


	// Creating Ad Routes define here

	Route::get('social/ad/create','SocialController@createAd')->name('social.ad.create');
	Route::post('social/ad/store','SocialController@storeAd')->name('social.ad.store');


	// End of Routes for social

});


//Route::get('deQueueNotfication/','NotificationQueueController@deQueueNotfication');
Route::get('deQueueNotfication/','NotificationQueueController@deQueueNotficationNew');
Route::get('mageOrders/','MagentoController@get_magento_orders');

Route::get('perHourActivityNotification','NotificationQueueController@perHourActivityNotification');
Route::get('recurringTask','TaskModuleController@recurringTask');

Route::get('twilio/token', 'TwilioController@createToken');
Route::post('twilio/ivr', 'TwilioController@ivr');
Route::post('twilio/gatherAction', 'TwilioController@gatherAction');
Route::post('twilio/incoming', 'TwilioController@incomingCall');
Route::post('twilio/outgoing', 'TwilioController@outgoingCall');
Route::get('twilio/getLeadByNumber', 'TwilioController@getLeadByNumber');
Route::post('twilio/recordingStatusCallback', 'TwilioController@recordingStatusCallback');

Route::post('whatsapp/incoming', 'WhatsAppController@incomingMessage');
Route::post('whatsapp/sendMessage/{context}', 'WhatsAppController@sendMessage');
Route::post('whatsapp/sendMultipleMessages', 'WhatsAppController@sendMultipleMessages');
Route::post('whatsapp/approve/{context}', 'WhatsAppController@approveMessage');
Route::get('whatsapp/pollMessages/{context}', 'WhatsAppController@pollMessages');
Route::get('whatsapp/pollMessagesCustomer', 'WhatsAppController@pollMessagesCustomer');
Route::get('whatsapp/updatestatus/', 'WhatsAppController@updateStatus');
Route::post('whatsapp/updateAndCreate/', 'WhatsAppController@updateAndCreate');
Route::post('whatsapp/forwardMessage/', 'WhatsAppController@forwardMessage')->name('whatsapp.forward');


/*
 * @date 1/13/2019
 * @author Rishabh Aryal
 * This is route for Instagram
 * feature in this ERP
 */

Route::prefix('instagram')->group(function () {
    Route::get('posts', 'InstagramController@showPosts');
    Route::get('comments', 'InstagramController@getComments');
    Route::post('comments', 'InstagramController@postComment');
    Route::get('post-media', 'InstagramController@showImagesToBePosted');
    Route::post('post-media', 'InstagramController@postMedia');
    Route::get('post-media-now/{schedule}', 'InstagramController@postMediaNow');
    Route::get('media/schedules', 'InstagramController@showSchedules');
    Route::post('media/schedules', 'InstagramController@postSchedules');
    Route::get('scheduled/events', 'InstagramController@getScheduledEvents');
});

/*
 * @date 1/17/2019
 * @author Rishabh Aryal
 * This is route API for getting/replying comments
 * from Facebook API
 */

Route::prefix('comments')->group(function () {
    Route::get('/facebook', 'SocialController@getComments');
    Route::post('/facebook', 'SocialController@postComment');
});
