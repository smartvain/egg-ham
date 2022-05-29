<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        $defaultMessage = '正常にログインが行われませんでした。もう一度お試しください。';
        $successMessage = 'ログインに成功しました。';
        $errorMessage   = [
            'email'      => 'このメールアドレスは登録されていません。',
            'password'   => 'パスワードが違います。',
            'unverified' => 'まずはメールアドレスを認証してください。'
        ];

        $message = $defaultMessage;
        $token   = null;
        
        if (!$user) {
            $message = $errorMessage['email'];
        } else if (!Hash::check($request->password, $user->password)) {
            $message = $errorMessage['password'];
        } else if ($user->email_verified_at === null) {
            $message = $errorMessage['unverified'];
        } else {
            $message = $successMessage;
            $token   = $user->createToken('normal')->plainTextToken;
        }

        return [ 'token' => $token, 'message' => $message ];
    }

    public function user(Request $request)
    {
        return [ 'user' => $request->user() ];
    }

    public function handleCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::firstOrCreate(['email' => $socialUser->getEmail()], [
            'name' => $socialUser->getName(),
            'avatar' => $socialUser->getAvatar(),
            'provider_id' => $socialUser->getId(),
            'provider_name' => $provider
        ]);

        $user->markEmailAsVerified();

        $token = $user->createToken('social')->plainTextToken;

        return [
            'token'   => $token,
            'message' => "${provider}でのログインに成功しました。"
        ];
    }
}
