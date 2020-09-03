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

Auth::routes();


Route::get('/test/test', function(){
   dd(\Cache::get('key'));
});
Route::get('/test/dhl', 'TmpTaskController@test');
Route::get('create-media-image', 'CustomerController@testImage');
Route::get('generate-favicon', 'HomeController@generateFavicon');


Route::get('/products/affiliate', 'ProductController@affiliateProducts');
Route::post('/products/published', 'ProductController@published');

//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/productselection/list', 'ProductSelectionController@sList')->name('productselection.list');
Route::get('/productsearcher/list', 'ProductSearcherController@sList')->name('productsearcher.list');

// adding chat contro

Route::get('sop', 'ProductController@showSOP');

Route::get('/mageOrders', 'MagentoController@get_magento_orders');

Route::get('/message', 'MessageController@index')->name('message');
Route::post('/message', 'MessageController@store')->name('message.store');
Route::post('/message/{message}', 'MessageController@update')->name('message.update');
Route::post('/message/{id}/removeImage', 'MessageController@removeImage')->name('message.removeImage');
Route::get('/chat/getnew', 'ChatController@checkfornew')->name('checkfornew');
Route::get('/chat/updatenew', 'ChatController@updatefornew')->name('updatefornew');
//Route::resource('/chat','ChatController@getmessages');

Route::get('users/check/logins', 'UserController@checkUserLogins')->name('users.check.logins');
Route::resource('courier', 'CourierController');
Route::resource('product-location', 'ProductLocationController');

Route::prefix('product')->middleware('auth')->group(static function () {
    Route::get('manual-crop/assign-products', 'Products\ManualCroppingController@assignProductsToUser');
    Route::resource('manual-crop', 'Products\ManualCroppingController');
    Route::get('hscode', 'ProductController@hsCodeIndex');
    Route::post('hscode/save-group', 'ProductController@saveGroupHsCode')->name('hscode.save.group');
    Route::post('hscode/edit-group', 'ProductController@editGroup')->name('hscode.edit.group');
    Route::post('store-website-description', 'ProductController@storeWebsiteDescription')->name('product.store.website.description');
});

Route::prefix('logging')->middleware('auth')->group(static function () {
    Route::get('list-magento', 'Logging\LogListMagentoController@index')->name('list.magento.logging');
    Route::post('list-magento/{id}', 'Logging\LogListMagentoController@updateMagentoStatus');
    Route::get('list-laravel-logs', 'LaravelLogController@index')->name('logging.laravel.log');
    Route::get('live-laravel-logs', 'LaravelLogController@liveLogs')->name('logging.live.logs');
    Route::get('sku-logs', 'Logging\LogScraperController@logSKU')->name('logging.laravel.log');
    Route::get('sku-logs-errors', 'Logging\LogScraperController@logSKUErrors')->name('logging.sku.errors.log');
    Route::get('list-visitor-logs', 'VisitorController@index')->name('logging.visitor.log');
    Route::get('log-scraper', 'Logging\LogScraperController@index')->name('log-scraper.index');
    Route::get('live-scraper-logs', 'LaravelLogController@scraperLiveLogs')->name('logging.live.scraper-logs');
});

Route::prefix('category-messages')->group(function () {
    Route::post('bulk-messages/keyword', 'BulkCustomerRepliesController@storeKeyword');
    Route::post('bulk-messages/send-message', 'BulkCustomerRepliesController@sendMessagesByKeyword');
    Route::resource('bulk-messages', 'BulkCustomerRepliesController');
    Route::resource('keyword', 'KeywordToCategoryController');
    Route::resource('category', 'CustomerCategoryController');
});

