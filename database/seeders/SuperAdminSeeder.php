<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\Modules\Permissions\Permission;
use App\Http\Controllers\Api\Modules\Roles\Role;
use App\Http\Controllers\Api\Modules\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['title' =>'Super admin']);

        $user = User::create([
            'name'     => 'Super admin',
            'email'    => 'sadmin@mail.com',
            'password' => Hash::make('123456'),
            'role_id'  =>$role ->id
        ]);

        $permissions = Permission::pluck('id','id')->all();
        $role->permissions()->sync($permissions);
    }
}
