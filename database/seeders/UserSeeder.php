<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Modules\Permissions\Permission;
use App\Http\Controllers\Api\Modules\Roles\Role;
use App\Http\Controllers\Api\Modules\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['title' =>'user']);

        $user = User::create([
            'name'     => 'user',
            'email'    => 'user@mail.com',
            'password' => Hash::make('123456'),
            'role_id'  =>$role ->id
        ]);

        $permissions = [1,2,3];
        $role -> permissions() -> sync($permissions);

    }
}
