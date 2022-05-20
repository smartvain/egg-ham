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
        'word_type',
    ];

    public function store($words)
    {
        DB::transaction(function () use ($words) {
            foreach ($words as $word) {
                $instance = new Word();
                $instance->fill($word)->save();
            }
        });
    }
}
