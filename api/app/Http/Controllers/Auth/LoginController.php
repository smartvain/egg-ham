<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Socialite;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $email    = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();
        
        $successMessage = 'ログインしました。';
        $errorMessage   = [
            'email'      => 'このメールアドレスは登録されていません。',
            'password'   => 'パスワードが違います。',
            'unverified' => '先にメールアドレスを認証してください。'
        ];

        $token = null;
        
        if (!$user) {
            $message = $errorMessage['email'];
        } elseif (!Hash::check($password, $user->password)) {
            $message = $errorMessage['password'];
        } elseif ($user->email_verified_at === null) {
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

    public function socialRedirect($provider)
    {
        return Socialite::driver($provider)->redirect()->getTargetUrl();
    }

    public function handleGoogleCallback()
    {
        $provider = 'google';
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::firstOrCreate(['email' => $socialUser->getEmail()], [
            'name'          => $socialUser->getName(),
            'avatar'        => $socialUser->getAvatar(),
            'provider_id'   => $socialUser->getId(),
            'provider_name' => $provider
        ]);

        $user->markEmailAsVerified();

        $token = $user->createToken($provider)->plainTextToken;

        return [
            'token'   => $token,
            'message' => 'Googleでのログインに成功しました。'
        ];
    }

    public function handleTwitterCallback()
    {
        $provider = 'twitter';
        $socialUser = Socialite::driver($provider)->user();
        $user = User::firstOrCreate(['email' => $socialUser->getEmail()], [
            'name'          => $socialUser->getName(),
            'avatar'        => $socialUser->getAvatar(),
            'provider_id'   => $socialUser->getId(),
            'provider_name' => $provider
        ]);

        $user->markEmailAsVerified();

        $token = $user->createToken($provider)->plainTextToken;

        return [
            'token'   => $token,
            'message' => 'Twitterでのログインに成功しました。'
        ];
    }
}
