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
        
        $successMessage = '入力されたメールアドレスに確認メールを送信しました。';
        $errorMessage   = [
            'registered' => 'このメールアドレスはすでに登録されています。確認メールを再送信しました。',
            'mismatch'   => 'パスワードが一致しませんでした。'
        ];
        
        if ($form['password'] !== $form['confirm']) {
            $message = $errorMessage['mismatch'];
        } elseif ($user) {
            $user->sendEmailVerificationNotification();
            $message = $errorMessage['registered'];
        } else {
            $message          = $successMessage;
            $form['password'] = Hash::make($form['password']);
            $this->user->createUser($form)->sendEmailVerificationNotification();
        }
        
        return compact('message');
    }
}
