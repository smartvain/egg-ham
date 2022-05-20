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

    public function getWords()
    {
        return Word::get();
    }

    public function storeWords(Request $request)
    {
        $this->word->store($request->all());
    }

    public function deleteWord($wordId)
    {
        $word = $this->word->find($wordId);
        $this->word->remove($word);
    }
}
