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

    public function getWord()
    {
        return Word::get();
    }

    public function storeWord(Request $request)
    {
        $this->word->store($request->input());
    }

    public function saveWord(Request $request)
    {
        $this->word->replace($request->word);
    }

    public function deleteWord($wordId)
    {
        $word = $this->word->find($wordId);
        $this->word->remove($word);
    }
}
