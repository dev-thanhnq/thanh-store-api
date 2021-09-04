<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->truncate();
        for($i = 1; $i<20; $i++){
            DB::table('products')->insert([
                [
                    'sku' => 'sku'. $i,
                    'name' => 'Sản Phẩm ' . $i,
                    'description' => 'Mô tả thêm...',
                    'category_ids' => $i,
                    'weight' => '100' . $i,
                    'origin_price' => '10000'.$i,
                    'sale_price' => '11000' . $i,
                    'quantity_in_stock' => $i
                ]
            ]);
        }
    }
}
