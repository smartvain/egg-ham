<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function __invoke(RegisterRequest $request)
    {
        $form     = $request->all();
        $user     = $this->user->where('email', $form['email'])->first();
        $messages = $this->getMessages();
        
        if ($user) {
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
                'registered' => "このメールアドレスはすでに登録されています。\r\n確認メールを再送信しますか？",
            ]
        ];
    }
}
