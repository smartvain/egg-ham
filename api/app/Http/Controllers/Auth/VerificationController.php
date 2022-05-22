<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($userId, Request $request)
    {
        $user = User::find($userId);
        $path = config('app.home_url');

        $defaultMessage = 'メール認証に失敗しました。もう一度お試しください。';
        $successMessage = 'メール認証しました。';
        $errorMessage = [
            'expired' => '期限切れのURLです。'
        ];

        $message = $defaultMessage;

        if (!$request->hasValidSignature()) {
            $message = $errorMessage['expired'];
            return response()->json(compact('message'), 401);
        } else if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return redirect($path)->with('message', $message);
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
