<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function verify($userId, Request $request)
    {
        $user       = $this->user->find($userId);
        $isVerified = $user->hasVerifiedEmail();
        $messages   = $this->getVerifyMessages();
        $message    = $messages['default'];
        $status     = null;
        $token      = null;

        if (!$request->hasValidSignature()) {
            $status  = 'expired';
            $message = $messages['error'][$status];
        } elseif ($isVerified) {
            $status  = 'verified';
            $message = $messages['error'][$status];
        } elseif (!$isVerified) {
            $user->markEmailAsVerified();

            if ($this->isAfterChangingEmail($request)) {
                $oldUser = $this->user->find($request->oldUserId);
                $words   = $oldUser->words->each(function ($word) use ($user) {
                    $word->user_id = $user->id;
                });
                $word = new Word();
                $word->changeWordsInfo($words->toArray());
                $oldUser->delete();

                $token = $user->createToken('change_email')->plainTextToken;
            }

            $status  = 'success';
            $message = $messages[$status];
        }
        
        $redirectUrl = config('app.home_url') . '/oauth/email/verify';
        $param1      = "message={$message}";
        $param2      = "&status={$status}";
        $param3      = "&token={$token}";

        return redirect("{$redirectUrl}?{$param1}{$param2}{$param3}");
    }

    public function resend(Request $request)
    {
        $user     = $this->user->find($request->user()->id);
        $messages = $this->getResendMessages();

        if ($user->hasVerifiedEmail()) {
            $status  = 'verified';
            $message = $messages['error'][$status];
        } else {
            $user->sendEmailVerificationNotification();
            $status  = 'success';
            $message = $messages[$status];
        }
    
        return compact('message', 'status');
    }

    private function isAfterChangingEmail($request)
    {
        return $request->oldUserId;
    }

    private function getVerifyMessages()
    {
        return [
            'default' => 'メール認証に失敗しました。もう一度お試しください。',
            'success' => 'メール認証に成功しました。',
            'error'   => [
                'expired'  => '期限切れのURLです。',
                'verified' => 'このメールアドレスはすでに確認済みです。'
            ]
        ];
    }

    private function getResendMessages()
    {
        return [
            'success' => '入力されたメールアドレスに確認メールを再送信しました。',
            'error'   => [
                'verified' => 'このメールアドレスはすでに確認済みです。'
            ]
        ];
    }
}
