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
    @php
        $selectedEditDeparture = null;
        foreach ($departures as $departure) {
            if ((int) $departure['id'] === (int) old('departure_id', $diary['departure_id'])) {
                $selectedEditDeparture = $departure;
                break;
            }
        }
        $editStartDate = $selectedEditDeparture['departure_date'] ?? '';
        $editEndDate = $selectedEditDeparture['return_date'] ?? $editStartDate;
    @endphp
    <form method="POST" action="{{ route('hdv/nhat-ky-tour/update/' . $diary['id']) }}" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Chọn chuyến khởi hành -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Chuyến khởi hành phân công <span class="text-danger">*</span></label>
                <select name="departure_id" id="diary-departure" class="form-select" required>
                    @foreach($departures as $d)
                        @php $selId = old('departure_id', $diary['departure_id']); @endphp
                        <option value="{{ $d['id'] }}" data-start-date="{{ $d['departure_date'] }}" data-end-date="{{ $d['return_date'] ?: $d['departure_date'] }}" {{ (int) $selId === (int) $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} - {{ $d['category_name'] ?? 'Chưa phân loại' }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ngày nhật ký -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Ngày ghi nhật ký <span class="text-danger">*</span></label>
                <input type="date" name="diary_date" id="diary-date" class="form-control" value="{{ old('diary_date', $diary['diary_date']) }}" min="{{ $editStartDate }}" max="{{ $editEndDate }}" required>
                <div id="diary-date-help" class="form-text"></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Liên kết mốc hoạt động <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                <select name="tour_log_id" id="diary-tour-log" class="form-select">
                    <option value="">-- Chọn hoạt động trong timeline --</option>
                    @foreach($timelineLogs as $log)
                        <option value="{{ $log['id'] }}" data-departure-id="{{ $log['departure_id'] }}" data-log-date="{{ date('Y-m-d', strtotime($log['log_date'])) }}" {{ (int) old('tour_log_id', $diary['tour_log_id'] ?? 0) === (int) $log['id'] ? 'selected' : '' }}>
                            {{ date('d/m/Y H:i', strtotime($log['log_date'])) }} — {{ $log['title'] }}@if(!empty($log['location'])) ({{ $log['location'] }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tiêu đề -->
            <div class="col-12">
                <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $diary['title']) }}" required>
            </div>

            <!-- Thời tiết & Tâm trạng -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Thời tiết trong ngày</label>
                <input type="text" name="weather" class="form-control" value="{{ old('weather', $diary['weather']) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Tâm trạng / Không khí đoàn</label>
                <input type="text" name="mood" class="form-control" value="{{ old('mood', $diary['mood']) }}">
            </div>

            <!-- Nội dung chi tiết -->
            <div class="col-12">
                <label class="form-label fw-bold">Nội dung nhật ký chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6" required>{{ old('content', $diary['content']) }}</textarea>
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

<script>
    (function () {
        const departureSelect = document.getElementById('diary-departure');
        const diaryDate = document.getElementById('diary-date');
        const dateHelp = document.getElementById('diary-date-help');
        const timelineSelect = document.getElementById('diary-tour-log');

        function updateTimelineOptions() {
            const departureId = departureSelect.value;
            Array.from(timelineSelect.options).forEach(function (option, index) {
                if (index === 0) return;
                const belongsToDeparture = option.dataset.departureId === departureId;
                option.hidden = !belongsToDeparture;
                option.disabled = !belongsToDeparture;
            });
            if (timelineSelect.selectedIndex > 0 && timelineSelect.options[timelineSelect.selectedIndex].disabled) {
                timelineSelect.value = '';
            }
        }

        function updateDiaryDateRange(resetDate) {
            const option = departureSelect.options[departureSelect.selectedIndex];
            const startDate = option.dataset.startDate;
            const endDate = option.dataset.endDate;
            diaryDate.min = startDate;
            diaryDate.max = endDate;
            dateHelp.textContent = 'Ngày hợp lệ: ' + startDate.split('-').reverse().join('/') + ' – ' + endDate.split('-').reverse().join('/') + '.';

            if (resetDate || diaryDate.value < startDate || diaryDate.value > endDate) {
                diaryDate.value = startDate;
            }
        }

        departureSelect.addEventListener('change', function () {
            updateDiaryDateRange(true);
            updateTimelineOptions();
        });
        timelineSelect.addEventListener('change', function () {
            const option = timelineSelect.options[timelineSelect.selectedIndex];
            if (option && option.dataset.logDate) diaryDate.value = option.dataset.logDate;
        });
        updateDiaryDateRange(false);
        updateTimelineOptions();
    })();
</script>

@endsection
