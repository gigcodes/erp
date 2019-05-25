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

Route::get('/mageOrders', 'MagentoController@get_magento_orders');

	Route::get('/message', 'MessageController@index')->name('message');
	Route::post('/message', 'MessageController@store')->name('message.store');
	Route::post('/message/{message}', 'MessageController@update')->name('message.update');
	Route::post('/message/{id}/removeImage', 'MessageController@removeImage')->name('message.removeImage');
	Route::get('/chat/getnew', 'ChatController@checkfornew')->name('checkfornew');
	Route::get('/chat/updatenew', 'ChatController@updatefornew')->name('updatefornew');
	//Route::resource('/chat','ChatController@getmessages');
	Route::get('users/check/logins', 'UserController@checkUserLogins')->name('users.check.logins');

Route::group(['middleware'  => ['auth', 'optimizeImages'] ], function (){

	Route::resource('roles','RoleController');
	Route::get('users/logins', 'UserController@login')->name('users.login.index');
	Route::resource('users','UserController');
	Route::get('products/listing', 'ProductController@listing')->name('products.listing');
	Route::post('products/{id}/updateName', 'ProductController@updateName');
	Route::post('products/{id}/updateDescription', 'ProductController@updateDescription');
	Route::post('products/{id}/updateComposition', 'ProductController@updateComposition');
	Route::post('products/{id}/updateColor', 'ProductController@updateColor');
	Route::post('products/{id}/updateCategory', 'ProductController@updateCategory');
	Route::post('products/{id}/updateSize', 'ProductController@updateSize');
	Route::post('products/{id}/updatePrice', 'ProductController@updatePrice');
	Route::get('products/{id}/quickDownload', 'ProductController@quickDownload')->name('products.quick.download');
	Route::post('products/{id}/quickUpload', 'ProductController@quickUpload')->name('products.quick.upload');
	Route::post('products/{id}/listMagento', 'ProductController@listMagento');
	Route::post('products/{id}/approveMagento', 'ProductController@approveMagento');
	Route::post('products/{id}/updateMagento', 'ProductController@updateMagento');
	Route::post('products/{id}/approveProduct', 'ProductController@approveProduct');
	Route::resource('products','ProductController');
	Route::post('products/bulk/update', 'ProductController@bulkUpdate')->name('products.bulk.update');
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
	Route::get('productinventory/list', 'ProductInventoryController@list')->name('productinventory.list');
	Route::resource('productinventory','ProductInventoryController');
	Route::resource('sales','SaleController');
	Route::resource('stock','StockController');
	Route::post('stock/track/package', 'StockController@trackPackage')->name('stock.track.package');
	Route::delete('stock/{id}/permanentDelete', 'StockController@permanentDelete')->name('stock.permanentDelete');
	Route::post('stock/privateViewing/create', 'StockController@privateViewingStore')->name('stock.privateViewing.store');
	Route::get('stock/private/viewing', 'StockController@privateViewing')->name('stock.private.viewing');
	Route::delete('stock/private/viewing/{id}', 'StockController@privateViewingDestroy')->name('stock.private.viewing.destroy');
	Route::post('stock/private/viewing/upload', 'StockController@privateViewingUpload')->name('stock.private.viewing.upload');
	Route::post('stock/private/viewing/{id}/updateStatus', 'StockController@privateViewingUpdateStatus')->name('stock.private.viewing.updateStatus');
//	Route::resource('activity','ActivityConroller');
	Route::resource('brand','BrandController');
	Route::resource('reply','ReplyController');
	Route::post('reply/category/store', 'ReplyController@categoryStore')->name('reply.category.store');

	// Auto Replies
	Route::resource('autoreply', 'AutoReplyController');

	Route::post('settings/updateAutomatedMessages', 'SettingController@updateAutoMessages')->name('settings.update.automessages');
	Route::resource('settings','SettingController');
	Route::resource('category','CategoryController');
	Route::resource('benchmark','BenchmarkController');

	// adding lead routes
	Route::get('leads/imageGrid', 'LeadsController@imageGrid')->name('leads.image.grid');
	Route::post('leads/sendPrices', 'LeadsController@sendPrices')->name('leads.send.prices');
	Route::resource('leads','LeadsController');
	Route::post('leads/{id}/changestatus', 'LeadsController@updateStatus');
	Route::delete('leads/permanentDelete/{leads}','LeadsController@permanentDelete')->name('leads.permanentDelete');
	Route::resource('chat','ChatController');
//	Route::resource('task','TaskController');

	// Instruction
	Route::get('instruction/list', 'InstructionController@list')->name('instruction.list');
	Route::resource('instruction','InstructionController');
	Route::post('instruction/complete', 'InstructionController@complete')->name('instruction.complete');
	Route::post('instruction/pending', 'InstructionController@pending')->name('instruction.pending');
	Route::post('instruction/verify', 'InstructionController@verify')->name('instruction.verify');
	Route::post('instruction/verifySelected', 'InstructionController@verifySelected')->name('instruction.verify.selected');
	Route::get('instruction/complete/alert', 'InstructionController@completeAlert')->name('instruction.complete.alert');
	Route::post('instruction/category/store', 'InstructionController@categoryStore')->name('instruction.category.store');


	Route::get('order/{id}/send/confirmationEmail', 'OrderController@sendConfirmation')->name('order.send.confirmation.email');
	Route::post('order/{id}/refund/answer', 'OrderController@refundAnswer')->name('order.refund.answer');
	Route::post('order/send/Delivery', 'OrderController@sendDelivery')->name('order.send.delivery');
	Route::post('order/{id}/send/suggestion', 'OrderController@sendSuggestion')->name('order.send.suggestion');
	Route::post('order/{id}/changestatus', 'OrderController@updateStatus');
	Route::post('order/{id}/sendRefund', 'OrderController@sendRefund');
	Route::post('order/{id}/uploadForApproval', 'OrderController@uploadForApproval')->name('order.upload.approval');
	Route::post('order/{id}/deliveryApprove', 'OrderController@deliveryApprove')->name('order.delivery.approve');
	Route::get('order/{id}/printAdvanceReceipt', 'OrderController@printAdvanceReceipt')->name('order.advance.receipt.print');
	Route::get('order/{id}/emailAdvanceReceipt', 'OrderController@emailAdvanceReceipt')->name('order.advance.receipt.email');
	Route::get('order/{id}/generateInvoice', 'OrderController@generateInvoice')->name('order.generate.invoice');
	Route::post('order/{id}/createProductOnMagento', 'OrderController@createProductOnMagento')->name('order.create.magento.product');
	Route::get('order/{id}/download/PackageSlip', 'OrderController@downloadPackageSlip')->name('order.download.package-slip');
	Route::delete('order/permanentDelete/{order}','OrderController@permanentDelete')->name('order.permanentDelete');
	Route::get('order/products/list', 'OrderController@products')->name('order.products');
	Route::get('order/missed-calls', 'OrderController@missedCalls')->name('order.missed-calls');
	Route::get('order/calls/history', 'OrderController@callsHistory')->name('order.calls-history');
	Route::post('order/generate/awb/number', 'OrderController@generateAWB')->name('order.generate.awb');
  Route::resource('order','OrderController');

	Route::post('order/status/store','OrderReportController@statusStore')->name('status.store');
	Route::post('order/report/store','OrderReportController@store')->name('status.report.store');

	Route::get('task/list', 'TaskModuleController@list')->name('task.list');
	Route::post('task/assign/messages', 'TaskModuleController@assignMessages')->name('task.assign.messages');
	Route::resource('task','TaskModuleController');
	Route::resource('task_category','TaskCategoryController');
	Route::get('/', 'TaskModuleController@index')->name('home');

	Route::resource('refund', 'RefundController');

	Route::get('/notifications' , 'NotificaitonContoller@index')->name('notifications');
	Route::get('/notificaitonsJson','NotificaitonContoller@json')->name('notificationJson');
	Route::get('/salesNotificaitonsJson','NotificaitonContoller@salesJson')->name('salesNotificationJson');
	Route::post('/notificationMarkRead/{notificaion}','NotificaitonContoller@markRead')->name('notificationMarkRead');
	Route::get('/deQueueNotfication', 'NotificationQueueController@deQueueNotficationNew');

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
	Route::get('attachProducts/{model_type}/{model_id}/{type?}/{customer_id?}','ProductController@attachProducts')->name('attachProducts');
	Route::post('attachProductToModel/{model_type}/{model_id}/{product_id}','ProductController@attachProductToModel')->name('attachProductToModel');
	Route::post('deleteOrderProduct/{order_product}','OrderController@deleteOrderProduct')->name('deleteOrderProduct');

	Route::get('attachImages/{model_type}/{model_id?}/{status?}/{assigned_user?}','ProductController@attachImages')->name('attachImages');
	Route::post('download', 'MessageController@downloadImages')->name('download.images');

	Route::get('quickSell', 'QuickSellController@index')->name('quicksell.index');
	Route::post('quickSell', 'QuickSellController@store')->name('quicksell.store');
	Route::post('quickSell/{id}/edit', 'QuickSellController@update')->name('quicksell.update');

	// Customers
	Route::get('customer/test', 'CustomerController@customerstest');
	Route::post('customers/{id}/post-show', 'CustomerController@postShow')->name('customer.post.show');
	Route::post('customers/{id}/sendAdvanceLink', 'CustomerController@sendAdvanceLink')->name('customer.send.advanceLink');
	Route::get('customers/{id}/loadMoreMessages', 'CustomerController@loadMoreMessages');
	Route::get('customers', 'CustomerController@index')->name('customer.index');
	Route::get('customers-load', 'CustomerController@load')->name('customer.load');
	Route::post('customer/{id}/initiateFollowup', 'CustomerController@initiateFollowup')->name('customer.initiate.followup');
	Route::post('customer/{id}/stopFollowup', 'CustomerController@stopFollowup')->name('customer.stop.followup');
	Route::get('customer/export', 'CustomerController@export')->name('customer.export');
	Route::post('customer/merge', 'CustomerController@merge')->name('customer.merge');
	Route::post('customer/import', 'CustomerController@import')->name('customer.import');
	Route::get('customer/create', 'CustomerController@create')->name('customer.create');
	Route::post('customer/block', 'CustomerController@block')->name('customer.block');
	Route::post('customer/flag', 'CustomerController@flag')->name('customer.flag');
	Route::post('customer/prioritize', 'CustomerController@prioritize')->name('customer.priority');
	Route::post('customer/create', 'CustomerController@store')->name('customer.store');
	Route::get('customer/{id}', 'CustomerController@show')->name('customer.show');
	Route::get('customer/{id}/edit', 'CustomerController@edit')->name('customer.edit');
	Route::post('customer/{id}/edit', 'CustomerController@update')->name('customer.update');
	Route::post('customer/{id}/updateNumber', 'CustomerController@updateNumber')->name('customer.update.number');
	Route::post('customer/{id}/updateDND', 'CustomerController@updateDnd')->name('customer.update.dnd');
	Route::post('customer/{id}/updatePhone', 'CustomerController@updatePhone')->name('customer.update.phone');
	Route::delete('customer/{id}/destroy', 'CustomerController@destroy')->name('customer.destroy');
	Route::post('customer/send/message/all/{validate?}', 'WhatsAppController@sendToAll')->name('customer.whatsapp.send.all');
	Route::get('customer/stop/message/all', 'WhatsAppController@stopAll')->name('customer.whatsapp.stop.all');
	Route::get('customer/email/fetch', 'CustomerController@emailFetch')->name('customer.email.fetch');
	Route::get('customer/email/inbox', 'CustomerController@emailInbox')->name('customer.email.inbox');
	Route::post('customer/email/send', 'CustomerController@emailSend')->name('customer.email.send');
	Route::post('customer/send/suggestion', 'CustomerController@sendSuggestion')->name('customer.send.suggestion');
	Route::post('customer/send/instock', 'CustomerController@sendInstock')->name('customer.send.instock');
	Route::post('customer/issue/credit', 'CustomerController@issueCredit')->name('customer.issue.credit');
	Route::post('customer/attach/all', 'CustomerController@attachAll')->name('customer.attach.all');
	Route::post('customer/sendScraped/images', 'CustomerController@sendScraped')->name('customer.send.scraped');

	Route::get('broadcast', 'BroadcastMessageController@index')->name('broadcast.index');
	Route::get('broadcast/images', 'BroadcastMessageController@images')->name('broadcast.images');
	Route::post('broadcast/imagesUpload', 'BroadcastMessageController@imagesUpload')->name('broadcast.images.upload');
	Route::post('broadcast/imagesLink', 'BroadcastMessageController@imagesLink')->name('broadcast.images.link');
	Route::delete('broadcast/{id}/imagesDelete', 'BroadcastMessageController@imagesDelete')->name('broadcast.images.delete');
	Route::get('broadcast/calendar', 'BroadcastMessageController@calendar')->name('broadcast.calendar');
	Route::post('broadcast/restart', 'BroadcastMessageController@restart')->name('broadcast.restart');
	Route::post('broadcast/restart/{id}', 'BroadcastMessageController@restartGroup')->name('broadcast.restart.group');
	Route::post('broadcast/delete/{id}', 'BroadcastMessageController@deleteGroup')->name('broadcast.delete.group');
	Route::post('broadcast/stop/{id}', 'BroadcastMessageController@stopGroup')->name('broadcast.stop.group');
	Route::post('broadcast/{id}/doNotDisturb', 'BroadcastMessageController@doNotDisturb')->name('broadcast.donot.disturb');

	Route::get('purchases', 'PurchaseController@index')->name('purchase.index');
	Route::post('purchase/export', 'PurchaseController@export')->name('purchase.export');
	Route::post('purchase/sendExport', 'PurchaseController@sendExport')->name('purchase.send.export');
	Route::get('purchase/{id}', 'PurchaseController@show')->name('purchase.show');
	Route::get('purchase/{id}/edit', 'PurchaseController@edit')->name('purchase.edit');
	Route::post('purchase/{id}/changestatus', 'PurchaseController@updateStatus');
	Route::post('purchase/{id}/changeProductStatus', 'PurchaseController@updateProductStatus');
	Route::post('purchase/{id}/saveBill', 'PurchaseController@saveBill');
	Route::post('purchase/{id}/downloadFile', 'PurchaseController@downloadFile')->name('purchase.file.download');
	Route::post('purchase/{id}/confirmProforma', 'PurchaseController@confirmProforma')->name('purchase.confirm.Proforma');
	Route::get('purchase/download/attachments', 'PurchaseController@downloadAttachments')->name('purchase.download.attachments');
	Route::delete('purchase/{id}/delete', 'PurchaseController@destroy')->name('purchase.destroy');
	Route::delete('purchase/{id}/permanentDelete', 'PurchaseController@permanentDelete')->name('purchase.permanentDelete');
	Route::get('purchaseGrid/{page?}', 'PurchaseController@purchaseGrid')->name('purchase.grid');
	Route::post('purchaseGrid', 'PurchaseController@store')->name('purchase.store');
	Route::post('purchase/product/replace', 'PurchaseController@productReplace')->name('purchase.product.replace');
	Route::post('purchase/product/create/replace', 'PurchaseController@productCreateReplace')->name('purchase.product.create.replace');
	Route::get('purchase/product/{id}', 'PurchaseController@productShow')->name('purchase.product.show');
	Route::post('purchase/product/{id}', 'PurchaseController@updatePercentage')->name('purchase.product.percentage');
	Route::post('purchase/product/{id}/remove', 'PurchaseController@productRemove')->name('purchase.product.remove');
	Route::get('purchase/email/inbox', 'PurchaseController@emailInbox')->name('purchase.email.inbox');
	Route::get('purchase/email/fetch', 'PurchaseController@emailFetch')->name('purchase.email.fetch');
	Route::post('purchase/email/send', 'PurchaseController@emailSend')->name('purchase.email.send');
	Route::post('purchase/email/resend', 'PurchaseController@emailResend')->name('purchase.email.resend');

	// Master Plan
	Route::get('mastercontrol/clearAlert', 'MasterControlController@clearAlert')->name('mastercontrol.clear.alert');
	Route::resource('mastercontrol', 'MasterControlController');

	// Cash Vouchers
	Route::post('voucher/{id}/approve', 'VoucherController@approve')->name('voucher.approve');
	Route::resource('voucher', 'VoucherController');

	// Budget
	Route::resource('budget', 'BudgetController');
	Route::post('budget/category/store', 'BudgetController@categoryStore')->name('budget.category.store');
	Route::post('budget/subcategory/store', 'BudgetController@subCategoryStore')->name('budget.subcategory.store');

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
	Route::post('task/{id}/makePrivate','TaskModuleController@makePrivate');

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
	Route::post('images/grid/update/schedule', 'ImageController@updateSchedule')->name('image.grid.update.schedule');

  Route::post('leads/save-leave-message', 'LeadsController@saveLeaveMessage')->name('leads.message.save');

	// Development
	Route::get('development', 'DevelopmentController@index')->name('development.index');
	Route::post('development/create', 'DevelopmentController@store')->name('development.store');
	Route::post('development/{id}/edit', 'DevelopmentController@update')->name('development.update');
	Route::post('development/{id}/verify', 'DevelopmentController@verify')->name('development.verify');
	Route::get('development/verify/view', 'DevelopmentController@verifyView')->name('development.verify.view');
	Route::delete('development/{id}/destroy', 'DevelopmentController@destroy')->name('development.destroy');
	Route::post('development/{id}/updateCost', 'DevelopmentController@updateCost')->name('development.update.cost');
	Route::post('development/{id}/status', 'DevelopmentController@updateStatus')->name('development.update.status');
	Route::post('development/{id}/updateTask', 'DevelopmentController@updateTask')->name('development.update.task');
	Route::post('development/{id}/updatePriority', 'DevelopmentController@updatePriority')->name('development.update.priority');

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

	/*Routes For Social */
	Route::any('social/get-post/page','SocialController@pagePost')->name('social.get-post.page');

	// post creating routes define's here
	Route::get('social/post/page','SocialController@index')->name('social.post.page');
	Route::post('social/post/page/create','SocialController@createPost')->name('social.post.page.create');

	// Ad reports routes
	Route::get('social/ad/report','SocialController@report')->name('social.report');
	Route::get('social/ad/schedules','SocialController@getSchedules')->name('social.ads.schedules');
	Route::post('social/ad/schedules','SocialController@getSchedules')->name('social.ads.schedules.p');
	Route::get('social/ad/schedules/calendar','SocialController@getAdSchedules')->name('social.ads.schedules.calendar');
	Route::post('social/ad/schedules/','SocialController@createAdSchedule')->name('social.ads.schedules.create');
	Route::post('social/ad/schedules/attach-images/{id}','SocialController@attachMedia')->name('social.ads.schedules.attach_images');
	Route::post('social/ad/schedules/attach-products/{id}','SocialController@attachProducts')->name('social.ads.schedules.attach_products');
	Route::post('social/ad/schedules/','SocialController@createAdSchedule')->name('social.ads.schedules.attach_image');
	Route::get('social/ad/schedules/{id}','SocialController@showSchedule')->name('social.ads.schedules.show');
	Route::get('social/ad/insight/{adId}','SocialController@getAdInsights')->name('social.ad.insight');
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

	// Paswords Manager
	Route::get('passwords', 'PasswordController@index')->name('password.index');
	Route::post('password/store', 'PasswordController@store')->name('password.store');

	// Documents Manager
	Route::get('documents', 'DocumentController@index')->name('document.index');
	Route::post('document/store', 'DocumentController@store')->name('document.store');
	Route::get('document/{id}/download', 'DocumentController@download')->name('document.download');
	Route::delete('document/{id}/destroy', 'DocumentController@destroy')->name('document.destroy');

	// Cash Flow Module
	Route::get('cashflow/{id}/download', 'CashFlowController@download')->name('cashflow.download');
	Route::resource('cashflow', 'CashFlowController');
	Route::resource('dailycashflow', 'DailyCashFlowController');

	// Reviews Module
    Route::post('review/createFromInstagramHashtag', 'ReviewController@createFromInstagramHashtag');
    Route::get('review/instagram/reply', 'ReviewController@replyToPost');
    Route::post('review/instagram/dm', 'ReviewController@sendDm');
	Route::get('review/{id}/updateStatus', 'ReviewController@updateStatus');
	Route::post('review/{id}/updateStatus', 'ReviewController@updateStatus');
	Route::post('review/{id}/updateReview', 'ReviewController@updateReview');
	Route::resource('review', 'ReviewController');
	Route::post('review/schedule/create', 'ReviewController@scheduleStore')->name('review.schedule.store');
	Route::put('review/schedule/{id}', 'ReviewController@scheduleUpdate')->name('review.schedule.update');
	Route::post('review/schedule/{id}/status', 'ReviewController@scheduleUpdateStatus')->name('review.schedule.updateStatus');
	Route::delete('review/schedule/{id}/destroy', 'ReviewController@scheduleDestroy')->name('review.schedule.destroy');
	Route::get('account/{id}', 'AccountController@show');
	Route::post('account/igdm/{id}', 'AccountController@sendMessage');
	Route::post('account/bulk/{id}', 'AccountController@addMessageSchedule');
	Route::post('account/create', 'ReviewController@accountStore')->name('account.store');
	Route::put('account/{id}', 'ReviewController@accountUpdate')->name('account.update');
	Route::delete('account/{id}/destroy', 'ReviewController@accountDestroy')->name('account.destroy');

	// Threads Routes
	Route::resource('thread', 'ThreadController');
	Route::post('thread/{id}/status', 'ThreadController@updateStatus')->name('thread.updateStatus');

	// Complaints Routes
	Route::resource('complaint', 'ComplaintController');
	Route::post('complaint/{id}/status', 'ComplaintController@updateStatus')->name('complaint.updateStatus');

	// Vendor Module
	Route::get('vendor/product', 'VendorController@product')->name('vendor.product.index');
	Route::post('vendor/product', 'VendorController@productStore')->name('vendor.product.store');
	Route::put('vendor/product/{id}', 'VendorController@productUpdate')->name('vendor.product.update');
	Route::delete('vendor/product/{id}', 'VendorController@productDestroy')->name('vendor.product.destroy');
	Route::resource('vendor', 'VendorController');

	// Suppliers Module
	// Route::post('supplier/agent/store', 'SupplierController@agentStore')->name('supplier.agent.store');
	// Route::put('supplier/agent/update/{id}', 'SupplierController@agentUpdate')->name('supplier.agent.update');
	Route::post('supplier/send/emailBulk', 'SupplierController@sendEmailBulk')->name('supplier.email.send.bulk');
	Route::get('supplier/{id}/loadMoreMessages', 'SupplierController@loadMoreMessages');
	Route::resource('supplier', 'SupplierController');

	// Agent Routes
	Route::resource('agent', 'AgentController');
});

