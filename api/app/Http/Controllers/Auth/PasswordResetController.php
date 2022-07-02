<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function __invoke(PasswordResetRequest $request)
    {
        $messages = $this->getMessages();
        $response = Password::sendResetLink($request->only('email'));
        
        if ($response !== Password::RESET_LINK_SENT) {
            $status  = 'error';
            $message = $messages[$status];
        } else {
            $status  = 'success';
            $message = $messages[$status];
        }

        return compact('message', 'status');
    }

    private function getMessages()
    {
        return [
            'success' => 'パスワード再設定メールを送信しました。',
            'error'   => 'パスワード再設定メールの送信に失敗しました。'
        ];
    }
}
