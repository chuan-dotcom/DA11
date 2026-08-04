<?php
namespace App\Controllers\Admin;
  
use App\Controller;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Booking;

class DashboardController extends Controller {
    private $modelTour;
    private $modelCategory;
    private $modelBooking;

    public function __construct() {
        $this->modelTour     = new Tour();
        $this->modelCategory = new TourCategory();
        $this->modelBooking  = new Booking();
    }

    public function index() {
        $title = 'Báo cáo - Thống kê';

        // Tháng/năm lọc (mặc định = hiện tại)
        $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
        $year  = isset($_GET['year'])  ? (int) $_GET['year']  : (int) date('Y');

        // =============================================
        // CARD 1: Tổng số tour + tổng danh mục
        // =============================================
        $totalTours      = $this->modelTour->getTotalTours();
        $totalActiveTours = $this->modelTour->getTotalActiveTours();
        $totalCategories = $this->modelCategory->getTotalCategories();
        $totalCustomers = $this->modelBooking->getTotalCustomers();

        // =============================================
        // CARD 2: Booking đang chờ xử lý
        // =============================================
        $totalPendingBookings = $this->modelBooking->getTotalPendingBookings();

        // =============================================
        // CARD 3: Tổng doanh thu (booking hoàn thành)
        // =============================================
        $totalRevenue = $this->modelBooking->getTotalRevenue();

        // =============================================
        // CARD 4: Hoàn thành / Tổng booking
        // =============================================
        $totalBookings          = $this->modelBooking->getTotalBookings();
        $totalCompletedBookings = $this->modelBooking->getTotalCompletedBookings();

        // =============================================
        // Biểu đồ 1: Số booking theo ngày trong tháng
        // =============================================
        $daysInMonth     = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dailyBookingMap = $this->modelBooking->getDailyBookingCounts($month, $year);
        $dailyRevenueMap = $this->modelBooking->getDailyRevenue($month, $year);

        $chartLabels   = [];
        $chartBookings = [];
        $chartRevenue  = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[]   = str_pad($d, 2, '0', STR_PAD_LEFT);
            $chartBookings[] = $dailyBookingMap[$d] ?? 0;
            $chartRevenue[]  = $dailyRevenueMap[$d] ?? 0;
        }

        // =============================================
        // Biểu đồ 2: Số tour theo danh mục (từ bảng tours + tour_categories)
        // =============================================
        $toursByCategory = $this->modelTour->getToursByCategory();
        $catChartLabels  = array_column($toursByCategory, 'category_name');
        $catChartCounts  = array_map('intval', array_column($toursByCategory, 'tour_count'));

        // =============================================
        // Bảng tour hoàn thành (bookings status=1 JOIN tours JOIN categories)
        // =============================================
        $completedBookings = $this->modelBooking->getCompletedBookingsWithTour();

        // =============================================
        // Bảng quản lý tour: danh sách tour + doanh thu booking thực tế
        // =============================================
        $revenueByTour = $this->modelBooking->getRevenueByTour();

        return view('admin.dashboard.index', compact(
            'title',
            'month', 'year',
            // cards
            'totalTours', 'totalActiveTours', 'totalCategories',
            'totalCustomers',
            'totalPendingBookings',
            'totalRevenue',
            'totalBookings', 'totalCompletedBookings',
            // biểu đồ booking theo ngày
            'chartLabels', 'chartBookings', 'chartRevenue',
            // biểu đồ tour theo danh mục
            'catChartLabels', 'catChartCounts',
            // bảng
            'completedBookings',
            'revenueByTour'
        ));
    }
}
