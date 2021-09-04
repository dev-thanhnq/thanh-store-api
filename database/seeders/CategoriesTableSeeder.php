<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();
        for($i = 1; $i<20; $i++){
            DB::table('categories')->insert([
                [
                    'sku' => 'sku' . $i,
                    'name' => 'Danh mục ' . $i,
                    'description' => 'Mô tả thêm...',
                ]
            ]);
        }
    }
}
