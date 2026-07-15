<?php

use App\Controllers\TourController;
use App\Controllers\TourCategoryController;
use App\Controllers\DashboardController;

use Bramus\Router\Router;

$router = new Router();

// Trang chủ - Báo cáo thống kê
$router->get('/', DashboardController::class . '@index');
$router->get('admin/dashboard', DashboardController::class . '@index');

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

$router->run();
