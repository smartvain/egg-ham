<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangeNameRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function changeName(ChangeNameRequest $request)
    {
        $isSuccess = $this->user->replace($request->user()->id, $request->input());
        $messages  = $this->getMessages();
        
        if (!$isSuccess) {
            $status  = 'error';
            $message = $messages[$status];
        } else {
            $status  = 'success';
            $message = $messages[$status];
        }

        return compact('message', 'status');
    }

    private function getMessages()
    {
        return [
            'success' => '名前を変更しました。',
            'error'   => '名前の変更に失敗しました。時間をおいてもう一度お試し下さい。'
        ];
    }
}
