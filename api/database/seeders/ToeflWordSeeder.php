<?php

namespace Database\Seeders;

use App\Models\ToeflWord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToeflWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('words')->truncate();
        ToeflWord::factory(20)->create();
    }
}
