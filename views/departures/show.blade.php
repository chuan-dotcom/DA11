@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .status-badge { font-size: 0.85rem; padding: 6px 12px; }
    .info-row { border-bottom: 1px solid #f0f0f0; padding: 10px 0; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6c757d; font-weight: 500; }
    .role-chip { font-size: 0.75rem; }
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
            <a href="{{ route('admin/departures') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Danh sách khởi hành
            </a>
            <a href="{{ route('admin/departures/edit/' . $departure['id']) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ $departure['id'] }}" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Phân bổ nhân sự
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin chuyến khởi hành</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Tour:</span>
                                <div class="fw-bold fs-5">{{ $departure['tour_name'] ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày khởi hành:</span>
                                <div class="fw-semibold">{{ date('d/m/Y', strtotime($departure['departure_date'])) }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày trở về:</span>
                                <div class="fw-semibold">{{ $departure['return_date'] ? date('d/m/Y', strtotime($departure['return_date'])) : '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Thời gian:</span>
                                <div class="fw-semibold">{{ $departure['tour_duration'] ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Điểm tập trung:</span>
                                <div class="fw-semibold">{{ $departure['meeting_point'] ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Giờ tập trung:</span>
                                <div class="fw-semibold">{{ $departure['meeting_time'] ? date('H:i', strtotime($departure['meeting_time'])) : '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Phương tiện:</span>
                                <div class="fw-semibold">{{ $departure['vehicle'] ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Số khách tối đa:</span>
                                <div class="fw-semibold">{{ $departure['max_participants'] }} người</div>
                            </div>
                        </div>
                    </div>
                    <div class="info-row mt-3">
                        <span class="info-label">Trạng thái:</span>
                        <div>
                            @php
                                $statusMap = [
                                    'scheduled' => ['Lên lịch', 'bg-primary'],
                                    'in_progress' => ['Đang diễn ra', 'bg-warning text-dark'],
                                    'completed' => ['Hoàn thành', 'bg-success'],
                                    'cancelled' => ['Đã hủy', 'bg-secondary']
                                ];
                                $s = $statusMap[$departure['status']] ?? [$departure['status'], 'bg-secondary'];
                            @endphp
                            <span class="badge status-badge {{ $s[1] }}">{{ $s[0] }}</span>
                        </div>
                    </div>
                    @if($departure['notes'])
                        <div class="info-row">
                            <span class="info-label">Ghi chú:</span>
                            <div>{{ nl2br(htmlspecialchars($departure['notes'])) }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Nhân sự tham gia</h5>
                </div>
                <div class="card-body">
                    @if(empty($assignments))
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                            Chưa phân bổ nhân sự cho chuyến này
                            <br>
                            <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ $departure['id'] }}" class="btn btn-sm btn-success mt-3">
                                <i class="bi bi-person-plus"></i> Phân bổ ngay
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Vai trò</th>
                                        <th>Liên hệ</th>
                                        <th>Ngôn ngữ</th>
                                        <th>Trách nhiệm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $roleMap = [
                                            'lead_guide' => ['HDV chính', 'bg-primary'],
                                            'assistant_guide' => ['HDV phụ', 'bg-info'],
                                            'driver' => ['Lái xe', 'bg-warning text-dark'],
                                            'photographer' => ['Nhiếp ảnh', 'bg-success'],
                                            'other' => ['Khác', 'bg-secondary']
                                        ];
                                    @endphp
                                    @foreach($assignments as $a)
                                        <tr>
                                            <td class="fw-semibold">
                                                <i class="bi bi-person-circle me-1"></i>
                                                {{ $a['staff_name'] }}
                                            </td>
                                            <td>
                                                @php $r = $roleMap[$a['role']] ?? [$a['role'], 'bg-secondary']; @endphp
                                                <span class="badge role-chip {{ $r[1] }}">{{ $r[0] }}</span>
                                            </td>
                                            <td>{{ $a['staff_phone'] ?? '-' }}</td>
                                            <td>{{ $a['staff_languages'] ?? '-' }}</td>
                                            <td>{{ $a['responsibilities'] ?? '-' }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('admin/staff-assignments/show/' . $a['id']) }}" class="btn btn-sm btn-info text-white">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin/staff-assignments/edit/' . $a['id']) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Thông tin khác</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Mã chuyến:</span>
                        <div class="fw-bold">#{{ str_pad($departure['id'], 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Giá tour:</span>
                        <div class="fw-semibold text-danger">{{ number_format($departure['tour_price'] ?? 0) }} VNĐ</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Số nhân sự:</span>
                        <div class="fw-semibold">{{ count($assignments) }} người</div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ngày tạo:</span>
                        <div class="fw-semibold">{{ $departure['created_at'] ? date('d/m/Y H:i', strtotime($departure['created_at'])) : '-' }}</div>
                    </div>
                    @if($departure['updated_at'])
                        <div class="info-row">
                            <span class="info-label">Cập nhật lần cuối:</span>
                            <div class="fw-semibold">{{ date('d/m/Y H:i', strtotime($departure['updated_at'])) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
