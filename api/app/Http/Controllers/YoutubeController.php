<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ToeflWord;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function getLangList(Request $request)
    {
        $videoId = $request->videoId;
        
        $res = $this->getUrlContent("https://www.youtube.com/watch?v={$videoId}");

        $captionTracks = $this->extractCaptionTrack($res);
        
        $langList = collect();
        foreach ($captionTracks as $captionTrack) {
            $langList->push([
                'text'  => $captionTrack->name->simpleText,
                'value' => substr(
                    $captionTrack->vssId,
                    strpos($captionTrack->vssId, '.') + strlen('.')
                )
            ]);
        }
        
        return $langList;
    }

    public function getCaptions(Request $request)
    {
        $videoId = $request->videoId;
        $lang    = $request->lang;
        
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
        $words = [];
        
        $captionsIndex = 0;
        foreach ($captions as $caption) {
            if (!$caption) {
                unset($captions[$captionsIndex]);
                continue;
            }

            $caption = trim($caption);

            $startRegex = '/start="([\d.]+)"/';
            preg_match($startRegex, $caption, $start);

            $caption = preg_replace('/<text.+>/', '', $caption);
            $caption = preg_replace('/&amp;/i', '&', $caption);
            $caption = preg_replace('/&#39;/i', "'", $caption);
            $caption = preg_replace('/<\/?[^>]+(>|$)/', '', $caption);
            
            $captions = array_replace($captions, [$captionsIndex => [
                'startSecond' => $start[1],
                'caption' => $caption
            ]]);

            $words = array_merge($words, explode(' ', $caption));
            
            $captionsIndex++;
        }

        $words = $this->calcUtilizationRate(array_count_values($words));

        return ['captions' => $captions, 'words' => $words];
    }

    public function getVideoInfo(Request $request) {
        return $this->getUrlContent("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$request->videoId}&format=json");
    }

    private function calcUtilizationRate($words)
    {
        $toeflWords = ToeflWord::pluck('text');
        $includeWords = array_filter($words, function ($value, $word) use ($toeflWords) {
            return $toeflWords->contains($word);
        }, ARRAY_FILTER_USE_BOTH);
        arsort($includeWords);

        return $includeWords;
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
