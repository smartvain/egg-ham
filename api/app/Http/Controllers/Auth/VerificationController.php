<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function verify($userId, Request $request)
    {
        $user = $this->user->find($userId);

        $messages = $this->getVerifyMessages();
        $message  = $messages['default'];
        $token    = null;

        $isVerified = $user->hasVerifiedEmail();
        if (!$request->hasValidSignature()) {
            $message = $messages['error']['expired'];
        } elseif ($isVerified) {
            $message = $messages['error']['verified'];
        } elseif (!$isVerified) {
            $user->markEmailAsVerified();
            if ($request->oldUserId) {
                $token = $user->createToken('change_email')->plainTextToken;
                $this->user->find($request->oldUserId)->delete();
            }
            $message = $messages['success'];
        }
        
        $redirectUrl = config('app.home_url') . '/oauth/email/verify';
        $param1 = "message={$message}";
        $param2 = "&token={$token}";

        return redirect("{$redirectUrl}?{$param1}{$param2}");
    }
    
    public function resend(Request $request)
    {
        $user = $this->user->where('email', $request->email)->first();
        
        $defaultMessage = '確認メールの再送信に失敗しました。もう一度お試しください。';
        $successMessage = '入力されたメールアドレスに確認メールを再送信しました。';
        $errorMessage   = [
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

    private function getVerifyMessages()
    {
        return [
            'default' => 'メール認証に失敗しました。もう一度お試しください。',
            'success' => 'メール認証に成功しました。',
            'error'   => [
                'verified' => 'このメールアドレスはすでに確認済みです。',
                'expired'  => '期限切れのURLです。'
            ]
        ];
    }
}
