<?php

use App\Http\Controllers\SocialController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\Social\SocialAdsController;
use App\Http\Controllers\Social\SocialPostController;
use App\Http\Controllers\SocialAccountPostController;
use App\Http\Controllers\Social\SocialAdsetController;
use App\Http\Controllers\Social\SocialConfigController;
use App\Http\Controllers\SocialAccountCommentController;
use App\Http\Controllers\Social\SocialCampaignController;
use App\Http\Controllers\Social\SocialAdCreativeController;

Route::get('inbox', [SocialAccountController::class, 'inbox'])->name('direct-message');
Route::post('send-message', [SocialAccountController::class, 'sendMessage'])->name('message.send');
Route::post('list-message', [SocialAccountController::class, 'listMessage'])->name('message.list');
Route::get('{account_id}/posts', [SocialAccountPostController::class, 'index'])->name('account.posts');
Route::get('{post_id}/comments', [SocialAccountCommentController::class, 'index'])->name('account.comments');
Route::post('delete-post', [SocialPostController::class, 'deletePost'])->name('post.postdelete');
Route::post('reply-comments', [SocialAccountCommentController::class, 'replyComments'])->name('account.comments.reply');
Route::post('dev-reply-comment', [SocialAccountCommentController::class, 'devCommentsReply'])->name('dev.reply.comment');
Route::get('email-replise/{id}', [SocialAccountCommentController::class, 'getEmailreplies']);
Route::get('all-comments', [SocialAccountCommentController::class, 'allcomments'])->name('all-comments');

Route::get('config', [SocialConfigController::class, 'index'])->name('config.index');
Route::post('config/store', [SocialConfigController::class, 'store'])->name('config.store');
Route::post('config/edit', [SocialConfigController::class, 'edit'])->name('config.edit');
Route::post('config/delete', [SocialConfigController::class, 'destroy'])->name('config.delete');
Route::get('config/adsmanager', [SocialConfigController::class, 'getadsAccountManager'])->name('config.adsmanager');

Route::get('config/fbtokenback', [SocialConfigController::class, 'getfbTokenBack'])->name('config.fbtokenback');
Route::get('config/fbtoken', [SocialConfigController::class, 'getfbToken'])->name('config.fbtoken');

Route::get('posts/{id}', [SocialPostController::class, 'index'])->name('post.index');
Route::post('post/store', [SocialPostController::class, 'store'])->name('post.store');
Route::post('post/edit', [SocialPostController::class, 'edit'])->name('post.edit');
Route::post('post/delete', [SocialPostController::class, 'destroy'])->name('post.delete');
Route::get('post/create/{id}', [SocialPostController::class, 'create'])->name('post.create');
Route::get('post/getimage/{id}', [SocialPostController::class, 'getImage'])->name('post.getimage');
Route::post('post/history', [SocialPostController::class, 'history'])->name('post.history');
Route::post('post/translationapproval', [SocialPostController::class, 'translationapproval'])->name('post.translationapproval');
Route::post('post/approvepost', [SocialPostController::class, 'approvepost'])->name('post.approvepost');

Route::get('post/grid', [SocialPostController::class, 'grid'])->name('post.grid');

Route::get('campaigns', [SocialCampaignController::class, 'index'])->name('campaign.index');
Route::post('campaign/store', [SocialCampaignController::class, 'store'])->name('campaign.store');
Route::post('campaign/edit', [SocialCampaignController::class, 'edit'])->name('campaign.edit');
Route::post('campaign/delete', [SocialCampaignController::class, 'destroy'])->name('campaign.delete');
Route::get('campaign/create', [SocialCampaignController::class, 'create'])->name('campaign.create');
Route::post('campaign/history', [SocialCampaignController::class, 'history'])->name('campaign.history');

Route::get('adsets', [SocialAdsetController::class, 'index'])->name('adset.index');
Route::post('adset/store', [SocialAdsetController::class, 'store'])->name('adset.store');
Route::post('adset/edit', [SocialAdsetController::class, 'edit'])->name('adset.edit');
Route::post('adset/delete', [SocialAdsetController::class, 'destroy'])->name('adset.delete');
Route::get('adset/create', [SocialAdsetController::class, 'create'])->name('adset.create');
Route::post('adset/history', [SocialAdsetController::class, 'history'])->name('adset.history');

