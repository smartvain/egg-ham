<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function __invoke(Request $request)
    {
        $form     = $request->all();
        $user     = $this->user->where('email', $form['email'])->first();
        $messages = $this->getMessages();
        
        if ($form['password'] !== $form['confirm']) {
            $status  = 'mismatch_confirm_pass';
            $message = $messages['error'][$status];
        } elseif ($user) {
            $user->sendEmailVerificationNotification();
            $status  = 'registered';
            $message = $messages['error'][$status];
        } else {
            $form['password'] = Hash::make($form['password']);
            $this->user->store($form)->sendEmailVerificationNotification();
            $status  = 'success';
            $message = $messages[$status];
        }
        
        return compact('message', 'status');
    }

    private function getMessages()
    {
        return [
            'success' => '入力されたメールアドレスに確認メールを送信しました。',
            'error'   => [
                'mismatch_confirm_pass' => '確認パスワードが一致しませんでした。',
                'registered'            => 'このメールアドレスはすでに登録されています。確認メールを再送信しました。',
            ]
        ];
    }
}
