<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function getUsers()
    {
        return User::get();
    }

    public function changeName(Request $request)
    {
        $isSuccess = $this->user->changeUserInfo($request->user()->id, $request->input());
        $message = $isSuccess ? '名前を変更しました。' : '名前の変更に失敗しました。';
        return compact('message');
    }
}
