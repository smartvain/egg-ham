<?php

use App\Http\Controllers\DeepLController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\YoutubeController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('captions', [YoutubeController::class, 'getCaptions']);
Route::get('langList', [YoutubeController::class, 'getLangList']);

Route::post('translate', [DeepLController::class, 'translate']);
Route::post('character_count', [DeepLController::class, 'getCharacterCount']);

Route::get('words', [WordController::class, 'getWords']);
Route::post('words', [WordController::class, 'storeWords']);