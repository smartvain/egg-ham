<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DeepLController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [LoginController::class, 'login']);

Route::get('captions', [YoutubeController::class, 'getCaptions']);
Route::get('langList', [YoutubeController::class, 'getLangList']);
Route::get('videoInfo', [YoutubeController::class, 'getVideoInfo']);

Route::post('translate', [DeepLController::class, 'translate']);
Route::post('character_count', [DeepLController::class, 'getCharacterCount']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user', [LoginController::class, 'user']);
    
    Route::get('words', [WordController::class, 'getWords']);
    Route::post('words', [WordController::class, 'storeWords']);
    Route::delete('word/{wordId}', [WordController::class, 'deleteWord']);
});

