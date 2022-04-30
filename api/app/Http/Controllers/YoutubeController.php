<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function getLangList(Request $request)
    {
        $videoId = $request->get('videoId');
        
        $res = $this->getUrlContent("https://www.youtube.com/watch?v={$videoId}");

        $captionTracks = $this->extractCaptionTrack($res);
        
        $langList = collect();
        foreach ($captionTracks as $captionTrack) {
            $langList->push([
                'text'  => $captionTrack->name->simpleText,
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

        $captionTrack = array_filter($captionTracks, function ($captionTrack) use ($lang) {
            switch ($captionTrack->vssId) {
                case ".{$lang}" : return true; break;
                case "a.{$lang}": return true; break;
                default         : return false;
            }
        });
        $captionTrack = current($captionTrack);

        $res = $this->getUrlContent($captionTrack->baseUrl);
        
        $res = str_replace('<?xml version="1.0" encoding="utf-8" ?><transcript>', '', $res);
        $res = str_replace('</transcript>', '', $res);
        $transcripts = explode('</text>', $res);
        
        $transcriptsIndex = 0;
        foreach ($transcripts as $transcript) {
            $transcript = trim($transcript);
            $transcript = preg_replace('/<text.+>/', '', $transcript);
            $transcript = preg_replace('/&amp;/i', '&', $transcript);
            $transcript = preg_replace('/&#39;/i', "'", $transcript);
            $transcript = preg_replace('/<\/?[^>]+(>|$)/', '', $transcript);
            
            $transcripts = array_replace($transcripts, [$transcriptsIndex => $transcript]);
            
            $transcriptsIndex++;
        }

        $hiraText = $this->convertToHira(implode($transcripts));

        return ['transcript' => $transcripts, 'hiraText' => $hiraText];
    }

    private function getUrlContent($url)
    {
        $client = new Client();
        
        return $client->requestAsync('GET', $url)
                      ->wait()
                      ->getBody();
    }

    private function extractCaptionTrack($content)
    {
        $regex = '/({"captionTracks":.*isTranslatable":(true|false)}])/';
        preg_match($regex, $content, $matches);
        
        return json_decode("{$matches[0]}}")->captionTracks;
    }

    public function convertToHira($sentence)
    {
        $options = [
            'form_params' => [
                'appid'    => config('yahoo.yahoo_app_id'),
                'sentence' => $sentence,
                'results'  => 'ma'
            ]
        ];
        
        $client = new Client();

        $xml = $client->requestAsync('POST', 'https://jlp.yahooapis.jp/MAService/V1/parse', $options)
                        ->wait()
                        ->getBody()
                        ->getContents();

        $regex = '|<reading>(.*?)</reading>|is';
        preg_match_all($regex, $xml, $matches);

        return implode($matches[1]);
    }
}
