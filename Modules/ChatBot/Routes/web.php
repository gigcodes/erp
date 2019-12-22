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
    
    Route::prefix('keyword')->group(function () {
        Route::get('/', 'KeywordController@index')->name("chatbot.keyword.list");
        Route::post('/', 'KeywordController@save')->name("chatbot.keyword.save");
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
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', 'DialogController@edit')->name("chatbot.dialog.edit");
            Route::post('/edit', 'DialogController@update')->name("chatbot.dialog.update");
            Route::get('/delete', 'DialogController@destroy')->name("chatbot.dialog.delete");
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', 'DialogController@destroyValue')->name("chatbot.dialog-response.delete");
            });
        });
    });

});
