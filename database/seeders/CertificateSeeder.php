<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Modules\UserCertificate\Certificate;
use Illuminate\Database\Seeder;

class certificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Certificate::create([
            'title'    => 'PHP for advanced',
            'topic_id' => 1 ,
            'level'    => 3 ,
            'number'   => 10,
            'duration' => 10,
        ]);
    }
}
