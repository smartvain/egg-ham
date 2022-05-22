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

    public function register(Request $request)
    {
        $form = $request->all();
        $user = $this->user->where('email', $form['email'])->first();
        
        $defaultMessage = 'アカウント作成に失敗しました。もう一度お試しください。';
        $successMessage = '入力されたメールアドレスに確認メールを送信しました。';
        $errorMessage = [
            'registered' => 'このメールアドレスはすでに登録されています。',
            'mismatch' => 'パスワードが一致しませんでした。'
        ];
        
        $message = $defaultMessage;

        if ($user) {
            $message = $errorMessage['registered'];
        } else if ($form['password'] !== $form['confirm']) {
            $message = $errorMessage['mismatch'];
        } else {
            $message = $successMessage;
            $form['password'] = Hash::make($form['password']);
            $newUser = $this->user->createUser($form)->sendEmailVerificationNotification();
        }
        
        return compact('message');
    }
}
