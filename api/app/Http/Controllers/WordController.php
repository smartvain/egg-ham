<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WordController extends Controller
{
    public function getWords()
    {
        return Word::get();
    }

    public function storeWords(Request $request)
    {
        $words = $request->all();
        
        DB::transaction(function () use ($words) {
            foreach ($words as $word) {
                $instance = new Word();
                $instance->fill($word)->save();
            }
        });
    }
}
