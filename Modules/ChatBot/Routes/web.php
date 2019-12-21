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
        Route::get('/', 'KeywordController@index');
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
        Route::get('/', 'QuestionController@index');
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

});
