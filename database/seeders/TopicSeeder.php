<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Modules\Topics\Topic;
use Illuminate\Database\Seeder;

class topicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Topic::create([
            'title'       => 'PHP',
            'category_id' => 1,
            'status'      => 1
        ]);
    }
}
