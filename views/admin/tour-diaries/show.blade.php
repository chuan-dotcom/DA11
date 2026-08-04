@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .diary-hero {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        background: linear-gradient(135deg, #0ea5e9 0%, #18bfd4 50%, #14b8a6 100%);
        color: white;
        padding: 2rem 1.75rem;
        box-shadow: 0 10px 25px rgba(8, 47, 73, 0.18);
    }
    .diary-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.15) 0%, transparent 50%);
        pointer-events: none;
    }
    .diary-hero > * { position: relative; z-index: 2; }
    .diary-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255,255,255,0.18);
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 0.85rem;
        backdrop-filter: blur(4px);
        margin-right: 6px;
        margin-bottom: 4px;
    }
    .diary-body {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 2rem 1.75rem;
        white-space: pre-line;
        line-height: 1.8;
        color: #1f2937;
        font-size: 1.05rem;
    }
    .diary-photo-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform .15s ease;
    }
    .diary-photo-card:hover { transform: translateY(-2px); }
    .diary-photo-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }
    .info-tile {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.9rem 1rem;
    }
    .info-tile-label {
        font-size: 0.78rem;
        color: #6b7280;
        margin-bottom: 0.2rem;
    }
    .info-tile-value {
        font-weight: 700;
        color: #111827;
    }
    .other-diary-item {
        border-left: 3px solid #18bfd4;
        padding: 0.5rem 0.9rem;
        background: #f9fafb;
        border-radius: 0 8px 8px 0;
    }
    .lightbox {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.88);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .lightbox img {
        max-width: 92%;
        max-height: 92vh;
        border-radius: 10px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .lightbox-close {
        position: absolute; top: 18px; right: 22px;
        background: white;
        border: none;
        width: 40px; height: 40px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="mb-0">{{ $title }}</h2>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="btn btn-outline-secondary btn-sm d-none">
                <i class="bi bi-arrow-clockwise"></i> Tải lại
            </a>
            <a href="{{ route('admin/tour-diaries/edit/' . $diary['id']) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Sửa nhật ký
            </a>
            <a href="{{ route('admin/tour-diaries') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Danh sách
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

    <div class="diary-hero mb-4">
        <h3 class="fw-bold mb-2">{{ $diary['title'] }}</h3>
        <div class="mb-3">
            <span class="diary-meta-chip"><i class="bi bi-calendar-event"></i> {{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}</span>
            <span class="diary-meta-chip"><i class="bi bi-airplane"></i> {{ $diary['tour_name'] ?? 'Chưa gắn tour' }}</span>
            <span class="diary-meta-chip"><i class="bi bi-people"></i> {{ $diary['departure_group_name'] ?? 'Đoàn #' . $diary['departure_id'] }}</span>
            @if(!empty($diary['weather']))
                <span class="diary-meta-chip"><i class="bi bi-cloud-sun"></i> {{ $diary['weather'] }}</span>
            @endif
            @if(!empty($diary['mood']))
                <span class="diary-meta-chip"><i class="bi bi-emoji-smile"></i> {{ $diary['mood'] }}</span>
            @endif
        </div>
        <div class="small opacity-75">
            @if(!empty($diary['tour_departure_date']) || !empty($diary['tour_return_date']))
                <i class="bi bi-calendar-range"></i>
                Lịch trình: {{ !empty($diary['tour_departure_date']) ? date('d/m/Y', strtotime($diary['tour_departure_date'])) : '?' }}
                &rarr;
                {{ !empty($diary['tour_return_date']) ? date('d/m/Y', strtotime($diary['tour_return_date'])) : '?' }}
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="diary-body mb-4">
                {!! nl2br(e($diary['content'])) !!}
            </div>

            @if(!empty($photos))
                <h5 class="fw-bold mb-3"><i class="bi bi-images"></i> Hình ảnh ({{ count($photos) }})</h5>
                <div class="row g-3 mb-4">
                    @foreach($photos as $i => $p)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="diary-photo-card" onclick="openLightbox(this.querySelector('img').src)">
                                <img src="{{ file_url($p) }}" alt="Hình ảnh {{ $i + 1 }}" class="cursor-pointer diary-photo-img">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history"></i> Timeline hoạt động chuyến đi</div>
                <div class="card-body">
                    @if(empty($tourLogs))
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-info-circle fs-2 mb-2 d-block"></i>
                            Chưa có ghi nhận timeline hoạt động cho chuyến này.
                        </div>
                    @else
                        <div class="timeline-list">
                            @foreach($tourLogs as $log)
                                <div class="timeline-item mb-3 p-3 rounded-4 border bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $log['title'] }}</h6>
                                            <div class="small text-muted">
                                                {{ !empty($log['log_date']) ? date('d/m/Y H:i', strtotime($log['log_date'])) : 'Không có thời gian' }}
                                                @if(!empty($log['location']))
                                                    · {{ $log['location'] }}
                                                @endif
                                            </div>
                                        </div>
                                        @if(!empty($log['weather']))
                                            <span class="badge bg-primary text-white">{{ $log['weather'] }}</span>
                                        @endif
                                    </div>
                                    <p class="mb-2 text-dark">{{ $log['content'] }}</p>
                                    <div class="d-flex flex-wrap gap-2 small text-muted">
                                        @if(!empty($log['mood']))
                                            <span class="badge bg-white border">Tâm trạng: {{ $log['mood'] }}</span>
                                        @endif
                                        @if(!empty($log['location']))
                                            <span class="badge bg-white border">Địa điểm: {{ $log['location'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Mã nhật ký</div>
                <div class="info-tile-value">#{{ $diary['id'] }}</div>
            </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Mã chuyến khởi hành</div>
                <div class="info-tile-value">
                    <a href="{{ route('admin/departures/show/' . $diary['departure_id']) }}" class="text-decoration-none">
                        #{{ $diary['departure_id'] }}
                    </a>
                </div>
            </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Thời tiết</div>
                <div class="info-tile-value">
                    @if(!empty($diary['weather']))
                        <i class="bi bi-cloud-sun"></i> {{ $diary['weather'] }}
                    @else
                        <span class="text-muted fw-normal">Chưa ghi nhận</span>
                    @endif
                </div>
            </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Tâm trạng</div>
                <div class="info-tile-value">
                    @if(!empty($diary['mood']))
                        <i class="bi bi-emoji-smile"></i> {{ $diary['mood'] }}
                    @else
                        <span class="text-muted fw-normal">Chưa ghi nhận</span>
                    @endif
                </div>
            </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Số hình ảnh</div>
                <div class="info-tile-value"><i class="bi bi-image"></i> {{ count($photos) }} ảnh</div>
            </div>
            <div class="info-tile mb-3">
                <div class="info-tile-label">Ngày tạo</div>
                <div class="info-tile-value small">
                    {{ !empty($diary['created_at']) ? date('d/m/Y H:i', strtotime($diary['created_at'])) : '—' }}
                </div>
            </div>
            <div class="info-tile mb-4">
                <div class="info-tile-label">Cập nhật lần cuối</div>
                <div class="info-tile-value small">
                    {{ !empty($diary['updated_at']) ? date('d/m/Y H:i', strtotime($diary['updated_at'])) : '—' }}
                </div>
            </div>

            @if(!empty($otherDiaries) && count($otherDiaries) > 1)
                <div class="card">
                    <div class="card-header fw-bold bg-white"><i class="bi bi-journal-text"></i> Nhật ký khác của đoàn này</div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-column gap-2">
                            @foreach($otherDiaries as $od)
                                @if($od['id'] != $diary['id'])
                                    <a href="{{ route('admin/tour-diaries/show/' . $od['id']) }}" class="text-decoration-none">
                                        <div class="other-diary-item">
                                            <div class="fw-semibold text-dark">{{ $od['title'] }}</div>
                                            <div class="small text-muted">
                                                {{ !empty($od['diary_date']) ? date('d/m/Y', strtotime($od['diary_date'])) : 'Chưa ghi ngày' }}
                                                @if(!empty($od['weather'])) · Thời tiết: {{ $od['weather'] }} @endif
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="lightbox" id="photoLightbox" onclick="closeLightbox()">
    <button type="button" class="lightbox-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
    <img id="lightboxImg" src="" alt="">
</div>
<script>
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('photoLightbox').style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('photoLightbox').style.display = 'none';
    }
    document.querySelectorAll('.diary-photo-img').forEach(function (img) {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function () {
            openLightbox(img.src);
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endsection
