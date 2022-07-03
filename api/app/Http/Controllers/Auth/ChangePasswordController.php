<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function __invoke(ChangePasswordRequest $request)
    {
        $currentPass = $request->currentPass;
        $newPass     = $request->newPass;
        $confirmPass = $request->newPass_confirmation;
        $messages    = $this->getMessages();
        $user        = $this->user->find($request->user()->id);
        
        if (!$user) {
            $status  = 'non_existent_user';
            $message = $messages['error'][$status];
        } elseif ($user->provider_id) {
            $status  = 'sns_logged';
            $message = $messages['error'][$status];
        } elseif (!Hash::check($currentPass, $user->password)) {
            $status  = 'mismatch_current_pass';
            $message = $messages['error'][$status];
        } elseif (Hash::check($newPass, $user->password)) {
            $status  = 'registered_pass';
            $message = $messages['error'][$status];
        } else {
            $this->user->replace($user->id, ['password' => Hash::make($newPass)]);
            $status  = 'success';
            $message = $messages[$status];
        }

        return compact('message', 'status');
    }

    private function getMessages()
    {
        return [
            'success' => 'パスワードの変更に成功しました。',
            'error'   => [
                'non_existent_user'     => 'ユーザーが存在しません。',
                'sns_logged'            => 'SNSログインしている場合はメールアドレス変更はできません。',
                'mismatch_current_pass' => '現在のパスワードが違います。',
                'registered_pass'       => '登録済みのパスワードです。'
            ]
        ];
    }
}
