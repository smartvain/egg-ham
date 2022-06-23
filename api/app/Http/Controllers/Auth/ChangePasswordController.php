<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function changePassword(Request $request)
    {
        $currentPass = $request->currentPass;
        $newPass     = $request->newPass;
        $confirmPass = $request->confirmPass;
        $messages    = $this->getMessages();
        $user        = $this->user->find($request->user()->id);
        
        if (!$user) {
            $status  = 'non_existent_user';
            $message = $messages['error'][$status];
        } elseif (!Hash::check($currentPass, $user->password)) {
            $status  = 'mismatch_current_pass';
            $message = $messages['error'][$status];
        } elseif ($newPass !== $confirmPass) {
            $status  = 'mismatch_confirm_pass';
            $message = $messages['error'][$status];
        } else {
            $this->user->changeUserInfo($user->id, ['password' => Hash::make($newPass)]);
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
                'mismatch_current_pass' => '現在のパスワードが違います。',
                'mismatch_confirm_pass' => '確認パスワードが合いません。'
            ]
        ];
    }
}
