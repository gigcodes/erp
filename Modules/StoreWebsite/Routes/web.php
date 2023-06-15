<?php

use Illuminate\Support\Facades\Route;
use Modules\StoreWebsite\Http\Controllers\SeoController;
use Modules\StoreWebsite\Http\Controllers\GoalController;
use Modules\StoreWebsite\Http\Controllers\PageController;
use Modules\StoreWebsite\Http\Controllers\BrandController;
use Modules\StoreWebsite\Http\Controllers\ColorController;
use Modules\StoreWebsite\Http\Controllers\WebsiteController;
use Modules\StoreWebsite\Http\Controllers\CategoryController;
use Modules\StoreWebsite\Http\Controllers\SiteAssetController;
use Modules\StoreWebsite\Http\Controllers\CategorySeoController;
use Modules\StoreWebsite\Http\Controllers\CountryGroupController;
use Modules\StoreWebsite\Http\Controllers\StoreWebsiteController;
use Modules\StoreWebsite\Http\Controllers\StoreWebsiteEnvironmentController;
use Modules\StoreWebsite\Http\Controllers\WebsiteStoreController;
use Modules\StoreWebsite\Http\Controllers\PriceOverrideController;
use Modules\StoreWebsite\Http\Controllers\PaymentResponseController;
use Modules\StoreWebsite\Http\Controllers\SiteAttributesControllers;
use Modules\StoreWebsite\Http\Controllers\SiteDevelopmentController;
use Modules\StoreWebsite\Http\Controllers\WebsiteStoreViewController;
use Modules\StoreWebsite\Http\Controllers\SiteDevelopmentStatusController;
use Modules\StoreWebsite\Http\Controllers\StoreWebsiteProductAttributeController;

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

