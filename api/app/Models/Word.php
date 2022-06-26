<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Word extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function store($word)
    {
        $this->fill($word)->save();
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
