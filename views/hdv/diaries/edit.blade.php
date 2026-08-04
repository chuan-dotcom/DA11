@extends('layouts.hdv')

@section('title', $title)

@section('content')

<div class="hdv-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i> {{ $title }}</h4>
        <a href="{{ route('hdv/nhat-ky-tour') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<div class="hdv-card">
    <form method="POST" action="{{ route('hdv/nhat-ky-tour/update/' . $diary['id']) }}" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Chọn chuyến khởi hành -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Chuyến khởi hành phân công <span class="text-danger">*</span></label>
                <select name="departure_id" class="form-select" required>
                    @foreach($departures as $d)
                        <option value="{{ $d['id'] }}" {{ $diary['departure_id'] == $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ngày nhật ký -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Ngày ghi nhật ký <span class="text-danger">*</span></label>
                <input type="date" name="diary_date" class="form-control" value="{{ $diary['diary_date'] }}" required>
            </div>

            <!-- Tiêu đề -->
            <div class="col-12">
                <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ $diary['title'] }}" required>
            </div>

            <!-- Thời tiết & Tâm trạng -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Thời tiết trong ngày</label>
                <input type="text" name="weather" class="form-control" value="{{ $diary['weather'] }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Tâm trạng / Không khí đoàn</label>
                <input type="text" name="mood" class="form-control" value="{{ $diary['mood'] }}">
            </div>

            <!-- Nội dung chi tiết -->
            <div class="col-12">
                <label class="form-label fw-bold">Nội dung nhật ký chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6" required>{{ $diary['content'] }}</textarea>
            </div>

            <!-- Hình ảnh đã có -->
            @if(!empty($photos))
                <div class="col-12">
                    <label class="form-label fw-bold">Hình ảnh hiện tại (Tích vào ảnh nếu muốn xóa):</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($photos as $p)
                            <div class="card p-2 text-center shadow-sm">
                                <img src="{{ file_url($p) }}" style="width: 120px; height: 90px; object-fit: cover;" class="rounded mb-2">
                                <div class="form-check text-start">
                                    <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $p }}" id="del_{{ md5($p) }}">
                                    <label class="form-check-label text-danger small fw-bold" for="del_{{ md5($p) }}">Xóa ảnh</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tải thêm ảnh -->
            <div class="col-12">
                <label class="form-label fw-bold">Tải thêm ảnh mới</label>
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold text-dark">
                    <i class="bi bi-pencil me-1"></i> Cập nhật nhật ký
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
