@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-airplane" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">{{ $totalTours }}</h3>
                    <p class="mb-0">Tổng số Tour</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-list-ul" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">{{ $totalCategories }}</h3>
                    <p class="mb-0">Tổng số Danh mục</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">{{ number_format($totalPrice) }} VNĐ</h3>
                    <p class="mb-0">Tổng giá trị Tour</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning stat-card">
                <div class="card-body text-center">
                    <i class="bi bi-trophy" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">{{ $mostExpensiveTour ? number_format($mostExpensiveTour['price']) : 0 }} VNĐ</h3>
                    <p class="mb-0">Tour đắt nhất</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin chi tiết -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-star-fill"></i> Tour đắt nhất</h5>
                </div>
                <div class="card-body">
                    @if($mostExpensiveTour)
                        <h5 class="card-title">{{ $mostExpensiveTour['name'] }}</h5>
                        <p class="text-danger fw-bold">{{ number_format($mostExpensiveTour['price']) }} VNĐ</p>
                        @if($mostExpensiveTour['image'])
                            <img src="{{ file_url($mostExpensiveTour['image']) }}" style="width: 100%; height: 200px; object-fit: cover;" alt="Tour">
                        @endif
                    @else
                        <p>Chưa có tour nào</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bag-heart"></i> Tour rẻ nhất</h5>
                </div>
                <div class="card-body">
                    @if($cheapestTour)
                        <h5 class="card-title">{{ $cheapestTour['name'] }}</h5>
                        <p class="text-success fw-bold">{{ number_format($cheapestTour['price']) }} VNĐ</p>
                        @if($cheapestTour['image'])
                            <img src="{{ file_url($cheapestTour['image']) }}" style="width: 100%; height: 200px; object-fit: cover;" alt="Tour">
                        @endif
                    @else
                        <p>Chưa có tour nào</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê số tour theo danh mục -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Thống kê số Tour theo Danh mục</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Danh mục</th>
                                <th>Số lượng Tour</th>
                                <th>Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($toursByCategory as $item)
                            <tr>
                                <td>{{ $item['category_name'] }}</td>
                                <td>{{ $item['tour_count'] }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: {{ $totalTours > 0 ? round(($item['tour_count'] / $totalTours) * 100) : 0 }}%">
                                            {{ $totalTours > 0 ? round(($item['tour_count'] / $totalTours) * 100) : 0 }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
