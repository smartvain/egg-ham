<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangeEmailController extends Controller
{
    private $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function changeEmail(Request $request)
    {
        $currentPass = $request->currentPass;
        $newEmail    = $request->newEmail;
        
        $user = $this->user->find($request->user()->id);
        
        $successMessage = '変更したメールアドレスに確認メールを送信しました。';
        $errorMessage   = [
            'not_exist'  => 'ユーザーが存在しません。',
            'exist'      => 'すでに登録されているメールアドレスです。',
            'password'   => 'パスワードが違います。',
            'unverified' => '先にメールアドレスを認証してください。'
        ];
        
        if (!$user) {
            $message = $errorMessage['not_exist'];
        } elseif ($user->email === $newEmail) {
            $message = $errorMessage['exist'];
        } elseif (!Hash::check($currentPass, $user->password)) {
            $message = $errorMessage['password'];
        } elseif (!$user->hasVerifiedEmail()) {
            $message = $errorMessage['unverified'];
        } else {
            $user->email = $newEmail;
            $this->user->createUser($user->toArray())->changeEmailVerificationNotification($user->id);
            $message = $successMessage;
        }

        return compact('message');
    }
}
