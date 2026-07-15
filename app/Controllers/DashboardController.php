<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Tour;
use App\Models\TourCategory;

class DashboardController extends Controller{
    private $modelTour;
    private $modelCategory;

    public function __construct()
    {
        $this->modelTour = new Tour();
        $this->modelCategory = new TourCategory();
    }

    public function index() {
        $title = 'Báo cáo thống kê';

        // Tổng số tour
        $totalTours = $this->modelTour->getTotalTours();

        // Tổng số danh mục
        $totalCategories = $this->modelCategory->getTotalCategories();

        // Tour có giá cao nhất
        $mostExpensiveTour = $this->modelTour->getMostExpensiveTour();

        // Tour có giá thấp nhất
        $cheapestTour = $this->modelTour->getCheapestTour();

        // Tổng giá trị tất cả tour
        $totalPrice = $this->modelTour->getTotalPrice();

        // Thống kê số tour theo danh mục
        $toursByCategory = $this->modelTour->getToursByCategory();

        return view('dashboard.index', compact(
            'title',
            'totalTours',
            'totalCategories',
            'mostExpensiveTour',
            'cheapestTour',
            'totalPrice',
            'toursByCategory'
        ));
    }
}
