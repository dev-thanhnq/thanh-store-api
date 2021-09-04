<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->truncate();
        for($i = 1; $i<20; $i++){
            DB::table('customers')->insert([
                [
                    'code' => $i,
                    'name' => 'Khách hàng ' . $i,
                    'email' => 'email@gmail.com',
                    'phone' => '0835904783',
                    'address' => '112 trương định'
                ]
            ]);
        }
    }
}
