<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ChatBot\Http\Controllers\MessageController;
use Modules\ChatBot\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/chatbot', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'chatbot'], function () {
    Route::post('bot-reply', [QuestionController::class, 'botReply'])->name('chatbot-api.reply');
});
Route::Post('/chat-bot/send-suggested-replay', [MessageController::class, 'sendSuggestedMessage'])->name('chatbot.send.suggested.message');
