@extends('layouts.hdv')

@section('title', $title)

@section('content')

<div class="hdv-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-primary me-2"></i> {{ $title }}</h4>
        <a href="{{ route('hdv/nhat-ky-tour') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<div class="hdv-card">
    <form method="POST" action="{{ route('hdv/nhat-ky-tour/store') }}" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Chọn chuyến khởi hành -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Chuyến khởi hành phân công <span class="text-danger">*</span></label>
                <select name="departure_id" class="form-select" required>
                    <option value="">-- Chọn tour bạn được phân công --</option>
                    @foreach($departures as $d)
                        @php $selId = old('departure_id', $selectedDepartureId ?? null); @endphp
                        <option value="{{ $d['id'] }}" {{ (int) $selId === (int) $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} - {{ $d['category_name'] ?? 'Chưa phân loại' }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ngày nhật ký -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Ngày ghi nhật ký <span class="text-danger">*</span></label>
                <input type="date" name="diary_date" class="form-control" value="{{ old('diary_date', date('Y-m-d')) }}" required>
            </div>

            @if(!empty($selectedDeparture))
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <div class="fw-bold mb-1">Chuyến đang chọn</div>
                        <div>Tour: <strong>{{ $selectedDeparture['tour_name'] }}</strong></div>
                        <div>Danh mục: <strong>{{ $selectedDeparture['category_name'] ?? 'Chưa phân loại' }}</strong></div>
                        <div>Đoàn: <strong>{{ $selectedDeparture['group_name'] ?: ('Chuyến #' . $selectedDeparture['id']) }}</strong></div>
                        <div>Thời gian: <strong>{{ !empty($selectedDeparture['departure_date']) ? date('d/m/Y', strtotime($selectedDeparture['departure_date'])) : 'Chưa có' }}</strong>@if(!empty($selectedDeparture['return_date'])) - <strong>{{ date('d/m/Y', strtotime($selectedDeparture['return_date'])) }}</strong>@endif</div>
                    </div>
                </div>
            @endif

            <!-- Tiêu đề -->
            <div class="col-12">
                <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Ví dụ: Ngày 1 - Đón đoàn và ổn định lịch trình" required>
            </div>

            <!-- Thời tiết & Tâm trạng -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Thời tiết trong ngày</label>
                <input type="text" name="weather" class="form-control" value="{{ old('weather') }}" placeholder="Nắng nhẹ, Nắng mát, Mưa rào...">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Tâm trạng / Không khí đoàn</label>
                <input type="text" name="mood" class="form-control" value="{{ old('mood') }}" placeholder="Hào hứng, Vui vẻ, Mệt mỏi nhẹ...">
            </div>

            <!-- Nội dung chi tiết -->
            <div class="col-12">
                <label class="form-label fw-bold">Nội dung nhật ký chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6" placeholder="Ghi chép tình hình đoàn, tiến độ lịch trình, sự cố nếu có..." required>{{ old('content') }}</textarea>
            </div>

            <!-- Ảnh minh họa -->
            <div class="col-12">
                <label class="form-label fw-bold">Tải lên hình ảnh chuyến đi (Có thể chọn nhiều ảnh)</label>
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                    <i class="bi bi-save me-1"></i> Lưu nhật ký tour
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
