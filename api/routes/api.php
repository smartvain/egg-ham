<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
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

Route::post('login',            [LoginController::class,        'login'   ]);
Route::post('register',         [RegisterController::class,     'register']);
Route::get('email/verify/{id}', [VerificationController::class, 'verify'  ])->name('verification.verify');
Route::get('email/resend',      [VerificationController::class, 'resend'  ])->name('verification.resend');

Route::get('captions',  [YoutubeController::class, 'getCaptions' ]);
Route::get('langList',  [YoutubeController::class, 'getLangList' ]);
Route::get('videoInfo', [YoutubeController::class, 'getVideoInfo']);

Route::post('translate',       [DeepLController::class, 'translate'        ]);
Route::post('character_count', [DeepLController::class, 'getCharacterCount']);

Route::group(['middleware' => ['session'], 'prefix' => 'oauth'], function () {
    Route::get('{provider}/redirect', [LoginController::class, 'socialRedirect'       ]);
    Route::get('google/callback',     [LoginController::class, 'handleGoogleCallback' ]);
    Route::get('twitter/callback',    [LoginController::class, 'handleTwitterCallback']);
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('user', [LoginController::class, 'user']);
    
    Route::get('words',            [WordController::class, 'getWords'  ]);
    Route::put('words',            [WordController::class, 'saveWords' ]);
    Route::post('words',           [WordController::class, 'storeWords']);
    Route::delete('word/{wordId}', [WordController::class, 'deleteWord']);
});

