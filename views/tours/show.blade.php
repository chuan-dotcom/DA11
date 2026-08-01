@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .tour-detail-card {
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .tour-detail-image {
        width: 100%;
        height: 420px;
        object-fit: cover;
        display: block;
    }
    .tour-detail-image-empty {
        width: 100%;
        height: 420px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }
    .tour-price {
        color: #dc2626;
        font-size: 1.5rem;
        font-weight: 700;
    }
    .info-label {
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 2px;
    }
    .info-value {
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    .desc-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        white-space: pre-line;
        line-height: 1.7;
        color: #374151;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/tours') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="tour-detail-card">
        <div class="row g-0">
            <div class="col-md-6">
                @if(!empty($tour['image']))
                    <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" class="tour-detail-image">
                @else
                    <div class="tour-detail-image-empty">
                        <span><i class="bi bi-image" style="font-size:2rem;"></i><br>Không có ảnh</span>
                    </div>
                @endif
            </div>

            <div class="col-md-6 p-4">
                <h3 class="fw-bold mb-2">{{ $tour['name'] }}</h3>
                <div class="tour-price mb-3">{{ number_format($tour['price']) }} VNĐ</div>

                <div class="row">
                    <div class="col-6">
                        <div class="info-label">Danh mục</div>
                        <div class="info-value">
                            <i class="bi bi-tag"></i>
                            {{ $tour['category_name'] ?? 'Chưa phân loại' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Thời gian</div>
                        <div class="info-value">
                            <i class="bi bi-clock"></i>
                            {{ $tour['duration'] ?: 'Chưa cập nhật' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Trạng thái</div>
                        <div class="info-value">
                            @if($tour['status'] == 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-label">Mã tour</div>
                        <div class="info-value">#{{ $tour['id'] }}</div>
                    </div>
                </div>

                <hr>

                <h5 class="fw-bold mb-2">
                    <i class="bi bi-info-circle"></i> Mô tả / Giới thiệu tour
                </h5>
                <div class="desc-box">
                    @if(!empty(trim($tour['description'] ?? '')))
                        {{ $tour['description'] }}
                    @else
                        <span class="text-muted">Tour này chưa có mô tả giới thiệu.</span>
                    @endif
                </div>

                @php $detailUrl = absolute_url('tour/' . $tour['id']); @endphp
                <div class="mt-4 p-3 border rounded bg-white">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div id="tour-detail-qr"></div>
                        <div>
                            <div class="fw-semibold mb-1">
                                <i class="bi bi-qr-code"></i> Quét QR để mở trang chi tiết tour
                            </div>
                            <div class="small text-muted text-break">{{ $detailUrl }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin/tours/participants/' . $tour['id']) }}" class="btn btn-success">
                        <i class="bi bi-people"></i> Xem người tham gia
                    </a>
                    <a href="{{ route('admin/tours/edit/' . $tour['id']) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Sửa tour
                    </a>
                    <a href="{{ route('admin/tours') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul"></i> Danh sách tour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('tour-detail-qr'), {
        text: {!! json_encode($detailUrl) !!},
        width: 140,
        height: 140,
        colorDark: '#111827',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
@endsection
