<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::where('name', 'Admin')->first();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@zent.vn',
            'phone' => '0123456789',
            'address' => 'Ngõ Trại Cá, Hà Nội',
            'password' => Hash::make('Zent@123.edu.vn'),
            'role_id' => $roleAdmin->id,
        ]);

    }
}
