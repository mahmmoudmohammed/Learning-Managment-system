<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($j=1; $j<=100; $j++) {
            for ($i = 1; $i<5 ; $i++) {
                DB::table('answers')->insert([
                    'answer' => Str::random(10),
                    'question_id' => $j,
                    'is_correct' => $i==4?1:0,
                ]);
            }
        }
    }
}
