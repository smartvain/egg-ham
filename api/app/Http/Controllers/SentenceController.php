<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SentenceController extends Controller
{
    public function storeSentences(Request $request)
    {
        $sentences = $request->all();
        
        DB::transaction(function () use ($sentences) {
            foreach ($sentences as $sentence) {
                $instance = new Sentence();
                $instance->fill($sentence)->save();
            }
        });
    }
}
