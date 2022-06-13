<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'mean',
        'video_title',
        'url',
        'start_second',
        'word_type',
    ];

    public function store($word)
    {
        $instance = new Word();
        $instance->fill($word)->save();
    }

    public function replace($words)
    {
        DB::transaction(function () use ($words) {
            foreach ($words as $word) {
                $this->find($word['id'])->fill($word)->save();
            }
        });
    }

    public function remove($word)
    {
        $word->delete();
    }
}
