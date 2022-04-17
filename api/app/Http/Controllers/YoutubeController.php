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
        $client = new Google_Client();
        $client->setScopes('https://www.googleapis.com/auth/youtubepartner');
        $client->setDeveloperKey(config('google_api_key'));

        $this->youtube = new Google_Service_YouTube($client);
    }

    public function getLangList(Request $request)
    {
        $videoId = $request->get('videoId');
        
        $res = $this->getUrlContent("https://www.youtube.com/watch?v={$videoId}");

        $captionTracks = $this->extractCaptionTrack($res);
        
        $langList = collect();
        foreach ($captionTracks as $captionTrack) {
            $langList->push([
                'text' => $captionTrack->name->simpleText,
                'value' => substr($captionTrack->vssId, (strpos($captionTrack->vssId, '.') + strlen('.')))
            ]);
        }
        
        return $langList;
    }

    public function getTranscript(Request $request)
    {
        $videoId = $request->get('videoId');
        $lang    = $request->get('lang');
        
        $res = $this->getUrlContent("https://www.youtube.com/watch?v={$videoId}");
        
        $captionTracks = $this->extractCaptionTrack($res);

        // captionTracksから選択した言語のcaptionTrackだけを抽出する。
        $captionTrack = array_filter($captionTracks, function ($captionTrack) use ($lang) {
            switch ($captionTrack->vssId) {
                case "a.{$lang}": return true; break;
                case ".{$lang}" : return true; break;
                default         : return false;
            }
        });
        $captionTrack = current($captionTrack);

        // baseUrlからtranscriptを取得する。
        $res = $this->getUrlContent($captionTrack->baseUrl);
        
        // レスポンスから不必要な情報を切り取る。
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

        return $transcripts;
    }

    private function getUrlContent($url)
    {
        $client = new Client();
        
        return  $client->requestAsync('GET', $url)
                        ->wait()
                        ->getBody()
                        ->getContents();
    }

    private function extractCaptionTrack($content)
    {
        $regex = '/({"captionTracks":.*isTranslatable":(true|false)}])/';
        preg_match($regex, $content, $matches);
        
        return json_decode("{$matches[0]}}")->captionTracks;
    }
}
