<?php

use App\Controllers\AuthController;
use App\Controllers\Admin\TourController as AdminTourController;
use App\Controllers\Admin\TourCategoryController as AdminTourCategoryController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\BookingController as AdminBookingController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\StaffController as AdminStaffController;
use App\Controllers\Admin\DepartureController as AdminDepartureController;
use App\Controllers\Admin\StaffAssignmentController as AdminStaffAssignmentController;
use App\Controllers\Admin\ServiceController as AdminServiceController;
use App\Controllers\Admin\GuestGroupController as AdminGuestGroupController;
use App\Controllers\Admin\TourDiaryController as AdminTourDiaryController;

use App\Controllers\Hdv\TourInfoController as HdvTourInfoController;
use App\Controllers\Hdv\AssignedTourController as HdvAssignedTourController;
use App\Controllers\Hdv\ScheduleController as HdvScheduleController;
use App\Controllers\Hdv\DiaryController as HdvDiaryController;
use App\Support\Auth;
    
use Bramus\Router\Router;

$router = new Router();
$router->setBasePath(app_base_path());

$router->before('GET|POST', '/auth/(login|register)', function () {
    if (Auth::check()) {
        redirect(Auth::redirectPath());
    }
});

$router->before('GET|POST', '/auth/account', function () {
    if (!Auth::check()) {
        setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('auth/login');
    }
});