Route::group(['middleware' => ['auth', 'optimizeImages']], function () {
    //Crop Reference
    Route::get('crop-references', 'CroppedImageReferenceController@index');
    Route::get('crop-references-grid', 'CroppedImageReferenceController@grid');
    Route::get('crop-referencesx', 'CroppedImageReferenceController@index');

    Route::get('/magento/status', 'MagentoController@addStatus');
    Route::post('/magento/status/save', 'MagentoController@saveStatus')->name('magento.save.status');

    Route::post('crop-references-grid/reject', 'CroppedImageReferenceController@rejectCropImage');

    Route::get('public-key', 'EncryptController@index')->name('encryption.index');
    Route::post('save-key', 'EncryptController@saveKey')->name('encryption.save.key');
    Route::post('forget-key', 'EncryptController@forgetKey')->name('encryption.forget.key');

    Route::get('reject-listing-by-supplier', 'ProductController@rejectedListingStatistics');
    Route::get('lead-auto-fill-info', 'LeadsController@leadAutoFillInfo');
    Route::resource('color-reference', 'ColorReferenceController');
    Route::get('crop/approved', 'ProductCropperController@getApprovedImages');
    Route::get('order-cropped-images', 'ProductCropperController@showCropVerifiedForOrdering');
    Route::post('save-sequence/{id}', 'ProductCropperController@saveSequence');
    Route::get('skip-sequence/{id}', 'ProductCropperController@skipSequence');
    Route::get('reject-sequence/{id}', 'ProductCropperController@rejectSequence');
    Route::post('ammend-crop/{id}', 'ProductCropperController@ammendCrop');
    Route::get('products/auto-cropped', 'ProductCropperController@getListOfImagesToBeVerified');
    Route::get('products/crop-issue-summary', 'ProductCropperController@cropIssuesPage');
    Route::get('products/rejected-auto-cropped', 'ProductCropperController@showRejectedCrops');
    Route::get('products/auto-cropped/{id}', 'ProductCropperController@showImageToBeVerified');
    Route::get('products/auto-cropped/{id}/show-rejected', 'ProductCropperController@showRejectedImageToBeverified');
    Route::get('products/auto-cropped/{id}/approve', 'ProductCropperController@approveCrop');
    Route::post('products/auto-cropped/{id}/approve-rejected', 'ProductCropperController@approveRejectedCropped');
    Route::get('products/auto-cropped/{id}/reject', 'ProductCropperController@rejectCrop');
    Route::get('products/auto-cropped/{id}/crop-approval-confirmation', 'ProductCropperController@cropApprovalConfirmation');
    Route::get('customer/livechat-redirect', 'LiveChatController@reDirect');
    Route::get('livechat/setting', 'LiveChatController@setting');
    Route::post('livechat/save', 'LiveChatController@save')->name('livechat.save');
    Route::post('livechat/remove', 'LiveChatController@remove')->name('livechat.remove');
    Route::resource('roles', 'RoleController');
    Route::resource('permissions', 'PermissionController');
    Route::get('permissions/grandaccess/users', 'PermissionController@users')->name('permissions.users');
    Route::get('unauthorized', 'RoleController@unAuthorized');
    Route::get('users/logins', 'UserController@login')->name('users.login.index');
    Route::get('permissions/grandaccess/users', 'PermissionController@users')->name('permissions.users');
    Route::get('userlogs', 'UserLogController@index')->name('userlogs.index');
    Route::get('userlogs/{$id}', 'UserLogController@index');
    Route::get('userlogs/datatables', 'UserLogController@getData')->name('userlogs.datatable');
    Route::get('users/{id}/assigned', 'UserController@showAllAssignedProductsForUser');
    Route::post('users/{id}/unassign/products', 'UserController@unassignProducts');
    Route::post('users/{id}/assign/products', 'UserController@assignProducts')->name('user.assign.products');
    Route::post('users/{id}/activate', 'UserController@activate')->name('user.activate');
    Route::resource('users', 'UserController');
    Route::resource('listing-payments', 'ListingPaymentsController');

    Route::get('products/assign-product', 'ProductController@getPreListProducts')->name('products.product-assign');
    Route::post('products/assign-product', 'ProductController@assignProduct')->name('products.product-assign-submit');


    Route::get('products/product-translation', 'ProductController@productTranslation')->name('products.product-translation');
    Route::get('products/product-translation/{id}', 'ProductController@viewProductTranslation')->name('products.product-translation.view');
    Route::post('products/product-translation/submit/{product_translation_id}', 'ProductController@editProductTranslation')->name('products.product-translation.edit');
    Route::get('products/product-translation/details/{id}/{locale}', 'ProductController@getProductTranslationDetails')->name('products.product-translation.locale');
    Route::get('product/listing/users', 'ProductController@showListigByUsers');
    Route::get('products/listing', 'ProductController@listing')->name('products.listing');
    Route::get('products/listing/final', 'ProductController@approvedListing')->name('products.listing.approved');
    Route::post('products/listing/final/pushproduct', 'ProductController@pushProduct');
    Route::get('products/listing/final-crop', 'ProductController@approvedListingCropConfirmation');
    Route::post('products/listing/final-crop-image', 'ProductController@cropImage')->name('products.crop.image');
    Route::get('products/listing/magento', 'ProductController@approvedMagento')->name('products.listing.magento');
    Route::get('products/listing/rejected', 'ProductController@showRejectedListedProducts');
    Route::get('product/listing-remark', 'ProductController@addListingRemarkToProduct');
    Route::get('product/update-listing-remark', 'ProductController@updateProductListingStats');
    Route::get('product/delete-product', 'ProductController@deleteProduct');
    Route::get('product/relist-product', 'ProductController@relistProduct');
    Route::get('products/stats', 'ProductController@productStats');
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
    Route::post('products/{id}/unlistMagento', 'ProductController@unlistMagento');
    Route::post('products/{id}/approveMagento', 'ProductController@approveMagento');
    Route::post('products/{id}/updateMagento', 'ProductController@updateMagento');
    Route::post('products/updateMagentoProduct', 'ProductController@updateMagentoProduct')->name('product.update.magento');
    Route::post('products/{id}/approveProduct', 'ProductController@approveProduct');
    Route::post('products/{id}/originalCategory', 'ProductController@originalCategory');
    Route::post('products/{id}/originalColor', 'ProductController@originalColor');
    Route::post('products/{id}/submitForApproval', 'ProductController@submitForApproval');
    Route::get('products/{id}/category-history', 'ProductCategoryController@history');
    Route::get('products/{id}/color-history', 'ProductColorController@history');

    Route::post('products/{id}/changeCategorySupplier', 'ProductController@changeAllCategoryForAllSupplierProducts');
    Route::post('products/{id}/changeColorSupplier', 'ProductController@changeAllColorForAllSupplierProducts');
    Route::resource('products', 'ProductController');
    Route::resource('attribute-replacements', 'AttributeReplacementController');
    Route::post('products/bulk/update', 'ProductController@bulkUpdate')->name('products.bulk.update');
    Route::post('products/{id}/archive', 'ProductController@archive')->name('products.archive');
    Route::post('products/{id}/restore', 'ProductController@restore')->name('products.restore');
    Route::get('/manual-image-upload', 'ProductSelectionController@manualImageUpload')->name('manual.image.upload');
    Route::resource('productselection', 'ProductSelectionController');
    Route::get('productattribute/delSizeQty/{id}', 'ProductAttributeController@delSizeQty');
    Route::resource('productattribute', 'ProductAttributeController');
    Route::resource('productsearcher', 'ProductSearcherController');
    Route::resource('productimagecropper', 'ProductCropperController');
    Route::resource('productsupervisor', 'ProductSupervisorController');
    Route::resource('productlister', 'ProductListerController');
    Route::resource('productapprover', 'ProductApproverController');
    Route::post('productinventory/import', 'ProductInventoryController@import')->name('productinventory.import');
    Route::get('productinventory/list', 'ProductInventoryController@list')->name('productinventory.list');
    Route::resource('productinventory', 'ProductInventoryController');

    Route::prefix('product-inventory')->group(function () {
        Route::get('/', 'NewProductInventoryController@index')->name('product-inventory.new');
        Route::post('/push-in-shopify-records', 'NewProductInventoryController@pushInStore')->name('product-inventory.pushInStore');
        Route::prefix('{id}')->group(function () {
            Route::get('push-in-shopify', 'NewProductInventoryController@pushInShopify')->name('product-inventory.push-in-shopify');
        });
    });


    Route::resource('sales', 'SaleController');
    Route::resource('stock', 'StockController');
    Route::post('stock/track/package', 'StockController@trackPackage')->name('stock.track.package');
    Route::delete('stock/{id}/permanentDelete', 'StockController@permanentDelete')->name('stock.permanentDelete');
    Route::post('stock/privateViewing/create', 'StockController@privateViewingStore')->name('stock.privateViewing.store');
    Route::get('stock/private/viewing', 'StockController@privateViewing')->name('stock.private.viewing');
    Route::delete('stock/private/viewing/{id}', 'StockController@privateViewingDestroy')->name('stock.private.viewing.destroy');
    Route::post('stock/private/viewing/upload', 'StockController@privateViewingUpload')->name('stock.private.viewing.upload');
    Route::post('stock/private/viewing/{id}/updateStatus', 'StockController@privateViewingUpdateStatus')->name('stock.private.viewing.updateStatus');
    Route::post('stock/private/viewing/{id}/updateOfficeBoy', 'StockController@updateOfficeBoy')->name('stock.private.viewing.updateOfficeBoy');
    Route::post('sop', 'ProductController@saveSOP');

    Route::get('product/delete-image', 'ProductController@deleteImage')->name('product.deleteImages');

    // Delivery Approvals
    Route::post('deliveryapproval/{id}/updateStatus', 'DeliveryApprovalController@updateStatus')->name('deliveryapproval.updateStatus');
    Route::resource('deliveryapproval', 'DeliveryApprovalController');

    //	Route::resource('activity','ActivityConroller');

    // For Brand size chart
    Route::get('brand/size/chart', 'BrandSizeChartController@index')->name('brand/size/chart');
    Route::get('brand/create/size/chart', 'BrandSizeChartController@createSizeChart')->name('brand/create/size/chart');
    Route::post('brand/store/size/chart', 'BrandSizeChartController@storeSizeChart')->name('brand/store/size/chart');

    Route::post('brand/attach-website', 'BrandController@attachWebsite');
    Route::post('brand/change-segment', 'BrandController@changeSegment');
    Route::get('brand/{id}/create-remote-id', 'BrandController@createRemoteId');
    Route::resource('brand', 'BrandController');
    Route::resource('reply', 'ReplyController');
    Route::post('reply/category/store', 'ReplyController@categoryStore')->name('reply.category.store');

    // Auto Replies
    Route::post('autoreply/{id}/updateReply', 'AutoReplyController@updateReply');
    Route::post('autoreply/delete-chat-word', 'AutoReplyController@deleteChatWord');
    Route::get('autoreply/replied-chat/{id}', 'AutoReplyController@getRepliedChat');
    Route::post('autoreply/save-group', 'AutoReplyController@saveGroup')->name('autoreply.save.group');
    Route::post('autoreply/save-group/phrases', 'AutoReplyController@saveGroupPhrases')->name('autoreply.save.group.phrases');
    Route::post('autoreply/save-by-question', 'AutoReplyController@saveByQuestion');
    Route::post('autoreply/delete-most-used-phrases', 'AutoReplyController@deleteMostUsedPharses')->name("chatbot.delete-most-used-pharses");
    Route::get('autoreply/get-phrases', 'AutoReplyController@getPhrases');
    Route::post('autoreply/phrases/reply', 'AutoReplyController@getPhrasesReply')->name('autoreply.group.phrases.reply');
    Route::get('autoreply/phrases/reply-response', 'AutoReplyController@getPhrasesReplyResponse')->name('autoreply.group.phrases.reply.response');

    Route::resource('autoreply', 'AutoReplyController');
    Route::get('most-used-words', 'AutoReplyController@mostUsedWords')->name("chatbot.mostUsedWords");
    Route::get('most-used-phrases', 'AutoReplyController@mostUsedPhrases')->name("chatbot.mostUsedPhrases");

    Route::get('most-used-phrases/deleted', 'AutoReplyController@mostUsedPhrasesDeleted')->name("chatbot.mostUsedPhrasesDeleted");
    Route::get('most-used-phrases/deleted/records', 'AutoReplyController@mostUsedPhrasesDeletedRecords')->name("chatbot.mostUsedPhrasesDeletedRecords");

    Route::post('settings/updateAutomatedMessages', 'SettingController@updateAutoMessages')->name('settings.update.automessages');
    Route::resource('settings', 'SettingController');
    Route::get('category/references', 'CategoryController@mapCategory');
    Route::post('category/references', 'CategoryController@saveReferences');
    Route::post('category/update-field', 'CategoryController@updateField');
    Route::post('category/reference', 'CategoryController@saveReference');
    Route::post('category/save-form', 'CategoryController@saveForm')->name("category.save.form");
    Route::resource('category', 'CategoryController');

    Route::resource('resourceimg', 'ResourceImgController');
    Route::get('resourceimg/pending/1', 'ResourceImgController@pending');
    Route::post('add-resource', 'ResourceImgController@addResource')->name('add.resource');
    Route::post('add-resourceCat', 'ResourceImgController@addResourceCat')->name('add.resourceCat');
    Route::post('edit-resourceCat', 'ResourceImgController@editResourceCat')->name('edit.resourceCat');
    Route::post('remove-resourceCat', 'ResourceImgController@removeResourceCat')->name('remove.resourceCat');
    Route::post('acitvate-resourceCat', 'ResourceImgController@activateResourceCat')->name('activate.resourceCat');

    Route::get('resourceimg/pending', 'ResourceImgController@pending');


    Route::post('delete-resource', 'ResourceImgController@deleteResource')->name('delete.resource');
    Route::get('images/resource/{id}', 'ResourceImgController@imagesResource')->name('images/resource');

    Route::resource('benchmark', 'BenchmarkController');

    // adding lead routes
    Route::get('leads/imageGrid', 'LeadsController@imageGrid')->name('leads.image.grid');
    Route::post('leads/sendPrices', 'LeadsController@sendPrices')->name('leads.send.prices');
    Route::resource('leads', 'LeadsController');
    Route::post('leads/{id}/changestatus', 'LeadsController@updateStatus');
    Route::delete('leads/permanentDelete/{leads}', 'LeadsController@permanentDelete')->name('leads.permanentDelete');
    Route::resource('chat', 'ChatController');
    Route::get('erp-leads', 'LeadsController@erpLeads');
    Route::post('erp-leads-send-message', 'LeadsController@sendMessage')->name('erp-leads-send-message');
    Route::get('erp-leads/response', 'LeadsController@erpLeadsResponse')->name('leads.erpLeadsResponse');
    Route::post('erp-leads/{id}/changestatus', 'LeadsController@updateErpStatus');
    Route::get('erp-leads/edit', 'LeadsController@erpLeadsEdit')->name('leads.erpLeads.edit');
    Route::get('erp-leads/create', 'LeadsController@erpLeadsCreate')->name('leads.erpLeads.create');
    Route::post('erp-leads/store', 'LeadsController@erpLeadsStore')->name('leads.erpLeads.store');
    Route::get('erp-leads/delete', 'LeadsController@erpLeadDelete')->name('leads.erpLeads.delete');
    Route::get('erp-leads/customer-search', 'LeadsController@customerSearch')->name('leads.erpLeads.customerSearch');

    //Cron
    Route::get('cron', 'CronController@index')->name('cron.index');
    Route::get('cron/run', 'CronController@runCommand')->name('cron.run.command');
    Route::get('cron/history/{id}', 'CronController@history')->name('cron.history');
    Route::post('cron/history/show', 'CronController@historySearch')->name('cron.history.search');


  Route::prefix('store-website')->middleware('auth')->group(static function () {
        Route::get('/status/all', 'OrderController@viewAllStatuses')->name('store-website.all.status');
        Route::get('/status/edit/{id}', 'OrderController@viewEdit')->name('store-website.status.edit');
        Route::post('/status/edit/{id}', 'OrderController@editStatus')->name('store-website.status.submitEdit');
        Route::get('/status/create', 'OrderController@viewCreateStatus');
        Route::post('/status/create', 'OrderController@createStatus')->name('store-website.submit.status');
        Route::get('/status/fetch', 'OrderController@viewFetchStatus');
        Route::post('/status/fetch', 'OrderController@fetchStatus')->name('store-website.fetch.status');
        Route::get('/status/fetchMasterStatus/{id}', 'OrderController@fetchMasterStatus');
    });




    //plesk
    Route::prefix('plesk')->middleware('auth')->group(static function () {
        Route::get('/domains', 'PleskController@index')->name('plesk.domains');
        Route::get('/domains/mail/create/{id}', 'PleskController@create')->name('plesk.domains.view-mail-create');
        Route::post('/domains/mail/create/{id}', 'PleskController@submitMail')->name('plesk.domains.submit-mail');
        Route::post('/domains/mail/delete/{id}', 'PleskController@deleteMail')->name('plesk.domains.delete-mail');
        Route::get('/domains/mail/accounts/{id}', 'PleskController@getMailAccounts')->name('plesk.domains.mail-accounts');
        Route::post('/domains/mail/change-password', 'PleskController@changePassword')->name('plesk.domains.mail-accounts.change-password');
        Route::get('/domains/view/{id}', 'PleskController@show')->name('plesk.domains.view');
    });



      //plesk
      Route::prefix('content-management')->middleware('auth')->group(static function () {
        Route::get('/', 'ContentManagementController@index')->name('content-management.index');
        Route::get('/preview-img/{id}', 'ContentManagementController@previewImage')->name('content-management.preview-img');
        Route::get('/manage/show-history', 'ContentManagementController@showHistory')->name('content-management.manage.show-history');
        Route::get('/social/account/create', 'ContentManagementController@viewAddSocialAccount')->name('content-management.social.create');
        Route::post('/social/account/create', 'ContentManagementController@addSocialAccount')->name('content-management.social.submit');
        Route::get('/manage/{id}', 'ContentManagementController@manageContent')->name('content-management.manage');
        Route::get('/manage/task-list/{id}', 'ContentManagementController@getTaskList')->name('content-management.manage.task-list');
        Route::get('/manage/preview-img/{id}', 'ContentManagementController@previewCategoryImage')->name('content-management.manage.preview-img');
        Route::get('/manage/milestone-task/{id}', 'ContentManagementController@getTaskMilestones')->name('content-management.manage.milestone-task');
        Route::post('/manage/save-category', 'ContentManagementController@saveContentCategory')->name('content-management.manage.save-category');
        Route::post('/manage/edit-category', 'ContentManagementController@editCategory')->name("content-management.category.edit");
        Route::post('/manage/save-content', 'ContentManagementController@saveContent')->name('content-management.manage.save-content');
        Route::post('/upload-documents', 'ContentManagementController@uploadDocuments')->name("content-management.upload-documents");
        Route::post('/save-documents', 'ContentManagementController@saveDocuments')->name("content-management.save-documents");
        Route::post('/delete-document', 'ContentManagementController@deleteDocument')->name("content-management.delete-documents");
        Route::post('/send-document', 'ContentManagementController@sendDocument')->name("content-management.send-documents");
        Route::post('/save-reviews', 'ContentManagementController@saveReviews')->name("content-management.save-reviews");
        Route::post('/manage/milestone-task/submit', 'ContentManagementController@submitMilestones')->name("content-management.submit-milestones");
        Route::prefix('{id}')->group(function () {
        Route::get('list-documents', 'ContentManagementController@listDocuments')->name("content-management.list-documents");
            Route::prefix('remarks')->group(function () {
                Route::get('/', 'ContentManagementController@remarks')->name("content-management.remarks");
                Route::post('/', 'ContentManagementController@saveRemarks')->name("content-management.saveRemarks");
            });
        });
    });

    Route::prefix('content-management-status')->group(function () {
        Route::get('/', 'StoreSocialContentStatusController@index')->name('content-management-status.index');
        Route::post('save', 'StoreSocialContentStatusController@save')->name('content-management-status.save');
        Route::post('statusEdit', 'StoreSocialContentStatusController@statusEdit')->name('content-management-status.edit-status');
        Route::post('store', 'StoreSocialContentStatusController@store')->name('content-management-status.store');
        Route::post('merge-status', 'StoreSocialContentStatusController@mergeStatus')->name('content-management-status.merge-status');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'StoreSocialContentStatusController@edit')->name('content-management-status.edit');
            Route::get('delete', 'StoreSocialContentStatusController@delete')->name('content-management-status.delete');
        });
    });



    //
    // Route::post('/delete-document', 'SiteDevelopmentController@deleteDocument')->name("site-development.delete-documents");
    // Route::post('/send-document', 'SiteDevelopmentController@sendDocument')->name("site-development.send-documents");
    // Route::prefix('{id}')->group(function () {
    //     Route::get('list-documents', 'SiteDevelopmentController@listDocuments')->name("site-development.list-documents");
    //     Route::prefix('remarks')->group(function () {
    //         Route::get('/', 'SiteDevelopmentController@remarks')->name("site-development.remarks");
    //         Route::post('/', 'SiteDevelopmentController@saveRemarks')->name("site-development.saveRemarks");
    //     });
    // });

    //	Route::resource('task','TaskController');

    // Instruction
    Route::get('instruction/quick-instruction', 'InstructionController@quickInstruction');
    Route::post('instruction/store-instruction-end-time', 'InstructionController@storeInstructionEndTime');
    Route::get('instruction/list', 'InstructionController@list')->name('instruction.list');
    Route::resource('instruction', 'InstructionController');
    Route::post('instruction/complete', 'InstructionController@complete')->name('instruction.complete');
    Route::post('instruction/pending', 'InstructionController@pending')->name('instruction.pending');
    Route::post('instruction/verify', 'InstructionController@verify')->name('instruction.verify');
    Route::post('instruction/skipped-count', 'InstructionController@skippedCount')->name('instruction.skipped.count');
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
    Route::get('order/{id}/send-invoice', 'OrderController@sendInvoice')->name('order.send.invoice');
    Route::get('order/{id}/send-order-email', 'OrderController@sendOrderEmail')->name('order.send.email');

    Route::get('order/{id}/preview-invoice', 'OrderController@previewInvoice')->name('order.perview.invoice');
    Route::post('order/{id}/createProductOnMagento', 'OrderController@createProductOnMagento')->name('order.create.magento.product');
    Route::get('order/{id}/download/PackageSlip', 'OrderController@downloadPackageSlip')->name('order.download.package-slip');
    Route::get('order/track/packageSlip', 'OrderController@trackPackageSlip')->name('order.track.package-slip');
    Route::delete('order/permanentDelete/{order}', 'OrderController@permanentDelete')->name('order.permanentDelete');
    Route::get('order/products/list', 'OrderController@products')->name('order.products');
    Route::get('order/missed-calls', 'OrderController@missedCalls')->name('order.missed-calls');
    Route::get('order/calls/history', 'OrderController@callsHistory')->name('order.calls-history');
    Route::post('order/generate/awb/number', 'OrderController@generateAWB')->name('order.generate.awb');
    Route::post('order/generate/awb/dhl', 'OrderController@generateAWBDHL')->name('order.generate.awbdhl');
    Route::get('order/generate/awb/rate-request', 'OrderController@generateRateRequet')->name('order.generate.rate-request');
    Route::post('order/generate/awb/rate-request', 'OrderController@generateRateRequet')->name('order.generate.rate-request');
    Route::get('orders/download', 'OrderController@downloadOrderInPdf');
    Route::get('order/change-status', 'OrderController@statusChange');
    Route::get('order/invoices', 'OrderController@viewAllInvoices');

    Route::get('order/{id}/edit-invoice', 'OrderController@editInvoice')->name('order.edit.invoice');
    Route::post('order/edit-invoice', 'OrderController@submitEdit')->name('order.submitEdit.invoice');
    Route::get('order/order-search', 'OrderController@searchOrderForInvoice')->name('order.search.invoice');
    Route::get('order/{id}/add-invoice', 'OrderController@addInvoice')->name('order.add.invoice');
    Route::post('order/submit-invoice', 'OrderController@submitInvoice')->name('order.submit.invoice');
    Route::get('order/view-invoice/{id}', 'OrderController@viewInvoice')->name('order.view.invoice');
    Route::get('order/{id}/mail-invoice', 'OrderController@mailInvoice')->name('order.mail.invoice');
    Route::resource('order', 'OrderController');

    Route::post('order/status/store', 'OrderReportController@statusStore')->name('status.store');
    Route::post('order/report/store', 'OrderReportController@store')->name('status.report.store');


    //emails
    Route::get('email/replyMail/{id}', 'EmailController@replyMail');
    Route::post('email/replyMail', 'EmailController@submitReply')->name('email.submit-reply');

    Route::get('email/forwardMail/{id}', 'EmailController@forwardMail');
    Route::post('email/forwardMail', 'EmailController@submitForward')->name('email.submit-forward');

    Route::post('email/resendMail/{id}', 'EmailController@resendMail');
    Route::put('email/{id}/mark-as-read', 'EmailController@markAsRead');
    Route::resource('email', 'EmailController');

    Route::get('email-remark', 'EmailController@getRemark')->name('email.getremark');
    Route::post('email-remark', 'EmailController@addRemark')->name('email.addRemark');


    // Zoom Meetings
    //Route::get( 'twilio/missedCallStatus', 'TwilioController@missedCallStatus' );
    Route::post('meeting/create', 'Meeting\ZoomMeetingController@createMeeting');
    Route::get('meeting/allmeetings', 'Meeting\ZoomMeetingController@getMeetings');
    Route::get('meetings/show-data', 'Meeting\ZoomMeetingController@showData')->name('meetings.show.data');
    Route::get('meetings/show', 'Meeting\ZoomMeetingController@show')->name('meetings.show');

    Route::get('task/time/history', 'TaskModuleController@getTimeHistory')->name('task.time.history');
    Route::get('task/categories', 'TaskModuleController@getTaskCategories')->name('task.categories');
    Route::get('task/list', 'TaskModuleController@list')->name('task.list');
    Route::get('task/get-discussion-subjects', 'TaskModuleController@getDiscussionSubjects')->name('task.discussion-subjects');
    // Route::get('task/create-task', 'TaskModuleController@createTask')->name('task.create-task');
    Route::post('task/flag', 'TaskModuleController@flag')->name('task.flag');
    Route::post('task/{id}/plan', 'TaskModuleController@plan')->name('task.plan');
    Route::post('task/assign/messages', 'TaskModuleController@assignMessages')->name('task.assign.messages');
    Route::post('task/loadView', 'TaskModuleController@loadView')->name('task.load.view');
    Route::post('task/bulk-complete', 'TaskModuleController@completeBulkTasks')->name('task.bulk.complete');
    Route::post('task/bulk-delete', 'TaskModuleController@deleteBulkTasks')->name('task.bulk.delete');
    Route::post('task/send-document', 'TaskModuleController@sendDocument')->name('task.send-document');
    Route::post('task/message/reminder', 'TaskModuleController@messageReminder')->name('task.message.reminder');
    Route::post('task/{id}/convertTask', 'TaskModuleController@convertTask')->name('task.convert.appointment');
    Route::post('task/{id}/updateSubject', 'TaskModuleController@updateSubject')->name('task.update.subject');
    Route::post('task/{id}/addNote', 'TaskModuleController@addNote')->name('task.add.note');
    Route::post('task/{id}/addSubnote', 'TaskModuleController@addSubnote')->name('task.add.subnote');
    Route::post('task/{id}/updateCategory', 'TaskModuleController@updateCategory')->name('task.update.category');
    Route::post('task/list-by-user-id', 'TaskModuleController@taskListByUserId')->name('task.list.by.user.id');
    Route::post('task/set-priority', 'TaskModuleController@setTaskPriority')->name('task.set.priority');
    Route::resource('task', 'TaskModuleController');
    Route::post('task/update/approximate', 'TaskModuleController@updateApproximate')->name('task.update.approximate');
    Route::post('task/time/history/approve', 'TaskModuleController@approveTimeHistory')->name('task.time.history.approve');

    
    Route::post('task/update/due_date', 'TaskModuleController@updateTaskDueDate')->name('task.update.due_date');
    Route::get('task/time/tracked/history', 'TaskModuleController@getTrackedHistory')->name('task.time.tracked.history');
    Route::post('task/create/hubstaff_task', 'TaskModuleController@createHubstaffManualTask')->name('task.create.hubstaff_task');
    Route::post('task/update/cost', 'TaskModuleController@updateCost')->name('task.update.cost');
    Route::get('task/update/milestone', 'TaskModuleController@saveMilestone')->name('task.update.milestone');
    Route::get('task/get/details', 'TaskModuleController@getDetails')->name('task.json.details');
    Route::post('task/get/save-notes', 'TaskModuleController@saveNotes')->name('task.json.saveNotes');
    Route::post('task_category/{id}/approve', 'TaskCategoryController@approve');
    Route::post('task_category/change-status', 'TaskCategoryController@changeStatus');
    Route::resource('task_category', 'TaskCategoryController');
    Route::post('task/addWhatsAppGroup', 'TaskModuleController@addWhatsAppGroup')->name('task.add.whatsapp.group');
    Route::post('task/addGroupParticipant', 'TaskModuleController@addGroupParticipant')->name('task.add.whatsapp.participant');
    Route::post('task/create-task-from-shortcut', 'TaskModuleController@createTaskFromSortcut')->name('task.create.task.shortcut');
    

    // Route::get('/', 'TaskModuleController@index')->name('home');
    Route::get('/', 'MasterControlController@index')->name('home');
    Route::get('/master-dev-task', 'MasterDevTaskController@index')->name('master.dev.task');

    // Daily Planner
    Route::post('dailyplanner/complete', 'DailyPlannerController@complete')->name('dailyplanner.complete');
    Route::post('dailyplanner/reschedule', 'DailyPlannerController@reschedule')->name('dailyplanner.reschedule');
    Route::resource('dailyplanner', 'DailyPlannerController');

    Route::resource('refund', 'RefundController');

    // Contacts
    Route::resource('contact', 'ContactController');

    Route::get('/notifications', 'NotificaitonContoller@index')->name('notifications');
    Route::get('/notificaitonsJson', 'NotificaitonContoller@json')->name('notificationJson');
    Route::get('/salesNotificaitonsJson', 'NotificaitonContoller@salesJson')->name('salesNotificationJson');
    Route::post('/notificationMarkRead/{notificaion}', 'NotificaitonContoller@markRead')->name('notificationMarkRead');
    Route::get('/deQueueNotfication', 'NotificationQueueController@deQueueNotficationNew');

    Route::post('/productsupervisor/approve/{product}', 'ProductSupervisorController@approve')->name('productsupervisor.approve');
    Route::post('/productsupervisor/reject/{product}', 'ProductSupervisorController@reject')->name('productsupervisor.reject');
    Route::post('/productlister/isUploaded/{product}', 'ProductListerController@isUploaded')->name('productlister.isuploaded');
    Route::post('/productapprover/isFinal/{product}', 'ProductApproverController@isFinal')->name('productapprover.isfinal');

    Route::get('/productinventory/in/stock', 'ProductInventoryController@instock')->name('productinventory.instock');
    Route::post('/productinventory/in/stock/update-field', 'ProductInventoryController@updateField')->name('productinventory.instock.update-field');
    Route::get('/productinventory/in/delivered', 'ProductInventoryController@inDelivered')->name('productinventory.indelivered');
    Route::get('/productinventory/in/stock/instruction-create', 'ProductInventoryController@instructionCreate')->name('productinventory.instruction.create');
    Route::post('/productinventory/in/stock/instruction', 'ProductInventoryController@instruction')->name('productinventory.instruction');
    Route::get('/productinventory/in/stock/location-history', 'ProductInventoryController@locationHistory')->name('productinventory.location.history');
    Route::post('/productinventory/in/stock/dispatch-store', 'ProductInventoryController@dispatchStore')->name('productinventory.dispatch.store');
    Route::get('/productinventory/in/stock/dispatch', 'ProductInventoryController@dispatchCreate')->name('productinventory.dispatch.create');
    Route::post('/productinventory/stock/{product}', 'ProductInventoryController@stock')->name('productinventory.stock');
    Route::get('productinventory/in/stock/location/change', 'ProductInventoryController@locationChange')->name('productinventory.location.change');


    Route::prefix('google-search-image')->group(function () {
        Route::get('/', 'GoogleSearchImageController@index')->name('google.search.image');
        Route::post('/crop', 'GoogleSearchImageController@crop')->name('google.search.crop');
        Route::post('/crop-search', 'GoogleSearchImageController@searchImageOnGoogle')->name('google.search.crop.post');
        Route::post('details', 'GoogleSearchImageController@details')->name('google.search.details');
        Route::post('queue', 'GoogleSearchImageController@queue')->name('google.search.queue');
        Route::post('/multiple-products', 'GoogleSearchImageController@getImageForMultipleProduct')->name('google.product.queue');
        Route::post('/image-crop-sequence', 'GoogleSearchImageController@cropImageSequence')->name('google.crop.sequence');
        Route::post('/update-product-status', 'GoogleSearchImageController@updateProductStatus')->name('google.product.status');
        Route::post('product-by-image', 'GoogleSearchImageController@getProductFromImage')->name('google.product.image');
    });

    Route::prefix('search-image')->group(function () {
        Route::get('/', 'GoogleSearchImageController@product')->name('google.search.product');
        Route::post('/', 'GoogleSearchImageController@product')->name('google.search.product-save');
    });

    Route::prefix('multiple-search-image')->group(function () {
        Route::get('/', 'GoogleSearchImageController@nultipeImageProduct')->name('google.search.multiple');
        Route::post('/save-images', 'GoogleSearchImageController@multipleImageStore')->name('multiple.google.search.product-save');
        Route::post('/single-save-images', 'GoogleSearchImageController@getProductFromText')->name('multiple.google.product-save');
    });

    Route::prefix('approve-search-image')->group(function () {
        Route::get('/', 'GoogleSearchImageController@approveProduct')->name('google.approve.product');
        Route::post('/approve-images-product', 'GoogleSearchImageController@approveTextGoogleImagesToProduct')->name('approve.google.search.images.product');
        Route::post('/reject', 'GoogleSearchImageController@rejectProducts')->name('reject.google.search.text.product');
    });


    Route::get('category', 'CategoryController@manageCategory')->name('category');
    Route::post('add-category', 'CategoryController@addCategory')->name('add.category');
    Route::post('category/{category}/edit', 'CategoryController@edit')->name('category.edit');
    Route::post('category/remove', 'CategoryController@remove')->name('category.remove');

    Route::get('productSearch/', 'SaleController@searchProduct');
    Route::post('productSearch/', 'SaleController@searchProduct');

    Route::get('user-search/', 'UserController@searchUser');
    Route::post('user-search/', 'UserController@searchUser');

    Route::get('activity/', 'ActivityConroller@showActivity')->name('activity');
    Route::get('graph/', 'ActivityConroller@showGraph')->name('graph');
    Route::get('graph/user', 'ActivityConroller@showUserGraph')->name('graph_user');

    Route::get('search/', 'SearchController@search')->name('search');
    Route::get('pending/{roletype}', 'SearchController@getPendingProducts')->name('pending');

    Route::get('loadEnvManager/', 'EnvController@loadEnvManager')->name('load_env_manager');
    
    //	Route::post('productAttachToSale/{sale}/{product_id}','SaleController@attachProduct');
    //	Route::get('productSelectionGrid/{sale}','SaleController@selectionGrid')->name('productSelectionGrid');

    //Attach Products
    Route::get('attachProducts/{model_type}/{model_id}/{type?}/{customer_id?}', 'ProductController@attachProducts')->name('attachProducts');
    Route::post('attachProductToModel/{model_type}/{model_id}/{product_id}', 'ProductController@attachProductToModel')->name('attachProductToModel');
    Route::post('deleteOrderProduct/{order_product}', 'OrderController@deleteOrderProduct')->name('deleteOrderProduct');
    Route::get('attachImages/{model_type}/{model_id?}/{status?}/{assigned_user?}', 'ProductController@attachImages')->name('attachImages');
    Route::post('selected_customer/sendMessage', 'ProductController@sendMessageSelectedCustomer')->name('whatsapp.send_selected_customer');

    // landing page
    Route::prefix('landing-page')->group(function () {
        Route::get('/', 'LandingPageController@index')->name('landing-page.index');
        Route::post('/save', 'LandingPageController@save')->name('landing-page.save');
        Route::get('/records', 'LandingPageController@records')->name('landing-page.records');
        Route::post('/store', 'LandingPageController@store')->name('landing-page.store');
        Route::post('/update-time', 'LandingPageController@updateTime')->name('landing-page.updateTime');
        Route::get('/image/{id}/{productId}/delete', 'LandingPageController@deleteImage')->name('landing-page.deleteImage');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'LandingPageController@edit')->name('landing-page.edit');
            Route::get('delete', 'LandingPageController@delete')->name('landing-page.delete');
            Route::get('push-to-shopify', 'LandingPageController@pushToShopify')->name('landing-page.push-to-shopify');
            Route::get('change-store', 'LandingPageController@changeStore')->name('landing-page.change.store');
        });
    });

    Route::post('download', 'MessageController@downloadImages')->name('download.images');

    Route::get('quickSell', 'QuickSellController@index')->name('quicksell.index');
    Route::post('quickSell', 'QuickSellController@store')->name('quicksell.store');
    Route::post('quickSell/edit', 'QuickSellController@update')->name('quicksell.update');
    Route::post('quickSell/saveGroup', 'QuickSellController@saveGroup')->name('quicksell.save.group');
    Route::get('quickSell/pending', 'QuickSellController@pending')->name('quicksell.pending');
    Route::post('quickSell/activate', 'QuickSellController@activate')->name('quicksell.activate');
    Route::get('quickSell/search', 'QuickSellController@search')->name('quicksell.search');
    Route::post('quickSell/groupUpdate', 'QuickSellController@groupUpdate')->name('quicksell.group.update');


    // Chat messages
    Route::get('chat-messages/{object}/{object_id}/loadMoreMessages', 'ChatMessagesController@loadMoreMessages');
    Route::post('chat-messages/{id}/set-reviewed', 'ChatMessagesController@setReviewed');

    // Customers
    Route::get('customer/exportCommunication/{id}', 'CustomerController@exportCommunication');
    Route::get('customer/test', 'CustomerController@customerstest');
    Route::post('customer/reminder', 'CustomerController@updateReminder');
    Route::post('supplier/reminder', 'SupplierController@updateReminder');
    Route::post('supplier/excel-import', 'SupplierController@excelImport');
    Route::post('vendors/reminder', 'VendorController@updateReminder');
    Route::post('customer/add-note/{id}', 'CustomerController@addNote');
    Route::post('supplier/add-note/{id}', 'SupplierController@addNote');
    Route::get('customers/{id}/post-show', 'CustomerController@postShow')->name('customer.post.show');
    Route::post('customers/{id}/post-show', 'CustomerController@postShow')->name('customer.post.show');
    Route::post('customers/{id}/sendAdvanceLink', 'CustomerController@sendAdvanceLink')->name('customer.send.advanceLink');
    Route::get('customers/{id}/loadMoreMessages', 'CustomerController@loadMoreMessages');
    Route::get('customer/search', 'CustomerController@search');
    Route::get('customers', 'CustomerController@index')->name('customer.index');
    Route::post('add-reply-category', 'CustomerController@addReplyCategory')->name('add.reply.category');
    Route::post('destroy-reply-category', 'CustomerController@destroyReplyCategory')->name('destroy.reply.category');
    Route::get('customers-load', 'CustomerController@load')->name('customer.load');
    Route::post('customer/{id}/initiateFollowup', 'CustomerController@initiateFollowup')->name('customer.initiate.followup');
    Route::post('customer/{id}/stopFollowup', 'CustomerController@stopFollowup')->name('customer.stop.followup');
    Route::get('customer/export', 'CustomerController@export')->name('customer.export');
    Route::post('customer/merge', 'CustomerController@merge')->name('customer.merge');
    Route::post('customer/import', 'CustomerController@import')->name('customer.import');
    Route::get('customer/create', 'CustomerController@create')->name('customer.create');
    Route::post('customer/block', 'CustomerController@block')->name('customer.block');
    Route::post('customer/flag', 'CustomerController@flag')->name('customer.flag');
    Route::post('customer/in-w-list', 'CustomerController@addInWhatsappList')->name('customer.in-w-list');
    Route::post('customer/prioritize', 'CustomerController@prioritize')->name('customer.priority');
    Route::post('customer/create', 'CustomerController@store')->name('customer.store');
    Route::get('customer/broadcast', 'CustomerController@broadcast')->name('customer.broadcast.list');
    Route::get('customer/broadcast-details', 'CustomerController@broadcastDetails')->name('customer.broadcast.details');
    Route::get('customer/broadcast-send-price', 'CustomerController@broadcastSendPrice')->name('customer.broadcast.run');
    Route::get('customer/contact-download/{id}', 'CustomerController@downloadContactDetailsPdf')->name('customer.download.contact-pdf');
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
    Route::post('customer/change-whatsapp-no', 'CustomerController@changeWhatsappNo')->name('customer.change.whatsapp');
    Route::post('customer/update-field', 'CustomerController@updateField')->name('customer.update.field');
    Route::post('customer/send-contact-details', 'CustomerController@sendContactDetails')->name('customer.send.contact');
    Route::post('customer/contact-download-donload', 'CustomerController@downloadContactDetails')->name('customer.download.contact');
    Route::post('customer/create-kyc', 'CustomerController@createKyc')->name('customer.create.kyc');

    Route::get('quickcustomer', 'CustomerController@quickcustomer')->name('quickcustomer');
    Route::get('quick-customer', 'QuickCustomerController@index')->name('quick.customer.index');
    Route::get('quick-customer/records', 'QuickCustomerController@records')->name('quick.customer.records');

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
    Route::get('purchase/calendar', 'PurchaseController@calendar')->name('purchase.calendar');
    Route::post('purchase/{id}/updateDelivery', 'PurchaseController@updateDelivery');
    Route::post('purchase/{id}/assignBatch', 'PurchaseController@assignBatch')->name('purchase.assign.batch');
    Route::post('purchase/{id}/assignSplitBatch', 'PurchaseController@assignSplitBatch')->name('purchase.assign.split.batch');
    Route::post('purchase/export', 'PurchaseController@export')->name('purchase.export');
    Route::post('purchase/merge', 'PurchaseController@merge')->name('purchase.merge');
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
    Route::post('purchase/email/reply', 'PurchaseController@emailReply')->name('purchase.email.reply');
    Route::get('pc/test', 'PictureColorsController@index');
    Route::post('purchase/email/forward', 'PurchaseController@emailForward')->name('purchase.email.forward');
    Route::get('download/crop-rejected/{id}/{type}', 'ProductCropperController@downloadImagesForProducts');

    Route::post('purchase/sendmsgsupplier', 'PurchaseController@sendmsgsupplier')->name('purchase.sendmsgsupplier');
    Route::get('get-supplier-msg', 'PurchaseController@getMsgSupplier')->name('get.msg.supplier');
    Route::post('purchase/send/emailBulk', 'PurchaseController@sendEmailBulk')->name('purchase.email.send.bulk');
    Route::resource('purchase-status', 'PurchaseStatusController');

    Route::get('download/crop-rejected/{id}/{type}', 'ProductCropperController@downloadImagesForProducts');

    // Master Plan
    Route::get('mastercontrol/clearAlert', 'MasterControlController@clearAlert')->name('mastercontrol.clear.alert');
    Route::resource('mastercontrol', 'MasterControlController');


    // Cash Vouchers
    Route::get('/voucher/payment/request', 'VoucherController@paymentRequest')->name("voucher.payment.request");
    Route::post('/voucher/payment/request', 'VoucherController@createPaymentRequest')->name('voucher.payment.request-submit');
    Route::get('/voucher/payment/{id}', 'VoucherController@viewPaymentModal')->name("voucher.payment");
    Route::post('/voucher/payment/{id}', 'VoucherController@submitPayment')->name("voucher.payment.submit");
    Route::post('voucher/{id}/approve', 'VoucherController@approve')->name('voucher.approve');
    Route::post('voucher/store/category', 'VoucherController@storeCategory')->name('voucher.store.category');
    Route::post('voucher/{id}/reject', 'VoucherController@reject')->name('voucher.reject');
    Route::post('voucher/{id}/resubmit', 'VoucherController@resubmit')->name('voucher.resubmit');
    Route::get('/voucher/manual-payment', 'VoucherController@viewManualPaymentModal')->name("voucher.payment.manual-payment");
    Route::post('/voucher/manual-payment', 'VoucherController@manualPaymentSubmit')->name("voucher.payment.manual-payment-submit");

    Route::resource('voucher', 'VoucherController');

    // Budget
    Route::resource('budget', 'BudgetController');
    Route::post('budget/category/store', 'BudgetController@categoryStore')->name('budget.category.store');
    Route::post('budget/subcategory/store', 'BudgetController@subCategoryStore')->name('budget.subcategory.store');

    //Comments
    Route::post('doComment', 'CommentController@store')->name('doComment');
    Route::post('deleteComment/{comment}', 'CommentController@destroy')->name('deleteComment');
    Route::get('message/updatestatus', 'MessageController@updatestatus')->name('message.updatestatus');
    Route::get('message/loadmore', 'MessageController@loadmore')->name('message.loadmore');

    //Push Notifications new
    Route::get('/new-notifications', 'PushNotificationController@index')->name('pushNotification.index');
    Route::get('/pushNotifications', 'PushNotificationController@getJson')->name('pushNotifications');
    Route::post('/pushNotificationMarkRead/{push_notification}', 'PushNotificationController@markRead')->name('pushNotificationMarkRead');
    Route::post('/pushNotificationMarkReadReminder/{push_notification}', 'PushNotificationController@markReadReminder')->name('pushNotificationMarkReadReminder');
    Route::post('/pushNotification/status/{push_notification}', 'PushNotificationController@changeStatus')->name('pushNotificationStatus');

    Route::post('dailyActivity/store', 'DailyActivityController@store')->name('dailyActivity.store');
    Route::post('dailyActivity/quickStore', 'DailyActivityController@quickStore')->name('dailyActivity.quick.store');
    Route::get('dailyActivity/complete/{id}', 'DailyActivityController@complete');
    Route::get('dailyActivity/start/{id}', 'DailyActivityController@start');
    Route::get('dailyActivity/get', 'DailyActivityController@get')->name('dailyActivity.get');

    // Complete the task
    // Route::get('/task/count/{taskid}', 'TaskModuleController@taskCount')->name('task.count');
    Route::get('/task/assign/master-user', 'TaskModuleController@assignMasterUser')->name('task.asign.master-user');
    Route::post('/task/upload-documents', 'TaskModuleController@uploadDocuments')->name("task.upload-documents");
    Route::post('/task/save-documents', 'TaskModuleController@saveDocuments')->name("task.save-documents");
    Route::get('/task/preview-img/{id}', 'TaskModuleController@previewTaskImage')->name('task.preview-img');
    Route::get('/task/complete/{taskid}', 'TaskModuleController@complete')->name('task.complete');
    Route::get('/task/start/{taskid}', 'TaskModuleController@start')->name('task.start');
    Route::get('/statutory-task/complete/{taskid}', 'TaskModuleController@statutoryComplete')->name('task.statutory.complete');
    Route::post('/task/addremark', 'TaskModuleController@addRemark')->name('task.addRemark');
    Route::get('tasks/getremark', 'TaskModuleController@getremark')->name('task.getremark');
    Route::get('tasks/gettaskremark', 'TaskModuleController@getTaskRemark')->name('task.gettaskremark');
    Route::post('task/{id}/makePrivate', 'TaskModuleController@makePrivate');
    Route::post('task/{id}/isWatched', 'TaskModuleController@isWatched');
    Route::post('task-remark/{id}/delete', 'TaskModuleController@archiveTaskRemark')->name('task.archive.remark');
    Route::post('tasks/deleteTask', 'TaskModuleController@deleteTask');
    Route::post('tasks/{id}/delete', 'TaskModuleController@archiveTask')->name('task.archive');
    //	Route::get('task/completeStatutory/{satutory_task}','TaskModuleController@completeStatutory');
    Route::post('task/deleteStatutoryTask', 'TaskModuleController@deleteStatutoryTask');

    Route::post('task/export', 'TaskModuleController@exportTask')->name('task.export');
    Route::post('/task/addRemarkStatutory', 'TaskModuleController@addRemark')->name('task.addRemarkStatutory');
    Route::get('delete/task/note', 'TaskModuleController@deleteTaskNote')->name('delete/task/note');
    Route::get('hide/task/remark', 'TaskModuleController@hideTaskRemark')->name('hide/task/remark');

    // Social Media Image Module
    Route::get('lifestyle/images/grid', 'ImageController@index')->name('image.grid');
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
    Route::post('images/searchQueue', 'ImageController@imageQueue')->name('image.queue');

    Route::post('leads/save-leave-message', 'LeadsController@saveLeaveMessage')->name('leads.message.save');

    Route::get('imported/leads', 'ColdLeadsController@showImportedColdLeads');
    Route::get('imported/leads/save', 'ColdLeadsController@addLeadToCustomer');

    // Development
    Route::post('development/task/move-to-progress', 'DevelopmentController@moveTaskToProgress');
    Route::post('development/task/complete-task', 'DevelopmentController@completeTask');
    Route::post('development/task/assign-task', 'DevelopmentController@updateAssignee');
    Route::post('development/task/relist-task', 'DevelopmentController@relistTask');
    Route::post('development/task/update-status', 'DevelopmentController@changeTaskStatus');
    Route::post('development/task/upload-document', 'DevelopmentController@uploadDocument');
    Route::post('development/task/bulk-delete', 'DevelopmentController@deleteBulkTasks');
    Route::get('development/task/get-document', 'DevelopmentController@getDocument');


    Route::resource('task-types', 'TaskTypesController');

    Route::resource('development-messages-schedules', 'DeveloperMessagesAlertSchedulesController');
    Route::get('development', 'DevelopmentController@index')->name('development.index');
    Route::post('development/task/list-by-user-id', 'DevelopmentController@taskListByUserId')->name('development.task.list.by.user.id');
    Route::post('development/task/set-priority', 'DevelopmentController@setTaskPriority')->name('development.task.set.priority');
    Route::post('development/create', 'DevelopmentController@store')->name('development.store');
    Route::post('development/{id}/edit', 'DevelopmentController@update')->name('development.update');
    Route::post('development/{id}/verify', 'DevelopmentController@verify')->name('development.verify');
    Route::get('development/verify/view', 'DevelopmentController@verifyView')->name('development.verify.view');
    Route::delete('development/{id}/destroy', 'DevelopmentController@destroy')->name('development.destroy');
    Route::post('development/{id}/updateCost', 'DevelopmentController@updateCost')->name('development.update.cost');
    Route::post('development/{id}/status', 'DevelopmentController@updateStatus')->name('development.update.status');
    Route::post('development/{id}/updateTask', 'DevelopmentController@updateTask')->name('development.update.task');
    Route::post('development/{id}/updatePriority', 'DevelopmentController@updatePriority')->name('development.update.priority');
    Route::post('development/upload-attachments', 'DevelopmentController@uploadAttachDocuments')->name('development.upload.files');
    Route::get('download-file', 'DevelopmentController@downloadFile')->name('download.file');

    //Route::get('development/issue/list', 'DevelopmentController@issueIndex')->name('development.issue.index');
    Route::get('development/list', 'DevelopmentController@issueTaskIndex')->name('development.issue.index');
    //Route::get('development/issue/list', 'DevelopmentController@issueIndex')->name('development.issue.index');
    Route::post('development/issue/list-by-user-id', 'DevelopmentController@listByUserId')->name('development.issue.list.by.user.id');
    Route::post('development/issue/set-priority', 'DevelopmentController@setPriority')->name('development.issue.set.priority');
    Route::post('development/time/history/approve', 'DevelopmentController@approveTimeHistory')->name('development/time/history/approve');
    Route::get('development/issue/create', 'DevelopmentController@issueCreate')->name('development.issue.create');
    Route::post('development/issue/create', 'DevelopmentController@issueStore')->name('development.issue.store');
    Route::get('development/issue/user/assign', 'DevelopmentController@assignUser');
    Route::get('development/issue/master/assign', 'DevelopmentController@assignMasterUser');
    Route::get('development/issue/module/assign', 'DevelopmentController@changeModule');
    Route::get('development/issue/user/resolve', 'DevelopmentController@resolveIssue');
    Route::get('development/issue/estimate_date/assign', 'DevelopmentController@saveEstimateTime');
    Route::get('development/issue/estimate_minutes/assign', 'DevelopmentController@saveEstimateMinutes');
    Route::get('development/issue/responsible-user/assign', 'DevelopmentController@assignResponsibleUser');
    Route::get('development/issue/cost/assign', 'DevelopmentController@saveAmount');
    Route::get('development/issue/milestone/assign', 'DevelopmentController@saveMilestone');
    Route::get('development/issue/language/assign', 'DevelopmentController@saveLanguage');
    Route::post('development/{id}/assignIssue', 'DevelopmentController@issueAssign')->name('development.issue.assign');
    Route::delete('development/{id}/issueDestroy', 'DevelopmentController@issueDestroy')->name('development.issue.destroy');
    Route::get('development/overview', 'DevelopmentController@overview')->name('development.overview');
    Route::get('development/task-detail/{id}', 'DevelopmentController@taskDetail')->name('taskDetail');
    Route::get('development/new-task-popup', 'DevelopmentController@openNewTaskPopup')->name('openNewTaskPopup');

    Route::post('development/status/create', 'DevelopmentController@statusStore')->name('development.status.store');
    Route::post('development/module/create', 'DevelopmentController@moduleStore')->name('development.module.store');
    Route::delete('development/module/{id}/destroy', 'DevelopmentController@moduleDestroy')->name('development.module.destroy');
    Route::post('development/{id}/assignModule', 'DevelopmentController@moduleAssign')->name('development.module.assign');

    Route::post('development/comment/create', 'DevelopmentController@commentStore')->name('development.comment.store');
    Route::post('task/comment/create', 'DevelopmentController@taskComment')->name('task.comment.store');
    Route::post('development/{id}/awaiting/response', 'DevelopmentController@awaitingResponse')->name('development.comment.awaiting.response');

    Route::post('development/cost/store', 'DevelopmentController@costStore')->name('development.cost.store');

    // Development
    Route::get('development', 'DevelopmentController@index')->name('development.index');
    Route::get('development/update-values', 'DevelopmentController@updateValues');
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
    Route::get('development/time/history', 'DevelopmentController@getTimeHistory')->name('development/time/history');
    Route::get('development/tracked/history', 'DevelopmentController@getTrackedHistory')->name('development/tracked/history');
    Route::post('development/create/hubstaff_task', 'DevelopmentController@createHubstaffManualTask')->name('development/create/hubstaff_task');

    /*Routes For Social */
    Route::any('social/get-post/page', 'SocialController@pagePost')->name('social.get-post.page');

    // post creating routes define's here
    Route::get('social/post/page', 'SocialController@index')->name('social.post.page');
    Route::post('social/post/page/create', 'SocialController@createPost')->name('social.post.page.create');

    /*Routes For Social */
    Route::any('social/get-post/page', 'SocialController@pagePost')->name('social.get-post.page');

    // post creating routes define's here
    Route::get('social/post/page', 'SocialController@index')->name('social.post.page');
    Route::post('social/post/page/create', 'SocialController@createPost')->name('social.post.page.create');

    // Ad reports routes
    Route::get('social/ad/report', 'SocialController@report')->name('social.report');
    Route::get('social/ad/schedules', 'SocialController@getSchedules')->name('social.ads.schedules');
    Route::post('social/ad/schedules', 'SocialController@getSchedules')->name('social.ads.schedules.p');
    Route::get('social/ad/schedules/calendar', 'SocialController@getAdSchedules')->name('social.ads.schedules.calendar');
    Route::post('social/ad/schedules/', 'SocialController@createAdSchedule')->name('social.ads.schedules.create');
    Route::post('social/ad/schedules/attach-images/{id}', 'SocialController@attachMedia')->name('social.ads.schedules.attach_images');
    Route::post('social/ad/schedules/attach-products/{id}', 'SocialController@attachProducts')->name('social.ads.schedules.attach_products');
    Route::post('social/ad/schedules/', 'SocialController@createAdSchedule')->name('social.ads.schedules.attach_image');
    Route::get('social/ad/schedules/{id}', 'SocialController@showSchedule')->name('social.ads.schedules.show');
    Route::get('social/ad/insight/{adId}', 'SocialController@getAdInsights')->name('social.ad.insight');
    Route::post('social/ad/report/paginate', 'SocialController@paginateReport')->name('social.report.paginate');
    Route::get('social/ad/report/{ad_id}/{status}/', 'SocialController@changeAdStatus')->name('social.report.ad.status');
    // end to ad reports routes

    // AdCreative reports routes
    Route::get('social/adcreative/report', 'SocialController@adCreativereport')->name('social.adCreative.report');
    Route::post('social/adcreative/report/paginate', 'SocialController@adCreativepaginateReport')->name('social.adCreative.paginate');
    // end to ad reports routes

    // Creating Ad Campaign Routes defines here
    Route::get('social/ad/campaign/create', 'SocialController@createCampaign')->name('social.ad.campaign.create');
    Route::post('social/ad/campaign/store', 'SocialController@storeCampaign')->name('social.ad.campaign.store');

    // Creating Adset Routes define here
    Route::get('social/ad/adset/create', 'SocialController@createAdset')->name('social.ad.adset.create');
    Route::post('social/ad/adset/store', 'SocialController@storeAdset')->name('social.ad.adset.store');

    // Creating Ad Routes define here
    Route::get('social/ad/create', 'SocialController@createAd')->name('social.ad.create');
    Route::post('social/ad/store', 'SocialController@storeAd')->name('social.ad.store');
    // End of Routes for social

    // Paswords Manager
    Route::get('passwords', 'PasswordController@index')->name('password.index');
    Route::post('password/store', 'PasswordController@store')->name('password.store');
    Route::get('password/passwordManager', 'PasswordController@manage')->name('password.manage');
    Route::post('password/change', 'PasswordController@changePassword')->name('password.change');
    Route::post('password/sendWhatsApp', 'PasswordController@sendWhatsApp')->name('password.sendwhatsapp');
    Route::post('password/update', 'PasswordController@update')->name('password.update');
    Route::post('password/getHistory', 'PasswordController@getHistory')->name('password.history');

    //Language Manager
    Route::get('languages', 'LanguageController@index')->name('language.index');
    Route::post('language/store', 'LanguageController@store')->name('language.store');
    Route::post('language/update', 'LanguageController@update')->name('language.update');
    Route::post('language/delete', 'LanguageController@delete')->name('language.delete');


    // Documents Manager
    Route::get('documents', 'DocumentController@index')->name('document.index');
    Route::get('documents-email', 'DocumentController@email')->name('document.email');
    Route::post('document/store', 'DocumentController@store')->name('document.store');
    Route::post('document/{id}/update', 'DocumentController@update')->name('document.update');
    Route::get('document/{id}/download', 'DocumentController@download')->name('document.download');
    Route::delete('document/{id}/destroy', 'DocumentController@destroy')->name('document.destroy');
    Route::post('document/send/emailBulk', 'DocumentController@sendEmailBulk')->name('document.email.send.bulk');
    Route::get('document/gettaskremark', 'DocumentController@getTaskRemark')->name('document.gettaskremark');
    Route::post('document/uploadocument', 'DocumentController@uploadDocument')->name('document.uploadDocument');
    Route::post('document/addremark', 'DocumentController@addRemark')->name('document.addRemark');

    //Document Cateogry
    Route::post('documentcategory/add', 'DocuemntCategoryController@addCategory')->name('documentcategory.add');

    //SKU
    Route::get('sku-format/datatables', 'SkuFormatController@getData')->name('skuFormat.datatable');
    Route::get('sku-format/history', 'SkuFormatController@history')->name('skuFormat.history');
    Route::resource('sku-format', 'SkuFormatController');
    Route::post('sku-format/update', 'SkuFormatController@update')->name('sku.update');
    Route::get('sku/color-codes', 'SkuController@colorCodes')->name('sku.color-codes');
    Route::get('sku/color-codes-update', 'SkuController@colorCodesUpdate')->name('sku.color-codes-update');

    // Cash Flow Module
    Route::get('cashflow/{id}/download', 'CashFlowController@download')->name('cashflow.download');
    Route::get('cashflow/mastercashflow', 'CashFlowController@mastercashflow')->name('cashflow.mastercashflow');
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
    Route::get('vendors/product', 'VendorController@product')->name('vendors.product.index');
    Route::post('vendors/reply/add', 'VendorController@addReply')->name('vendors.reply.add');
    Route::get('vendors/reply/delete', 'VendorController@deleteReply')->name('vendors.reply.delete');
    Route::post('vendors/send/emailBulk', 'VendorController@sendEmailBulk')->name('vendors.email.send.bulk');
    Route::post('vendors/create-user', 'VendorController@createUser')->name('vendors.create.user');

    Route::post('vendors/send/message', 'VendorController@sendMessage')->name('vendors/send/message');
    Route::post('vendors/send/email', 'VendorController@sendEmail')->name('vendors.email.send');
    Route::get('vendors/email/inbox', 'VendorController@emailInbox')->name('vendors.email.inbox');
    Route::post('vendors/product', 'VendorController@productStore')->name('vendors.product.store');
    Route::put('vendors/product/{id}', 'VendorController@productUpdate')->name('vendors.product.update');
    Route::delete('vendors/product/{id}', 'VendorController@productDestroy')->name('vendors.product.destroy');
    Route::get('vendors/{vendor}/payments', 'VendorPaymentController@index')->name('vendors.payments');
    Route::post('vendors/{vendor}/payments', 'VendorPaymentController@store')->name('vendors.payments.store');
    Route::put('vendors/{vendor}/payments/{vendor_payment}', 'VendorPaymentController@update')->name('vendors.payments.update');
    Route::delete('vendors/{vendor}/payments/{vendor_payment}', 'VendorPaymentController@destroy')->name('vendors.payments.destroy');
    Route::resource('vendors', 'VendorController');
    Route::get('vendor-search', 'VendorController@vendorSearch')->name('vendor-search');
    Route::get('vendor-search-phone', 'VendorController@vendorSearchPhone')->name('vendor-search-phone');
    
    Route::post('vendors/email', 'VendorController@email')->name('vendors.email');
    Route::post('vendot/block', 'VendorController@block')->name('vendors.block');
    Route::post('vendors/inviteGithub', 'VendorController@inviteGithub');
    Route::post('vendors/inviteHubstaff', 'VendorController@inviteHubstaff');
    Route::post('vendors/change-status', 'VendorController@changeStatus');
    Route::get('vendor_category/assign-user', 'VendorController@assignUserToCategory');

    Route::prefix('hubstaff-payment')->group(function () {
        Route::get('/', 'HubstaffPaymentController@index')->name('hubstaff-payment.index');
        Route::get('records', 'HubstaffPaymentController@records')->name('hubstaff-payment.records');
        Route::post('save', 'HubstaffPaymentController@save')->name('hubstaff-payment.save');
        Route::post('merge-category', 'HubstaffPaymentController@mergeCategory')->name('hubstaff-payment.merge-category');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'HubstaffPaymentController@edit')->name('hubstaff-payment.edit');
            Route::get('delete', 'HubstaffPaymentController@delete')->name('hubstaff-payment.delete');
        });
    });

    Route::prefix('hubstaff-activities')->group(function () {

        Route::prefix('notification')->group(function () {
            Route::get('/', 'HubstaffActivitiesController@notification')->name('hubstaff-acitivties.notification.index');
            Route::get('/records', 'HubstaffActivitiesController@notificationRecords')->name('hubstaff-acitivties.notification.records');
            Route::post('/save', 'HubstaffActivitiesController@notificationReasonSave')->name('hubstaff-acitivties.notification.save-reason');
            Route::post('/change-status', 'HubstaffActivitiesController@changeStatus')->name('hubstaff-acitivties.notification.change-status');
        });
        Route::prefix('activities')->group(function () {
            Route::get('/', 'HubstaffActivitiesController@getActivityUsers')->name('hubstaff-acitivties.activities');
            Route::get('/details', 'HubstaffActivitiesController@getActivityDetails')->name('hubstaff-acitivties.activity-details');
            Route::post('/details', 'HubstaffActivitiesController@approveActivity')->name('hubstaff-acitivties.approve-activity');
            Route::post('/final-submit', 'HubstaffActivitiesController@finalSubmit')->name('hubstaff-activities/activities/final-submit');
            Route::post('/manual-record', 'HubstaffActivitiesController@submitManualRecords')->name('hubstaff-acitivties.manual-record');
            Route::get('/records', 'HubstaffActivitiesController@notificationRecords')->name('hubstaff-acitivties.notification.records');
            Route::post('/save', 'HubstaffActivitiesController@notificationReasonSave')->name('hubstaff-acitivties.notification.save-reason');
            Route::post('/change-status', 'HubstaffActivitiesController@changeStatus')->name('hubstaff-acitivties.notification.change-status');
            Route::get('/approved/pending-payments', 'HubstaffActivitiesController@approvedPendingPayments')->name('hubstaff-acitivties.pending-payments');
            Route::post('/approved/payment', 'HubstaffActivitiesController@submitPaymentRequest')->name("hubstaff-acitivties.payment-request.submit");


        });
        Route::post('save', 'HubstaffPaymentController@save')->name('hubstaff-payment.save');
        Route::post('merge-category', 'HubstaffPaymentController@mergeCategory')->name('hubstaff-payment.merge-category');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'HubstaffPaymentController@edit')->name('hubstaff-payment.edit');
            Route::get('delete', 'HubstaffPaymentController@delete')->name('hubstaff-payment.delete');
        });
    });

    Route::prefix('manage-modules')->group(function () {
        Route::get('/', 'ManageModulesController@index')->name('manage-modules.index');
        Route::get('records', 'ManageModulesController@records')->name('manage-modules.records');
        Route::post('save', 'ManageModulesController@save')->name('manage-modules.save');
        Route::post('merge-module', 'ManageModulesController@mergeModule')->name('manage-modules.merge-module');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'ManageModulesController@edit')->name('manage-modules.edit');
            Route::get('delete', 'ManageModulesController@delete')->name('manage-modules.delete');
        });
    });

    Route::prefix('manage-task-category')->group(function () {
        Route::get('/', 'ManageTaskCategoryController@index')->name('manage-task-category.index');
        Route::get('records', 'ManageTaskCategoryController@records')->name('manage-task-category.records');
        Route::post('save', 'ManageTaskCategoryController@save')->name('manage-task-category.save');
        Route::post('merge-module', 'ManageTaskCategoryController@mergeModule')->name('manage-task-category.merge-module');
        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'ManageTaskCategoryController@edit')->name('manage-task-category.edit');
            Route::get('delete', 'ManageTaskCategoryController@delete')->name('manage-task-category.delete');
        });
    });


    Route::prefix('vendor-category')->group(function () {
        Route::get('/', 'VendorCategoryController@index')->name('vendor-category.index');
        Route::get('records', 'VendorCategoryController@records')->name('vendor-category.records');
        Route::post('save', 'VendorCategoryController@save')->name('vendor-category.save');
        Route::post('merge-category', 'VendorCategoryController@mergeCategory')->name('vendor-category.merge-category');
        Route::get('/permission', 'VendorCategoryController@usersPermission')->name('vendor-category.permission');
        Route::post('/update/permission', 'VendorCategoryController@updatePermission')->name('vendor-category.update.permission');

        Route::prefix('{id}')->group(function () {
            Route::get('edit', 'VendorCategoryController@edit')->name('vendor-category.edit');
            Route::get('delete', 'VendorCategoryController@delete')->name('vendor-category.delete');
        });
    });

    Route::resource('vendor_category', 'VendorCategoryController');

    // Suppliers Module
    Route::get('supplier/categorycount', 'SupplierController@addSupplierCategoryCount')->name('supplier.count');
    Route::post('supplier/saveCategoryCount', 'SupplierController@saveSupplierCategoryCount')->name('supplier.count.save');
    Route::post('supplier/getCategoryCount', 'SupplierController@getSupplierCategoryCount')->name('supplier.count.get');
    Route::post('supplier/updateCategoryCount', 'SupplierController@updateSupplierCategoryCount')->name('supplier.count.update');
    Route::post('supplier/deleteCategoryCount', 'SupplierController@deleteSupplierCategoryCount')->name('supplier.count.delete');

    Route::get('supplier/brandcount', 'SupplierController@addSupplierBrandCount')->name('supplier.brand.count');
    Route::post('supplier/saveBrandCount', 'SupplierController@saveSupplierBrandCount')->name('supplier.brand.count.save');
    Route::post('supplier/getBrandCount', 'SupplierController@getSupplierBrandCount')->name('supplier.brand.count.get');
    Route::post('supplier/updateBrandCount', 'SupplierController@updateSupplierBrandCount')->name('supplier.brand.count.update');
    Route::post('supplier/deleteBrandCount', 'SupplierController@deleteSupplierBrandCount')->name('supplier.brand.count.delete');

    // Get supplier brands and raw brands
    Route::get('supplier/get-scraped-brands', 'SupplierController@getScrapedBrandAndBrandRaw')->name('supplier.scrapedbrands.list');
    // Update supplier brands and raw brands
    Route::post('supplier/update-scraped-brands', 'SupplierController@updateScrapedBrandFromBrandRaw')->name('supplier.scrapedbrands.update');
    // Remove particular scrap brand from scraped brands
    Route::post('supplier/remove-scraped-brands', 'SupplierController@removeScrapedBrand')->name('supplier.scrapedbrands.remove');
    // Copy scraped brands to brands
    Route::post('supplier/copy-scraped-brands', 'SupplierController@copyScrapedBrandToBrand')->name('supplier.scrapedbrands.copy');

    Route::post('supplier/update-brands', 'SupplierController@updateScrapedBrandFromBrandRaw')->name('supplier.brands.update');

    Route::post('supplier/send/emailBulk', 'SupplierController@sendEmailBulk')->name('supplier.email.send.bulk');

    Route::post('supplier/change-whatsapp-no', 'SupplierController@changeWhatsappNo')->name('supplier.change.whatsapp');

    Route::get('supplier/{id}/loadMoreMessages', 'SupplierController@loadMoreMessages');
    Route::post('supplier/flag', 'SupplierController@flag')->name('supplier.flag');
    Route::resource('supplier', 'SupplierController');
    Route::resource('google-server', 'GoogleServerController');
    Route::post('log-google-cse', 'GoogleServerController@logGoogleCse')->name('log.google.cse');
    Route::resource('email-addresses', 'EmailAddressesController');
    Route::post('supplier/block', 'SupplierController@block')->name('supplier.block');
    Route::post('supplier/saveImage', 'SupplierController@saveImage')->name('supplier.image');;
    Route::post('supplier/change-status', 'SupplierController@changeStatus');
    Route::post('supplier/change/category', 'SupplierController@changeCategory')->name('supplier/change/category');
    Route::post('supplier/change/status', 'SupplierController@changeSupplierStatus')->name('supplier/change/status');
    Route::post('supplier/change/subcategory', 'SupplierController@changeSubCategory')->name('supplier/change/subcategory');
    Route::post('supplier/add/category', 'SupplierController@addCategory')->name('supplier/add/category');
    Route::post('supplier/add/subcategory', 'SupplierController@addSubCategory')->name('supplier/add/subcategory');
    Route::post('supplier/add/status', 'SupplierController@addStatus')->name('supplier/add/status');
    Route::post('supplier/add/suppliersize', 'SupplierController@addSupplierSize')->name('supplier/add/suppliersize');
    Route::post('supplier/change/inventorylifetime', 'SupplierController@editInventorylifetime')->name('supplier/change/inventorylifetime');
    Route::post('supplier/change/scrapper', 'SupplierController@changeScrapper')->name('supplier/change/scrapper');
    Route::post('supplier/send/message', 'SupplierController@sendMessage')->name('supplier/send/message');
    Route::post('supplier/change/mail', 'SupplierController@changeMail')->name('supplier/change/mail');
    Route::post('supplier/change/phone', 'SupplierController@changePhone')->name('supplier/change/phone');
    Route::post('supplier/change/size', 'SupplierController@changeSize')->name('supplier/change/size');
    Route::post('supplier/change/whatsapp', 'SupplierController@changeWhatsapp')->name('supplier/change/whatsapp');
    // Supplier Category Permission
    Route::get('supplier/category/permission', 'SupplierCategoryController@usersPermission')->name('supplier/category/permission');
    Route::post('supplier/category/update/permission', 'SupplierCategoryController@updatePermission')->name('supplier/category/update/permission');


    Route::resource('assets-manager', 'AssetsManagerController');
    Route::post('assets-manager/add-note/{id}', 'AssetsManagerController@addNote');

    // Agent Routes
    Route::resource('agent', 'AgentController');
    //Route::resource('product-templates', 'ProductTemplatesController');

    Route::prefix('product-templates')->middleware('auth')->group(function () {
        Route::get('/', 'ProductTemplatesController@index')->name('product.templates');
        Route::post('/', 'ProductTemplatesController@index')->name('product.templates');
        Route::get('response', 'ProductTemplatesController@response');
        Route::post('create', 'ProductTemplatesController@create');
        Route::get('destroy/{id}', 'ProductTemplatesController@destroy');
        Route::get('select-product-id', 'ProductTemplatesController@selectProductId');
        Route::get('image', 'ProductTemplatesController@imageIndex');
    });

    Route::prefix('templates')->middleware('auth')->group(function () {
        Route::get('/', 'TemplatesController@index')->name('templates');
        Route::get('response', 'TemplatesController@response');
        Route::post('create', 'TemplatesController@create');
        Route::post('edit', 'TemplatesController@edit');
        Route::get('destroy/{id}', 'TemplatesController@destroy');
        Route::get('generate-template-category-branch', 'TemplatesController@generateTempalateCategoryBrand');
        Route::get('type', 'TemplatesController@typeIndex')->name('templates.type');
    });

    Route::prefix('erp-events')->middleware('auth')->group(function () {
        Route::get('/', 'ErpEventController@index')->name('erp-events');
        Route::post('/store', 'ErpEventController@store')->name('erp-events.store');
        Route::get('/dummy', 'ErpEventController@dummy')->name('erp-events.dummy');
    });
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
Route::post('twilio/storerecording', 'TwilioController@storeRecording');
Route::post('twilio/storetranscript', 'TwilioController@storetranscript');
Route::get(
    '/twilio/hangup',
    [
        'as' => 'hangup',
        'uses' => 'TwilioController@showHangup'
    ]
);

