<?php

use App\Controllers\TourController;
use App\Controllers\TourCategoryController;
use App\Controllers\DashboardController;
use App\Controllers\BookingController;
use App\Controllers\UserController;

use Bramus\Router\Router;

$router = new Router();

// Trang chủ - Báo cáo thống kê
$router->get('/', DashboardController::class . '@index');
$router->get('admin/dashboard', DashboardController::class . '@index');

// Admin: Quản lý Tài khoản
$router->get('admin/users', UserController::class . '@index');
$router->get('admin/users/create', UserController::class . '@create');
$router->post('admin/users/store', UserController::class . '@store');
$router->get('admin/users/edit/(\d+)', UserController::class . '@edit');
$router->post('admin/users/update/(\d+)', UserController::class . '@update');
$router->get('admin/users/delete/(\d+)', UserController::class . '@delete');
$router->get('admin/users/show/(\d+)', UserController::class . '@show');

// Admin: Quản lý Danh mục Tour
$router->get('admin/tour-categories', TourCategoryController::class . '@index');
$router->get('admin/tour-categories/create', TourCategoryController::class . '@create');
$router->post('admin/tour-categories/store', TourCategoryController::class . '@store');
$router->get('admin/tour-categories/edit/(\d+)', TourCategoryController::class . '@edit');
$router->post('admin/tour-categories/update/(\d+)', TourCategoryController::class . '@update');
$router->get('admin/tour-categories/delete/(\d+)', TourCategoryController::class . '@delete');

// Admin: Quản lý Tour
$router->get('admin/tours', TourController::class . '@index');
$router->get('admin/tours/create', TourController::class . '@create');
$router->post('admin/tours/store', TourController::class . '@store');
$router->get('admin/tours/edit/(\d+)', TourController::class . '@edit');
$router->post('admin/tours/update/(\d+)', TourController::class . '@update');
$router->get('admin/tours/delete/(\d+)', TourController::class . '@delete');
$router->get('admin/tours/show/(\d+)', TourController::class . '@show');

// Trang công khai khi quét QR — hiện chi tiết tour ngay
$router->get('tour/(\d+)', TourController::class . '@qrShow');


/*
|--------------------------------------------------------------------------
| BOOKING
|--------------------------------------------------------------------------
*/

// Danh sách Booking
$router->get('admin/bookings', BookingController::class . '@index');

// Form thêm
$router->get('admin/bookings/create', BookingController::class . '@create');

// Lưu Booking
$router->post('admin/bookings/store', BookingController::class . '@store');

// Chi tiết
$router->get('admin/bookings/show/(\d+)', BookingController::class . '@show');

// Form sửa
$router->get('admin/bookings/edit/(\d+)', BookingController::class . '@edit');

// Cập nhật
$router->post('admin/bookings/update/(\d+)', BookingController::class . '@update');

// Xóa
$router->get('admin/bookings/delete/(\d+)', BookingController::class . '@delete');

$router->run();
