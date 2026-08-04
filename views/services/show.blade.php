@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $title }} #{{ $service['id'] }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin/services') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin/services/edit/' . $service['id']) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Sửa
            </a>
        </div>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Tour</div>
                <div class="col-md-8">
                    #{{ $service['tour_id'] }} - {{ $service['tour_name'] }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Loại dịch vụ</div>
                <div class="col-md-8">{{ $service['service_types'] }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Nhà cung cấp</div>
                <div class="col-md-8">{{ $service['supplier'] }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Số lượng</div>
                <div class="col-md-8">{{ $service['quantity'] }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Trạng thái</div>
                <div class="col-md-8">
                    @if($service['status'] == 0)
                        <span class="badge" style="background: #facc15; color: #78350f; font-size: 0.9rem; padding: 0.45rem 1rem; border-radius: 9999px;">Chờ</span>
                    @elseif($service['status'] == 1)
                        <span class="badge" style="background: #0ea5e9; color: white; font-size: 0.9rem; padding: 0.45rem 1rem; border-radius: 9999px;">Xác nhận</span>
                    @else
                        <span class="badge" style="background: #16a34a; color: white; font-size: 0.9rem; padding: 0.45rem 1rem; border-radius: 9999px;">Hoàn tất</span>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Thời gian bắt đầu</div>
                <div class="col-md-8">
                    {{ $service['start_time'] ? date('Y-m-d H:i:s', strtotime($service['start_time'])) : '-' }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Thời gian kết thúc</div>
                <div class="col-md-8">
                    {{ $service['end_time'] ? date('Y-m-d H:i:s', strtotime($service['end_time'])) : '-' }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Ghi chú</div>
                <div class="col-md-8">{{ $service['note'] ?: '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Ngày tạo</div>
                <div class="col-md-8">
                    {{ $service['created_at'] ? date('Y-m-d H:i:s', strtotime($service['created_at'])) : '-' }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 fw-bold">Cập nhật lần cuối</div>
                <div class="col-md-8">
                    {{ $service['updated_at'] ? date('Y-m-d H:i:s', strtotime($service['updated_at'])) : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