Route::get('exotel/outgoing', 'ExotelController@call')->name('exotel.call');
Route::get('exotel/checkNumber', 'ExotelController@checkNumber');
Route::post('exotel/recordingCallback', 'ExotelController@recordingCallback');

/* ---------------------------------------------------------------------------------- */

/* ------------------Twilio functionality Routes[PLEASE DONT MOVE INTO MIDDLEWARE AUTH] ------------------------ */

Route::post('livechat/incoming', 'LiveChatController@incoming');
Route::post('livechat/getChats', 'LiveChatController@getChats')->name('livechat.get.message');
Route::post('livechat/getChatsWithoutRefresh', 'LiveChatController@getChatMessagesWithoutRefresh')->name('livechat.message.withoutrefresh');
Route::post('livechat/sendMessage', 'LiveChatController@sendMessage')->name('livechat.send.message');
Route::post('livechat/sendFile', 'LiveChatController@sendFile')->name('livechat.send.file');
Route::post('livechat/getUserList', 'LiveChatController@getUserList')->name('livechat.get.userlist');
Route::post('livechat/save-token', 'LiveChatController@saveToken')->name('livechat.save.token');
Route::post('livechat/check-new-chat', 'LiveChatController@checkNewChat')->name('livechat.new.chat');

Route::get('livechat/getLiveChats', 'LiveChatController@getLiveChats')->name('livechat.get.chats');


