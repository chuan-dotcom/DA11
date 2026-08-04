<?php

use App\Controllers\TourController;
use App\Controllers\TourCategoryController;
use App\Controllers\DashboardController;
use App\Controllers\BookingController;
use App\Controllers\UserController;
use App\Controllers\StaffController;
use App\Controllers\DepartureController;
use App\Controllers\StaffAssignmentController;
use App\Controllers\ServiceController;
use App\Controllers\GuestGroupController;
    
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
$router->get('admin/tours/participants/(\d+)', TourController::class . '@participants');

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

/*
|--------------------------------------------------------------------------
| STAFF - QUẢN LÝ NHÂN SỰ
|--------------------------------------------------------------------------
*/

// Danh sách nhân sự
$router->get('admin/staff', StaffController::class . '@index');

// Form thêm mới
$router->get('admin/staff/create', StaffController::class . '@create');

// Lưu nhân sự
$router->post('admin/staff/store', StaffController::class . '@store');

// Chi tiết
$router->get('admin/staff/show/(\d+)', StaffController::class . '@show');

// Form sửa
$router->get('admin/staff/edit/(\d+)', StaffController::class . '@edit');

// Cập nhật
$router->post('admin/staff/update/(\d+)', StaffController::class . '@update');

// Xóa
$router->get('admin/staff/delete/(\d+)', StaffController::class . '@delete');

$router->get('admin/departures', DepartureController::class . '@index');
$router->get('admin/departures/create', DepartureController::class . '@create');
$router->post('admin/departures/store', DepartureController::class . '@store');
$router->get('admin/departures/show/(\d+)', DepartureController::class . '@show');
$router->get('admin/departures/edit/(\d+)', DepartureController::class . '@edit');
$router->post('admin/departures/update/(\d+)', DepartureController::class . '@update');
$router->get('admin/departures/delete/(\d+)', DepartureController::class . '@delete');

$router->get('admin/staff-assignments', StaffAssignmentController::class . '@index');
$router->get('admin/staff-assignments/create', StaffAssignmentController::class . '@create');
$router->post('admin/staff-assignments/store', StaffAssignmentController::class . '@store');
$router->get('admin/staff-assignments/show/(\d+)', StaffAssignmentController::class . '@show');
$router->get('admin/staff-assignments/edit/(\d+)', StaffAssignmentController::class . '@edit');
$router->post('admin/staff-assignments/update/(\d+)', StaffAssignmentController::class . '@update');
$router->get('admin/staff-assignments/delete/(\d+)', StaffAssignmentController::class . '@delete');

$router->get('admin/services', ServiceController::class . '@index');
$router->get('admin/services/create', ServiceController::class . '@create');
$router->post('admin/services/store', ServiceController::class . '@store');
$router->get('admin/services/show/(\d+)', ServiceController::class . '@show');
$router->get('admin/services/edit/(\d+)', ServiceController::class . '@edit');
$router->post('admin/services/update/(\d+)', ServiceController::class . '@update');
$router->get('admin/services/delete/(\d+)', ServiceController::class . '@delete');

$router->get('admin/guest-groups', GuestGroupController::class . '@index');
$router->get('admin/guest-groups/show/(\d+)', GuestGroupController::class . '@show');
$router->get('admin/guest-groups/print/(\d+)', GuestGroupController::class . '@printList');
$router->get('admin/guest-groups/assign/(\d+)/(\d+)', GuestGroupController::class . '@assign');
$router->get('admin/guest-groups/unassign/(\d+)/(\d+)', GuestGroupController::class . '@unassign');
$router->get('admin/guest-groups/check-in/(\d+)/(\d+)', GuestGroupController::class . '@checkIn');
$router->get('admin/guest-groups/check-in-cancel/(\d+)/(\d+)', GuestGroupController::class . '@cancelCheckIn');

$router->run();
