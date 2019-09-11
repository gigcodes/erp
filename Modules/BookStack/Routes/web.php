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

//Route::get('/', 'BookStackController@index');

// Authenticated routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('/create-book', 'BookController@create');

    Route::group(['prefix' => 'books'], function () {

        // Books
        Route::get('/', 'BookController@index');
        Route::post('/', 'BookController@store');
        Route::get('/{slug}/edit', 'BookController@edit');
        Route::put('/{slug}', 'BookController@update');
        Route::delete('/{id}', 'BookController@destroy');
        Route::get('/{slug}/sort-item', 'BookController@getSortItem');
        Route::get('/{slug}', 'BookController@show');
        Route::get('/{bookSlug}/permissions', 'BookController@showPermissions');
        Route::put('/{bookSlug}/permissions', 'BookController@permissions');
        Route::get('/{slug}/delete', 'BookController@showDelete');
        Route::get('/{bookSlug}/sort', 'BookController@sort');
        Route::put('/{bookSlug}/sort', 'BookController@saveSort');
        Route::get('/{bookSlug}/export/html', 'BookController@exportHtml');
        Route::get('/{bookSlug}/export/pdf', 'BookController@exportPdf');
        Route::get('/{bookSlug}/export/plaintext', 'BookController@exportPlainText');

        // Pages
        Route::get('/{bookSlug}/create-page', 'PageController@create');
        Route::post('/{bookSlug}/create-guest-page', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/draft/{pageId}', 'PageController@editDraft');
        Route::post('/{bookSlug}/draft/{pageId}', 'PageController@store');
        Route::get('/{bookSlug}/page/{pageSlug}', 'PageController@show');
        Route::get('/{bookSlug}/page/{pageSlug}/export/pdf', 'PageController@exportPdf');
        Route::get('/{bookSlug}/page/{pageSlug}/export/html', 'PageController@exportHtml');
        Route::get('/{bookSlug}/page/{pageSlug}/export/plaintext', 'PageController@exportPlainText');
        Route::get('/{bookSlug}/page/{pageSlug}/edit', 'PageController@edit');
        Route::get('/{bookSlug}/page/{pageSlug}/move', 'PageController@showMove');
        Route::put('/{bookSlug}/page/{pageSlug}/move', 'PageController@move');
        Route::get('/{bookSlug}/page/{pageSlug}/copy', 'PageController@showCopy');
        Route::post('/{bookSlug}/page/{pageSlug}/copy', 'PageController@copy');
        Route::get('/{bookSlug}/page/{pageSlug}/delete', 'PageController@showDelete');
        Route::get('/{bookSlug}/draft/{pageId}/delete', 'PageController@showDeleteDraft');
        Route::get('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@showPermissions');
        Route::put('/{bookSlug}/page/{pageSlug}/permissions', 'PageController@permissions');
        Route::put('/{bookSlug}/page/{pageSlug}', 'PageController@update');
        Route::delete('/{bookSlug}/page/{pageSlug}', 'PageController@destroy');
        Route::delete('/{bookSlug}/draft/{pageId}', 'PageController@destroyDraft');

        // Revisions
        Route::get('/{bookSlug}/page/{pageSlug}/revisions', 'PageController@showRevisions');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}', 'PageController@showRevision');
        Route::get('/{bookSlug}/page/{pageSlug}/revisions/{revId}/changes', 'PageController@showRevisionChanges');
        Route::put('/{bookSlug}/page/{pageSlug}/revisions/{revId}/restore', 'PageController@restoreRevision');
        Route::delete('/{bookSlug}/page/{pageSlug}/revisions/{revId}/delete', 'PageController@destroyRevision');

        // Chapters
        Route::get('/{bookSlug}/chapter/{chapterSlug}/create-page', 'PageController@create');
        Route::post('/{bookSlug}/chapter/{chapterSlug}/create-guest-page', 'PageController@createAsGuest');
        Route::get('/{bookSlug}/create-chapter', 'ChapterController@create');
        Route::post('/{bookSlug}/create-chapter', 'ChapterController@store');
        Route::get('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@show');
        Route::put('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@update');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@showMove');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/move', 'ChapterController@move');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/edit', 'ChapterController@edit');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@showPermissions');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/pdf', 'ChapterController@exportPdf');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/html', 'ChapterController@exportHtml');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/export/plaintext', 'ChapterController@exportPlainText');
        Route::put('/{bookSlug}/chapter/{chapterSlug}/permissions', 'ChapterController@permissions');
        Route::get('/{bookSlug}/chapter/{chapterSlug}/delete', 'ChapterController@showDelete');
        Route::delete('/{bookSlug}/chapter/{chapterSlug}', 'ChapterController@destroy');
    });
});