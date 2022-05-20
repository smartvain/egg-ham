<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();
        $errorMessage = [
            'email' => 'このメールアドレスは登録されていません。',
            'password' => 'パスワードが違います。'
        ];

        if (!$user) {
            throw ValidationException::withMessages([$errorMessage['email']]);
        } else if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([$errorMessage['password']]);
        }

        $token = $user->createToken('token')->plainTextToken;

        return compact('token');
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return compact('user');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}
