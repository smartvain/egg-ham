<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ChangeEmailController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\UserController;
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

Route::post('register', RegisterController::class);

Route::post('login', [LoginController::class, 'login']);
Route::post('token', [LoginController::class, 'refresh']);

Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend',      [VerificationController::class, 'resend'])->name('verification.resend');

Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset',  [ForgotPasswordController::class, 'resetPassword'     ])->name('password.reset');

Route::get('captions',  [YoutubeController::class, 'getCaptions' ]);
Route::get('langList',  [YoutubeController::class, 'getLangList' ]);
Route::get('videoInfo', [YoutubeController::class, 'getVideoInfo']);

Route::group(['middleware' => ['session'], 'prefix' => 'oauth'], function () {
    Route::get('{provider}/redirect', [LoginController::class, 'socialRedirect'   ]);
    Route::get('{provider}/callback', [LoginController::class, 'handleSnsCallback']);
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('user', [LoginController::class, 'user']);

    Route::put('user/name',     [UserController::class, 'changeName']);
    Route::put('user/email',    ChangeEmailController::class);
    Route::put('user/password', ChangePasswordController::class);
    
    Route::get('words',            [WordController::class, 'getWords'  ]);
    Route::post('word',            [WordController::class, 'storeWord' ]);
    Route::put('words',            [WordController::class, 'saveWords' ]);
    Route::delete('word/{wordId}', [WordController::class, 'removeWord']);
});

