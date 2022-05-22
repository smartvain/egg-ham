<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($userId, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["message" => "期限切れのURLです。"], 401);
        }
    
        $user = User::findOrFail($userId);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return redirect()->to('http://dev.yuleapp.com:1080/');
    }
    
    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        $defaultMessage = 'メールの再送信に失敗しました。もう一度お試しください。';
        $successMessage = '入力されたメールアドレスにメールを再送信しました。';
        $errorMessage = [
            'verified' => 'このメールアドレスはすでに確認済みです。'
        ];

        $message = $defaultMessage;

        if ($user->hasVerifiedEmail()) {
            $message = $errorMessage['verified'];
        } else {
            $user->sendEmailVerificationNotification();
            $message = $successMessage;
        }
    
        return compact('message');
    }
}
