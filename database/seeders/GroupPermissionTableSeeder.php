<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class GroupPermissionTableSeeder extends Seeder
{
    public function checkIssetBeforeCreate($data) {
        $groupPermission = PermissionGroup::where('code', $data['code'])->first();
        if (empty($groupPermission)) {
            PermissionGroup::create($data);
        } else {
            $groupPermission->update($data);
        }
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::checkIssetBeforeCreate([
            'name' => 'Tổng quan',
            'code' => 'dashboard',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến tổng quan'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Quản lý đơn hàng', 
            'code' => 'order-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Quản lý sản phẩm',
            'code' => 'product-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Quản lý nhân viên',
            'code' => 'user-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến nhân viên'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Quản lý khách hàng',
            'code' => 'customer-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan đến khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'Bán hàng',
            'code' => 'cart-management',
            'description' => 'Quản lý toàn bộ chức năng liên quan bán hàng'
        ]);
    }
}
