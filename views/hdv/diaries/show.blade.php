@extends('layouts.hdv')

@section('title', $title)

@section('content')

<div class="hdv-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-primary mb-2"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y', strtotime($diary['diary_date'])) }}</span>
            <h3 class="fw-bold text-dark mb-1">{{ $diary['title'] }}</h3>
            <div class="text-muted small">Tour: <strong>{{ $diary['tour_name'] }}</strong> (Chuyến khởi hành #{{ $diary['departure_id'] }})</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hdv/nhat-ky-tour/edit/' . $diary['id']) }}" class="btn btn-warning rounded-pill px-3 fw-bold">
                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('hdv/nhat-ky-tour') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="hdv-card">
            <div class="d-flex gap-3 mb-4">
                @if(!empty($diary['weather']))
                    <div class="bg-info-subtle text-info-emphasis rounded-3 px-3 py-2 fw-semibold">
                        <i class="bi bi-cloud-sun me-1"></i> Thời tiết: {{ $diary['weather'] }}
                    </div>
                @endif
                @if(!empty($diary['mood']))
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 px-3 py-2 fw-semibold">
                        <i class="bi bi-emoji-smile me-1"></i> Không khí: {{ $diary['mood'] }}
                    </div>
                @endif
            </div>

            <div class="fs-6 lh-lg text-dark mb-4 style-content">
                {!! nl2br(e($diary['content'])) !!}
            </div>

            @if(!empty($photos))
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-images me-1"></i> Hình ảnh ghi nhận ({{ count($photos) }})</h6>
                <div class="row g-3">
                    @foreach($photos as $photo)
                        <div class="col-md-4">
                            <a href="{{ file_url($photo) }}" target="_blank">
                                <img src="{{ file_url($photo) }}" class="img-fluid rounded-3 shadow-sm hover-zoom" style="height: 180px; width: 100%; object-fit: cover;">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="hdv-card">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge text-primary me-2"></i> Người thực hiện</h6>
            <div class="p-3 bg-light rounded-3 mb-3">
                <div class="fw-bold">{{ $activeHdv['Hoten'] ?? 'Hướng dẫn viên' }}</div>
                <div class="text-muted small">SĐT: {{ $activeHdv['Lienhe'] ?? '—' }}</div>
                <div class="text-muted small">Chứng chỉ: {{ $activeHdv['chungchiHDV'] ?? 'HDV Quốc gia' }}</div>
            </div>

            <div class="small text-muted border-top pt-3">
                <div><i class="bi bi-clock me-1"></i> Ngày tạo bài: {{ date('d/m/Y H:i', strtotime($diary['created_at'])) }}</div>
                @if(!empty($diary['updated_at']))
                    <div><i class="bi bi-arrow-repeat me-1"></i> Cập nhật gần nhất: {{ date('d/m/Y H:i', strtotime($diary['updated_at'])) }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