/* ------------------Twilio functionality Routes[PLEASE DONT MOVE INTO MIDDLEWARE AUTH] ------------------------ */
Route::get('twilio/token', 'TwilioController@createToken');
Route::post('twilio/ivr', 'TwilioController@ivr');
Route::post('twilio/gatherAction', 'TwilioController@gatherAction');
Route::post('twilio/incoming', 'TwilioController@incomingCall');
Route::post('twilio/outgoing', 'TwilioController@outgoingCall');
Route::get('twilio/getLeadByNumber', 'TwilioController@getLeadByNumber');
Route::post('twilio/recordingStatusCallback', 'TwilioController@recordingStatusCallback');
Route::post('twilio/handleDialCallStatus', 'TwilioController@handleDialCallStatus');
Route::post('twilio/handleOutgoingDialCallStatus', 'TwilioController@handleOutgoingDialCallStatus');
Route::post('twilio/storerecording','TwilioController@storeRecording');
Route::post('twilio/storetranscript','TwilioController@storetranscript');
Route::get(
            '/twilio/hangup', [
                'as' => 'hangup',
                'uses' => 'TwilioController@showHangup'
            ]
        );

Route::get('exotel/outgoing', 'ExotelController@call')->name('exotel.call');
Route::get('exotel/checkNumber', 'ExotelController@checkNumber');
Route::post('exotel/recordingCallback', 'ExotelController@recordingCallback');

