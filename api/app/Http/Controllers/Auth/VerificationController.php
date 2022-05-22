<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($userId, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["message" => "期限切れのURLです。"], 401);
        }
    
        $user = User::findOrFail($userId);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return redirect()->to('http://dev.yuleapp.com:1080/');
    }
    
    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user->hasVerifiedEmail()) {
            return response()->json(["message" => "このメールアドレスはすでに確認済みです。"], 400);
        }
    
        $user->sendEmailVerificationNotification();
    
        return response()->json(["message" => "入力されたメールアドレスにメールを再送信しました。"]);
    }
}