$router->before('GET|POST', '/admin.*', function () {
    if (!Auth::check()) {
        setFlash('error', 'Vui lòng đăng nhập để vào trang quản trị.');
        redirect('auth/login');
    }

    if (!Auth::isAdmin()) {
        setFlash('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        redirect('auth/account');
    }
});

$router->before('GET|POST', '/hdv.*', function () {
    if (!Auth::check()) {
        setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('auth/login');
    }

    if (!Auth::isAdmin() && !Auth::isHdv()) {
        setFlash('error', 'Bạn không có quyền truy cập khu vực HDV.');
        redirect('auth/account');
    }
});

/*
|--------------------------------------------------------------------------
| XÁC THỰC TÀI KHOẢN
|--------------------------------------------------------------------------
*/
$router->get('/', AuthController::class . '@index');
$router->get('auth/login', AuthController::class . '@showLogin');
$router->post('auth/login', AuthController::class . '@login');
$router->get('auth/register', AuthController::class . '@showRegister');
$router->post('auth/register', AuthController::class . '@register');
$router->get('auth/logout', AuthController::class . '@logout');
$router->get('auth/account', AuthController::class . '@account');

/*
|--------------------------------------------------------------------------
| QUẢN TRỊ ADMIN
|--------------------------------------------------------------------------
*/
$router->get('admin', AdminDashboardController::class . '@index');
$router->get('admin/dashboard', AdminDashboardController::class . '@index');

// Admin: Quản lý Tài khoản
$router->get('admin/users', AdminUserController::class . '@index');
$router->get('admin/users/create', AdminUserController::class . '@create');
$router->post('admin/users/store', AdminUserController::class . '@store');
$router->get('admin/users/edit/(\d+)', AdminUserController::class . '@edit');
$router->post('admin/users/update/(\d+)', AdminUserController::class . '@update');
$router->get('admin/users/delete/(\d+)', AdminUserController::class . '@delete');
$router->post('admin/users/delete-multiple', AdminUserController::class . '@bulkDelete');
$router->get('admin/users/show/(\d+)', AdminUserController::class . '@show');

// Admin: Quản lý Danh mục Tour
$router->get('admin/tour-categories', AdminTourCategoryController::class . '@index');
$router->get('admin/tour-categories/create', AdminTourCategoryController::class . '@create');
$router->post('admin/tour-categories/store', AdminTourCategoryController::class . '@store');
$router->get('admin/tour-categories/edit/(\d+)', AdminTourCategoryController::class . '@edit');
$router->post('admin/tour-categories/update/(\d+)', AdminTourCategoryController::class . '@update');
$router->get('admin/tour-categories/delete/(\d+)', AdminTourCategoryController::class . '@delete');

// Admin: Quản lý Tour
$router->get('admin/tours', AdminTourController::class . '@index');
$router->get('admin/tours/create', AdminTourController::class . '@create');
$router->post('admin/tours/store', AdminTourController::class . '@store');
$router->get('admin/tours/edit/(\d+)', AdminTourController::class . '@edit');
$router->post('admin/tours/update/(\d+)', AdminTourController::class . '@update');
$router->get('admin/tours/delete/(\d+)', AdminTourController::class . '@delete');
$router->get('admin/tours/show/(\d+)', AdminTourController::class . '@show');
$router->get('admin/tours/participants/(\d+)', AdminTourController::class . '@participants');

// Trang công khai QR
$router->get('tour/(\d+)', AdminTourController::class . '@qrShow');

// Admin: Booking
$router->get('admin/bookings', AdminBookingController::class . '@index');
$router->get('admin/bookings/create', AdminBookingController::class . '@create');
$router->post('admin/bookings/store', AdminBookingController::class . '@store');
$router->get('admin/bookings/show/(\d+)', AdminBookingController::class . '@show');
$router->get('admin/bookings/edit/(\d+)', AdminBookingController::class . '@edit');
$router->post('admin/bookings/update/(\d+)', AdminBookingController::class . '@update');
$router->get('admin/bookings/delete/(\d+)', AdminBookingController::class . '@delete');
$router->get('admin/bookings/unassign-departure/(\d+)', AdminBookingController::class . '@unassignDeparture');

// Admin: Staff / HDV
$router->get('admin/staff', AdminStaffController::class . '@index');
$router->get('admin/staff/create', AdminStaffController::class . '@create');
$router->post('admin/staff/store', AdminStaffController::class . '@store');
$router->get('admin/staff/show/(\d+)', AdminStaffController::class . '@show');
$router->get('admin/staff/edit/(\d+)', AdminStaffController::class . '@edit');
$router->post('admin/staff/update/(\d+)', AdminStaffController::class . '@update');
$router->get('admin/staff/delete/(\d+)', AdminStaffController::class . '@delete');

// Admin: Departures
$router->get('admin/departures', AdminDepartureController::class . '@index');
$router->get('admin/departures/create', AdminDepartureController::class . '@create');
$router->post('admin/departures/store', AdminDepartureController::class . '@store');
$router->get('admin/departures/show/(\d+)', AdminDepartureController::class . '@show');
$router->get('admin/departures/edit/(\d+)', AdminDepartureController::class . '@edit');
$router->post('admin/departures/update/(\d+)', AdminDepartureController::class . '@update');
$router->get('admin/departures/delete/(\d+)', AdminDepartureController::class . '@delete');

// Admin: Staff Assignments
$router->get('admin/staff-assignments', AdminStaffAssignmentController::class . '@index');
$router->get('admin/staff-assignments/create', AdminStaffAssignmentController::class . '@create');
$router->post('admin/staff-assignments/store', AdminStaffAssignmentController::class . '@store');
$router->get('admin/staff-assignments/show/(\d+)', AdminStaffAssignmentController::class . '@show');
$router->get('admin/staff-assignments/edit/(\d+)', AdminStaffAssignmentController::class . '@edit');
$router->post('admin/staff-assignments/update/(\d+)', AdminStaffAssignmentController::class . '@update');
$router->get('admin/staff-assignments/delete/(\d+)', AdminStaffAssignmentController::class . '@delete');

// Admin: Services
$router->get('admin/services', AdminServiceController::class . '@index');
$router->get('admin/services/create', AdminServiceController::class . '@create');
$router->post('admin/services/store', AdminServiceController::class . '@store');
$router->get('admin/services/show/(\d+)', AdminServiceController::class . '@show');
$router->get('admin/services/edit/(\d+)', AdminServiceController::class . '@edit');
$router->post('admin/services/update/(\d+)', AdminServiceController::class . '@update');
$router->get('admin/services/delete/(\d+)', AdminServiceController::class . '@delete');

// Admin: Guest Groups
$router->get('admin/guest-groups', AdminGuestGroupController::class . '@index');
$router->get('admin/guest-groups/show/(\d+)', AdminGuestGroupController::class . '@show');
$router->get('admin/guest-groups/print/(\d+)', AdminGuestGroupController::class . '@printList');
$router->get('admin/guest-groups/assign/(\d+)/(\d+)', AdminGuestGroupController::class . '@assign');
$router->get('admin/guest-groups/unassign/(\d+)/(\d+)', AdminGuestGroupController::class . '@unassign');
$router->get('admin/guest-groups/check-in/(\d+)/(\d+)', AdminGuestGroupController::class . '@checkIn');
$router->get('admin/guest-groups/check-in-cancel/(\d+)/(\d+)', AdminGuestGroupController::class . '@cancelCheckIn');
$router->get('admin/guest-groups/seed-customers/(\d+)', AdminGuestGroupController::class . '@seedCustomers');
$router->get('admin/guest-groups/booking-guests/create/(\d+)/(\d+)', AdminGuestGroupController::class . '@createGuest');
$router->post('admin/guest-groups/booking-guests/store/(\d+)/(\d+)', AdminGuestGroupController::class . '@storeGuest');
$router->get('admin/guest-groups/booking-guests/edit/(\d+)/(\d+)', AdminGuestGroupController::class . '@editGuest');
$router->post('admin/guest-groups/booking-guests/update/(\d+)/(\d+)', AdminGuestGroupController::class . '@updateGuest');
$router->get('admin/guest-groups/booking-guests/delete/(\d+)/(\d+)', AdminGuestGroupController::class . '@deleteGuest');
$router->get('admin/guest-groups/booking-guests/check-in/(\d+)/(\d+)', AdminGuestGroupController::class . '@checkInGuest');
$router->get('admin/guest-groups/booking-guests/check-in-cancel/(\d+)/(\d+)', AdminGuestGroupController::class . '@cancelCheckInGuest');
$router->get('admin/guest-groups/booking-guests/payment-paid/(\d+)/(\d+)', AdminGuestGroupController::class . '@markGuestPaid');
$router->get('admin/guest-groups/booking-guests/payment-unpaid/(\d+)/(\d+)', AdminGuestGroupController::class . '@markGuestUnpaid');

// Admin: Tour Diaries
$router->get('admin/tour-diaries', AdminTourDiaryController::class . '@index');
$router->get('admin/tour-diaries/create', AdminTourDiaryController::class . '@create');
$router->post('admin/tour-diaries/store', AdminTourDiaryController::class . '@store');
$router->get('admin/tour-diaries/show/(\d+)', AdminTourDiaryController::class . '@show');
$router->get('admin/tour-diaries/edit/(\d+)', AdminTourDiaryController::class . '@edit');
$router->post('admin/tour-diaries/update/(\d+)', AdminTourDiaryController::class . '@update');
$router->get('admin/tour-diaries/delete/(\d+)', AdminTourDiaryController::class . '@delete');

/*
|--------------------------------------------------------------------------
| KÊNH HƯỚNG DẪN VIÊN (HDV)
|--------------------------------------------------------------------------
*/
$router->get('hdv', HdvTourInfoController::class . '@index');
$router->get('hdv/dashboard', HdvTourInfoController::class . '@index');
$router->get('hdv/thong-tin-tour', HdvTourInfoController::class . '@index');
$router->post('hdv/tour-logs/store', HdvTourInfoController::class . '@storeTourLog');
$router->post('hdv/tour-logs/update/(\d+)', HdvTourInfoController::class . '@updateTourLog');
$router->post('hdv/tour-logs/delete/(\d+)', HdvTourInfoController::class . '@deleteTourLog');

$router->get('hdv/tour-phan-cong', HdvAssignedTourController::class . '@index');
$router->post('hdv/guest/check-in', HdvAssignedTourController::class . '@toggleCheckIn');

$router->get('hdv/lich-trinh', HdvScheduleController::class . '@index');

$router->get('hdv/nhat-ky-tour', HdvDiaryController::class . '@index');
$router->get('hdv/nhat-ky-tour/create', HdvDiaryController::class . '@create');
$router->post('hdv/nhat-ky-tour/store', HdvDiaryController::class . '@store');
$router->get('hdv/nhat-ky-tour/show/(\d+)', HdvDiaryController::class . '@show');
$router->get('hdv/nhat-ky-tour/edit/(\d+)', HdvDiaryController::class . '@edit');
$router->post('hdv/nhat-ky-tour/update/(\d+)', HdvDiaryController::class . '@update');
$router->get('hdv/nhat-ky-tour/delete/(\d+)', HdvDiaryController::class . '@delete');
$router->post('hdv/nhat-ky-tour/update-cost', HdvDiaryController::class . '@updateCost');

$router->run();
