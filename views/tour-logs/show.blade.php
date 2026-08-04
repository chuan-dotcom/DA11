@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .log-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        background: rgba(255,255,255,0.15);
        border-radius: 20px;
        backdrop-filter: blur(4px);
        font-size: 0.9rem;
    }
    .content-body {
        line-height: 1.8;
        font-size: 1.05rem;
        color: #333;
    }
    .content-body p { margin-bottom: 1rem; }
    .gallery-img {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s;
        cursor: pointer;
    }
    .gallery-img:hover { transform: scale(1.02); }
    .gallery-img img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .related-item {
        padding: 12px;
        border-radius: 8px;
        border-left: 3px solid #0d6efd;
        background: #f8f9fa;
        transition: background 0.2s;
    }
    .related-item:hover { background: #e7f1ff; }
    .mood-big { font-size: 3rem; }
    #imageModal .modal-body img {
        max-width: 100%;
        border-radius: 8px;
    }
</style>

<div class="container mt-4">
    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="{{ route('admin/tour-logs') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Danh sách nhật ký
        </a>
        <a href="{{ route('admin/tour-logs/edit/' . $log['id']) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Sửa nhật ký
        </a>
        <a href="{{ route('admin/tour-logs/create') }}?departure_id={{ $log['departure_id'] }}"
           class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Ghi tiếp nhật ký
        </a>
        <a href="{{ route('admin/tour-logs/delete/' . $log['id']) }}"
           class="btn btn-danger ms-auto"
           onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')">
            <i class="bi bi-trash"></i> Xóa
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="log-header">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <span class="info-chip">
                                <i class="bi bi-calendar-event"></i>
                                {{ date('d/m/Y H:i', strtotime($log['log_date'])) }}
                            </span>
                            @if(!empty($log['location']))
                                <span class="info-chip">
                                    <i class="bi bi-geo-alt"></i> {{ $log['location'] }}
                                </span>
                            @endif
                            @if(!empty($log['weather']))
                                <span class="info-chip">
                                    <i class="bi bi-cloud-sun"></i> {{ $log['weather'] }}
                                </span>
                            @endif
                            <span class="info-chip">
                                {{ $log['status'] === 'draft' ? '📝 Nháp' : '✅ Công khai' }}
                            </span>
                        </div>
                        <h1 class="h2 mb-0 fw-bold">{{ $log['title'] }}</h1>
                    </div>
                    @if(!empty($log['mood']))
                        <div class="text-center">
                            @php
                                $moodEmoji = [
                                    'happy'=>'😊','excited'=>'🎉','calm'=>'😌',
                                    'tired'=>'😴','sad'=>'😢','neutral'=>'😐'
                                ];
                                $moodText = [
                                    'happy'=>'Vui vẻ','excited'=>'Phấn khởi','calm'=>'Bình yên',
                                    'tired'=>'Mệt mỏi','sad'=>'Buồn','neutral'=>'Bình thường'
                                ];
                            @endphp
                            <div class="mood-big">{{ $moodEmoji[$log['mood']] ?? '📝' }}</div>
                            <div class="small opacity-75">{{ $moodText[$log['mood']] ?? $log['mood'] }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="content-body">
                        {!! nl2br(htmlspecialchars($log['content'])) !!}
                    </div>
                </div>
            </div>

            @if(!empty($images))
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-images me-2 text-primary"></i>Hình ảnh ({{ count($images) }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($images as $idx => $img)
                                <div class="col-6 col-md-4">
                                    <div class="gallery-img"
                                         onclick="showImage('{{ file_url($img) }}', 'Ảnh {{ $idx + 1 }}')">
                                        <img src="{{ file_url($img) }}" alt="Ảnh {{ $idx + 1 }}" loading="lazy">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bus-front me-2"></i>Thông tin tour</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Tour du lịch</div>
                        <div class="fw-bold fs-5">{{ $log['tour_name'] ?? 'Chưa cập nhật' }}</div>
                    </div>
                    @if(!empty($log['group_name']))
                        <div class="mb-3">
                            <div class="text-muted small">Tên đoàn</div>
                            <div class="fw-semibold">{{ $log['group_name'] }}</div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <div class="text-muted small">Ngày khởi hành</div>
                        <div class="fw-semibold">
                            {{ $log['departure_date'] ? date('d/m/Y', strtotime($log['departure_date'])) : '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Ngày trở về</div>
                        <div class="fw-semibold">
                            {{ $log['return_date'] ? date('d/m/Y', strtotime($log['return_date'])) : '-' }}
                        </div>
                    </div>
                    <a href="{{ route('admin/departures/show/' . $log['departure_id']) }}"
                       class="btn btn-outline-info w-100">
                        <i class="bi bi-eye"></i> Xem chi tiết chuyến
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin khác</h5>
                </div>
                <div class="card-body">
                    <div class="small mb-2">
                        <span class="text-muted">Mã nhật ký:</span>
                        <span class="fw-bold">#{{ str_pad($log['id'], 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="small mb-2">
                        <span class="text-muted">Người viết:</span>
                        <span class="fw-semibold">{{ $log['author_name'] ?? 'Hệ thống' }}</span>
                    </div>
                    <div class="small mb-2">
                        <span class="text-muted">Tạo lúc:</span>
                        <span class="fw-semibold">
                            {{ $log['created_at'] ? date('d/m/Y H:i', strtotime($log['created_at'])) : '-' }}
                        </span>
                    </div>
                    @if(!empty($log['updated_at']) && $log['updated_at'] !== $log['created_at'])
                        <div class="small mb-2">
                            <span class="text-muted">Cập nhật:</span>
                            <span class="fw-semibold">{{ date('d/m/Y H:i', strtotime($log['updated_at'])) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if(!empty($relatedLogs))
                <div class="card">
                    <div class="card-header bg-warning bg-opacity-20">
                        <h5 class="mb-0"><i class="bi bi-journal me-2"></i>Nhật ký khác cùng chuyến</h5>
                    </div>
                    <div class="card-body p-2">
                        @foreach($relatedLogs as $r)
                            <a href="{{ route('admin/tour-logs/show/' . $r['id']) }}"
                               class="text-decoration-none text-dark d-block mb-2">
                                <div class="related-item">
                                    <div class="fw-semibold small mb-1">{{ $r['title'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ date('d/m/Y H:i', strtotime($r['log_date'])) }}
                                        @if(!empty($r['location']))
                                            · <i class="bi bi-geo-alt ms-1 me-1"></i>{{ $r['location'] }}
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImg" src="" alt="">
            </div>
        </div>
    </div>
</div>

<script>
function showImage(src, title) {
    document.getElementById('imageModalImg').src = src;
    document.getElementById('imageModalTitle').textContent = title;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>
@endsection
