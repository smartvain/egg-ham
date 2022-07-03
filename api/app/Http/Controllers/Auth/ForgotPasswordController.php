<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendResetLinkEmailRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(SendResetLinkEmailRequest $request)
    {
        $messages = $this->getSendResetLinkMessages();
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

    public function resetPassword(ResetPasswordRequest $request)
    {
        $messages    = $this->getResetPasswordMessages();
        $credentials = $request->only(['email', 'token', 'password']);

        $response = Password::reset($credentials, function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response !== Password::PASSWORD_RESET) {
            $status  = 'error';
            $message = $messages[$status];
        } else {
            $status  = 'success';
            $message = $messages[$status];
        }

        return compact('message', 'status');
    }

    private function getSendResetLinkMessages()
    {
        return [
            'success' => 'パスワード再設定メールを送信しました。',
            'error'   => 'パスワード再設定メールの送信に失敗しました。'
        ];
    }

    private function getResetPasswordMessages()
    {
        return [
            'success' => 'パスワードをリセットしました。',
            'error'   => 'パスワードリセットに失敗しました。'
        ];
    }
}
