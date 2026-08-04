@extends('layouts.admin')

@section('title', $title)
                  
@section('content')
<style>
    .status-assigned { background-color: #0d6efd; }
    .status-confirmed { background-color: #198754; }
    .status-completed { background-color: #0f5132; }
    .status-rejected { background-color: #dc3545; }
    .role-lead_guide { background-color: #084298; }
    .role-assistant_guide { background-color: #055160; }
    .role-driver { background-color: #997404; color: #000; }
    .role-photographer { background-color: #146c43; }
    .role-other { background-color: #6c757d; }
</style>

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Tổng phân bổ</div>
                            <div class="fs-4 fw-bold">{{ $totalAssignments }}</div>
                        </div>
                        <i class="bi bi-person-check fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('admin/staff-assignments/create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Phân bổ mới
        </a>
        <a href="{{ route('admin/departures') }}" class="btn btn-outline-secondary">
            <i class="bi bi-calendar3"></i> Quản lý khởi hành
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Chuyến khởi hành</th>
                            <th>Ngày đi</th>
                            <th>Nhân viên</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày phân bổ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($assignments))
                            <tr>
                                <td colspan="8" class="text-center">Chưa có phân bổ nào</td>
                            </tr>
                        @else
                            @php
                                $roleText = [
                                    'lead_guide' => 'HDV chính',
                                    'assistant_guide' => 'HDV phụ',
                                    'driver' => 'Lái xe',
                                    'photographer' => 'Nhiếp ảnh',
                                    'other' => 'Khác'
                                ];
                                $statusText = [
                                    'assigned' => 'Đã phân bổ',
                                    'confirmed' => 'Đã xác nhận',
                                    'completed' => 'Hoàn thành',
                                    'rejected' => 'Từ chối'
                                ];
                            @endphp
                            @foreach($assignments as $a)
                                <tr>
                                    <td>{{ $a['id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin/departures/show/' . $a['departure_id']) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $a['tour_name'] ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $a['departure_date'] ? date('d/m/Y', strtotime($a['departure_date'])) : '-' }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $a['staff_name'] ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge role-{{ $a['role'] }}">
                                            {{ $roleText[$a['role']] ?? $a['role'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $a['status'] }}">
                                            {{ $statusText[$a['status']] ?? $a['status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $a['assigned_at'] ? date('d/m/Y H:i', strtotime($a['assigned_at'])) : '-' }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/staff-assignments/show/' . $a['id']) }}" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/staff-assignments/edit/' . $a['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/staff-assignments/delete/' . $a['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa phân bổ này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
