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
        $user     = User::where('email', $email)->first();
        $messages = $this->getLoginMessages();
        $token    = null;
        
        if (!$user) {
            $status  = 'non_existent_email';
            $message = $messages['error'][$status];
        } elseif (!Hash::check($password, $user->password)) {
            $status  = 'mismatch_password';
            $message = $messages['error'][$status];
        } elseif ($user->email_verified_at === null) {
            $status  = 'unverified';
            $message = $messages['error'][$status];
        } else {
            $token   = $user->createToken('normal')->plainTextToken;
            $status  = 'success';
            $message = $messages['success'];
        }

        return compact('token', 'message', 'status');
    }

    public function handleGoogleCallback()
    {
        $provider   = 'google';
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $messages   = $this->getGoogleLoginMessages();
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
            'message' => $messages['success']
        ];
    }

    public function handleTwitterCallback()
    {
        $provider   = 'twitter';
        $socialUser = Socialite::driver($provider)->user();
        $messages   = $this->getTwitterLoginMessages();
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
            'message' => $messages['success']
        ];
    }

    public function user(Request $request)
    {
        return [ 'user' => $request->user() ];
    }

    public function socialRedirect($provider)
    {
        return Socialite::driver($provider)->redirect()->getTargetUrl();
    }

    private function getLoginMessages()
    {
        return [
            'success' => 'ログインしました。',
            'error'   => [
                'email'      => 'このメールアドレスは登録されていません。',
                'password'   => 'パスワードが違います。',
                'unverified' => '先にメールアドレスを認証してください。'
            ]
        ];
    }

    private function getGoogleLoginMessages()
    {
        return [
            'success' => 'Googleでのログインに成功しました。',
            'error'   => 'Google認証中にエラーが発生しました。時間をおいてもう一度お試し下さい。'
        ];
    }

    private function getTwitterLoginMessages()
    {
        return [
            'success' => 'Twitterでのログインに成功しました。',
            'error'   => 'Twitter認証中にエラーが発生しました。時間をおいてもう一度お試し下さい。'
        ];
    }
}
