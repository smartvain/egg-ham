<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeEmailController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function __invoke(ChangeEmailRequest $request)
    {
        $currentPass = $request->currentPass;
        $newEmail    = $request->newEmail;
        $messages    = $this->getMessages();
        $user        = $this->user->find($request->user()->id);
        
        if (!$user) {
            $status  = 'non_existent_user';
            $message = $messages['error'][$status];
        } elseif ($user->email === $newEmail) {
            $status  = 'registered_email';
            $message = $messages['error'][$status];
        } elseif (!Hash::check($currentPass, $user->password)) {
            $status  = 'mismatch_password';
            $message = $messages['error'][$status];
        } elseif (!$user->hasVerifiedEmail()) {
            $status  = 'unverified_email';
            $message = $messages['error'][$status];
        } else {
            $user->email = $newEmail;
            $this->user->store($user->toArray())->changeEmailVerificationNotification($user->id);
            $status  = 'success';
            $message = $messages[$status];
        }

        return compact('message', 'status');
    }

    private function getMessages()
    {
        return [
            'success' => '変更したメールアドレスに確認メールを送信しました。',
            'error'   => [
                'non_existent_user' => 'ユーザーが存在しません。',
                'registered_email'  => 'すでに登録されているメールアドレスです。',
                'mismatch_password' => 'パスワードが違います。',
                'unverified_email'  => '先にメールアドレスを認証してください。'
            ]
        ];
    }
}
