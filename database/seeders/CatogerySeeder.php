<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Modules\Categories\Category;
use Illuminate\Database\Seeder;


class CatogerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Category::create([
            'name'     => 'Backend',
            'parent_id'    => null,
        ]);
    }
}
