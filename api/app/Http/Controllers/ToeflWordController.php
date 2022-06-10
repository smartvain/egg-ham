<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ToeflWordController extends Controller
{
    public function getToeflWordList()
    {
        $res = $this->getUrlContent('https://uwl.weblio.jp/toefl-word-list');
        dd($res);
    }

    private function getUrlContent($url)
    {
        $client = new Client();
        
        return $client->requestAsync('GET', $url)
                    ->wait()
                    ->getBody();
    }
}
