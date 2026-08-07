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
    @php
        $initialDiaryDate = old('diary_date', !empty($selectedDeparture['departure_date']) ? $selectedDeparture['departure_date'] : '');
        $initialStartDate = $selectedDeparture['departure_date'] ?? '';
        $initialEndDate = $selectedDeparture['return_date'] ?? $initialStartDate;
    @endphp
    <form method="POST" action="{{ route('hdv/nhat-ky-tour/store') }}" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Chọn chuyến khởi hành -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Chuyến khởi hành phân công <span class="text-danger">*</span></label>
                <select name="departure_id" id="diary-departure" class="form-select" required>
                    <option value="">-- Chọn tour bạn được phân công --</option>
                    @foreach($departures as $d)
                        @php $selId = old('departure_id', $selectedDepartureId ?? null); @endphp
                        <option value="{{ $d['id'] }}" data-start-date="{{ $d['departure_date'] }}" data-end-date="{{ $d['return_date'] ?: $d['departure_date'] }}" {{ (int) $selId === (int) $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} - {{ $d['category_name'] ?? 'Chưa phân loại' }} ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ngày nhật ký -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Ngày ghi nhật ký <span class="text-danger">*</span></label>
                <input type="date" name="diary_date" id="diary-date" class="form-control" value="{{ $initialDiaryDate }}" min="{{ $initialStartDate }}" max="{{ $initialEndDate }}" required>
                <div id="diary-date-help" class="form-text">Chọn chuyến để xác định ngày hợp lệ.</div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Liên kết mốc hoạt động <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                <select name="tour_log_id" id="diary-tour-log" class="form-select">
                    <option value="">-- Chọn hoạt động trong timeline --</option>
                    @foreach($timelineLogs as $log)
                        <option value="{{ $log['id'] }}" data-departure-id="{{ $log['departure_id'] }}" data-log-date="{{ date('Y-m-d', strtotime($log['log_date'])) }}" {{ (int) old('tour_log_id', 0) === (int) $log['id'] ? 'selected' : '' }}>
                            {{ date('d/m/Y H:i', strtotime($log['log_date'])) }} — {{ $log['title'] }}@if(!empty($log['location'])) ({{ $log['location'] }}) @endif
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Chọn mốc để liên kết nhật ký này với hoạt động thực tế của chuyến đi.</div>
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

            <!-- Ảnh minh họa & Bằng chứng thực địa -->
            <div class="col-12">
                <label class="form-label fw-bold">Hình ảnh thực địa / Bằng chứng chuyến đi (Có thể chọn nhiều ảnh)</label>
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
            </div>

            <!-- Chi phí phát sinh nếu có -->
            <div class="col-md-6">
                <label class="form-label fw-bold">Chi phí phát sinh (nếu có)</label>
                <div class="input-group">
                    <input type="number" name="expense_amount" class="form-control" value="{{ old('expense_amount') }}" placeholder="Ví dụ: 500000" min="0" step="1000">
                    <span class="input-group-text">VNĐ</span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Loại chi phí phát sinh</label>
                <select name="expense_category" class="form-select">
                    <option value="">-- Không có chi phí --</option>
                    <option value="Ăn uống" {{ old('expense_category') === 'Ăn uống' ? 'selected' : '' }}>🍲 Ăn uống</option>
                    <option value="Vé tham quan" {{ old('expense_category') === 'Vé tham quan' ? 'selected' : '' }}>🎟️ Vé tham quan</option>
                    <option value="Di chuyển / Cầu đường" {{ old('expense_category') === 'Di chuyển / Cầu đường' ? 'selected' : '' }}>🚗 Di chuyển / Cầu đường / Xăng xe</option>
                    <option value="Lưu trú / Khách sạn" {{ old('expense_category') === 'Lưu trú / Khách sạn' ? 'selected' : '' }}>🏨 Lưu trú / Khách sạn</option>
                    <option value="Khác" {{ old('expense_category') === 'Khác' ? 'selected' : '' }}>⭐ Phát sinh khác</option>
                </select>
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                    <i class="bi bi-save me-1"></i> Lưu
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
            const startDate = option && option.dataset.startDate ? option.dataset.startDate : '';
            const endDate = option && option.dataset.endDate ? option.dataset.endDate : '';

            diaryDate.min = startDate;
            diaryDate.max = endDate;

            if (!startDate) {
                diaryDate.value = '';
                diaryDate.disabled = true;
                dateHelp.textContent = 'Vui lòng chọn chuyến khởi hành trước.';
                return;
            }

            diaryDate.disabled = false;
            dateHelp.textContent = 'Chỉ được ghi nhật ký từ ' + startDate.split('-').reverse().join('/') + ' đến ' + endDate.split('-').reverse().join('/') + '.';

            if (resetDate || !diaryDate.value || diaryDate.value < startDate || diaryDate.value > endDate) {
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
