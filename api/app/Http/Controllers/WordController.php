<?php

namespace App\Http\Controllers;

use App\Http\Requests\Word\GetWordsRequest;
use App\Http\Requests\Word\SaveWordsRequest;
use App\Http\Requests\Word\StoreWordRequest;
use App\Models\Word;

class WordController extends Controller
{
    private $word;

    public function __construct(Word $word)
    {
        $this->word = $word;
    }

    public function getWords(GetWordsRequest $request)
    {
        return $this->word->where('user_id', $request->user_id)->get();
    }

    public function storeWord(StoreWordRequest $request)
    {
        $this->word->store($request->input());
    }

    public function saveWords(SaveWordsRequest $request)
    {
        $this->word->replace($request->words);
    }

    public function removeWord($wordId)
    {
        $this->word->remove($this->word->find($wordId));
    }
}
