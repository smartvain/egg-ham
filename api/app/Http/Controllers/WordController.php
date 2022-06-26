<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    private $word;

    public function __construct(Word $word)
    {
        $this->word = $word;
    }

    public function getWords(Request $request)
    {
        return Word::where('user_id', $request->user_id)->get();
    }

    public function storeWord(Request $request)
    {
        $this->word->store($request->input());
    }

    public function saveWords(Request $request)
    {
        $this->word->replace($request->words);
    }

    public function removeWord($wordId)
    {
        $this->word->remove($this->word->find($wordId));
    }
}