Route::prefix('livechat')->group(function () {
    Route::post('/attach-image', 'LiveChatController@attachImage')->name('live-chat.attach.image');
});

/* ---------------------------------------------------------------------------------- */

Route::post('livechat/send-file', 'LiveChatController@sendFileToLiveChatInc')->name('livechat.upload.file');
Route::get('livechat/get-customer-info', 'LiveChatController@getLiveChatIncCustomer')->name('livechat.customer.info');
/*------------------------------------------- livechat tickets -------------------------------- */
Route::get('livechat/tickets', 'LiveChatController@tickets')->name('livechat.get.tickets');


Route::post('whatsapp/incoming', 'WhatsAppController@incomingMessage');
Route::post('whatsapp/incomingNew', 'WhatsAppController@incomingMessageNew');
Route::post('whatsapp/outgoingProcessed', 'WhatsAppController@outgoingProcessed');
Route::post('whatsapp/webhook', 'WhatsAppController@webhook');

Route::get('whatsapp/pullApiwha', 'WhatsAppController@pullApiwha');

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
Route::get('message/resend', 'WhatsAppController@resendMessage2');
Route::get('message/delete', 'WhatsAppController@delete');


Route::group(['middleware' => ['auth']], function () {
    Route::get('hubstaff/members', 'HubstaffController@index');
    Route::post('hubstaff/members/{id}/save-field', 'HubstaffController@saveMemberField');
    Route::post('hubstaff/linkuser', 'HubstaffController@linkUser');
    Route::get('hubstaff/projects', 'HubstaffController@getProjects');
    Route::post('hubstaff/projects/create', 'HubstaffController@createProject');
    Route::get('hubstaff/projects/{id}', 'HubstaffController@editProject');
    Route::put('hubstaff/projects/edit', 'HubstaffController@editProjectData');
    Route::get('hubstaff/tasks', 'HubstaffController@getTasks');
    Route::get('hubstaff/tasks/add', 'HubstaffController@addTaskFrom');
    Route::put('hubstaff/tasks/editData', 'HubstaffController@editTask');
    Route::post('hubstaff/tasks/addData', 'HubstaffController@addTask');
    Route::get('hubstaff/tasks/{id}', 'HubstaffController@editTaskForm');
    Route::get('hubstaff/redirect', 'HubstaffController@redirect');
    Route::get('hubstaff/debug', 'HubstaffController@debug');
    Route::get('hubstaff/payments', 'UserController@payments');
    Route::post('hubstaff/makePayment', 'UserController@makePayment');
});
/*
 * @date 1/13/2019
 * @author Rishabh Aryal
 * This is route for Instagram
 * feature in this ERP
 */