/* ---------------------------------------------------------------------------------- */

Route::post('whatsapp/incoming', 'WhatsAppController@incomingMessage');
Route::post('whatsapp/incomingNew', 'WhatsAppController@incomingMessageNew');
Route::post('whatsapp/outgoingProcessed', 'WhatsAppController@outgoingProcessed');
Route::post('whatsapp/webhook', 'WhatsAppController@webhook');

Route::post('whatsapp/sendMessage/{context}', 'WhatsAppController@sendMessage')->name('whatsapp.send');
Route::post('whatsapp/sendMultipleMessages', 'WhatsAppController@sendMultipleMessages');
Route::post('whatsapp/approve/{context}', 'WhatsAppController@approveMessage');
Route::get('whatsapp/pollMessages/{context}', 'WhatsAppController@pollMessages');
Route::get('whatsapp/pollMessagesCustomer', 'WhatsAppController@pollMessagesCustomer');
Route::get('whatsapp/updatestatus/', 'WhatsAppController@updateStatus');
Route::post('whatsapp/updateAndCreate/', 'WhatsAppController@updateAndCreate');
Route::post('whatsapp/forwardMessage/', 'WhatsAppController@forwardMessage')->name('whatsapp.forward');
Route::post('whatsapp/{id}/fixMessageError', 'WhatsAppController@fixMessageError');
Route::post('whatsapp/{id}/resendMessage', 'WhatsAppController@resendMessage');


