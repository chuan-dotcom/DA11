@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if($tour['image'])
                        <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" class="img-fluid rounded" style="width: 100%; height: 400px; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 400px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">Không có ảnh</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h3>{{ $tour['name'] }}</h3>
                    <h4 class="text-danger">{{ number_format($tour['price']) }} VNĐ</h4>
                    <p><strong>Danh mục:</strong> {{ $tour['category_name'] }}</p>
                    <p><strong>Thời gian:</strong> {{ $tour['duration'] }}</p>
                    <p><strong>Trạng thái:</strong> 
                        @if($tour['status'] == 1)
                            <span class="badge bg-success">Hiển thị</span>
                        @else
                            <span class="badge bg-secondary">Ẩn</span>
                        @endif
                    </p>
                    <hr>
                    <h5>Mô tả</h5>
                    <p>{{ $tour['description'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin/tours/edit/' . $tour['id']) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Sửa
        </a>
        <a href="{{ route('admin/tours') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>
@endsection