Route::middleware('auth')->group(function () {
    Route::get('cold-leads/delete', 'ColdLeadsController@deleteColdLead');
    Route::resource('cold-leads-broadcasts', 'ColdLeadBroadcastsController');
    Route::resource('cold-leads', 'ColdLeadsController');
});

Route::prefix('sitejabber')->middleware('auth')->group(function () {
    Route::post('sitejabber/attach-detach', 'SitejabberQAController@attachOrDetachReviews');
    Route::post('review/reply', 'SitejabberQAController@sendSitejabberQAReply');
    Route::get('review/{id}/confirm', 'SitejabberQAController@confirmReviewAsPosted');
    Route::get('review/{id}/delete', 'SitejabberQAController@detachBrandReviews');
    Route::get('review/{id}', 'SitejabberQAController@attachBrandReviews');
    Route::get('accounts', 'SitejabberQAController@accounts');
    Route::get('reviews', 'SitejabberQAController@reviews');
    Route::resource('qa', 'SitejabberQAController');
});

Route::prefix('pinterest')->middleware('auth')->group(function () {
    Route::resource('accounts', 'PinterestAccountAcontroller');
});

Route::prefix('database')->middleware('auth')->group(function () {
    Route::get('/', 'DatabaseController@index')->name("database.index");
    Route::get('/states', 'DatabaseController@states')->name("database.states");
    Route::get('/process-list', 'DatabaseController@processList')->name("database.process.list");
    Route::get('/process-kill', 'DatabaseController@processKill')->name("database.process.kill");

});