/*
 * @date 1/13/2019
 * @author Rishabh Aryal
 * This is route for Instagram
 * feature in this ERP
 */

Route::resource('cold-leads-broadcasts', 'ColdLeadBroadcastsController');
Route::resource('cold-leads', 'ColdLeadsController');

Route::prefix('sitejabber')->group(function() {
    Route::get('review/{id}/confirm', 'SitejabberQAController@confirmReviewAsPosted');
    Route::get('review/{id}/delete', 'SitejabberQAController@detachBrandReviews');
    Route::get('review/{id}', 'SitejabberQAController@attachBrandReviews');
    Route::get('accounts', 'SitejabberQAController@accounts');
    Route::get('reviews', 'SitejabberQAController@reviews');
    Route::resource('qa', 'SitejabberQAController');
});

Route::prefix('instagram')->group(function () {
    Route::get('flag/{id}', 'HashtagController@flagMedia');
    Route::get('thread/{id}', 'ColdLeadsController@getMessageThread');
    Route::post('thread/{id}', 'ColdLeadsController@sendMessage');
    Route::resource('brand-tagged', 'BrandTaggedPostsController');
    Route::post('media/comment', 'HashtagController@commentOnHashtag');
    Route::get('test/{id}', 'AccountController@test');
    Route::get('accounts', 'InstagramController@accounts');
    Route::get('notification', 'HashtagController@showNotification');
    Route::resource('influencer', 'InfluencersController');
    Route::resource('automated-reply', 'InstagramAutomatedMessagesController');
    Route::get('/', 'InstagramController@index');
    Route::get('comments/processed', 'HashtagController@showProcessedComments');
    Route::get('hashtag/post/comments/{mediaId}', 'HashtagController@loadComments');
    Route::post('leads/store', 'InstagramProfileController@add');
    Route::get('profiles/followers/{id}', 'InstagramProfileController@getFollowers');
    Route::resource('keyword', 'KeywordsController');
    Route::resource('profiles', 'InstagramProfileController');
    Route::get('posts', 'InstagramController@showPosts');
    Route::resource('hashtagposts', 'HashtagPostsController');
    Route::resource('hashtagpostscomments', 'HashtagPostCommentController');
    Route::get('hashtag/grid/{id}', 'HashtagController@showGrid');
    Route::resource('hashtag', 'HashtagController');
    Route::get('hashtags/grid', 'InstagramController@hashtagGrid');
    Route::get('comments', 'InstagramController@getComments');
    Route::post('comments', 'InstagramController@postComment');
    Route::get('post-media', 'InstagramController@showImagesToBePosted');
    Route::post('post-media', 'InstagramController@postMedia');
    Route::get('post-media-now/{schedule}', 'InstagramController@postMediaNow');
    Route::get('delete-schedule/{schedule}', 'InstagramController@cancelSchedule');
    Route::get('media/schedules', 'InstagramController@showSchedules');
    Route::post('media/schedules', 'InstagramController@postSchedules');
    Route::get('scheduled/events', 'InstagramController@getScheduledEvents');
    Route::get('schedule/{scheduleId}', 'InstagramController@editSchedule');
    Route::post('schedule/{scheduleId}', 'InstagramController@updateSchedule');
    Route::post('schedule/{scheduleId}/attach', 'InstagramController@attachMedia');
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

Route::prefix('scrap')->group(function () {
    Route::resource('facebook', 'FacebookController');
    Route::get('/dubbizle', 'DubbizleController@index');
    Route::get('/products', 'ScrapController@showProductStat');
    Route::get('/activity', 'ScrapController@activity')->name('scrap.activity');
    Route::get('/excel', 'ScrapController@excel_import');
    Route::post('/excel', 'ScrapController@excel_store');
    Route::get('/google/images', 'ScrapController@index');
    Route::post('/google/images', 'ScrapController@scrapGoogleImages');
    Route::post('/google/images/download', 'ScrapController@downloadImages');
    Route::get('/{name}', 'ScrapController@showProducts');
});

Route::resource('social-tags', 'SocialTagsController')->middleware('auth');

Route::get('test', 'WhatsAppController@getAllMessages');

Route::resource('track', 'UserActionsController');
Route::get('competitor-page/hide/{id}', 'CompetitorPageController@hideLead');
Route::resource('competitor-page', 'CompetitorPageController');
Route::resource('target-location', 'TargetLocationController');
