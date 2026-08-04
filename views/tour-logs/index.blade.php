@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .log-card {
        transition: all 0.2s ease;
        border-left: 4px solid #0d6efd;
    }
    .log-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .status-published { border-left-color: #198754; }
    .status-draft { border-left-color: #6c757d; }
    .mood-emoji { font-size: 1.5rem; }
    .log-preview {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .stat-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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

    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Tổng số nhật ký</div>
                            <div class="fs-4 fw-bold">{{ $totalLogs }}</div>
                        </div>
                        <i class="bi bi-journal-text fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Hiển thị</div>
                            <div class="fs-4 fw-bold text-success">{{ count($logs) }}</div>
                        </div>
                        <i class="bi bi-eye fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Chuyến khởi hành</div>
                            <div class="fs-4 fw-bold text-info">{{ count($departures) }}</div>
                        </div>
                        <i class="bi bi-bus-front fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 d-flex gap-2 flex-wrap align-items-end">
        <a href="{{ route('admin/tour-logs/create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ghi nhật ký mới
        </a>
        <form method="get" class="d-flex gap-2 flex-wrap align-items-end ms-auto">
            <div>
                <label for="departure_id" class="form-label small mb-1">Chuyến khởi hành</label>
                <select name="departure_id" id="departure_id" class="form-select form-select-sm">
                    <option value="">Tất cả chuyến</option>
                    @foreach($departures as $d)
                        <option value="{{ $d['id'] }}" {{ (!empty($departureId) && (int)$departureId === (int)$d['id']) ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] ?? 'Tour' }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="form-label small mb-1">Trạng thái</label>
                <select name="status" id="status" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Đã công khai</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Nháp</option>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-funnel"></i> Lọc
            </button>
            @if(!empty($departureId) || $status !== null)
                <a href="{{ route('admin/tour-logs') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Xóa lọc
                </a>
            @endif
        </form>
    </div>

    @if(!empty($departures) && !empty($departureId))
        @php
            $selectedDeparture = null;
            foreach ($departures as $d) {
                if ((int)$d['id'] === (int)$departureId) { $selectedDeparture = $d; break; }
            }
        @endphp
        @if($selectedDeparture)
            <div class="alert alert-info py-2 mb-3 d-flex justify-content-between align-items-center">
                <span>
                    Đang xem nhật ký của chuyến:
                    <strong>{{ $selectedDeparture['tour_name'] ?? 'Tour' }}</strong>
                    ({{ date('d/m/Y', strtotime($selectedDeparture['departure_date'])) }})
                    @if(!empty($selectedDeparture['group_name'])) - {{ $selectedDeparture['group_name'] }} @endif
                </span>
                <a href="{{ route('admin/tour-logs/create') }}?departure_id={{ $selectedDeparture['id'] }}"
                   class="btn btn-sm btn-success">
                    <i class="bi bi-plus"></i> Thêm nhật ký chuyến này
                </a>
            </div>
        @endif
    @endif

    @if(empty($logs))
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Chưa có nhật ký nào</h5>
                <p class="text-muted mb-3">Hãy bắt đầu ghi lại những khoảnh khắc đáng nhớ của tour</p>
                <a href="{{ route('admin/tour-logs/create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ghi nhật ký đầu tiên
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($logs as $log)
                <div class="col-md-6 col-lg-4">
                    <div class="card log-card status-{{ $log['status'] }} h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ date('d/m/Y H:i', strtotime($log['log_date'])) }}
                                    </small>
                                    @if(!empty($log['location']))
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $log['location'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if(!empty($log['mood']))
                                        <span class="mood-emoji" title="Tâm trạng: {{ $log['mood'] }}">
                                            @php
                                                $moodEmoji = [
                                                    'happy' => '😊', 'excited' => '🎉', 'calm' => '😌',
                                                    'tired' => '😴', 'sad' => '😢', 'neutral' => '😐'
                                                ];
                                            @endphp
                                            {{ $moodEmoji[$log['mood']] ?? '📝' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <h5 class="card-title mb-2">
                                <a href="{{ route('admin/tour-logs/show/' . $log['id']) }}"
                                   class="text-dark text-decoration-none">
                                    {{ $log['title'] }}
                                </a>
                            </h5>

                            <div class="small text-info mb-2">
                                <i class="bi bi-bus-front me-1"></i>
                                {{ $log['tour_name'] ?? 'Tour' }}
                                @if(!empty($log['group_name']))
                                    <span class="text-muted">({{ $log['group_name'] }})</span>
                                @endif
                            </div>

                            <p class="card-text log-preview text-muted small mb-3">
                                {{ strip_tags($log['content']) }}
                            </p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-1">
                                    @if(!empty($log['weather']))
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-cloud-sun me-1"></i>{{ $log['weather'] }}
                                        </span>
                                    @endif
                                    @if($log['status'] === 'draft')
                                        <span class="badge bg-secondary"><i class="bi bi-lock"></i> Nháp</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-globe"></i> Công khai</span>
                                    @endif
                                    @php
                                        $imgCount = 0;
                                        $imgs = json_decode($log['images'] ?? '[]', true);
                                        if (is_array($imgs)) { $imgCount = count($imgs); }
                                    @endphp
                                    @if($imgCount > 0)
                                        <span class="badge bg-info">
                                            <i class="bi bi-image me-1"></i>{{ $imgCount }}
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin/tour-logs/show/' . $log['id']) }}"
                                       class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin/tour-logs/edit/' . $log['id']) }}"
                                       class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin/tour-logs/delete/' . $log['id']) }}"
                                       class="btn btn-sm btn-outline-danger" title="Xóa"
                                       onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
