<?php

use Illuminate\Support\Facades\Route;
use Modules\BookStack\Http\Controllers\Images;
use Modules\BookStack\Http\Controllers\TagController;
use Modules\BookStack\Http\Controllers\BookController;
use Modules\BookStack\Http\Controllers\HomeController;
use Modules\BookStack\Http\Controllers\PageController;
use Modules\BookStack\Http\Controllers\UserController;
use Modules\BookStack\Http\Controllers\SearchController;
use Modules\BookStack\Http\Controllers\ChapterController;
use Modules\BookStack\Http\Controllers\CommentController;
use Modules\BookStack\Http\Controllers\SettingController;
use Modules\BookStack\Http\Controllers\BookshelfController;
use Modules\BookStack\Http\Controllers\AttachmentController;
use Modules\BookStack\Http\Controllers\PermissionController;

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

//Route::get('/', 'BookStackController@index');

// Authenticated routes...
Route::middleware('auth')->group(function () {
    // Secure images routing
    Route::get('/uploads/images/{path}', [Images\ImageController::class, 'showImage'])->where('path', '.*$');

    Route::group(['prefix' => 'kb'], function () {
        // Shelves
        Route::get('/create-shelf', [BookshelfController::class, 'create']);
        Route::get('/', [BookshelfController::class, 'index']);
        Route::group(['prefix' => 'shelves'], function () {
            Route::get('/', [BookshelfController::class, 'index']);
            Route::post('/{slug}/add', [BookshelfController::class, 'store']);
            Route::get('/{slug}/edit', [BookshelfController::class, 'edit']);
            Route::get('/{slug}/delete', [BookshelfController::class, 'showDelete']);
            Route::get('/{slug}', [BookshelfController::class, 'show']);
            Route::put('/{slug}', [BookshelfController::class, 'update']);
            Route::delete('/{slug}', [BookshelfController::class, 'destroy']);
            Route::get('/{slug}/permissions', [BookshelfController::class, 'showPermissions']);
            Route::put('/{slug}/permissions', [BookshelfController::class, 'permissions']);
            Route::post('/{slug}/copy-permissions', [BookshelfController::class, 'copyPermissions']);

            Route::get('/{shelfSlug}/create-book', [BookController::class, 'create']);
            Route::post('/{shelfSlug}/create-book', [BookController::class, 'store']);

            Route::get('/show/{sortByView}/{sortByDate}', [BookshelfController::class, 'showShelf']);
        });

        Route::get('/create-book', [BookController::class, 'create']);
        Route::group(['prefix' => 'books'], function () {
            // Books
            Route::get('/', [BookController::class, 'index']);
            Route::post('/', [BookController::class, 'store']);
            Route::get('/{slug}/edit', [BookController::class, 'edit']);
            Route::put('/{slug}', [BookController::class, 'update']);
            Route::delete('/{id}', [BookController::class, 'destroy']);
            Route::get('/{slug}/sort-item', [BookController::class, 'getSortItem']);
            Route::get('/{slug}', [BookController::class, 'show']);
            Route::get('/{bookSlug}/permissions', [BookController::class, 'showPermissions']);
            Route::put('/{bookSlug}/permissions', [BookController::class, 'permissions']);
            Route::get('/{slug}/delete', [BookController::class, 'showDelete']);
            Route::get('/{bookSlug}/sort', [BookController::class, 'sort']);
            Route::put('/{bookSlug}/sort', [BookController::class, 'saveSort']);
            Route::get('/{bookSlug}/export/html', [BookController::class, 'exportHtml']);
            Route::get('/{bookSlug}/export/pdf', [BookController::class, 'exportPdf']);
            Route::get('/{bookSlug}/export/plaintext', [BookController::class, 'exportPlainText']);

            Route::get('/show/{sortByView}/{sortByDate}', [BookController::class, 'showBook']);

            // Pages
            Route::get('/{bookSlug}/create-page', [PageController::class, 'create']);
            Route::post('/{bookSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
            Route::get('/{bookSlug}/draft/{pageId}', [PageController::class, 'editDraft']);
            Route::post('/{bookSlug}/draft/{pageId}', [PageController::class, 'store']);
            Route::post('/{bookSlug}/page/{pageSlug}', [PageController::class, 'show']);
            Route::get('/{bookSlug}/page/{pageSlug}/export/pdf', [PageController::class, 'exportPdf']);
            Route::get('/{bookSlug}/page/{pageSlug}/export/html', [PageController::class, 'exportHtml']);
            Route::get('/{bookSlug}/page/{pageSlug}/export/plaintext', [PageController::class, 'exportPlainText']);
            Route::get('/{bookSlug}/page/{pageSlug}/edit', [PageController::class, 'edit']);
            Route::get('/{bookSlug}/page/{pageSlug}/move', [PageController::class, 'showMove']);
            Route::put('/{bookSlug}/page/{pageSlug}/move', [PageController::class, 'move']);
            Route::get('/{bookSlug}/page/{pageSlug}/copy', [PageController::class, 'showCopy']);
            Route::post('/{bookSlug}/page/{pageSlug}/copy', [PageController::class, 'copy']);
            Route::get('/{bookSlug}/page/{pageSlug}/delete', [PageController::class, 'showDelete']);
            Route::get('/{bookSlug}/draft/{pageId}/delete', [PageController::class, 'showDeleteDraft']);
            Route::get('/{bookSlug}/page/{pageSlug}/permissions', [PageController::class, 'showPermissions']);
            Route::put('/{bookSlug}/page/{pageSlug}/permissions', [PageController::class, 'permissions']);
            Route::put('/{bookSlug}/page/{pageSlug}', [PageController::class, 'update']);
            Route::delete('/{bookSlug}/page/{pageSlug}', [PageController::class, 'destroy']);
            Route::delete('/{bookSlug}/draft/{pageId}', [PageController::class, 'destroyDraft']);

            // Revisions
            Route::get('/{bookSlug}/page/{pageSlug}/revisions', [PageController::class, 'showRevisions']);
            Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}', [PageController::class, 'showRevision']);
            Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', [PageController::class, 'showRevisionChanges']);
            Route::put('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', [PageController::class, 'restoreRevision']);
            Route::delete('/{bookSlug}/page/{pageSlug}/revisions/{revId}/delete', [PageController::class, 'destroyRevision']);

            // Chapters
            Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', [PageController::class, 'create']);
            Route::post('/{bookSlug}/chapter/{chapterSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
            Route::get('/{bookSlug}/create-chapter', [ChapterController::class, 'create']);
            Route::post('/{bookSlug}/create-chapter', [ChapterController::class, 'store']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'show']);
            Route::put('/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'update']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'showMove']);
            Route::put('/{bookSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'move']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', [ChapterController::class, 'edit']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'showPermissions']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/export/pdf', [ChapterController::class, 'exportPdf']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/export/html', [ChapterController::class, 'exportHtml']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/export/plaintext', [ChapterController::class, 'exportPlainText']);
            Route::put('/{bookSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'permissions']);
            Route::get('/{bookSlug}/chapter/{chapterSlug}/delete', [ChapterController::class, 'showDelete']);
            Route::delete('/{bookSlug}/chapter/{chapterSlug}', [ChapterController::class, 'destroy']);
        });

        // Settings
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings');
            Route::post('/', [SettingController::class, 'update']);

            // Maintenance
            Route::get('/maintenance', [SettingController::class, 'showMaintenance']);
            Route::delete('/maintenance/cleanup-images', [SettingController::class, 'cleanupImages']);

            // Users
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/create', [UserController::class, 'create']);
            Route::get('/users/{id}/delete', [UserController::class, 'delete']);
            Route::patch('/users/{id}/switch-book-view', [UserController::class, 'switchBookView']);
            Route::patch('/users/{id}/switch-shelf-view', [UserController::class, 'switchShelfView']);
            Route::patch('/users/{id}/change-sort/{type}', [UserController::class, 'changeSort']);
            Route::patch('/users/{id}/update-expansion-preference/{key}', [UserController::class, 'updateExpansionPreference']);
            Route::post('/users/create', [UserController::class, 'store']);
            Route::get('/users/{id}', [UserController::class, 'edit']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);

            // Roles
            Route::get('/roles', [PermissionController::class, 'listRoles']);
            Route::get('/roles/new', [PermissionController::class, 'createRole']);
            Route::post('/roles/new', [PermissionController::class, 'storeRole']);
            Route::get('/roles/delete/{id}', [PermissionController::class, 'showDeleteRole']);
            Route::delete('/roles/delete/{id}', [PermissionController::class, 'deleteRole']);
            Route::get('/roles/{id}', [PermissionController::class, 'editRole']);
            Route::put('/roles/{id}', [PermissionController::class, 'updateRole']);
        });
    });

    // AJAX routes
    Route::put('/ajax/page/{id}/save-draft', [PageController::class, 'saveDraft']);
    Route::get('/ajax/page/{id}', [PageController::class, 'getPageAjax']);
    Route::delete('/ajax/page/{id}', [PageController::class, 'ajaxDestroy']);

    // Tag routes (AJAX)
    Route::group(['prefix' => 'ajax/tags'], function () {
        Route::get('/get/{entityType}/{entityId}', [TagController::class, 'getForEntity']);
        Route::get('/suggest/names', [TagController::class, 'getNameSuggestions']);
        Route::get('/suggest/values', [TagController::class, 'getValueSuggestions']);
    });

    // Comments
    Route::post('/ajax/page/{pageId}/comment', [CommentController::class, 'savePageComment']);
    Route::put('/ajax/comment/{id}', [CommentController::class, 'update']);
    Route::delete('/ajax/comment/{id}', [CommentController::class, 'destroy']);

    // Attachments routes
    Route::get('/attachments/{id}', [AttachmentController::class, 'get']);
    Route::post('/attachments/upload', [AttachmentController::class, 'upload']);
    Route::post('/attachments/upload/{id}', [AttachmentController::class, 'uploadUpdate']);
    Route::post('/attachments/link', [AttachmentController::class, 'attachLink']);
    Route::put('/attachments/{id}', [AttachmentController::class, 'update']);
    Route::get('/attachments/get/page/{pageId}', [AttachmentController::class, 'listForPage']);
    Route::put('/attachments/sort/page/{pageId}', [AttachmentController::class, 'sortForPage']);
    Route::delete('/attachments/{id}', [AttachmentController::class, 'delete']);

    Route::get('/custom-head-content', [HomeController::class, 'customHeadContent']);

    // Search
    Route::get('/searchGrid', [SearchController::class, 'searchGrid'])->name('searchGrid');
});
