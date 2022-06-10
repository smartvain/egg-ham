<?php

namespace App\Http\Controllers;

use App\Models\ToeflWord;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ToeflWordController extends Controller
{
    public function getToeflWords()
    {
        return ToeflWord::get();
    }
    
    public function insertToeflWords()
    {
        $words = $this->fetchToeflWords();

        DB::transaction(function () use ($words) {
            foreach ($words as $word) {
                $existWord = ToeflWord::where('text', $word[2])->first();
                if ($existWord) {continue;}

                $toeflWord = new ToeflWord();
                $toeflWord->fill([
                    'text' => $word[2],
                    'url'  => $word[1]
                ])->save();
            }
        });
    }
    
    private function fetchToeflWords()
    {
        $res = $this->fetchUrlContent('https://uwl.weblio.jp/toefl-word-list');
        $table = $this->extractToeflWordTable($res);
        $words = $this->extractToeflWord(current(current($table)));

        return $words;
    }

    private function fetchUrlContent($url)
    {
        $client = new Client();
        
        return $client->requestAsync('GET', $url)
                    ->wait()
                    ->getBody()
                    ->getContents();
    }

    private function extractToeflWordTable($content)
    {
        $regex = '/<table>.*<\/table>/';
        preg_match_all($regex, $content, $matches);

        return $matches;
    }

    private function extractToeflWord($table)
    {
        $regex = '/<a title=".*?意味" href="([^"]*)"[^>]*>(.*?)<\/a>/';
        preg_match_all($regex, $table, $matches, PREG_SET_ORDER);

        return $matches;
    }
}
