<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        $oldEmail    = $request->oldEmail;
        
        $user = User::where('email', $oldEmail)->first();
        
        $defaultMessage = '正常に処理が行われませんでした。もう一度お試しください。';
        $successMessage = '変更したメールアドレスに確認メールを送信しました。認証すると新しいメールアドレスでログインが可能になります。';
        $errorMessage   = [
            'email'      => 'ユーザーが存在しません',
            'password'   => 'パスワードが違います。',
            'unverified' => 'まずはメールアドレスを認証してください。'
        ];
        
        $message = $defaultMessage;
        
        if (!$user) {
            $message = $errorMessage['email'];
        } elseif (!Hash::check($currentPass, $user->password)) {
            $message = $errorMessage['password'];
        } elseif ($user->email_verified_at === null) {
            $message = $errorMessage['unverified'];
        } else {
            $oldUserId = $user->id;
            $user->email = $newEmail;
            $this->user->createUser($user->toArray())->changeEmailVerificationNotification($oldUserId);
            $message = $successMessage;
        }

        return compact('message');
    }
}
