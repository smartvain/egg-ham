<?php

namespace Database\Seeders;

use App\Models\Sentence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SentenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sentences')->truncate();
        Sentence::factory(10)->create();
    }
}