Route::get('adcreatives', [SocialAdCreativeController::class, 'index'])->name('adcreative.index');
Route::post('adcreative/store', [SocialAdCreativeController::class, 'store'])->name('adcreative.store');
Route::post('adcreative/edit', [SocialAdCreativeController::class, 'edit'])->name('adcreative.edit');
Route::post('adcreative/delete', [SocialAdCreativeController::class, 'destroy'])->name('adcreative.delete');
Route::get('adcreative/create', [SocialAdCreativeController::class, 'create'])->name('adcreative.create');
Route::get('adcreative/getconfigPost', [SocialAdCreativeController::class, 'getpost'])->name('adcreative.getpost');
Route::post('adcreative/history', [SocialAdCreativeController::class, 'history'])->name('adcreative.history');

Route::get('ads', [SocialAdsController::class, 'index'])->name('ad.index');
Route::post('ads/store', [SocialAdsController::class, 'store'])->name('ad.store');
Route::post('ads/edit', [SocialAdsController::class, 'edit'])->name('ad.edit');
Route::post('ads/delete', [SocialAdsController::class, 'destroy'])->name('ad.delete');
Route::get('ads/create', [SocialAdsController::class, 'create'])->name('ad.create');
Route::post('ads/history', [SocialAdsController::class, 'history'])->name('ad.history');
Route::get('ads/getconfigPost', [SocialAdsController::class, 'getpost'])->name('ad.getpost');

Route::any('get-post/page', [SocialController::class, 'pagePost'])->name('get-post.page');
Route::get('post/page', [SocialController::class, 'index'])->name('post.page');
Route::post('post/page/create', [SocialController::class, 'createPost'])->name('post.page.create');
Route::any('get-post/page', [SocialController::class, 'pagePost'])->name('get-post.page');
Route::get('post/page', [SocialController::class, 'index'])->name('post.page');
Route::post('post/page/create', [SocialController::class, 'createPost'])->name('post.page.create');
Route::get('ad/report', [SocialController::class, 'report'])->name('report');
Route::get('ad/report-history', [SocialController::class, 'reportHistory'])->name('report.history');
Route::get('ad/schedules', [SocialController::class, 'getSchedules'])->name('ads.schedules');
Route::post('ad/schedules', [SocialController::class, 'getSchedules'])->name('ads.schedules.p');
Route::get('ad/schedules/calendar', [SocialController::class, 'getAdSchedules'])->name('ads.schedules.calendar');
Route::post('ad/schedules/', [SocialController::class, 'createAdSchedule'])->name('ads.schedules.create');
Route::post('ad/schedules/attach-images/{id}', [SocialController::class, 'attachMedia'])->name('ads.schedules.attach_images');
Route::post('ad/schedules/attach-products/{id}', [SocialController::class, 'attachProducts'])->name('ads.schedules.attach_products');
Route::post('ad/schedules/', [SocialController::class, 'createAdSchedule'])->name('ads.schedules.attach_image');
Route::get('ad/schedules/{id}', [SocialController::class, 'showSchedule'])->name('ads.schedules.show');
Route::get('ad/insight/{adId}', [SocialController::class, 'getAdInsights'])->name('ad.insight');
Route::post('ad/report/paginate', [SocialController::class, 'paginateReport'])->name('report.paginate');
Route::get('ad/report/{ad_id}/{status}/{token}/', [SocialController::class, 'changeAdStatus'])->name('report.ad.status');
Route::get('adcreative/report', [SocialController::class, 'adCreativereport'])->name('adCreative.report');
Route::post('adcreative/report/paginate', [SocialController::class, 'adCreativepaginateReport'])->name('adCreative.paginate');
Route::get('ad/campaign/create', [SocialController::class, 'createCampaign'])->name('ad.campaign.create');
Route::post('ad/campaign/store', [SocialController::class, 'storeCampaign'])->name('ad.campaign.store');
Route::get('ad/adset/create', [SocialController::class, 'createAdset'])->name('ad.adset.create');
Route::post('ad/adset/store', [SocialController::class, 'storeAdset'])->name('ad.adset.store');
Route::get('ad/create', [SocialController::class, 'createAd'])->name('ad.create');
Route::post('ad/store', [SocialController::class, 'storeAd'])->name('ad.store');
