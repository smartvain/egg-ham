<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_YouTube;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function __construct()
    {
        // Googleへの接続情報のインスタンスを作成と設定
        $client = new Google_Client();
        $client->setScopes('https://www.googleapis.com/auth/youtubepartner');
        // $client->setAccessToken(env('OAUTH2_TOKEN'));
        $client->setDeveloperKey(env('GOOGLE_API_KEY'));

        // 接続情報のインスタンスを用いてYoutubeのデータへアクセス可能なインスタンスを生成
        $this->youtube = new Google_Service_YouTube($client);
    }

    public function getTranscript(Request $request)
    {
        // $videoId = 'moNnVg-BQyE'; // 字幕ありサンプル
        // $videoId = '8DvywoWv6fI'; // 字幕なしサンプル
        $videoId = $request->get('videoId');
        $lang = $request->get('language');
        
        $url = "https://www.youtube.com/watch?v={$videoId}";

        $client = new Client();
        
        // 動画情報取得。
        $res = $client->requestAsync('GET', $url)
                      ->wait()
                      ->getBody()
                      ->getContents();
                      
        // レスポンスからcaptionTracksの項目だけ抽出する。
        $regex = '/({"captionTracks":.*isTranslatable":(true|false)}])/';
        preg_match($regex, $res, $matches);
        dd($matches);
        $captionTracks = json_decode("{$matches[0]}}")->captionTracks;

        // captionTracksから選択した言語のcaptionTrackだけを抽出する。
        $captionTrack = array_filter($captionTracks, function ($captionTrack) use ($lang) {
            switch ($captionTrack->vssId) {
                case ".{$lang}" : return true; break;
                case "a.{$lang}": return true; break;
                default         : return false;
            }
        });
        $captionTrack = current($captionTrack);

        // baseUrlからtranscriptを取得する。
        $res = $client->requestAsync('GET', $captionTrack->baseUrl)
                      ->wait()
                      ->getBody()
                      ->getContents();
        
        // レスポンスから必要な情報以外を切り取る。
        $res = str_replace('<?xml version="1.0" encoding="utf-8" ?><transcript>', '', $res);
        $res = str_replace('</transcript>', '', $res);
        $transcripts = explode('</text>', $res);
        
        $transcriptsIndex = 0;
        foreach ($transcripts as $transcript) {
            $transcript = trim($transcript);

            // 字幕の開始時間を取得。
            // $startRegex = '/start="([\d.]+)"/';
            // $durRegex = '/dur="([\d.]+)"/';
            // preg_match($startRegex, $transcript, $start);
            // preg_match($durRegex, $transcript, $dur);

            $transcript = preg_replace('/<text.+>/', '', $transcript);
            $transcript = preg_replace('/&amp;/i', '&', $transcript);
            $transcript = preg_replace('/&#39;/i', "'", $transcript);
            $transcript = preg_replace('/<\/?[^>]+(>|$)/', '', $transcript);
            $transcripts = array_replace($transcripts, [$transcriptsIndex => $transcript]);
            $transcriptsIndex++;
        }

        return ['transcripts' => $transcripts];
    }
}