Route::resource('pre-accounts', 'PreAccountController')->middleware('auth');

Route::prefix('instagram')->middleware('auth')->group(function () {
    Route::get('auto-comment-history', 'UsersAutoCommentHistoriesController@index');
    Route::get('auto-comment-history/assign', 'UsersAutoCommentHistoriesController@assignPosts');
    Route::get('auto-comment-history/send-posts', 'UsersAutoCommentHistoriesController@sendMessagesToWhatsappToScrap');
    Route::get('auto-comment-history/verify', 'UsersAutoCommentHistoriesController@verifyComment');
    Route::post('store', 'InstagramController@store');
    Route::get('{id}/edit', 'InstagramController@edit');
    Route::put('update/{id}', 'InstagramController@update');
    Route::get('delete/{id}', 'InstagramController@deleteAccount');
    Route::resource('auto-comment-report', 'AutoCommentHistoryController');
    Route::resource('auto-comment-hashtags', 'AutoReplyHashtagsController');
    Route::get('flag/{id}', 'HashtagController@flagMedia');
    Route::get('thread/{id}', 'ColdLeadsController@getMessageThread');
    Route::post('thread/{id}', 'ColdLeadsController@sendMessage');
    Route::resource('brand-tagged', 'BrandTaggedPostsController');
    Route::resource('auto-comments', 'InstagramAutoCommentsController');
    Route::post('media/comment', 'HashtagController@commentOnHashtag');
    Route::get('test/{id}', 'AccountController@test');
    Route::get('start-growth/{id}', 'AccountController@startAccountGrowth');
    Route::get('accounts', 'InstagramController@accounts');
    Route::get('notification', 'HashtagController@showNotification');
    Route::get('hashtag/markPriority', 'HashtagController@markPriority')->name('hashtag.priority');
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
    Route::get('hashtag/grid/{id}', 'HashtagController@showGrid')->name('hashtag.grid');
    Route::get('users/grid/{id}', 'HashtagController@showUserGrid')->name('users.grid');
    Route::get('hashtag/comments/{id?}', 'HashtagController@showGridComments')->name('hashtag.grid');
    Route::get('hashtag/users/{id?}', 'HashtagController@showGridUsers')->name('hashtag.users.grid');
    Route::resource('hashtag', 'HashtagController');
    Route::post('hashtag/process/queue', 'HashtagController@rumCommand')->name('hashtag.command');
    Route::get('hashtags/grid', 'InstagramController@hashtagGrid');
    Route::get('influencers', 'HashtagController@influencer')->name('influencers.index');

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

    Route::get('direct-message','ColdLeadsController@home');

     // Media manager
    Route::get('media', 'MediaController@index')->name('media.index');
    Route::post('media', 'MediaController@upload')->name('media.upload');
    Route::get('media/files', 'MediaController@files')->name('media.files');
    Route::delete('media', 'MediaController@delete')->name('media.delete');

    //Add Post
    Route::get('post/create', 'InstagramPostsController@post')->name('instagram.post');
    Route::get('post', 'InstagramPostsController@viewPost')->name('post.index');
    Route::get('post/edit', 'InstagramPostsController@editPost')->name('post.edit');
    Route::post('post/create','InstagramPostsController@createPost')->name('post.store');

     Route::get('users', 'InstagramPostsController@users')->name('instagram.users');
     Route::post('users/save', 'InstagramController@addUserForPost')->name('instagram.users.add');
     Route::get('users/{id}', 'InstagramPostsController@userPost')->name('instagram.users.post');


     //direct message new 
     Route::get('direct', 'DirectMessageController@index')->name('direct.index');
     Route::post('direct/send', 'DirectMessageController@sendMessage')->name('direct.send');
     Route::post('direct/sendImage', 'DirectMessageController@sendImage')->name('direct.send.file');
     Route::post('direct/newChats', 'DirectMessageController@incomingPendingRead')->name('direct.new.chats');

     Route::post('direct/messages', 'DirectMessageController@messages')->name('direct.messages');

     

});

// logScraperVsAiController
Route::prefix('log-scraper-vs-ai')->middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/{id}', 'logScraperVsAiController@index');
});

Route::prefix('social-media')->middleware('auth')->group(function () {
    Route::get('/instagram-posts/grid', 'InstagramPostsController@grid');
    Route::get('/instagram-posts', 'InstagramPostsController@index');
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

Route::prefix('scrap')->middleware('auth')->group(function () {
    Route::get('statistics/update-field', 'ScrapStatisticsController@updateField');
    Route::get('statistics/update-scrap-field', 'ScrapStatisticsController@updateScrapperField');
    Route::get('statistics/show-history', 'ScrapStatisticsController@showHistory');
    Route::post('statistics/update-priority', 'ScrapStatisticsController@updatePriority');
    Route::get('statistics/history', 'ScrapStatisticsController@getHistory');
    Route::resource('statistics', 'ScrapStatisticsController');
    Route::get('getremark', 'ScrapStatisticsController@getRemark')->name('scrap.getremark');
    Route::get('latest-remark', 'ScrapStatisticsController@getLastRemark')->name('scrap.latest-remark');
    Route::post('addremark', 'ScrapStatisticsController@addRemark')->name('scrap.addRemark');
    Route::post('scrap/add/note', 'ScrapStatisticsController@addNote')->name('scrap/add/note');
    Route::get('facebook/inbox', 'FacebookController@getInbox');
    Route::resource('facebook', 'FacebookController');
    Route::get('gmails/{id}', 'GmailDataController@show');
    Route::resource('gmail', 'GmailDataController');
    Route::resource('designer', 'DesignerController');
    Route::resource('sales', 'SalesItemController');
    Route::get('/dubbizle', 'DubbizleController@index');
    Route::post('/dubbizle/set-reminder', 'DubbizleController@updateReminder');
    Route::post('/dubbizle/bulkWhatsapp', 'DubbizleController@bulkWhatsapp')->name('dubbizle.bulk.whatsapp');
    Route::get('/dubbizle/{id}/edit', 'DubbizleController@edit');
    Route::put('/dubbizle/{id}', 'DubbizleController@update');
    Route::get('/dubbizle/{id}', 'DubbizleController@show')->name('dubbizle.show');
    Route::get('/products', 'ScrapController@showProductStat');
    Route::get('/products/auto-rejected-stat', 'ProductController@showAutoRejectedProducts');
    Route::get('/activity', 'ScrapController@activity')->name('scrap.activity');
    Route::get('/excel', 'ScrapController@excel_import');
    Route::post('/excel', 'ScrapController@excel_store');
    Route::get('/google/images', 'ScrapController@index');
    Route::post('/google/images', 'ScrapController@scrapGoogleImages');
    Route::post('/google/images/download', 'ScrapController@downloadImages');
    Route::get('/scraped-urls', 'ScrapController@scrapedUrls');
    Route::get('/generic-scraper', 'ScrapController@genericScraper');
    Route::post('/generic-scraper/save', 'ScrapController@genericScraperSave')->name('generic.save.scraper');
    Route::post('/generic-scraper/full-scrape', 'ScrapController@scraperFullScrape')->name('generic.full-scrape');
    Route::get('/generic-scraper/mapping/{id}', 'ScrapController@genericMapping')->name('generic.mapping');
    Route::post('/generic-scraper/mapping/save', 'ScrapController@genericMappingSave')->name('generic.mapping.save');
    Route::post('/generic-scraper/mapping/delete', 'ScrapController@genericMappingDelete')->name('generic.mapping.delete');

    Route::post('/scraper/saveChildScraper', 'ScrapController@saveChildScraper')->name('save.childrenScraper');
    Route::get('/server-statistics', 'ScrapStatisticsController@serverStatistics')->name('scrap.scrap_server_status');
    Route::get('/server-statistics/history/{scrap_name}', 'ScrapStatisticsController@serverStatisticsHistory')->name('scrap.scrap_server_history');
    Route::get('/{name}', 'ScrapController@showProducts')->name('show.logFile');
});

Route::resource('quick-reply', 'QuickReplyController');
Route::resource('social-tags', 'SocialTagsController')->middleware('auth');


Route::get('test', 'WhatsAppController@getAllMessages');

Route::resource('track', 'UserActionsController');
Route::get('competitor-page/hide/{id}', 'CompetitorPageController@hideLead');
Route::get('competitor-page/approve/{id}', 'CompetitorPageController@approveLead');
Route::resource('competitor-page', 'CompetitorPageController');
Route::resource('target-location', 'TargetLocationController');

//Legal Module
Route::middleware('auth')->group(function () {
    Route::post('lawyer-speciality', ['uses' => 'LawyerController@storeSpeciality', 'as' => 'lawyer.speciality.store']);
    Route::resource('lawyer', 'LawyerController');
    Route::get('case/{case}/receivable', 'CaseReceivableController@index')->name('case.receivable');
    Route::post('case/{case}/receivable', 'CaseReceivableController@store')->name('case.receivable.store');
    Route::put('case/{case}/receivable/{case_receivable}', 'CaseReceivableController@update')->name('case.receivable.update');
    Route::delete('case/{case}/receivable/{case_receivable}', 'CaseReceivableController@destroy')->name('case.receivable.destroy');
    Route::resource('case', 'CaseController');
    Route::get('case-costs/{case}', ['uses' => 'CaseController@getCosts', 'as' => 'case.cost']);
    Route::post('case-costs', ['uses' => 'CaseController@costStore', 'as' => 'case.cost.post']);
    Route::put('case-costs/update/{case_cost}', ['uses' => 'CaseController@costUpdate', 'as' => 'case.cost.update']);
});

Route::middleware('auth')->resource('keyword-instruction', 'KeywordInstructionController')->except(['create']);

Route::prefix('/seo')->name('seo.')->group(function () {
    Route::get('/analytics', 'SEOAnalyticsController@show')->name('analytics');
    Route::get('/analytics/filter', 'SEOAnalyticsController@filter')->name('analytics.filter');
    Route::post('/analytics/filter', 'SEOAnalyticsController@filter')->name('analytics.filter');
    Route::post('/analytics/delete/{id}', 'SEOAnalyticsController@delete')->name('delete_entry');
});

Route::get('display/broken-link-details', 'BrokenLinkCheckerController@displayBrokenLinkDetails')->name('brokenLinks');
Route::get('display/broken-link-details', 'BrokenLinkCheckerController@displayBrokenLinkDetails')->name('filteredResults');

Route::middleware('auth')->group(function () {
    Route::get('display/broken-link-details', 'BrokenLinkCheckerController@displayBrokenLinkDetails')->name('filteredResults');

    Route::get('old-incomings', 'OldIncomingController@index')->name('oldIncomings');
    Route::get('old-incomings', 'OldIncomingController@index')->name('filteredOldIncomings');
    Route::post('store/old-incomings', 'OldIncomingController@store')->name('storeOldIncomings');
    Route::get('edit/old-incomings/{id}', 'OldIncomingController@edit')->name('editOldIncomings');
    Route::post('update/old-incomings/{id}', 'OldIncomingController@update')->name('updateOldIncomings');

    // Old Module
    Route::post('old/send/emailBulk', 'OldController@sendEmailBulk')->name('old.email.send.bulk');
    Route::post('old/send/email', 'OldController@sendEmail')->name('old.email.send');
    Route::get('old/gettaskremark', 'OldController@getTaskRemark')->name('old.gettaskremark');
    Route::post('old/addremark', 'OldController@addRemark')->name('old.addRemark');
    Route::get('old/email/inbox', 'OldController@emailInbox')->name('old.email.inbox');
    Route::get('old/{old}/payments', 'OldController@paymentindex')->name('old.payments');
    Route::post('old/{old}/payments', 'OldController@paymentStore')->name('old.payments.store');
    Route::put('old/{old}/payments/{old_payment}', 'OldController@paymentUpdate')->name('old.payments.update');
    Route::delete('old/{old}/payments/{old_payment}', 'OldController@paymentDestroy')->name('old.payments.destroy');
    Route::resource('old', 'OldController');
    Route::post('old/block', 'OldController@block')->name('old.block');
    Route::post('old/category/create', 'OldController@createCategory')->name('old.category.create');
    Route::post('old/update/status', 'OldController@updateOld')->name('old.update.status');

    //Simple Duty

    //Simple duty category
    Route::get('duty/category', 'SimplyDutyCategoryController@index')->name('simplyduty.category.index');
    Route::get('duty/category/update', 'SimplyDutyCategoryController@getCategoryFromApi')->name('simplyduty.category.update');

    Route::get('duty/hscode', 'HsCodeController@index')->name('simplyduty.hscode.index');

    Route::post('duty/setting', 'HsCodeController@saveKey')->name('simplyduty.hscode.key');


    //Simple Duty Currency
    Route::get('duty/currency', 'SimplyDutyCurrencyController@index')->name('simplyduty.currency.index');
    Route::get('duty/currency/update', 'SimplyDutyCurrencyController@getCurrencyFromApi')->name('simplyduty.currency.update');

    //Simple Duty Country
    Route::get('duty/country', 'SimplyDutyCountryController@index')->name('simplyduty.country.index');
    Route::get('duty/country/update', 'SimplyDutyCountryController@getCountryFromApi')->name('simplyduty.country.update');

    //Simple Duty Calculation
    Route::get('duty/calculation', 'SimplyDutyCalculationController@index')->name('simplyduty.calculation.index');
    Route::post('duty/calculation', 'SimplyDutyCalculationController@calculation')->name('simplyduty.calculation');

    //Simply Duty Common
    Route::get('hscode/most-common', 'HsCodeController@mostCommon')->name('hscode.mostcommon.index');

    //Simply Duty Common
    Route::get('hscode/most-common-category', 'HsCodeController@mostCommonByCategory')->name('hscode.mostcommon.category');

    Route::get('display/analytics-data', 'AnalyticsController@showData')->name('showAnalytics');

    Route::get('display/back-link-details', 'BackLinkController@displayBackLinkDetails')->name('backLinkFilteredResults');
    Route::get('links-to-post', 'SEOAnalyticsController@linksToPost');

    Route::prefix('country-duty')->group(function () {
        Route::get('/', 'CountryDutyController@index')->name('country.duty.index');
        Route::post('/search', 'CountryDutyController@search')->name('country.duty.search');
        Route::post('/save-country-group', 'CountryDutyController@saveCountryGroup')->name('country.duty.search');
        Route::prefix('list')->group(function () {
            Route::get('/', 'CountryDutyController@list')->name('country.duty.list');
            Route::get('/records', 'CountryDutyController@records')->name('country.duty.records');
            Route::post('save', 'CountryDutyController@store')->name('country.duty.save');
            Route::post('update-group-field', 'CountryDutyController@updateGroupField')->name('country.duty.update-group-field');
            Route::prefix('{id}')->group(function () {
                Route::get('edit', 'CountryDutyController@edit')->name('country.duty.edit');
                Route::get('delete', 'CountryDutyController@delete')->name('country.duty.delete');
            });
        });
    });
});

//Blogger Module
Route::middleware('auth')->group(function () {

    Route::get('blogger-email', ['uses' => 'BloggerEmailTemplateController@index', 'as' => 'blogger.email.template']);
    Route::put('blogger-email/{bloggerEmailTemplate}', ['uses' => 'BloggerEmailTemplateController@update', 'as' => 'blogger.email.template.update']);

    Route::get('blogger/{blogger}/payments', 'BloggerPaymentController@index')->name('blogger.payments');
    Route::post('blogger/{blogger}/payments', 'BloggerPaymentController@store')->name('blogger.payments.store');
    Route::put('blogger/{blogger}/payments/{blogger_payment}', 'BloggerPaymentController@update')->name('blogger.payments.update');
    Route::delete('blogger/{blogger}/payments/{blogger_payment}', 'BloggerPaymentController@destroy')->name('blogger.payments.destroy');

    Route::resource('blogger', 'BloggerController');

    Route::post('blogger-contact', ['uses' => 'ContactBloggerController@store', 'as' => 'blogger.contact.store']);
    Route::put('blogger-contact/{contact_blogger}', ['uses' => 'ContactBloggerController@update', 'as' => 'blogger.contact.update']);
    Route::delete('blogger-contact/{contact_blogger}', ['uses' => 'ContactBloggerController@destroy', 'as' => 'contact.blogger.destroy']);


    Route::get('display/back-link-details', 'BackLinkController@displayBackLinkDetails')->name('backLinks');
    Route::get('display/back-link-details', 'BackLinkController@displayBackLinkDetails')->name('backLinkFilteredResults');
    Route::post('blogger-product-image/{blogger_product}', ['uses' => 'BloggerProductController@uploadImages', 'as' => 'blogger.image.upload']);
    Route::get('blogger-product-get-image/{blogger_product}', ['uses' => 'BloggerProductController@getImages', 'as' => 'blogger.image']);
    Route::resource('blogger-product', 'BloggerProductController');
});


//Monetary Account Module
Route::middleware('auth')->group(function () {
    Route::resource('monetary-account', 'MonetaryAccountController');
});

// Mailchimp Module
Route::group(['middleware' => 'auth', 'namespace' => 'Mail'], function () {
    Route::get('manageMailChimp', 'MailchimpController@manageMailChimp')->name('manage.mailchimp');
    Route::post('subscribe', ['as' => 'subscribe', 'uses' => 'MailchimpController@subscribe']);
    Route::post('sendCompaign', ['as' => 'sendCompaign', 'uses' => 'MailchimpController@sendCompaign']);
    Route::get('make-active-subscribers', 'MailchimpController@makeActiveSubscriber')->name('make.active.subscriber');
});


Route::group(['middleware' => 'auth', 'namespace' => 'marketing'], function () {
    Route::get('test', function () {
        return 'hello';
    });
});

//Hubstaff Module
Route::group(['middleware' => 'auth', 'namespace' => 'Hubstaff'], function () {

    Route::get('v1/auth', 'HubstaffController@authenticationPage')->name('get.token');

    Route::post('user-details-token', 'HubstaffController@getToken')->name('user.token');

    Route::get('get-users', 'HubstaffController@gettingUsersPage')->name('get.users');

    Route::post('v1/users', 'HubstaffController@userDetails')->name('get.users.api');

    Route::get('get-user-from-id', 'HubstaffController@showFormUserById')->name('get.user-fromid');

    Route::post('get-user-from-id', 'HubstaffController@getUserById')->name('post.user-fromid');

    Route::get('v1/users/projects', 'HubstaffController@getProjectPage')->name('get.user-project-page');

    Route::post('v1/users/projects', 'HubstaffController@getProjects')->name('post.user-project-page');

    // ------------Projects---------------

    Route::get('get-projects', 'HubstaffController@getUserProject')->name('user.project');
    Route::post('get-projects', 'HubstaffController@postUserProject')->name('post.user-project');

    // --------------Tasks---------------

    Route::get('get-project-tasks', 'HubstaffController@getProjectTask')->name('project.task');
    Route::post('get-project-taks', 'HubstaffController@postProjectTask')->name('post.project-task');


    Route::get('v1/tasks', 'HubstaffController@getTaskFromId')->name('get-project.task-from-id');

    Route::post('v1/tasks', 'HubstaffController@postTaskFromId')->name('post-project.task-from-id');

    // --------------Organizaitons--------------
    Route::get('v1/organizations', 'HubstaffController@index')->name('organizations');
    Route::post('v1/organizations', 'HubstaffController@getOrganization')->name('post.organizations');


    // -------v2 preview verion post requests----------
    //    Route::get('v2/organizations/projects', 'HubstaffProjectController@getProject');
    //    Route::post('v2/organizations/projects', 'HubstaffProjectController@postProject');


    Route::get('v1/organization/members', 'HubstaffController@organizationMemberPage')->name('organization.members');
    Route::post('v1/organization/members', 'HubstaffController@showMembers')->name('post.organization-member');

    // --------------Screenshots--------------

    Route::get('v1/screenshots', 'HubstaffController@getScreenshotPage')->name('get.screenshots');

    Route::post('v1/screenshots', 'HubstaffController@postScreenshots')->name('post.screenshot');

    // -------------payments----------------

    Route::get('v1/team_payments', 'HubstaffController@getTeamPaymentPage')->name('team.payments');
    Route::post('v1/team_payments', 'HubstaffController@getPaymentDetail')->name('post.payment-page');


    // ------------Attendance---------------
    Route::get('v2/organizations/attendance-shifts', 'AttendanceController@index')->name('attendance.shifts');

    Route::post('v2/organizations/attendance-shifts', 'AttendanceController@show')->name('attendance.shifts-post');
});
Route::get('display/analytics-data', 'AnalyticsController@showData')->name('showAnalytics');
Route::get('display/analytics-data', 'AnalyticsController@showData')->name('filteredAnalyticsResults');
Route::get('display/analytics-summary', 'AnalyticsController@analyticsDataSummary')->name('analyticsDataSummary');
Route::get('display/analytics-summary', 'AnalyticsController@analyticsDataSummary')->name('filteredAnalyticsSummary');
Route::get('display/analytics-customer-behaviour', 'AnalyticsController@customerBehaviourByPage')->name('customerBehaviourByPage');
Route::get('display/analytics-customer-behaviour', 'AnalyticsController@customerBehaviourByPage')->name('filteredcustomerBehaviourByPage');

// Broken Links
Route::post('back-link/{id}/updateDomain', 'BrokenLinkCheckerController@updateDomain');
Route::post('back-link/{id}/updateTitle', 'BrokenLinkCheckerController@updateTitle');

// Article Links
Route::get('display/articles', 'ArticleController@index')->name('articleApproval');
Route::post('article/{id}/updateTitle', 'ArticleController@updateTitle');
Route::post('article/{id}/updateDescription', 'ArticleController@updateDescription');

//Back Linking
Route::post('back-linking/{id}/updateTitle', 'BackLinkController@updateTitle');
Route::post('back-linking/{id}/updateDesc', 'BackLinkController@updateDesc');
Route::post('back-linking/{id}/updateURL', 'BackLinkController@updateURL');

//SE Ranking Links
Route::get('se-ranking/sites', 'SERankingController@getSites')->name('getSites');
Route::get('se-ranking/keywords', 'SERankingController@getKeyWords')->name('getKeyWords');
Route::get('se-ranking/keywords', 'SERankingController@getKeyWords')->name('filteredSERankKeywords');
Route::get('se-ranking/competitors', 'SERankingController@getCompetitors')->name('getCompetitors');
Route::get('se-ranking/analytics', 'SERankingController@getAnalytics')->name('getAnalytics');
Route::get('se-ranking/backlinks', 'SERankingController@getBacklinks')->name('getBacklinks');
Route::get('se-ranking/research-data', 'SERankingController@getResearchData')->name('getResearchData');
Route::get('se-ranking/audit', 'SERankingController@getSiteAudit')->name('getSiteAudit');
Route::get('se-ranking/competitors/keyword-positions/{id}', 'SERankingController@getCompetitors')->name('getCompetitorsKeywordPos');
//Dev Task Planner Route
Route::get('dev-task-planner', 'NewDevTaskController@index')->name('newDevTaskPlanner');
Route::get('dev-task-planner', 'NewDevTaskController@index')->name('filteredNewDevTaskPlanner');
//Supplier scrapping info
Route::get('supplier-scrapping-info', 'ProductController@getSupplierScrappingInfo')->name('getSupplierScrappingInfo');

Route::group(['middleware' => 'auth', 'admin'], function () {
    Route::get('category/brand/min-max-pricing', 'CategoryController@brandMinMaxPricing');
    Route::post('category/brand/update-min-max-pricing', 'CategoryController@updateBrandMinMaxPricing');
});

// pages notes started from here
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('page-notes')->group(function () {
        Route::post('create', 'PageNotesController@create')->name('createPageNote');
        Route::get('list', 'PageNotesController@list')->name('listPageNote');
        Route::get('edit', 'PageNotesController@edit')->name('editPageNote');
        Route::post('update', 'PageNotesController@update')->name('updatePageNote');
        Route::get('delete', 'PageNotesController@delete')->name('deletePageNote');
        Route::get('records', 'PageNotesController@records')->name('pageNotesRecords');
        Route::get('/', 'PageNotesController@index')->name('pageNotes.viewList');
    });
    Route::prefix('instruction-notes')->group(function () {
        Route::post('create', 'PageNotesController@instructionCreate')->name('instructionCreate');
    });
});

