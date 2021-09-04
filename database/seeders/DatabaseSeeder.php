<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GroupPermissionTableSeeder::class,
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
        ]);
    }
}
