<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();

        $permission = Permission::where('name', 'admin')->first();
        $permissionUsers = Permission::where('group_code','!=','user-management')->where('group_code','!=',null)->get();
        $roleAdmin = Role::create([
            'name' => 'Admin',
            'is_protected' => true,
            'description' => 'Quản trị hệ thống',
        ]);
        $roleUser = Role::create([
            'name' => 'Nhân viên',
            'is_protected' => false,
            'description' => 'Vai trò nhân viên',
        ]);
        $roleAdmin->permissions()->attach($permission->_id);
        foreach ($permissionUsers as $permissionUser) {
            $roleUser->permissions()->attach($permissionUser->_id);
        }
    }
}
