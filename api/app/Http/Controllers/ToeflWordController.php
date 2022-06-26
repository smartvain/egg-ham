<?php

namespace App\Http\Controllers;

use App\Models\ToeflWord;
use GuzzleHttp\Client;

class ToeflWordController extends Controller
{
    private $toeflWord;
    
    public function __construct(ToeflWord $toeflWord)
    {
        $this->toeflWord = $toeflWord;
    }

    public function __invoke()
    {
        $client = new Client();
        $res    = $client->requestAsync('GET', 'https://uwl.weblio.jp/toefl-word-list')
            ->wait()
            ->getBody()
            ->getContents();

        $el    = $this->extractToeflWordsElement($res);
        $words = $this->extractToeflWords(current(current($el)));
        
        $this->toeflWord->store($words);
    }
    
    private function extractToeflWordsElement($content)
    {
        $regex = '/<table>.*<\/table>/';
        preg_match_all($regex, $content, $matches);
        return $matches;
    }

    private function extractToeflWords($el)
    {
        $regex = '/<a title=".*?意味" href="[^"]*"[^>]*>(.*?)<\/a>/';
        preg_match_all($regex, $el, $matches, PREG_SET_ORDER);
        return $matches;
    }
}
