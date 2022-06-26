<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DeepLController extends Controller
{
    private $deeplAuthKey;

    public function __construct()
    {
        $this->deeplAuthKey = config('deepl.deepl_auth_key');
    }

    public function translate(Request $request)
    {
        return $this->fetchJsonContent('https://api-free.deepl.com/v2/translate', [
            'form_params' => [
                'auth_key'    => $this->deeplAuthKey,
                'text'        => $request->text,
                'target_lang' => $request->lang
            ]
        ]);
    }

    public function getCharacterCount()
    {
        return $this->fetchJsonContent('https://api-free.deepl.com/v2/usage', [
            'form_params' => [
                'auth_key' => $this->deeplAuthKey,
            ]
        ]);
    }

    private function fetchJsonContent($url, $options)
    {
        $client = new Client();
        
        return json_decode($client->requestAsync('POST', $url, $options)
            ->wait()
            ->getBody());
    }
}
