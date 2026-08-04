@extends('layouts.admin')
                
@section('title', $title)

@section('content')
<style>
    .info-row { border-bottom: 1px solid #f0f0f0; padding: 10px 0; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6c757d; font-weight: 500; }
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

    <div class="row mb-3">
        <div class="col-12 d-flex gap-2 flex-wrap">
            <a href="{{ route('admin/staff-assignments') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Danh sách phân bổ
            </a>
            <a href="{{ route('admin/staff-assignments/edit/' . $assignment['id']) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('admin/departures/show/' . $assignment['departure_id']) }}" class="btn btn-info text-white">
                <i class="bi bi-calendar3"></i> Xem chuyến khởi hành
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Thông tin phân bổ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Mã phân bổ:</span>
                                <div class="fw-bold">#{{ str_pad($assignment['id'], 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Chuyến khởi hành:</span>
                                <div class="fw-semibold">{{ $assignment['tour_name'] ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày đi:</span>
                                <div class="fw-semibold">{{ $assignment['departure_date'] ? date('d/m/Y', strtotime($assignment['departure_date'])) : '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày về:</span>
                                <div class="fw-semibold">{{ $assignment['return_date'] ? date('d/m/Y', strtotime($assignment['return_date'])) : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Nhân viên:</span>
                                <div class="fw-bold fs-5">{{ $assignment['staff_name'] ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Liên hệ:</span>
                                <div class="fw-semibold">{{ $assignment['staff_phone'] ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngôn ngữ:</span>
                                <div class="fw-semibold">{{ $assignment['staff_languages'] ?? 'Tiếng Việt' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Vai trò:</span>
                                <div>
                                    @php
                                        $roleMap = [
                                            'lead_guide' => ['HDV chính', 'bg-primary'],
                                            'assistant_guide' => ['HDV phụ', 'bg-info'],
                                            'driver' => ['Lái xe', 'bg-warning text-dark'],
                                            'photographer' => ['Nhiếp ảnh', 'bg-success'],
                                            'other' => ['Khác', 'bg-secondary']
                                        ];
                                        $statusMap = [
                                            'assigned' => ['Đã phân bổ', 'bg-primary'],
                                            'confirmed' => ['Đã xác nhận', 'bg-success'],
                                            'completed' => ['Hoàn thành', 'bg-dark'],
                                            'rejected' => ['Từ chối', 'bg-danger']
                                        ];
                                        $r = $roleMap[$assignment['role']] ?? [$assignment['role'], 'bg-secondary'];
                                    @endphp
                                    <span class="badge {{ $r[1] }}">{{ $r[0] }}</span>
                                </div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Trạng thái:</span>
                                <div>
                                    @php $s = $statusMap[$assignment['status']] ?? [$assignment['status'], 'bg-secondary']; @endphp
                                    <span class="badge {{ $s[1] }}">{{ $s[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($assignment['responsibilities'])
                        <div class="info-row mt-3">
                            <span class="info-label">Trách nhiệm chính:</span>
                            <div>{{ nl2br(htmlspecialchars($assignment['responsibilities'])) }}</div>
                        </div>
                    @endif

                    @if($assignment['notes'])
                        <div class="info-row">
                            <span class="info-label">Ghi chú:</span>
                            <div>{{ nl2br(htmlspecialchars($assignment['notes'])) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Ngày phân bổ:</span>
                        <div class="fw-semibold">{{ $assignment['assigned_at'] ? date('d/m/Y H:i', strtotime($assignment['assigned_at'])) : '-' }}</div>
                    </div>
                    @if($assignment['updated_at'])
                        <div class="info-row">
                            <span class="info-label">Cập nhật lần cuối:</span>
                            <div class="fw-semibold">{{ date('d/m/Y H:i', strtotime($assignment['updated_at'])) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