Route::group([
    'prefix' => 'store-website',
    'middleware' => 'auth',
], function () {
    Route::get('/', [StoreWebsiteController::class, 'index'])->name('store-website.index');
    Route::get('api-token', [StoreWebsiteController::class, 'apiToken'])->name('store-website.apiToken');
    Route::post('api-token/generate-api-token', [StoreWebsiteController::class, 'apiTokenGenerate'])->name('store-website.apiTokenGenerate');
    Route::post('api-token/bulk-generate-api-token', [StoreWebsiteController::class, 'apiTokenBulkGenerate'])->name('store-website.apiTokenBulkGenerate');
    Route::post('api-token/get-api-token-logs/{id}', [StoreWebsiteController::class, 'getApiTokenLogs'])->name('store-website.getApiTokenLogs');
    Route::post('api-token/test-api-token/{id}', [StoreWebsiteController::class, 'testApiToken'])->name('store-website.testApiToken');
    Route::post('generate-reindex', [StoreWebsiteController::class, 'generateReIndexfile']);
    Route::post('generate-api-token', [StoreWebsiteController::class, 'generateApiToken']);
    Route::get('get-api-token', [StoreWebsiteController::class, 'getApiToken']);
    Route::post('generate-admin-password', [StoreWebsiteController::class, 'generateAdminPassword']);
    Route::get('get-admin-password', [StoreWebsiteController::class, 'getAdminPassword']);

    Route::get('/magento-user-lising', [StoreWebsiteController::class, 'magentoUserList'])->name('store-website.user-list');

    Route::get('/cancellation', [StoreWebsiteController::class, 'cancellation'])->name('store-website.cancellation');
    Route::get('/records', [StoreWebsiteController::class, 'records'])->name('store-website.records');
    Route::post('/save', [StoreWebsiteController::class, 'save'])->name('store-website.save');
    Route::get('/log-website-users/{id}', [StoreWebsiteController::class, 'logWebsiteUsers'])->name('store-website.logwebsiteusers');
    Route::post('/save-cancellation', [StoreWebsiteController::class, 'saveCancellation'])->name('store-website.save-cancellation');
    Route::post('/save-duplicate', [StoreWebsiteController::class, 'saveDuplicateStore'])->name('store-website.save-duplicate');
    Route::post('/generate-file-store', [StoreWebsiteController::class, 'generateStorefile'])->name('store-website.generate-file-store');

    Route::post('/save-user-in-magento', [StoreWebsiteController::class, 'saveUserInMagento'])->name('store-website.save-user-in-magento');
    Route::post('/delete-user-in-magento', [StoreWebsiteController::class, 'deleteUserInMagento'])->name('store-website.delete-user-in-magento');
    Route::post('/update-company-website-address', [StoreWebsiteController::class, 'updateCompanyWebsiteAddress']);
    Route::get('/copy-website-store-views/{id}', [StoreWebsiteController::class, 'copyWebsiteStoreViews']);
    Route::get('/delete-store-views/{id}', [StoreWebsiteController::class, 'deleteStoreViews']);

    // Create Tags for multiple website
    Route::get('list-tag', [StoreWebsiteController::class, 'list_tags'])->name('store-website.list_tags');
    Route::post('create-tag', [StoreWebsiteController::class, 'create_tags'])->name('store-website.create_tags');
    Route::post('attach-tag', [StoreWebsiteController::class, 'attach_tags'])->name('store-website.attach_tags');

    Route::get('attach-tag-store', [StoreWebsiteController::class, 'attach_tags_store'])->name('store-website.attach_tags_store');

    Route::group([
        'prefix' => '{id}',
    ], function () {
        Route::post('magento-setting-update-history', [StoreWebsiteController::class, 'getMagentoUpdateWebsiteSetting']);

        Route::post('magento-dev-update-script-history', [StoreWebsiteController::class, 'getMagentoDevScriptUpdatesLogs']);

        Route::post('select-folder', [StoreWebsiteController::class, 'getFolderName']);

        Route::post('magento-dev-script-update/{folder_name?}', [StoreWebsiteController::class, 'magentoDevScriptUpdate']);

        Route::get('/sync-stage-to-master', [StoreWebsiteController::class, 'syncStageToMaster']);

        Route::get('/token-check/', [StoreWebsiteController::class, 'checkMagentoToken']);

        Route::get('/userhistory', [StoreWebsiteController::class, 'userHistoryList']);

        Route::get('/store-reindex-history', [StoreWebsiteController::class, 'storeReindexHistory']);

        Route::get('/edit', [StoreWebsiteController::class, 'edit'])->name('store-website.edit');

        Route::get('/add-company-website-address', [StoreWebsiteController::class, 'addCompanyWebsiteAddress']);

        Route::get('/edit-cancellation', [StoreWebsiteController::class, 'editCancellation'])->name('store-website.edit-cancellation');

        Route::get('/delete', [StoreWebsiteController::class, 'delete'])->name('store-website.delete');

        Route::get('/child-categories', [CategoryController::class, 'getChildCategories'])->name('store-website.child-categories');

        Route::post('/submit-social-remarks', [StoreWebsiteController::class, 'updateSocialRemarks'])->name('store-website.update.social-remarks');

        Route::group([
            'prefix' => 'build-process',
        ], function () {
            Route::get('/', [StoreWebsiteController::class, 'buildProcess'])->name('store-website.build.process');
            Route::get('/history', [StoreWebsiteController::class, 'buildProcessHistory'])->name('store-website.build.process.history');
            Route::post('save', [StoreWebsiteController::class, 'buildProcessSave'])->name('store-website.build.process.save');
        });

        Route::group([
            'prefix' => 'social-strategy',
        ], function () {
            Route::get('/', [StoreWebsiteController::class, 'socialStrategy'])->name('store-website.social-strategy');
            Route::post('/add-subject', [StoreWebsiteController::class, 'submitSubject'])->name('store-website.social-strategy.add-subject');
            Route::post('/add-strategy', [StoreWebsiteController::class, 'submitStrategy'])->name('store-website.social-strategy.add-strategy');
            Route::post('/upload-documents', [StoreWebsiteController::class, 'uploadDocuments'])->name('store-website.social-strategy.upload-documents');
            Route::post('/save-documents', [StoreWebsiteController::class, 'saveDocuments'])->name('store-website.social-strategy.save-documents');
            Route::get('/list-documents', [StoreWebsiteController::class, 'listDocuments'])->name('store-website.social-strategy.list-documents');
            Route::post('/delete-document', [StoreWebsiteController::class, 'deleteDocument'])->name('store-website.social-strategy.delete-documents');
            Route::post('/send-document', [StoreWebsiteController::class, 'sendDocument'])->name('store-website.social-strategy.send-documents');
            Route::get('/remarks', [StoreWebsiteController::class, 'remarks'])->name('store-website.social-strategy.remarks');
            Route::post('/remarks', [StoreWebsiteController::class, 'saveRemarks'])->name('store-website.social-strategy.saveRemarks');
            Route::get('/edit-subject', [StoreWebsiteController::class, 'viewSubject'])->name('store-website.social-strategy.edit-subject');
            Route::post('/edit-subject', [StoreWebsiteController::class, 'submitSubjectChange'])->name('store-website.social-strategy.submit-edit-subject');
        });

        Route::group([
            'prefix' => 'attached-category',
        ], function () {
            Route::get('/', [CategoryController::class, 'index'])->name('store-website.attached-category.index');
            Route::post('/', [CategoryController::class, 'store'])->name('store-website.attached-category.store');
            Route::group(['prefix' => '{store_category_id}'], function () {
                Route::get('/delete', [CategoryController::class, 'delete'])->name('store-website.attached-category.delete');
            });
        });

        Route::group(['prefix' => 'attached-categories'], function () {
            Route::post('/', [CategoryController::class, 'storeMultipleCategories'])->name('store-website.attached-categories.store');
        });

        Route::group(['prefix' => 'attached-brand'], function () {
            Route::get('/', [BrandController::class, 'index'])->name('store-website.attached-brand.index');
            Route::post('/', [BrandController::class, 'store'])->name('store-website.attached-brand.store');
            Route::group(['prefix' => '{store_brand_id}'], function () {
                Route::get('/delete', [BrandController::class, 'delete'])->name('store-website.attached-brand.delete');
            });
        });

        Route::group(['prefix' => 'goal'], function () {
            Route::get('/', [GoalController::class, 'index'])->name('store-website.goal.index');
            Route::get('records', [GoalController::class, 'records'])->name('store-website.goal.records');
            Route::post('save', [GoalController::class, 'save'])->name('store-website.goal.save');
            Route::group(['prefix' => '{goalId}'], function () {
                Route::get('edit', [GoalController::class, 'edit'])->name('store-website.goal.edit');
                Route::get('delete', [GoalController::class, 'delete'])->name('store-website.goal.delete');
                Route::get('remarks', [GoalController::class, 'remarks'])->name('store-website.goal.remarks');
                Route::post('remarks', [GoalController::class, 'storeRemarks'])->name('store-website.goal.remarks.store');
            });
        });

        Route::group(['prefix' => 'seo-format'], function () {
            Route::get('/', [SeoController::class, 'index'])->name('store-website.seo.index');
            Route::post('save', [SeoController::class, 'save'])->name('store-website.seo.save');
        });
    });

    Route::group(['prefix' => 'brand'], function () {
        Route::get('/', [BrandController::class, 'list'])->name('store-website.brand.list');
        Route::get('records', [BrandController::class, 'records'])->name('store-website.brand.records');
        Route::post('push-to-store', [BrandController::class, 'pushToStore'])->name('store-website.brand.push-to-store');
        Route::post('refresh-min-max-price', [BrandController::class, 'refreshMinMaxPrice'])->name('store-website.refresh-min-max-price');
        Route::get('history', [BrandController::class, 'history'])->name('store-website.brand.history');
        Route::get('live-brands', [BrandController::class, 'liveBrands'])->name('store-website.brand.live-brands');
        Route::get('missing-brands', [BrandController::class, 'missingBrands'])->name('store-website.brand.missing-brands');
        Route::post('reconsile-brand', [BrandController::class, 'reconsileBrands'])->name('store-website.brand.reconsile-brands');
        Route::post('reconsile-brand-history-log', [BrandController::class, 'reconsileBrandsHistoryLog'])->name('reconsile-brands-history-log');
        Route::post('push-brand-history-log', [BrandController::class, 'pushBrandsLog'])->name('push-brands-history-log');
    });

    Route::group(['prefix' => 'price-override'], function () {
        Route::get('/', [PriceOverrideController::class, 'index'])->name('store-website.price-override.index');
        Route::get('records', [PriceOverrideController::class, 'records'])->name('store-website.price-override.records');
        Route::post('save', [PriceOverrideController::class, 'save'])->name('store-website.price-override.save');
        Route::get('calculate', [PriceOverrideController::class, 'calculate'])->name('store-website.price-override.calculate');
        Route::group(['prefix' => '{id}'], function () {
            Route::get('edit', [PriceOverrideController::class, 'edit'])->name('store-website.price-override.edit');
            Route::get('delete', [PriceOverrideController::class, 'delete'])->name('store-website.price-override.delete');
        });
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/', [CategoryController::class, 'list'])->name('store-website.category.list');
        Route::post('category-history', [CategoryController::class, 'categoryHistory'])->name('store-website.category,categoryHistory');
        Route::post('website-category-user-history', [CategoryController::class, 'webiteCategoryUserHistory'])->name('store-website.category,webiteCategoryUserHistory');
        Route::post('save/store/category', [CategoryController::class, 'saveStoreCategory'])->name('store-website.save.store.category');
        Route::post('/delete-category', [CategoryController::class, 'deleteCategory'])->name('store-website.delete-category');
    });

    Route::group(['prefix' => 'color'], function () {
        Route::get('/', [ColorController::class, 'index'])->name('store-website.color.list');
        Route::post('save', [ColorController::class, 'store'])->name('store-website.color.save');
        Route::put('/{id}', [ColorController::class, 'update'])->name('store-website.color.edit');
        Route::delete('/{id}', [ColorController::class, 'destroy'])->name('store-website.color.destroy');
        Route::post('push-to-store', [ColorController::class, 'pushToStore'])->name('store-website.color.push-to-store');
    });

    Route::group(['prefix' => 'websites'], function () {
        Route::get('/', [WebsiteController::class, 'index'])->name('store-website.websites.index');
        Route::get('/records', [WebsiteController::class, 'records'])->name('store-website.websites.records');
        Route::post('save', [WebsiteController::class, 'store'])->name('store-website.websites.save');
        Route::post('create-default-stores', [WebsiteController::class, 'createDefaultStores'])->name('store-website.websites.createDefaultStores');
        Route::post('move-stores', [WebsiteController::class, 'moveStores'])->name('store-website.websites.moveStores');
        Route::post('copy-stores', [WebsiteController::class, 'copyStores'])->name('store-website.websites.copyStores');
        Route::post('change-status', [WebsiteController::class, 'changeStatus'])->name('store-website.websites.changeStatus');
        Route::post('change-price-ovveride', [WebsiteController::class, 'changePriceOvveride'])->name('store-website.websites.changePriceOvveride');
        Route::post('copy-websites', [WebsiteController::class, 'copyWebsites'])->name('store-website.websites.copyWebsites');
        Route::get('/{id}/edit', [WebsiteController::class, 'edit'])->name('store-website.websites.edit');
        Route::get('/{id}/delete', [WebsiteController::class, 'delete'])->name('store-website.websites.delete');
        Route::get('/{id}/push', [WebsiteController::class, 'push'])->name('store-website.websites.push');
        Route::get('/{id}/push-stores', [WebsiteController::class, 'pushStores'])->name('store-website.websites.pushStores');
        Route::get('/{id}/copy-website-struct', [WebsiteController::class, 'copyWebsiteStructure'])->name('store-website.websites.copyWebsiteStructure');
    });

    Route::group(['prefix' => 'website-stores'], function () {
        Route::get('/', [WebsiteStoreController::class, 'index'])->name('store-website.website-stores.index');
        Route::get('/records', [WebsiteStoreController::class, 'records'])->name('store-website.website-stores.records');
        Route::post('save', [WebsiteStoreController::class, 'store'])->name('store-website.website-stores.save');
        Route::get('/{id}/edit', [WebsiteStoreController::class, 'edit'])->name('store-website.website-stores.edit');
        Route::get('/{id}/delete', [WebsiteStoreController::class, 'delete'])->name('store-website.website-stores.delete');
        Route::get('/{id}/push', [WebsiteStoreController::class, 'push'])->name('store-website.website-stores.push');
        Route::get('dropdown', [WebsiteStoreController::class, 'dropdown'])->name('store-website.website-stores.dropdown');
        Route::post('multiple-delete', [WebsiteStoreController::class, 'deteleMultiple'])->name('store-website.websites.deteleMultiple');
    });

    //Site Attributes
    Route::group(['prefix' => 'site-attributes'], function () {
        Route::get('/', [SiteAttributesControllers::class, 'index'])->name('store-website.site-attributes.index');
        Route::post('save', [SiteAttributesControllers::class, 'store'])->name('store-website.site-attributes-views.save');
        Route::post('attributeshistory', [SiteAttributesControllers::class, 'attributesHistory'])->name('store-website.site-attributes-views.attributeshistory');
        Route::get('list', [SiteAttributesControllers::class, 'list'])->name('store-website.site-attributes-views.list');
        Route::get('/records', [SiteAttributesControllers::class, 'records'])->name('store-website.site-attributes-views.records');
        Route::get('/{id}/delete', [SiteAttributesControllers::class, 'delete'])->name('store-website.site-attributes-views.delete');
        Route::get('/{id}/edit', [SiteAttributesControllers::class, 'edit'])->name('store-website.site-attributes-views.edit');
    });

    Route::group(['prefix' => 'website-store-views'], function () {
        Route::get('/', [WebsiteStoreViewController::class, 'index'])->name('store-website.website-store-views.index');
        Route::get('/records', [WebsiteStoreViewController::class, 'records'])->name('store-website.website-store-views.records');
        Route::post('save', [WebsiteStoreViewController::class, 'store'])->name('store-website.website-store-views.save');
        Route::get('/{id}/edit', [WebsiteStoreViewController::class, 'edit'])->name('store-website.website-store-views.edit');
        Route::get('/{id}/delete', [WebsiteStoreViewController::class, 'delete'])->name('store-website.website-store-views.delete');
        Route::get('/{id}/push', [WebsiteStoreViewController::class, 'push'])->name('store-website.website-store-views.push');
        Route::post('update-store-website', [WebsiteStoreViewController::class, 'updateStoreWebsite'])->name('store-website.website-store-views.update-store-website');
        Route::get('/group/{id}/edit/{store_group_id}', [WebsiteStoreViewController::class, 'editGroup'])->name('store-website.website-store-views.group.edit');
        Route::post('/group/save', [WebsiteStoreViewController::class, 'storeGroup'])->name('store-website.website-store-views.group.save');
        Route::get('/group/{id}/delete/{store_group_id}', [WebsiteStoreViewController::class, 'deleteGroup'])->name('store-website.website-store-views.group.delete');
        Route::get('/agents', [WebsiteStoreViewController::class, 'agents'])->name('store-website.website-store-views.group.agents');
        Route::get('/groups', [WebsiteStoreViewController::class, 'groups'])->name('store-website.website-store-views.group.groups');
    });
    Route::group(['prefix' => 'page'], function () {
        Route::get('/', [PageController::class, 'index'])->name('store-website.page.index');
        Route::get('/review-translate/{language?}', [PageController::class, 'reviewTranslate'])->name('store-website.page.review.translate');
        Route::get('/meta-title-keywords', [PageController::class, 'pageMetaTitleKeywords'])->name('store-website.page.keywords');
        Route::get('/records', [PageController::class, 'records'])->name('store-website.page.records');
        Route::get('/getReviewTranslateRecords', [PageController::class, 'getReviewTranslateRecords'])->name('store-website.page.review.translate.records');
        Route::post('save', [PageController::class, 'store'])->name('store-website.page.save');
        Route::get('/{id}/edit', [PageController::class, 'edit'])->name('store-website.page.edit');
        Route::get('/{id}/delete', [PageController::class, 'delete'])->name('store-website.page.delete');
        Route::get('/{id}/push', [PageController::class, 'push'])->name('store-website.page.push');
        Route::get('/{id}/pull', [PageController::class, 'pull'])->name('store-website.page.pull');
        Route::get('/pull/logs', [PageController::class, 'pullLogs']);
        Route::get('/{id}/pull/logs', [PageController::class, 'pullLogs']);
        Route::get('/{id}/get-stores', [PageController::class, 'getStores'])->name('store-website.page.getStores');
        Route::get('/{id}/load-page', [PageController::class, 'loadPage'])->name('store-website.page.loadPage');
        Route::get('/{id}/history', [PageController::class, 'pageHistory'])->name('store-website.page.history');
        Route::get('/{id}/activities', [PageController::class, 'pageActivities'])->name('store_website_page.activities');
        Route::get('/{id}/translate-for-other-langauge', [PageController::class, 'translateForOtherLanguage'])->name('store-website.page.translate-for-other-langauge');
        Route::get('/{id}/push-website-in-live', [PageController::class, 'pushWebsiteInLive'])->name('store-website.page.push-website-in-live');
        Route::get('/{id}/pull-website-in-live', [PageController::class, 'pullWebsiteInLive'])->name('store-website.page.pull-website-in-live');
        Route::get('/histories', [PageController::class, 'histories'])->name('store-website.page.histories');
        Route::put('/store-platform-id', [PageController::class, 'store_platform_id'])->name('store_website_page.store_platform_id');
        Route::post('/copy-to', [PageController::class, 'copyTo'])->name('store_website_page.copy.to');
    });

    Route::group(['prefix' => 'category-seo'], function () {
        Route::get('/', [CategorySeoController::class, 'index'])->name('store-website.category-seo.index');
        Route::get('/records', [CategorySeoController::class, 'records'])->name('store-website.category-seo.records');
        Route::post('save', [CategorySeoController::class, 'store'])->name('store-website.category-seo.save');
        Route::get('/{id}/edit', [CategorySeoController::class, 'edit'])->name('store-website.page.edit');
        Route::get('/{id}/delete', [CategorySeoController::class, 'destroy'])->name('store-website.page.delete');
        Route::get('/{id}/translate-for-other-langauge', [CategorySeoController::class, 'translateForOtherLanguage'])->name('store-website.page.translate-for-other-langauge');
        Route::get('/{id}/push', [CategorySeoController::class, 'push'])->name('store-website.page.push');
        Route::get('/{id}/push-website-in-live', [CategorySeoController::class, 'pushWebsiteInLive'])->name('store-website.page.push-website-in-live');
        Route::get('/{id}/history', [CategorySeoController::class, 'history'])->name('store-website.page.history');
        Route::get('/{id}/load-page', [CategorySeoController::class, 'loadPage'])->name('store-website.page.category.seo.loadPage');
        Route::post('/copy-to', [CategorySeoController::class, 'copyTo'])->name('store_website_page.category.seo.copy.to');
    });

    Route::group(['prefix' => 'product-attribute'], function () {
        Route::get('/', [StoreWebsiteProductAttributeController::class, 'index'])->name('store-website.product-attribute.index');
        Route::get('/records', [StoreWebsiteProductAttributeController::class, 'records'])->name('store-website.product-attribute.records');
        Route::post('save', [StoreWebsiteProductAttributeController::class, 'store'])->name('store-website.product-attribute.save');
        Route::post('create-default-stores', [StoreWebsiteProductAttributeController::class, 'createDefaultStores'])->name('store-website.product-attribute.createDefaultStores');
        Route::post('move-stores', [StoreWebsiteProductAttributeController::class, 'moveStores'])->name('store-website.product-attribute.moveStores');
        Route::post('copy-stores', [StoreWebsiteProductAttributeController::class, 'copyStores'])->name('store-website.product-attribute.copyStores');
        Route::post('change-status', [StoreWebsiteProductAttributeController::class, 'changeStatus'])->name('store-website.product-attribute.changeStatus');
        Route::post('copy-websites', [StoreWebsiteProductAttributeController::class, 'copyWebsites'])->name('store-website.product-attribute.copyWebsites');
        Route::get('/{id}/edit', [StoreWebsiteProductAttributeController::class, 'edit'])->name('store-website.product-attribute.edit');
        Route::get('/{id}/delete', [StoreWebsiteProductAttributeController::class, 'delete'])->name('store-website.product-attribute.delete');
        Route::get('/{id}/push', [StoreWebsiteProductAttributeController::class, 'push'])->name('store-website.product-attribute.push');
    });
    
    Route::group(['prefix' => 'environment'], function () {
        Route::get('/table', [StoreWebsiteEnvironmentController::class, 'index'])->name('store-website.environment.index');
        Route::get('/', [StoreWebsiteEnvironmentController::class, 'matrix'])->name('store-website.environment.matrix');
        Route::post('update', [StoreWebsiteEnvironmentController::class, 'environmentUpdate'])->name('store-website.environment.update');
        
        Route::get('records', [StoreWebsiteEnvironmentController::class, 'records'])->name('store-website.environment.records');

        Route::post('save', [StoreWebsiteEnvironmentController::class, 'store'])->name('store-website.environment.save');

        Route::post('updateValue', [StoreWebsiteEnvironmentController::class, 'updateValue'])->name('store-website.environment.updateValue');
        
        Route::get('/{id}/edit', [StoreWebsiteEnvironmentController::class, 'edit'])->name('store-website.environment.edit');

        Route::get('/{id}/history', [StoreWebsiteEnvironmentController::class, 'history'])->name('store-website.environment.history');
    });

});

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'site-development'], function () {
        Route::get('status/update', [SiteDevelopmentController::class, 'siteDevlopmentStatusUpdate'])->name('site_devlopment.status.update');
        Route::post('remark/user_flag', [SiteDevelopmentController::class, 'userRemarkFlag'])->name('remark.flag.user');
        Route::post('remark/admin_flag', [SiteDevelopmentController::class, 'adminRemarkFlag'])->name('remark.flag.admin');
        Route::get('/countdevtask/{id}', [SiteDevelopmentController::class, 'taskCount']);
        Route::get('/task/relation/{id}', [SiteDevelopmentController::class, 'taskRelation']);
        Route::get('/deletedevtask', [SiteDevelopmentController::class, 'deletedevtask'])->name('site.development.delete.task');
        Route::get('/{id?}', [SiteDevelopmentController::class, 'index'])->name('site-development.index');
        Route::post('/create-tasks', [SiteDevelopmentController::class, 'createTask'])->name('site-development.create.task');
        Route::post('/copy-tasks', [SiteDevelopmentController::class, 'copyTasksFromWebsite'])->name('site-development.copy.task');
        Route::post('/save-category', [SiteDevelopmentController::class, 'addCategory'])->name('site-development.category.save');
        Route::post('/save-master-category', [SiteDevelopmentController::class, 'addMasterCategory'])->name('site-development.master_category.save');
        Route::post('/edit-category', [SiteDevelopmentController::class, 'editCategory'])->name('site-development.category.edit');
        Route::post('/save-development', [SiteDevelopmentController::class, 'addSiteDevelopment'])->name('site-development.save');
        Route::post('/disallow-category', [SiteDevelopmentController::class, 'disallowCategory'])->name('site-development.disallow.category');
        Route::post('/upload-documents', [SiteDevelopmentController::class, 'uploadDocuments'])->name('site-development.upload-documents');
        Route::post('/save-documents', [SiteDevelopmentController::class, 'saveDocuments'])->name('site-development.save-documents');
        Route::post('/delete-document', [SiteDevelopmentController::class, 'deleteDocument'])->name('site-development.delete-documents');
        Route::post('/send-document', [SiteDevelopmentController::class, 'sendDocument'])->name('site-development.send-documents');
        Route::get('/preview-img/{site_id}', [SiteDevelopmentController::class, 'previewImage'])->name('site-development.preview-image');
        Route::get('/preview-img-task/{id}', [SiteDevelopmentController::class, 'previewTaskImage'])->name('site-development.preview-img');
        Route::get('/artwork-history/{site_id}', [SiteDevelopmentController::class, 'getArtworkHistory'])->name('site-development.artwork-history');
        Route::get('/status-history/{site_id}', [SiteDevelopmentController::class, 'statusHistory'])->name('site-development.status-history');
        Route::post('/send-sop', [SiteDevelopmentController::class, 'SendTaskSOP'])->name('site-development.sendSop');
        Route::post('/send', [SiteDevelopmentController::class, 'SendTask'])->name('site-development.senduser');
        Route::post('/check-site-asset', [SiteDevelopmentController::class, 'checkSiteAsset'])->name('site-development.check-site-asset');
        Route::post('/check-site-list', [SiteDevelopmentController::class, 'checkSiteList'])->name('site-development.check-site-list');
        Route::post('/check-site-ui-list', [SiteDevelopmentController::class, 'checkUi'])->name('site-development.check-ui');
        Route::post('/set-site-ui-list', [SiteDevelopmentController::class, 'setcheckUi'])->name('site-development.set-check-ui');
        Route::post('/set-site-asset', [SiteDevelopmentController::class, 'setSiteAsset'])->name('site-development.set-site-asset');
        Route::post('/set-site-list', [SiteDevelopmentController::class, 'setSiteList'])->name('site-development.set-site-list');
        Route::get('/latest-reamrks/{website_id}', [SiteDevelopmentController::class, 'latestRemarks'])->name('site-development.latest-reamrks');
        Route::get('/artwork-history/all-histories/{website_id}', [SiteDevelopmentController::class, 'allartworkHistory'])->name('site-development.artwork-history.all-histories');
        Route::post('/save-site-asset-data', [SiteDevelopmentController::class, 'saveSiteAssetData'])->name('site-development.save-site-asset-data');
        Route::group(['prefix' => '{id}'], function () {
            Route::get('list-documents', [SiteDevelopmentController::class, 'listDocuments'])->name('site-development.list-documents');
            Route::group(['prefix' => 'remarks'], function () {
                Route::get('/', [SiteDevelopmentController::class, 'remarks'])->name('site-development.remarks');
                Route::post('/', [SiteDevelopmentController::class, 'saveRemarks'])->name('site-development.saveRemarks');
            });
        });
        Route::get('/store-website/category', [SiteDevelopmentController::class, 'storeWebsiteCategory'])->name('site-development.store-website-category');
        Route::post('/store-website/category/save', [SiteDevelopmentController::class, 'updateMasterCategory'])->name('site-development.update-category');
        Route::post('/store-website/category/savebulk', [SiteDevelopmentController::class, 'updateBulkMasterCategory'])->name('site-development.update-category-bulk');
    });

    Route::group(['prefix' => 'site-development-status'], function () {
        Route::get('/', [SiteDevelopmentStatusController::class, 'index'])->name('site-development-status.index');
        Route::get('records', [SiteDevelopmentStatusController::class, 'records'])->name('site-development-status.records');
        Route::get('stats', [SiteDevelopmentStatusController::class, 'statusStats'])->name('site-development-status.stats');
        Route::post('save', [SiteDevelopmentStatusController::class, 'save'])->name('site-development-status.save');
        Route::post('merge-status', [SiteDevelopmentStatusController::class, 'mergeStatus'])->name('site-development-status.merge-status');
        Route::group(['prefix' => '{id}'], function () {
            Route::get('edit', [SiteDevelopmentStatusController::class, 'edit'])->name('site-development-status.edit');
            Route::get('delete', [SiteDevelopmentStatusController::class, 'delete'])->name('site-development-status.delete');
        });
    });

    Route::group(['prefix' => 'country-group'], function () {
        Route::get('/', [CountryGroupController::class, 'index'])->name('store-website.country-group.index');
        Route::get('records', [CountryGroupController::class, 'records'])->name('store-website.country-group.records');
        Route::post('save', [CountryGroupController::class, 'save'])->name('store-website.country-group.save');
        Route::group(['prefix' => '{id}'], function () {
            Route::get('edit', [CountryGroupController::class, 'edit'])->name('store-website.country-group.edit');
            Route::get('delete', [CountryGroupController::class, 'delete'])->name('store-website.country-group.delete');
        });
    });

    Route::group(['prefix' => 'site-assets'], function () {
        Route::get('/', [SiteAssetController::class, 'index'])->name('site-asset.index');
        Route::post('/download-site-asset-data', [SiteAssetController::class, 'downaloadSiteAssetData'])->name('site-asset.download');
    });
    Route::get('site-check-list', [SiteAssetController::class, 'siteCheckList'])->name('site-check-list');
    Route::post('site-check-list/upload-document', [SiteAssetController::class, 'uploadDocument'])->name('site-check-list.upload-document');
    Route::get('site-check-list/get-document', [SiteAssetController::class, 'getDocument'])->name('site-check-list.get-document');
    Route::post('site-check-list/download', [SiteAssetController::class, 'downaloadSiteCheckListData'])->name('site-check-list.download');

    //Payment Responses Routes
    Route::group(['prefix' => 'payment-responses'], function () {
        Route::get('/', [PaymentResponseController::class, 'index'])->name('payment-responses.index');
        Route::get('/records', [PaymentResponseController::class, 'records'])->name('payment-responses.records');
    });
    
});
