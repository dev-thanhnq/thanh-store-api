<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function checkIssetBeforeCreate($data) {
        $permission = Permission::where('name', $data['name'])->first();
        if (empty($permission)) {
            Permission::create($data);
        } else {
            $permission->update($data);
        }
    }
    
    public function run()
    {
        //Admin
        self::checkIssetBeforeCreate([
            'name' => 'admin',
            'display_name' => 'Toàn bộ quyền',
            'group_code' => null,
            'description' => 'Có toàn quyền sử dụng hệ thống'
        ]);

        //dashboard
        self::checkIssetBeforeCreate([
            'name' => 'revenue-daily-report',
            'display_name' => 'Báo cáo doanh thu hàng ngày',
            'group_code' => 'dashboard',
            'description' => 'Báo cáo chi tiết doanh thu hàng ngày'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'revenue-monthly-report',
            'display_name' => 'Báo cáo doanh thu tháng',
            'group_code' => 'dashboard',
            'description' => 'Báo cáo chi tiết doanh thu tháng'
        ]);

        //Order
        self::checkIssetBeforeCreate([
            'name' => 'get-order',
            'display_name' => 'Danh sách đơn hàng',
            'group_code' => 'order-management',
            'description' => 'Xem danh sách đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-detail-order',
            'display_name' => 'Chi tiết đơn hàng',
            'group_code' => 'order-management',
            'description' => 'Xem chi tiết một đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'change-status-order',
            'display_name' => 'Thay đổi trạng thái một đơn hàng',
            'group_code' => 'order-management',
            'description' => 'Thay đổi trạng thái cho một đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'change-status-orders',
            'display_name' => 'Thay đổi trạng thái nhiều đơn hàng',
            'group_code' => 'order-management',
            'description' => 'Thay đổi trạng thái cho nhiều đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'export-excel-order',
            'display_name' => 'Xuất file excel',
            'group_code' => 'order-management',
            'description' => 'Xuất file excel đơn hàng'
        ]);

        //Product

        self::checkIssetBeforeCreate([
            'name' => 'get-product',
            'display_name' => 'Danh sách sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Xem danh sách sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-product',
            'display_name' => 'Thêm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Thêm mới sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-product',
            'display_name' => 'Chỉnh sửa sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Chỉnh sửa sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-product',
            'display_name' => 'Xóa sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Xóa sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'import-excel-product',
            'display_name' => 'Nhập excel',
            'group_code' => 'product-management',
            'description' => 'Nhập excel cho sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-detail-product',
            'display_name' => 'Chi tiết sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Chi tiết sản phẩm'
        ]);

        //Category

        self::checkIssetBeforeCreate([
            'name' => 'get-category',
            'display_name' => 'Danh sách nhóm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Xem danh sách nhóm sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-category',
            'display_name' => 'Thêm nhóm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Thêm mới nhóm sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-category',
            'display_name' => 'Chỉnh sửa nhóm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Chỉnh sửa nhóm sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-category',
            'display_name' => 'Xóa nhóm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Xóa nhóm sản phẩm'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-all-category',
            'display_name' => 'Danh sách tất cả nhóm sản phẩm',
            'group_code' => 'product-management',
            'description' => 'Danh sách tất cả nhóm sản phẩm'
        ]);

        //Customer

        self::checkIssetBeforeCreate([
            'name' => 'get-customer',
            'display_name' => 'Danh sách khách hàng',
            'group_code' => 'customer-management',
            'description' => 'Xem danh sách khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-customer',
            'display_name' => 'Thêm khách hàng',
            'group_code' => 'customer-management',
            'description' => 'Thêm mới khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-customer',
            'display_name' => 'Chỉnh sửa khách hàng',
            'group_code' => 'customer-management',
            'description' => 'Chỉnh sửa khách hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-customer',
            'display_name' => 'Xóa khách hàng',
            'group_code' => 'customer-management',
            'description' => 'Xóa khách hàng'
        ]);

        //User

        self::checkIssetBeforeCreate([
            'name' => 'get-user',
            'display_name' => 'Danh sách nhân viên',
            'group_code' => 'user-management',
            'description' => 'Xem danh sách nhân viên'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-user',
            'display_name' => 'Thêm nhân viên',
            'group_code' => 'user-management',
            'description' => 'Thêm mới nhân viên'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-user',
            'display_name' => 'Chỉnh sửa nhân viên',
            'group_code' => 'user-management',
            'description' => 'Chỉnh sửa nhân viên'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-user',
            'display_name' => 'Xóa nhân viên',
            'group_code' => 'user-management',
            'description' => 'Xóa nhân viên'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'reset-password-user',
            'display_name' => 'Đặt lại mật khẩu nhân viên',
            'group_code' => 'user-management',
            'description' => 'Đặt lại mật khẩu cho nhân viên'
        ]);
        
        self::checkIssetBeforeCreate([
            'name' => 'change-password-user',
            'display_name' => 'Đổi mật khẩu người dùng',
            'group_code' => 'user-management',
            'description' => 'Đổi mật khẩu người dùng đang đăng nhập'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-profile-user',
            'display_name' => 'Cập nhật thông tin người dùng',
            'group_code' => 'user-management',
            'description' => 'Cập nhật thông tin người dùng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-profile-user',
            'display_name' => 'Hiển thị thông tin người dùng',
            'group_code' => 'user-management',
            'description' => 'Hiển thị thông tin người dùng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-role',
            'display_name' => 'Danh sách chức vụ',
            'group_code' => 'user-management',
            'description' => 'Xem danh sách chức vụ'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-role',
            'display_name' => 'Tạo chức vụ',
            'group_code' => 'user-management',
            'description' => 'Tạo chức vụ'
        
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-role',
            'display_name' => 'Chỉnh sửa chức vụ',
            'group_code' => 'user-management',
            'description' => 'Chỉnh sửa chức vụ'
        
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-role',
            'display_name' => 'Xóa chức vụ',
            'group_code' => 'user-management',
            'description' => 'Xóa chức vụ'
        
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'add-permission-for-role',
            'display_name' => 'Thêm mới quyền cho chức vụ',
            'group_code' => 'user-management',
            'description' => 'Thêm mới quyền cho chức vụ'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-permission-for-role',
            'display_name' => 'Xóa quyền cho chức vụ',
            'group_code' => 'user-management',
            'description' => 'Xóa quyền cho chức vụ'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'get-permission',
            'display_name' => 'Xem danh sách quyền',
            'group_code' => 'user-management',
            'description' => 'Xem danh sách quyền'
        ]);

        //Cart

        self::checkIssetBeforeCreate([
            'name' => 'get-cart',
            'display_name' => 'Danh sách sản phẩm trong giỏ hàng',
            'group_code' => 'cart-management',
            'description' => 'Xem danh sách các sản phẩm có trong giỏ'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'create-order',
            'display_name' => 'Tạo đơn hàng',
            'group_code' => 'cart-management',
            'description' => 'Tạo mới đơn hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'add-cart',
            'display_name' => 'Thêm sản phẩm vào giỏ hàng',
            'group_code' => 'cart-management',
            'description' => 'Thêm mới sản phẩm vào giỏ hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'delete-cart',
            'display_name' => 'Xóa sản phẩm khỏi giỏ hàng',
            'group_code' => 'cart-management',
            'description' => 'Xóa sản phẩm khỏi giỏ hàng'
        ]);

        self::checkIssetBeforeCreate([
            'name' => 'update-cart',
            'display_name' => 'Chỉnh sửa giỏ hàng',
            'group_code' => 'cart-management',
            'description' => 'Chỉnh sửa số lượng sản phẩm có trong giỏ'
        ]);

    }
}
