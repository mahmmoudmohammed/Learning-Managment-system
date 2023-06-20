<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Controllers\Api\Modules\Permissions\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = ['create-answer','index-answer','update-answer','show-answer','delete-answer'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
