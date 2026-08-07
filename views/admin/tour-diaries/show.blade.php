@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .diary-detail-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border-radius: 14px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 20px rgba(14, 165, 233, 0.15);
    }
    .diary-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.825rem;
        backdrop-filter: blur(4px);
    }
    .diary-content-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.5rem 1.75rem;
        white-space: pre-line;
        line-height: 1.8;
        color: #1e293b;
        font-size: 1rem;
    }
    .diary-photo-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform .15s ease;
    }
    .diary-photo-card:hover {
        transform: translateY(-3px);
    }
    .diary-photo-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        cursor: pointer;
    }
    .side-info-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .side-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 0.875rem;
    }
    .side-info-row:last-child {
        border-bottom: none;
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

<div class="container-fluid px-0">
    <!-- Action Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin/tour-diaries') }}?departure_id={{ $diary['departure_id'] }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Danh sách nhật ký
            </a>
            <span class="text-muted small">| Bài nhật ký #{{ $diary['id'] }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin/tour-diaries/edit/' . $diary['id']) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                <i class="bi bi-pencil me-1"></i> Chỉnh sửa bài viết
            </a>
        </div>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['flash']['success'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif

    <!-- Main Content Layout -->
    <div class="row g-4">
        <!-- Left Column: Main Post Detail (8/12) -->
        <div class="col-lg-8">
            <!-- Hero Header -->
            <div class="diary-detail-hero mb-4">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="diary-chip"><i class="bi bi-calendar-event"></i> {{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}</span>
                    <span class="diary-chip"><i class="bi bi-geo-alt"></i> {{ $diary['tour_name'] ?? 'Chuyến đi' }}</span>
                    @if(!empty($diary['diary_title']))
                        <span class="diary-chip bg-warning text-dark fw-bold"><i class="bi bi-clock-history"></i> Mốc: {{ $diary['diary_title'] }}</span>
                    @endif
                </div>
                <h3 class="fw-bold mb-0 text-white">{{ $diary['title'] }}</h3>
            </div>

            <!-- Detailed Content Box -->
            <div class="diary-content-box mb-4 shadow-sm">
                <div class="fw-bold text-secondary small text-uppercase mb-2 tracking-wider">
                    <i class="bi bi-journal-text me-1"></i> Nội dung chép thực địa
                </div>
                <div>{!! nl2br(e($diary['content'])) !!}</div>
            </div>

            <!-- Photos Gallery -->
            @if(!empty($photos))
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-images me-2 text-primary"></i> Bộ sưu tập hình ảnh thực địa ({{ count($photos) }})
                        </h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            @foreach($photos as $i => $p)
                                <div class="col-6 col-md-4">
                                    <div class="diary-photo-card" onclick="openLightbox(this.querySelector('img').src)">
                                        <img src="{{ file_url($p) }}" alt="Hình ảnh {{ $i + 1 }}" class="diary-photo-img" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Streamlined Info Sidebar (4/12) -->
        <div class="col-lg-4">
            <!-- 1. Financial Summary for this item -->
            <div class="side-info-card mb-4 border-start border-4 border-primary">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-cash-coin me-1 text-primary"></i> Tài chính mốc nhật ký này
                </h6>
                
                <div class="side-info-row">
                    <span class="text-muted">Chi phí thực tế:</span>
                    <span class="fw-bold text-primary fs-6">
                        @if(!empty($diary['actual_cost']) && (float)$diary['actual_cost'] > 0)
                            {{ number_format($diary['actual_cost'], 0, ',', '.') }} VNĐ
                        @else
                            <span class="text-muted fw-normal">—</span>
                        @endif
                    </span>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Chi phí phát sinh:</span>
                    <span class="fw-bold text-warning-emphasis fs-6">
                        @if(!empty($diary['expense_amount']) && (float)$diary['expense_amount'] > 0)
                            +{{ number_format($diary['expense_amount'], 0, ',', '.') }} VNĐ
                        @else
                            <span class="text-muted fw-normal">—</span>
                        @endif
                    </span>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Danh mục chi:</span>
                    <span class="badge bg-secondary-subtle text-dark fw-semibold px-2.5 py-1">
                        {{ $diary['expense_category'] ?: 'Khác' }}
                    </span>
                </div>
            </div>

            <!-- 2. Management & Meta Details -->
            <div class="side-info-card">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-info-circle me-1 text-info"></i> Thông tin ghi nhận
                </h6>

                <div class="side-info-row">
                    <span class="text-muted">Người viết nhật ký:</span>
                    <span class="fw-bold text-dark">{{ $diary['author_hdv_name'] ?? 'HDV Phụ trách' }}</span>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Mã chuyến đi:</span>
                    <a href="{{ route('admin/departures/show/' . $diary['departure_id']) }}" class="fw-bold text-primary text-decoration-none">
                        #{{ $diary['departure_id'] }} ({{ $diary['departure_group_name'] ?? 'Đoàn tour' }})
                    </a>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Thời tiết:</span>
                    <span class="fw-semibold text-dark">
                        @if(!empty($diary['weather']))
                            <i class="bi bi-cloud-sun text-warning me-1"></i>{{ $diary['weather'] }}
                        @else
                            <span class="text-muted fw-normal">—</span>
                        @endif
                    </span>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Không khí / Cảm xúc:</span>
                    <span class="fw-semibold text-dark">
                        @if(!empty($diary['mood']))
                            <i class="bi bi-emoji-smile text-success me-1"></i>{{ $diary['mood'] }}
                        @else
                            <span class="text-muted fw-normal">—</span>
                        @endif
                    </span>
                </div>

                <div class="side-info-row">
                    <span class="text-muted">Ngày ghi chép:</span>
                    <span class="fw-semibold text-dark">{{ !empty($diary['created_at']) ? date('d/m/Y H:i', strtotime($diary['created_at'])) : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
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
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endsection