Route::group(['middleware' => 'auth', 'namespace' => 'Marketing', 'prefix' => 'marketing'], function () {
    // Whats App Config
    Route::get('whatsapp-config', 'WhatsappConfigController@index')->name('whatsapp.config.index');
    Route::get('whatsapp-history/{id}', 'WhatsappConfigController@history')->name('whatsapp.config.history');
    Route::post('whatsapp-config/store', 'WhatsappConfigController@store')->name('whatsapp.config.store');
    Route::post('whatsapp-config/edit', 'WhatsappConfigController@edit')->name('whatsapp.config.edit');
    Route::post('whatsapp-config/delete', 'WhatsappConfigController@destroy')->name('whatsapp.config.delete');
    Route::get('whatsapp-queue/{id}', 'WhatsappConfigController@queue')->name('whatsapp.config.queue');
    Route::post('whatsapp-queue/delete', 'WhatsappConfigController@destroyQueue')->name('whatsapp.config.delete_queue');
    Route::post('whatsapp-queue/delete_all/', 'WhatsappConfigController@destroyQueueAll')->name('whatsapp.config.delete_all');
    Route::get('whatsapp-queue/delete_queues/{id}', 'WhatsappConfigController@clearMessagesQueue')->name('whatsapp.config.delete_all_queues');
    Route::get('whatsapp-config/get-barcode', 'WhatsappConfigController@getBarcode')->name('whatsapp.config.barcode');
    Route::get('whatsapp-config/get-screen', 'WhatsappConfigController@getScreen')->name('whatsapp.config.screen');
    Route::get('whatsapp-config/delete-chrome', 'WhatsappConfigController@deleteChromeData')->name('whatsapp.config.delete');
    Route::get('whatsapp-config/restart-script', 'WhatsappConfigController@restartScript')->name('whatsapp.restart.script');
    Route::get('whatsapp-config/blocked-number', 'WhatsappConfigController@blockedNumber')->name('whatsapp.block.number');

    Route::post('whatsapp-queue/switchBroadcast', 'BroadcastController@switchBroadcast')->name('whatsapp.config.switchBroadcast');

    //Instagram Config

    // Whats App Config
    Route::get('instagram-config', 'InstagramConfigController@index')->name('instagram.config.index');
    Route::get('instagram-history/{id}', 'InstagramConfigController@history')->name('instagram.config.history');
    Route::post('instagram-config/store', 'InstagramConfigController@store')->name('instagram.config.store');
    Route::post('instagram-config/edit', 'InstagramConfigController@edit')->name('instagram.config.edit');
    Route::post('instagram-config/delete', 'InstagramConfigController@destroy')->name('instagram.config.delete');
    Route::get('instagram-queue/{id}', 'InstagramConfigController@queue')->name('instagram.config.queue');
    Route::post('instagram-queue/delete', 'InstagramConfigController@destroyQueue')->name('instagram.config.delete_queue');
    Route::post('instagram-queue/delete_all/', 'InstagramConfigController@destroyQueueAll')->name('instagram.config.delete_all');

    //Social Config
    Route::get('accounts/{type?}', 'AccountController@index')->name('accounts.index');
    Route::post('accounts', 'AccountController@store')->name('accounts.store');
    Route::post('accounts/edit', 'AccountController@edit')->name('accounts.edit');
    Route::post('accounts/broadcast', 'AccountController@broadcast')->name('accounts.broadcast');



    Route::get('instagram-queue/delete_queues/{id}', 'InstagramConfigController@clearMessagesQueue')->name('instagram.config.delete_all_queues');
    Route::get('instagram-config/get-barcode', 'InstagramConfigController@getBarcode')->name('instagram.config.barcode');
    Route::get('instagram-config/get-screen', 'InstagramConfigController@getScreen')->name('instagram.config.screen');
    Route::get('instagram-config/delete-chrome', 'InstagramConfigController@deleteChromeData')->name('instagram.config.delete');
    Route::get('instagram-config/restart-script', 'InstagramConfigController@restartScript')->name('instagram.restart.script');
    Route::get('instagram-config/blocked-number', 'InstagramConfigController@blockedNumber')->name('instagram.block.number');


    // Route::post('whatsapp-queue/switchBroadcast', 'BroadcastController@switchBroadcast')->name('whatsapp.config.switchBroadcast');

    // Marketing Platform
    Route::get('platforms', 'MarketingPlatformController@index')->name('platforms.index');
    Route::post('platforms/store', 'MarketingPlatformController@store')->name('platforms.store');
    Route::post('platforms/edit', 'MarketingPlatformController@edit')->name('platforms.edit');
    Route::post('platforms/delete', 'MarketingPlatformController@destroy')->name('platforms.delete');

    Route::get('broadcast', 'BroadcastController@index')->name('broadcasts.index');
    Route::get('broadcast/dnd', 'BroadcastController@addToDND')->name('broadcast.add.dnd');
    Route::get('broadcast/gettaskremark', 'BroadcastController@getBroadCastRemark')->name('broadcast.gets.remark');
    Route::post('broadcast/addremark', 'BroadcastController@addRemark')->name('broadcast.add.remark');
    Route::get('broadcast/manual', 'BroadcastController@addManual')->name('broadcast.add.manual');
    Route::post('broadcast/update', 'BroadcastController@updateWhatsAppNumber')->name('broadcast.update.whatsappnumber');
    Route::get('broadcast/sendMessage/list', 'BroadcastController@broadCastSendMessage')->name('broadcast.message.send.list');
    Route::post('broadcast/customer/list', 'BroadcastController@getCustomerBroadcastList')->name('broadcast.customer.list');
    Route::post('broadcast/global/save', 'BroadcastController@saveGlobalValues')->name('broadcast.global.save');
    Route::post('broadcast/enable/count', 'BroadcastController@getCustomerCountEnable')->name('broadcast.enable.count');
    Route::get('broadcast/sendMessage/list', 'BroadcastController@broadCastSendMessage')->name('broadcast.message.send.list');
    Route::post('broadcast/customer/list', 'BroadcastController@getCustomerBroadcastList')->name('broadcast.customer.list');
    Route::post('broadcast/global/save', 'BroadcastController@saveGlobalValues')->name('broadcast.global.save');
    Route::post('broadcast/enable/count', 'BroadcastController@getCustomerCountEnable')->name('broadcast.enable.count');

    Route::get('instagram-broadcast','BroadcastController@instagram');

    Route::get('facebook-broadcast','BroadcastController@facebook');

    Route::get('mailinglist', 'MailinglistController@index')->name('mailingList');
    Route::get('mailinglist/{id}', 'MailinglistController@show')->name('mailingList.single');
    Route::get('mailinglist/add/{id}/{email}', 'MailinglistController@addToList')->name('mailingList.add_to_list');
    Route::get('mailinglist/delete/{id}/{email}', 'MailinglistController@delete')->name('mailingList.delete');
    Route::get('mailinglist/list/delete/{id}', 'MailinglistController@deleteList')->name('mailingList.delete.list');
    Route::post('mailinglist-create', 'MailinglistController@create')->name('mailingList.create');
    Route::get('mailinglist-add-manual', 'MailinglistController@addManual')->name('mailinglist.add.manual');
    Route::post('addRemark', 'MailinglistController@addRemark')->name('mailingList.addRemark');
    Route::get('gettaskremark', 'MailinglistController@getBroadCastRemark')->name('mailingList.gets.remark');


    Route::get('services', 'ServiceController@index')->name('services');
    Route::post('services/store', 'ServiceController@store')->name('services.store');
    Route::post('services/destroy', 'ServiceController@destroy')->name('services.destroy');
    Route::post('services/update', 'ServiceController@update')->name('services.update');

    Route::get('mailinglist-templates', 'MailinglistTemplateController@index')->name('mailingList-template');
    Route::get('mailinglist-ajax', 'MailinglistTemplateController@ajax');
    Route::post('mailinglist-templates/store', 'MailinglistTemplateController@store')->name('mailingList-template.store');

    Route::group(['prefix' => 'mailinglist-templates/{id}'], function () {
        Route::get('delete', 'MailinglistTemplateController@delete')->name('mailingList-template.delete');
    });


    Route::get('mailinglist-emails', 'MailinglistEmailController@index')->name('mailingList-emails');
    Route::post('mailinglist-ajax-index', 'MailinglistEmailController@ajaxIndex');
    Route::post('mailinglist-ajax-store', 'MailinglistEmailController@store');
    Route::post('mailinglist-ajax-show', 'MailinglistEmailController@show');
    Route::post('mailinglist-ajax-duplicate', 'MailinglistEmailController@duplicate');
    Route::post('mailinglist-stats', 'MailinglistEmailController@getStats');
});

