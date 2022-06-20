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
        $email       = $request->user()->email;
        
        $user = User::where('email', $email)->first();
        
        $defaultMessage = '正常に処理が行われませんでした。もう一度お試しください。';
        $successMessage = 'パスワードの変更に成功しました。';
        $errorMessage   = [
            'email'       => 'ユーザーが存在しません。',
            'currentPass' => '現在のパスワードが違います。',
            'confirmPass' => '確認パスワードが違います。',
        ];
        
        $message = $defaultMessage;
        
        if (!$user) {
            $message = $errorMessage['email'];
            $isSuccess = false;
        } elseif (!Hash::check($currentPass, $user->password)) {
            $message = $errorMessage['currentPass'];
            $isSuccess = false;
        } elseif ($newPass !== $confirmPass) {
            $message = $errorMessage['confirmPass'];
            $isSuccess = false;
        } else {
            $this->user->changeUserInfo($user->id, ['password' => Hash::make($newPass)]);
            $message = $successMessage;
            $isSuccess = true;
        }

        return ['message' => $message, 'isSuccess' => $isSuccess];
    }
}
