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

Route::prefix('chatbot')->middleware('auth')->group(function () {
    Route::get('/', 'ChatBotController@index');
    Route::post('/edit-message', 'ChatBotController@editMessage')->name("chatbot.edit.message");
    
    Route::prefix('keyword')->group(function () {
        Route::get('/', 'KeywordController@index')->name("chatbot.keyword.list");
        Route::post('/', 'KeywordController@save')->name("chatbot.keyword.save");
        Route::post('/submit', 'KeywordController@saveAjax')->name("chatbot.keyword.saveAjax");
        Route::get('/search', 'KeywordController@search')->name("chatbot.keyword.search");
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', 'KeywordController@edit')->name("chatbot.keyword.edit");
            Route::post('/edit', 'KeywordController@update')->name("chatbot.keyword.update");
            Route::get('/delete', 'KeywordController@destroy')->name("chatbot.keyword.delete");
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', 'KeywordController@destroyValue')->name("chatbot.value.delete");
            });
        });
    });

    Route::prefix('question')->group(function () {
        Route::get('/', 'QuestionController@index')->name("chatbot.question.list");
        Route::post('/', 'QuestionController@save')->name("chatbot.question.save");
        Route::post('/submit', 'QuestionController@saveAjax')->name("chatbot.question.saveAjax");
        Route::get('/search', 'QuestionController@search')->name("chatbot.question.search");
        Route::get('/search-category', 'QuestionController@searchCategory')->name("chatbot.question.search-category");
        Route::post('/change-category', 'QuestionController@changeCategory')->name("chatbot.question.change-category");

        Route::prefix('annotation')->group(function () {
            Route::post('/save', 'QuestionController@saveAnnotation')->name("chatbot.question.annotation.save");
            Route::get('/delete', 'QuestionController@deleteAnnotation')->name("chatbot.question.annotation.delete");
            
        });
            
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', 'QuestionController@edit')->name("chatbot.question.edit");
            Route::post('/edit', 'QuestionController@update')->name("chatbot.question.update");
            Route::get('/delete', 'QuestionController@destroy')->name("chatbot.question.delete");
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', 'QuestionController@destroyValue')->name("chatbot.question-example.delete");
            });
        });
    });

    Route::prefix('dialog')->group(function () {
        Route::get('/', 'DialogController@index')->name("chatbot.dialog.list");
        Route::post('/', 'DialogController@save')->name("chatbot.dialog.save");
        Route::get('/search', 'DialogController@search')->name("chatbot.dialog.search");
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', 'DialogController@edit')->name("chatbot.dialog.edit");
            Route::post('/edit', 'DialogController@update')->name("chatbot.dialog.update");
            Route::get('/delete', 'DialogController@destroy')->name("chatbot.dialog.delete");
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', 'DialogController@destroyValue')->name("chatbot.dialog-response.delete");
            });
        });
        Route::get('/log', 'DialogController@log')->name("chatbot.dialog.log");

        // store dialog via save response
        Route::post("dialog-save","DialogController@saveAjax")->name("chatbot.dialog.saveajax");

    });

    Route::prefix('analytics')->group(function () {
        Route::get('/', 'AnalyticsController@index')->name("chatbot.analytics.list");
    });

    Route::prefix('messages')->group(function () {
        Route::get('/', 'MessageController@index')->name("chatbot.messages.list");
        Route::post('/approve', 'MessageController@approve')->name("chatbot.messages.approve");
        Route::post('/remove-images', 'MessageController@removeImages')->name("chatbot.messages.remove-images");
        Route::get('/attach-images', 'MessageController@attachImages')->name("chatbot.messages.attach-images");
        Route::post('/forward-images', 'MessageController@forwardToCustomer')->name("chatbot.messages.forward-images");
    });

    Route::prefix('rest/dialog')->group(function () {
        Route::get('/create', 'DialogController@restCreate')->name("chatbot.rest.dialog.create");
        Route::post('/create', 'DialogController@restCreate')->name("chatbot.rest.dialog.create");
        Route::get('/status', 'DialogController@restStatus')->name("chatbot.rest.dialog.status");
        Route::prefix('{id}')->group(function () {
            Route::get('/', 'DialogController@restDetails')->name("chatbot.rest.dialog.detail");
            Route::get('/delete', 'DialogController@restDelete')->name("chatbot.rest.dialog.delete");
        });
    });

});