Route::group(['middleware' => 'auth', 'prefix' => 'checkout'], function () {
    Route::post('coupons/store', 'CouponController@store')->name('coupons.store');
    Route::post('coupons/{id}', 'CouponController@update');
    Route::get('coupons', 'CouponController@index')->name('coupons.index');
    Route::post('coupons/load', 'CouponController@loadData');
    Route::get('coupons/load', 'CouponController@loadData');
    Route::delete('coupons/{id}', 'CouponController@destroy');
    Route::get('coupons/{id}/report', 'CouponController@showReport');
    Route::get('coupons/report', 'CouponController@showReport');
});

Route::get('keywordassign', 'KeywordassignController@index')->name('keywordassign.index');
Route::get('keywordassign/load', 'KeywordassignController@loadData');
Route::get('keywordassign/create', 'KeywordassignController@create')->name('keywordassign.create');
Route::post('keywordassign/store', 'KeywordassignController@store')->name('keywordassign.store');
Route::post('keywordassign/taskcategory', 'KeywordassignController@taskcategory')->name('keywordassign.taskcategory');
Route::get('keywordassign/{id}', 'KeywordassignController@edit');
Route::post('keywordassign/{id}/update', 'KeywordassignController@update');
Route::get('keywordassign/{id}/destroy', 'KeywordassignController@destroy');



Route::post('attachImages/queue', 'ProductController@queueCustomerAttachImages')->name('attachImages.queue');
Route::group(['middleware' => 'auth'], function () {
    Route::prefix('tmp-task')->group(function () {
        Route::get('import-leads', 'TmpTaskController@importLeads')->name('importLeads');
    });
    // this is temp action
    Route::get('update-purchase-order-product', 'PurchaseController@syncOrderProductId');
    Route::get('update-media-directory', 'TmpController@updateImageDirectory');
    Route::resource('page-notes-categories', 'PageNotesCategoriesController');
});


Route::prefix('chat-bot')->middleware('auth')->group(function () {
    Route::get('/connection', 'ChatBotController@connection');
});

Route::get('scrap-logs', 'ScrapLogsController@index');
Route::get('scrap-logs/{name}', 'ScrapLogsController@indexByName');
Route::get('scrap-logs/fetch/{name}/{date}', 'ScrapLogsController@filter');
Route::get('fetchlog', 'ScrapLogsController@fetchlog');
Route::get('filtertosavelogdb', 'ScrapLogsController@filtertosavelogdb');
Route::get('scrap-logs/file-view/{filename}/{foldername}', 'ScrapLogsController@fileView');
Route::put('supplier/language-translate/{id}', 'SupplierController@languageTranslate');
Route::get('temp-task/product-creator', 'TmpTaskController@importProduct');

Route::prefix('google')->middleware('auth')->group(function () {
    Route::resource('/search/keyword', 'GoogleSearchController');
    Route::get('/search/keyword-priority', 'GoogleSearchController@markPriority')->name('google.search.keyword.priority');
    Route::get('/search/keyword', 'GoogleSearchController@index')->name('google.search.keyword');
    Route::get('/search/results', 'GoogleSearchController@searchResults')->name('google.search.results');
    Route::get('/search/scrap', 'GoogleSearchController@callScraper')->name('google.search.keyword.scrap');

    Route::resource('/affiliate/keyword', 'GoogleAffiliateController');
    Route::get('/affiliate/keyword', 'GoogleAffiliateController@index')->name('google.affiliate.keyword');
    Route::get('/affiliate/keyword-priority', 'GoogleAffiliateController@markPriority')->name('google.affiliate.keyword.priority');
    Route::get('/affiliate/results', 'GoogleAffiliateController@searchResults')->name('google.affiliate.results');
    Route::delete('/affiliate/results/{id}', 'GoogleAffiliateController@deleteSearch');
    Route::delete('/search/results/{id}', 'GoogleSearchController@deleteSearch');
    Route::post('affiliate/flag', 'GoogleAffiliateController@flag')->name('affiliate.flag');
    Route::post('affiliate/email/send', 'GoogleAffiliateController@emailSend')->name('affiliate.email.send');
    Route::get('/affiliate/scrap', 'GoogleAffiliateController@callScraper')->name('google.affiliate.keyword.scrap');
});

Route::get('/jobs', 'JobController@index')->middleware('auth')->name('jobs.list');
Route::get('/jobs/{id}/delete', 'JobController@delete')->middleware('auth')->name('jobs.delete');
Route::post('/jobs/delete-multiple', 'JobController@deleteMultiple')->middleware('auth')->name('jobs.delete.multiple');

Route::get('/wetransfer-queue', 'WeTransferController@index')->middleware('auth')->name('wetransfer.list');

Route::post('/supplier/manage-scrap-brands', 'SupplierController@manageScrapedBrands')->name('manageScrapedBrands');

Route::group(['middleware' => ['auth', 'role_or_permission:Admin|deployer']], function () {
    Route::prefix('github')->group(function () {
        Route::get('/repos', 'Github\RepositoryController@listRepositories');
        Route::get('/repos/{name}/users', 'Github\UserController@listUsersOfRepository');
        Route::get('/repos/{name}/users/add', 'Github\UserController@addUserToRepositoryForm');
        Route::get('/repos/{id}/branches', 'Github\RepositoryController@getRepositoryDetails');
        Route::get('/repos/{id}/pull-request', 'Github\RepositoryController@listPullRequests');
        Route::get('/repos/{id}/branch/merge', 'Github\RepositoryController@mergeBranch');
        Route::get('/repos/{id}/deploy', 'Github\RepositoryController@deployBranch');
        Route::post('/add_user_to_repo', 'Github\UserController@addUserToRepository');
        Route::get('/users', 'Github\UserController@listOrganizationUsers');
        Route::get('/users/{userId}', 'Github\UserController@userDetails');
        Route::get('/groups', 'Github\GroupController@listGroups');
        Route::post('/groups/users/add', 'Github\GroupController@addUser');
        Route::post('/groups/repositories/add', 'Github\GroupController@addRepository');
        Route::get('/groups/{groupId}', 'Github\GroupController@groupDetails');
        Route::get('/groups/{groupId}/repos/{repoId}/remove', 'Github\GroupController@removeRepositoryFromGroup');
        Route::get('/groups/{groupId}/users/{userId}/remove', 'Github\GroupController@removeUsersFromGroup');
        Route::get('/groups/{groupId}/users/add', 'Github\GroupController@addUserForm');
        Route::get('/groups/{groupId}/repositories/add', 'Github\GroupController@addRepositoryForm');
        Route::get('/sync', 'Github\SyncController@index');
        Route::get('/sync/start', 'Github\SyncController@startSync');
        Route::get('/repo_user_access/{id}/remove', 'Github\UserController@removeUserFromRepository');
        Route::post('/linkUser', 'Github\UserController@linkUser');
        Route::post('/modifyUserAccess', 'Github\UserController@modifyUserAccess');
        Route::get('/pullRequests', 'Github\RepositoryController@listAllPullRequests');
    });
});

Route::group(['middleware' => ['auth', 'role_or_permission:Admin|deployer']], function () {
    Route::get('/deploy-node', 'Github\RepositoryController@deployNodeScrapers');
});


Route::put('customer/language-translate/{id}', 'CustomerController@languageTranslate');
Route::get('get-language', 'CustomerController@getLanguage')->name('livechat.customer.language');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/calendar', 'UserEventController@index');
    Route::get('/calendar/events', 'UserEventController@list');
    Route::post('/calendar/events', 'UserEventController@createEvent')->name("calendar.event.create");
    Route::put('/calendar/events/{id}', 'UserEventController@editEvent');
    Route::delete('/calendar/events/{id}', 'UserEventController@removeEvent');
});

Route::prefix('calendar/public')->group(function () {
    Route::get('/{id}', 'UserEventController@publicCalendar');
    Route::get('/events/{id}', 'UserEventController@publicEvents');
    Route::get('/event/suggest-time/{invitationId}', 'UserEventController@suggestInvitationTiming');
    Route::post('/event/suggest-time/{invitationId}', 'UserEventController@saveSuggestedInvitationTiming');
});


Route::get('/vendor-form', 'VendorSupplierController@vendorForm')->name("developer.vendor.form");
Route::get('/supplier-form', 'VendorSupplierController@supplierForm')->name("developer.supplier.form");

Route::prefix('product-category')->middleware('auth')->group(function () {
    Route::get('/history', 'ProductCategoryController@history');
    Route::get('/', 'ProductCategoryController@index')->name("product.category.index.list");
    Route::get('/records', 'ProductCategoryController@records')->name("product.category.records");
    Route::post('/update-category-assigned', 'ProductCategoryController@updateCategoryAssigned')->name("product.category.update-assigned");
});

Route::prefix('product-color')->middleware('auth')->group(function () {
    Route::get('/history', 'ProductColorController@history');
    Route::get('/', 'ProductColorController@index')->name("product.color.index.list");
    Route::get('/records', 'ProductColorController@records')->name("product.color.records");
    Route::post('/update-color-assigned', 'ProductColorController@updateCategoryAssigned')->name("product.color.update-assigned");
});

Route::prefix('listing-history')->middleware('auth')->group(function () {
    Route::get('/', 'ListingHistoryController@index')->name("listing.history.index");
    Route::get('/records', 'ListingHistoryController@records');
});


Route::prefix('digital-marketing')->middleware('auth')->group(function () {
    Route::get('/', 'DigitalMarketingController@index')->name('digital-marketing.index');
    Route::get('/records', 'DigitalMarketingController@records')->name('digital-marketing.records');
    Route::post('/save', 'DigitalMarketingController@save')->name('digital-marketing.save');
    Route::prefix('{id}')->group(function () {
        Route::get('/edit', 'DigitalMarketingController@edit')->name("digital-marketing.edit");
        Route::get('/components', 'DigitalMarketingController@components')->name("digital-marketing.components");
        Route::post('/components', 'DigitalMarketingController@componentStore')->name("digital-marketing.components.save");
        Route::get('/delete', 'DigitalMarketingController@delete')->name("digital-marketing.delete");

        Route::prefix('solution')->group(function () {
            Route::get('/', 'DigitalMarketingController@solution')->name("digital-marketing.solutions");
            Route::get('/records', 'DigitalMarketingController@solutionRecords')->name("digital-marketing.records");
            Route::post('/save', 'DigitalMarketingController@solutionSave')->name("digital-marketing.solution.save");
            Route::post('/create-usp', 'DigitalMarketingController@solutionCreateUsp')->name("digital-marketing.solution.create-usp");
            Route::prefix('{solutionId}')->group(function () {
                Route::get('/edit', 'DigitalMarketingController@solutionEdit')->name("digital-marketing.solution.edit");
                Route::get('/delete', 'DigitalMarketingController@solutionDelete')->name("digital-marketing.solution.delete");
                Route::post('/save-usp', 'DigitalMarketingController@solutionSaveUsp')->name("digital-marketing.solution.delete");
                Route::prefix('research')->group(function () {
                    Route::get('/', 'DigitalMarketingController@research')->name("digital-marketing.solution.research");
                    Route::get('/records', 'DigitalMarketingController@researchRecords')->name("digital-marketing.solution.research");
                    Route::post('/save', 'DigitalMarketingController@researchSave')->name("digital-marketing.solution.research.save");
                    Route::prefix('{researchId}')->group(function () {
                        Route::get('/edit', 'DigitalMarketingController@researchEdit')->name("digital-marketing.solution.research.edit");
                        Route::get('/delete', 'DigitalMarketingController@researchDelete')->name("digital-marketing.solution.research.delete");
                    });
                });

            });
        });
    });
});

Route::group(['middleware' => 'auth', 'prefix' => 'return-exchange'], function() {
    Route::get('/', 'ReturnExchangeController@index')->name('return-exchange.list');
    Route::get('/records', 'ReturnExchangeController@records')->name('return-exchange.records');
    Route::get('/model/{id}', 'ReturnExchangeController@getOrders');
    Route::post('/model/{id}/save', 'ReturnExchangeController@save')->name('return-exchange.save');

    Route::prefix('{id}')->group(function () {
        Route::get('/detail', 'ReturnExchangeController@detail')->name('return-exchange.detail');
        Route::get('/delete', 'ReturnExchangeController@delete')->name('return-exchange.delete');
        Route::get('/history', 'ReturnExchangeController@history')->name('return-exchange.history');
        Route::post('/update', 'ReturnExchangeController@update')->name('return-exchange.update');
    });
});

/**
 * Shipment module
 */
Route::group(['middleware' => 'auth'], function () {
    Route::post('shipment/send/email', 'ShipmentController@sendEmail')->name('shipment/send/email');
    Route::get('shipment/view/sent/email', 'ShipmentController@viewSentEmail')->name('shipment/view/sent/email');
    Route::resource('shipment', 'ShipmentController');
    Route::get('shipment/customer-details/{id}', 'ShipmentController@showCustomerDetails');
    Route::post('shipment/generate-shipment', 'ShipmentController@generateShipment')->name('shipment/generate');
    Route::get('shipment/get-templates-by-name/{name}', 'ShipmentController@getShipmentByName');

    /**
     * Twilio account management
     */

    Route::get('twilio/manage-twilio-account', 'TwilioController@manageTwilioAccounts')->name('twilio-manage-accounts');
    Route::post('twilio/add-account', 'TwilioController@addAccount')->name('twilio-add-account');
    Route::get('twilio/delete-account/{id}', 'TwilioController@deleteAccount')->name('twilio-delete-account');
    Route::get('twilio/manage-numbers/{id}', 'TwilioController@manageNumbers')->name('twilio-manage-numbers');



    Route::get('get-twilio-numbers/{account_id}', 'TwilioController@getTwilioActiveNumbers')->name('twilio-get-numbers');
    Route::post('twilio/assign-number', 'TwilioController@assignTwilioNumberToStoreWebsite')->name('assign-number-to-store-website');
    Route::post('twilio/call-forward', 'TwilioController@twilioCallForward')->name('manage-twilio-call-forward');

    Route::get('twilio/call-recordings/{account_id}', 'TwilioController@CallRecordings')->name('twilio-call-recording');
    Route::get('/download-mp3/{sid}', 'TwilioController@downloadRecording')->name('download-mp3');

    Route::get('twilio/call-management', 'TwilioController@callManagement')->name('twilio-call-management');
    Route::get('twilio/incoming-calls/{number_sid}/{number}', 'TwilioController@getIncomingList')->name('twilio-incoming-calls');
    Route::get('twilio/incoming-calls-recording/{call_sid}', 'TwilioController@incomingCallRecording')->name('twilio-incoming-call-recording');

});
Route::post('message-queue/approve/approved', '\Modules\MessageQueue\Http\Controllers\MessageQueueController@approved');


/****Webhook URL for twilio****/
Route::get('/run-webhook/{sid}', 'TwilioController@runWebhook');
/*
 * Quick Reply Page
 * */
Route::get('/quick-replies', 'QuickReplyController@quickReplies')->name('quick-replies');
Route::get('/get-store-wise-replies/{category_id}/{store_website_id?}', 'QuickReplyController@getStoreWiseReplies')->name('store-wise-replies');
Route::post('/save-store-wise-reply', 'QuickReplyController@saveStoreWiseReply')->name('save-store-wise-reply');
