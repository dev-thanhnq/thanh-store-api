<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => ['api', 'jwt.auth']], function () {
    Route::group(['prefix' => '/customers'], function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });

    Route::group(['prefix' => '/categories'], function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::get('/all', [CategoryController::class, 'getAllCategories']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [UserController::class, 'getDataProfile']);
        Route::put('/change-profile', [UserController::class, 'changeProfile']);
        Route::put('/change-password', [UserController::class, 'changePassword']);
        Route::get('/list-staff', [UserController::class, 'listStaff']);
        Route::post('/staff', [UserController::class, 'createStaff']);
        Route::put('/{id}/staff', [UserController::class, 'updateStaff']);
        Route::put('/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::group(['prefix' => '/products'], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/import', [ProductController::class, 'import']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::get('/template-import-file', [ProductController::class, 'getTemplateImportFile']);
        Route::post('/import-product/{id}', [ProductController::class, 'importProduct']);
        Route::get('/{id}', [ProductController::class, 'show']);
    });

    Route::group(['prefix' => '/carts'], function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/store-order', [CartController::class, 'storeOrder']);
        Route::delete('/{id}', [CartController::class, 'destroy']);
        Route::post('/products/{id}', [CartController::class, 'addCart']);
        Route::put('/products/{id}', [CartController::class, 'updateCart']);
    });

    Route::group(['prefix' => '/reports'], function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('/get-order', [ReportController::class, 'getDataOrder']);
        Route::get('/get-order-month', [ReportController::class, 'getDataOrderMonth']);
    });

    Route::group(['prefix' => '/orders'], function () {
        Route::get('/export-excel', [OrderController::class, 'exportExcel']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/change-status/{id}', [OrderController::class, 'changeStatusOneOrder']);
        Route::post('/change-status-all', [OrderController::class, 'changeStatusOrders']);
    });

    Route::group(['prefix' => '/roles'], function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::post('/{id}/add-permission', [RoleController::class, 'addPermissionForRole']);
        Route::post('/{id}/remove-permission', [RoleController::class, 'removePermissionForRole']);
        Route::get('/{id}', [RoleController::class, 'show']);
    });

    Route::group(['prefix' => '/permissions'], function () {
        Route::get('/', [PermissionController::class, 'index']);
    });
});

