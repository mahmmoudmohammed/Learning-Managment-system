<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i<=100 ; $i++) {
            DB::table('questions')->insert([
                'question' => Str::random(10),
                'topic_id' =>1,
                'difficulty' =>rand(1,3)
            ]);
        }
    }
}
