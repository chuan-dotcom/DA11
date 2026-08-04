@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .info-label {
        color: #6b7280;                 
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }
</style>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin/staff') }}" class="text-decoration-none">
                    <i class="bi bi-people-fill"></i> Danh sách Hướng dẫn viên
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-person"></i> {{ $staff['Hoten'] }}
            </li>
        </ol>
    </nav>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0"><i class="bi bi-person-badge"></i> Thông tin chi tiết Hướng dẫn viên</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin/staff/edit/' . $staff['HDV_id']) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin/staff') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <h2 class="mb-0 fw-bold">{{ $staff['Hoten'] }}</h2>
                        @if($staff['Status'] == 'active')
                            <span class="badge bg-success px-3 py-2">Active (Đang làm)</span>
                        @elseif($staff['Status'] == 'on_leave')
                            <span class="badge bg-warning text-dark px-3 py-2">On Leave (Nghỉ phép)</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Inactive (Đã nghỉ)</span>
                        @endif
                    </div>
                </div>

                {{-- Thông tin cơ bản --}}
                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-key me-1"></i> Mã</div>
                    <div class="info-value">#{{ $staff['HDV_id'] }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-telephone me-1"></i> Thông tin liên hệ</div>
                    <div class="info-value">{{ $staff['Lienhe'] ?: '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-gender-ambiguous me-1"></i> Giới tính</div>
                    <div class="info-value">{{ $staff['Gioitinh'] ?: '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-calendar-heart me-1"></i> Ngày sinh</div>
                    <div class="info-value">{{ !empty($staff['Ngaysinh']) ? date('d/m/Y', strtotime($staff['Ngaysinh'])) : '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-geo-alt me-1"></i> Địa chỉ cư trú</div>
                    <div class="info-value">{{ $staff['Diachi'] ?: '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-calendar-check me-1"></i> Ngày bắt đầu làm việc</div>
                    <div class="info-value">{{ !empty($staff['Ngaybatdaulam']) ? date('d/m/Y', strtotime($staff['Ngaybatdaulam'])) : '—' }}</div>
                </div>

                <hr class="my-2">

                {{-- Kỹ năng & Chứng chỉ --}}
                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-translate me-1"></i> Ngôn ngữ hướng dẫn</div>
                    <div class="info-value text-primary">{{ $staff['Ngonngu'] ?: '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-award me-1"></i> Chứng chỉ</div>
                    <div class="info-value">{{ $staff['chungchiHDV'] ?: '—' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-clock-history me-1"></i> Số năm kinh nghiệm</div>
                    <div class="info-value">{{ $staff['Kinhnghiem'] ? $staff['Kinhnghiem'] . ' năm' : '0 năm' }}</div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-star-fill text-warning me-1"></i> Điểm đánh giá trung bình</div>
                    <div class="info-value text-warning fs-5">
                        {{ $staff['Diemdanhgia'] ? number_format($staff['Diemdanhgia'], 1) . ' / 5.0' : 'Chưa có' }}
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="info-label"><i class="bi bi-diagram-3 me-1"></i> ID Nhóm</div>
                    <div class="info-value">{{ $staff['HDV_group_id'] ?: '—' }}</div>
                </div>

                <hr class="my-2">

                {{-- Ghi chú & Đánh giá --}}
                <div class="col-md-4">
                    <div class="info-label"><i class="bi bi-heart-pulse me-1"></i> Tình trạng sức khỏe</div>
                    <div class="info-value fw-normal">{{ $staff['Trangthaisuckhoe'] ?: '—' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="info-label"><i class="bi bi-chat-left-text me-1"></i> Nhận xét đánh giá</div>
                    <div class="info-value fw-normal">{{ $staff['Nhanxetdanhgia'] ?: '—' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="info-label"><i class="bi bi-journal-bookmark me-1"></i> Ghi chú nội bộ</div>
                    <div class="info-value fw-normal text-muted">{{ $staff['Ghichunoibo'] ?: '—' }}</div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light py-3">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin/staff/edit/' . $staff['HDV_id']) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin/staff/delete/' . $staff['HDV_id']) }}"
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc muốn xóa Hướng dẫn viên này?')">
                    <i class="bi bi-trash"></i> Xóa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
