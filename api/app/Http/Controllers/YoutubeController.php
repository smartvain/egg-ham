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

    public function getCaption(Request $request)
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
        $captions = explode('</text>', $res);
        
        $captionsIndex = 0;
        foreach ($captions as $caption) {
            $caption = trim($caption);
            $caption = preg_replace('/<text.+>/', '', $caption);
            $caption = preg_replace('/&amp;/i', '&', $caption);
            $caption = preg_replace('/&#39;/i', "'", $caption);
            $caption = preg_replace('/<\/?[^>]+(>|$)/', '', $caption);
            
            $captions = array_replace($captions, [$captionsIndex => $caption]);
            
            $captionsIndex++;
        }

        return ['captions' => $captions];
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
}
