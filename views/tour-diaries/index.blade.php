@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .diary-thumb {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }
    .diary-title-link {
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 600;
    }
    .diary-title-link:hover {
        color: #0d6efd;
        text-decoration: underline;
    }
    .mood-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        background: #fff3cd;
        color: #856404;
    }
    .weather-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        background: #d1ecf1;
        color: #0c5460;
    }
    .stat-box {
        background: linear-gradient(135deg, #18bfd4, #0ea5b7);
        color: white;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }
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

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-box">
                <div class="small opacity-75 mb-1">Tổng số nhật ký</div>
                <div class="fs-2 fw-bold">{{ $totalDiaries ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin/tour-diaries') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">Lọc theo chuyến khởi hành</label>
                    <select class="form-select" name="departure_id" onchange="this.form.submit()">
                        <option value="">-- Tất cả chuyến khởi hành --</option>
                        @foreach($departures as $d)
                            <option value="{{ $d['id'] }}" {{ !empty($departureId) && $departureId == $d['id'] ? 'selected' : '' }}>
                                #{{ $d['id'] }} - {{ $d['group_name'] ?: $d['tour_name'] }} ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : 'chưa xếp lịch' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7 text-end">
                    <a href="{{ route('admin/tour-diaries/create') }}{{ !empty($departureId) ? '?departure_id=' . $departureId : '' }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Thêm nhật ký
                    </a>
                    @if(!empty($departureId))
                        <a href="{{ route('admin/tour-diaries') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Bỏ lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Chuyến khởi hành / Tour</th>
                            <th>Ngày nhật ký</th>
                            <th>Thời tiết</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($diaries))
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-text fs-1 d-block mb-2 opacity-50"></i>
                                    Chưa có nhật ký nào. Hãy nhấn "Thêm nhật ký" để bắt đầu.
                                </td>
                            </tr>
                        @else
                            @foreach($diaries as $diary)
                                <tr>
                                    <td>{{ $diary['id'] }}</td>
                                    <td>
                                        @php
                                            $firstPhoto = null;
                                            if (!empty($diary['photos'])) {
                                                $photos = explode(',', $diary['photos']);
                                                $firstPhoto = $photos[0] ?? null;
                                            }
                                        @endphp
                                        @if($firstPhoto)
                                            <img src="{{ file_url($firstPhoto) }}" alt="" class="diary-thumb">
                                        @else
                                            <span class="text-muted small">Không ảnh</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="diary-title-link">
                                            {{ $diary['title'] }}
                                        </a>
                                        @if(!empty($diary['mood']))
                                            <div class="mt-1"><span class="mood-badge"><i class="bi bi-emoji-smile"></i> {{ $diary['mood'] }}</span></div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $diary['tour_name'] ?? '—' }}</div>
                                        <div class="text-muted small">{{ $diary['departure_group_name'] ?? 'Đoàn #' . $diary['departure_id'] }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}</div>
                                    </td>
                                    <td>
                                        @if(!empty($diary['weather']))
                                            <span class="weather-badge"><i class="bi bi-cloud-sun"></i> {{ $diary['weather'] }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/tour-diaries/edit/' . $diary['id']) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/tour-diaries/delete/' . $diary['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')">
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
