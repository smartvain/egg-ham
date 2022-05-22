<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $email          = $request->email;
        $password       = $request->password;
        $user           = User::where('email', $email)->first();
        
        $defaultMessage = '正常にログインが行われませんでした。もう一度お試しください。';
        $successMessage = 'ログインに成功しました。';
        $errorMessage   = [
            'email'      => 'このメールアドレスは登録されていません。',
            'password'   => 'パスワードが違います。',
            'unverified' => 'まずはメールアドレスを認証してください。'
        ];

        $responseMessage = $defaultMessage;
        $token           = null;
        
        if (!$user) {
            $responseMessage = $errorMessage['email'];
        } else if (!Hash::check($password, $user->password)) {
            $responseMessage = $errorMessage['password'];
        } else if ($user->email_verified_at === null) {
            $responseMessage = $errorMessage['unverified'];
        } else {
            $responseMessage = $successMessage;
            $token           = $user->createToken('token')->plainTextToken;
        }

        return [ 'token' => $token, 'message' => $responseMessage ];
    }

    public function user(Request $request)
    {
        return [ 'user' => $request->user() ];
    }
}
