@extends('layouts.hdv')

@section('title', $title)

@section('content')
<style>
    .diary-thumb {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .diary-title-link {
        color: #1e293b;
        text-decoration: none;
        font-weight: 700;
    }
    .diary-title-link:hover {
        color: #0284c7;
    }
    .stat-box {
        background: linear-gradient(135deg, #00bcd4, #00acc1);
        color: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
    }
</style>

<div class="hdv-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text text-info me-2"></i> {{ $title }}</h4>
        <a href="{{ route('hdv/nhat-ky-tour/create') }}{{ !empty($selectedDepartureId) ? '?departure_id=' . $selectedDepartureId : '' }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Viết nhật ký mới
        </a>
    </div>
    <p class="text-muted small mb-0">Chỉ hiển thị các nhật ký của các tour mà hướng dẫn viên {{ $activeHdv['Hoten'] ?? '' }} được phân công tham gia.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-box shadow-sm">
            <div class="small opacity-75 mb-1">Tổng số nhật ký đã viết</div>
            <div class="fs-2 fw-bold">{{ $totalDiaries ?? 0 }} bài</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box shadow-sm">
            <div class="small opacity-75 mb-1">Chuyến đang lọc</div>
            <div class="fs-5 fw-bold">{{ !empty($selectedDepartureId) ? ('#' . $selectedDepartureId) : 'Tất cả chuyến' }}</div>
        </div>
    </div>
</div>

<!-- Filter by Departure -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('hdv/nhat-ky-tour') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Lọc theo chuyến công tác của bạn</label>
                <select class="form-select form-select-sm fw-semibold" name="departure_id" onchange="this.form.submit()">
                    <option value="">-- Tất cả chuyến khởi hành đã tham gia --</option>
                    @foreach($departures as $d)
                        <option value="{{ $d['id'] }}" {{ !empty($selectedDepartureId) && $selectedDepartureId == $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if(!empty($selectedDepartureId))
                <div class="col-md-auto">
                    <a href="{{ route('hdv/nhat-ky-tour') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Bỏ lọc
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<div class="hdv-card">
    <div class="table-responsive">
        <table class="table table-hdv table-hover align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 100px;">Hình ảnh</th>
                    <th>Tiêu đề nhật ký</th>
                            <th>Tour / Danh mục / Đoàn</th>
                    <th>Ngày nhật ký</th>
                    <th>Thời tiết</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($diaries))
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                            Chưa có nhật ký tour nào. Nhấn nút "Viết nhật ký mới" để tạo bài viết.
                        </td>
                    </tr>
                @else
                    @foreach($diaries as $diary)
                        <tr>
                            <td class="fw-bold">#{{ $diary['id'] }}</td>
                            <td>
                                @php
                                    $firstPhoto = null;
                                    if (!empty($diary['photos'])) {
                                        $rawP = trim($diary['photos']);
                                        if (str_starts_with($rawP, '[') && str_ends_with($rawP, ']')) {
                                            $decoded = json_decode($rawP, true);
                                            if (is_array($decoded) && !empty($decoded)) {
                                                $firstPhoto = $decoded[0];
                                            }
                                        }
                                        if (!$firstPhoto) {
                                            $photos = explode(',', $rawP);
                                            $firstPhoto = trim($photos[0] ?? '', "[] \"'\t\n\r");
                                        }
                                    }
                                @endphp
                                @if($firstPhoto)
                                    <img src="{{ file_url($firstPhoto) }}" alt="{{ $diary['title'] }}" class="diary-thumb" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';">
                                @else
                                    <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80" alt="Placeholder" class="diary-thumb">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hdv/nhat-ky-tour/show/' . $diary['id']) }}" class="diary-title-link">
                                    {{ $diary['title'] }}
                                </a>
                                @if(!empty($diary['mood']))
                                    <div class="mt-1"><span class="badge bg-warning-subtle text-dark"><i class="bi bi-emoji-smile me-1"></i> {{ $diary['mood'] }}</span></div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold small">{{ $diary['tour_name'] ?? '—' }}</div>
                                <div class="text-muted small">Danh mục: {{ $diary['category_name'] ?? 'Chưa phân loại' }}</div>
                                <div class="text-muted small">
                                    {{ $diary['departure_group_name'] ?: ('Chuyến #' . $diary['departure_id']) }}
                                    @if(!empty($diary['tour_departure_date']))
                                        - {{ date('d/m/Y', strtotime($diary['tour_departure_date'])) }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}</div>
                            </td>
                            <td>
                                @if(!empty($diary['weather']))
                                    <span class="badge bg-info-subtle text-info-emphasis"><i class="bi bi-cloud-sun me-1"></i> {{ $diary['weather'] }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('hdv/nhat-ky-tour/show/' . $diary['id']) }}" class="btn btn-sm btn-info text-white rounded-pill px-2" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('hdv/nhat-ky-tour/edit/' . $diary['id']) }}" class="btn btn-sm btn-warning rounded-pill px-2" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('hdv/nhat-ky-tour/delete/' . $diary['id']) }}"
                                   class="btn btn-sm btn-danger rounded-pill px-2"
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

@endsection
