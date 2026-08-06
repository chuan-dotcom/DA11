@extends('layouts.admin')

@section('title', $title)

@section('content')                
<style>
    .guest-group-shell {
        background: #f7f8fb;
        border-radius: 14px;
    }

    .soft-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
    }

    .soft-table thead th {
        font-size: 0.86rem;
        white-space: nowrap;
    }

    .soft-table tbody td {
        vertical-align: middle;
        font-size: 0.93rem;
    }

    .mini-badge {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 999px;
    }
</style>

<div class="container mt-4 guest-group-shell p-3 p-md-4">
    <div class="small text-uppercase text-muted fw-semibold mb-1">Quản lý hoạt động</div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/departures/create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tạo đoàn khách
        </a>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="soft-card p-3 p-md-4 mb-4">
        <h5 class="mb-3">Bộ lọc</h5>
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="tour_id" class="form-label small text-muted">Tour</label>
                <select name="tour_id" id="tour_id" class="form-select">
                    <option value="">-- Tất cả tour --</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour['id'] }}" {{ (int) ($tourId ?? 0) === (int) $tour['id'] ? 'selected' : '' }}>
                            {{ $tour['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label small text-muted">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="scheduled" {{ ($status ?? '') === 'scheduled' ? 'selected' : '' }}>Chờ khởi hành</option>
                    <option value="in_progress" {{ ($status ?? '') === 'in_progress' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                <a href="{{ route('admin/guest-groups') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="soft-card p-3 p-md-4">
        <h5 class="mb-3">Danh sách đoàn khách</h5>

        <div class="table-responsive">
            <table class="table soft-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đoàn</th>
                        <th>Tour</th>
                        <th>Số khách</th>
                        <th>Nhân sự/HDV</th>
                        <th>Khởi hành</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($guestGroups))
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Chưa có đoàn tour nào phù hợp.</td>
                        </tr>
                    @else
                        @foreach($guestGroups as $group)
                            @php
                                $statusMap = [
                                    'scheduled' => ['Chờ khởi hành', 'bg-warning text-dark'],
                                    'in_progress' => ['Đang diễn ra', 'bg-primary'],
                                    'completed' => ['Hoàn tất', 'bg-success'],
                                    'cancelled' => ['Đã hủy', 'bg-secondary'],
                                ];
                                $statusInfo = $statusMap[$group['status']] ?? [$group['status'], 'bg-secondary'];
                                $groupName = $group['group_name'] ?: ('Đoàn ' . ($group['tour_name'] ?? 'Tour #' . $group['tour_id']));
                                $staffInfo = $assignedStaffByDeparture[(int)$group['id']] ?? ['count' => 0, 'names' => [], 'has_lead' => false];
                            @endphp
                            <tr>
                                <td>#{{ $group['id'] }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $groupName }}</div>
                                </td>
                                <td>{{ $group['tour_name'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark mini-badge">
                                        {{ (int) ($group['assigned_people'] ?? 0) }}/{{ (int) ($group['max_participants'] ?? 0) }}
                                    </span>
                                </td>
                                <td>
                                    @if($staffInfo['count'] > 0)
                                        @if($staffInfo['has_lead'])
                                            <span class="badge bg-success text-white mini-badge me-1"><i class="bi bi-person-check-fill me-1"></i>Có HDV chính</span>
                                        @else
                                            <span class="badge bg-warning text-dark mini-badge me-1"><i class="bi bi-exclamation-triangle me-1"></i>Chưa có HDV chính</span>
                                        @endif
                                        <div class="small text-muted mt-1">{{ $staffInfo['count'] }} nhân sự
                                            @if(!empty($staffInfo['names']))
                                                · {{ implode(', ', array_slice($staffInfo['names'], 0, 2)) }}
                                                @if(count($staffInfo['names']) > 2)
                                                    +{{ count($staffInfo['names']) - 2 }}
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-danger text-white mini-badge"><i class="bi bi-person-x me-1"></i>Chưa phân công</span>
                                    @endif
                                </td>
                                <td>{{ !empty($group['departure_date']) ? date('Y-m-d', strtotime($group['departure_date'])) : '-' }}</td>
                                <td>
                                    <span class="badge mini-badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ (int)$group['id'] }}" class="btn btn-sm btn-outline-info me-1" title="Phân công HDV & nhân sự cho đoàn này">
                                        <i class="bi bi-person-plus"></i> Phân công HDV
                                    </a>
                                    <a href="{{ route('admin/guest-groups/print/' . $group['id']) }}" class="btn btn-sm btn-outline-secondary me-1" title="In danh sách" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="{{ route('admin/guest-groups/show/' . $group['id']) }}" class="btn btn-sm btn-primary me-1" title="Cập nhật">
                                        Cập nhật
                                    </a>
                                    <a href="{{ route('admin/departures/delete/' . $group['id']) }}"
                                       class="btn btn-sm btn-danger"
                                       title="Xóa"
                                       onclick="return confirm('Bạn có chắc muốn xóa đoàn khách này?')">
                                        Xóa
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
@endsection
