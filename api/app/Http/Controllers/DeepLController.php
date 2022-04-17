<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DeepLController extends Controller
{
    public function __construct()
    {
        //
    }

    public function connectDeepL(Request $request)
    {
        
        $param = array(
            'auth_key' => config('deepl_auth_key'), // 認証キーをここに設定
            'text' => $request->get('transcript'),
            'target_lang' => $request->get('lang')
        );
    }
}
