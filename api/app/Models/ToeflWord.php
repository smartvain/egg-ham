<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ToeflWord extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function store($words)
    {
        DB::transaction(function () use ($words) {
            foreach ($words as $word) {
                $this->firstOrCreate(['text' => $word[1]]);
            }
        });
    }
}
