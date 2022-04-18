<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DeepLController extends Controller
{
    public function translate(Request $request)
    {
        $options = [
            'form_params' => [
                'auth_key'    => config('deepl.deepl_auth_key'),
                'text'        => implode('', $request->get('transcript')),
                'target_lang' => $request->get('lang')
            ]
        ];
        
        $client = new Client();
        
        return json_decode($client->requestAsync('POST', 'https://api-free.deepl.com/v2/translate', $options)
                                  ->wait()
                                  ->getBody());
    }
}
