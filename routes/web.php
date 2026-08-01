<?php

use App\Controllers\TourController;
use App\Controllers\TourCategoryController;
use App\Controllers\DashboardController;
use App\Controllers\BookingController;
use App\Controllers\UserController;
use App\Controllers\StaffController;
use App\Controllers\DepartureController;
use App\Controllers\StaffAssignmentController;

use Bramus\Router\Router;

$router = new Router();

$router->get('/', DashboardController::class . '@index');
$router->get('admin/dashboard', DashboardController::class . '@index');

$router->get('admin/users', UserController::class . '@index');
$router->get('admin/users/create', UserController::class . '@create');
$router->post('admin/users/store', UserController::class . '@store');
$router->get('admin/users/edit/(\d+)', UserController::class . '@edit');
$router->post('admin/users/update/(\d+)', UserController::class . '@update');
$router->get('admin/users/delete/(\d+)', UserController::class . '@delete');
$router->get('admin/users/show/(\d+)', UserController::class . '@show');

$router->get('admin/tour-categories', TourCategoryController::class . '@index');
$router->get('admin/tour-categories/create', TourCategoryController::class . '@create');
$router->post('admin/tour-categories/store', TourCategoryController::class . '@store');
$router->get('admin/tour-categories/edit/(\d+)', TourCategoryController::class . '@edit');
$router->post('admin/tour-categories/update/(\d+)', TourCategoryController::class . '@update');
$router->get('admin/tour-categories/delete/(\d+)', TourCategoryController::class . '@delete');

$router->get('admin/tours', TourController::class . '@index');
$router->get('admin/tours/create', TourController::class . '@create');
$router->post('admin/tours/store', TourController::class . '@store');
$router->get('admin/tours/edit/(\d+)', TourController::class . '@edit');
$router->post('admin/tours/update/(\d+)', TourController::class . '@update');
$router->get('admin/tours/delete/(\d+)', TourController::class . '@delete');
$router->get('admin/tours/show/(\d+)', TourController::class . '@show');
$router->get('admin/tours/participants/(\d+)', TourController::class . '@participants');

$router->get('tour/(\d+)', TourController::class . '@qrShow');

$router->get('admin/bookings', BookingController::class . '@index');
$router->get('admin/bookings/create', BookingController::class . '@create');
$router->post('admin/bookings/store', BookingController::class . '@store');
$router->get('admin/bookings/show/(\d+)', BookingController::class . '@show');
$router->get('admin/bookings/edit/(\d+)', BookingController::class . '@edit');
$router->post('admin/bookings/update/(\d+)', BookingController::class . '@update');
$router->get('admin/bookings/delete/(\d+)', BookingController::class . '@delete');

$router->get('admin/staff', StaffController::class . '@index');
$router->get('admin/staff/create', StaffController::class . '@create');
$router->post('admin/staff/store', StaffController::class . '@store');
$router->get('admin/staff/show/(\d+)', StaffController::class . '@show');
$router->get('admin/staff/edit/(\d+)', StaffController::class . '@edit');
$router->post('admin/staff/update/(\d+)', StaffController::class . '@update');
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

$router->run();
