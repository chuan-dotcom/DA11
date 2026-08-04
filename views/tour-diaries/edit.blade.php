@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .form-section-title {
        font-weight: 700;
        color: #111827;
        border-left: 3px solid #18bfd4;
        padding-left: 0.6rem;
        margin-bottom: 1rem;
    }
    .photo-wrap {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .photo-wrap img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
    }
    .photo-check {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(255,255,255,0.9);
        border-radius: 20px;
        padding: 2px 6px;
    }
    .photo-caption {
        font-size: 0.75rem;
        color: #6b7280;
        text-align: center;
        margin-top: 0.25rem;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/tour-diaries') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/tour-diaries/update/' . $diary['id']) }}" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <div class="form-section-title">Thông tin chung</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="departure_id" class="form-label">Chuyến khởi hành <span class="text-danger">*</span></label>
                                <select class="form-select" id="departure_id" name="departure_id" required>
                                    <option value="">-- Chọn chuyến khởi hành --</option>
                                    @foreach($departures as $d)
                                        @php $selId = old('departure_id', $diary['departure_id']); @endphp
                                        @php
                                            $dDate = !empty($d['departure_date']) ? date('Y-m-d', strtotime($d['departure_date'])) : '';
                                            $rDate = !empty($d['return_date']) ? date('Y-m-d', strtotime($d['return_date'])) : '';
                                        @endphp
                                        <option value="{{ $d['id'] }}"
                                            {{ (string)$selId === (string)$d['id'] ? 'selected' : '' }}
                                            data-departure-date="{{ $dDate }}"
                                            data-return-date="{{ $rDate }}">
                                            #{{ $d['id'] }} - {{ $d['group_name'] ?: $d['tour_name'] }}
                                            ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : 'chưa xếp lịch' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="diary_date" class="form-label">Ngày ghi nhận <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="diary_date" name="diary_date" value="{{ old('diary_date', $diary['diary_date']) }}" required>
                                <div class="form-text" id="diary_date_hint">Ngày nhật ký phải nằm trong lịch trình của chuyến khởi hành.</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề nhật ký <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $diary['title']) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-section-title">Chi tiết nhật ký</div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content', $diary['content']) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="weather" class="form-label">Thời tiết</label>
                                <select class="form-select" id="weather" name="weather">
                                    <option value="">-- Chọn --</option>
                                    @php
                                        $oldWeather = old('weather', $diary['weather'] ?? '');
                                        $weathers = ['Nắng đẹp','Nhiều mây','Mưa rào','Mưa phùn','Sương mù','Gió mạnh','Lạnh','Nóng'];
                                    @endphp
                                    @foreach($weathers as $w)
                                        <option value="{{ $w }}" {{ (string)$oldWeather === (string)$w ? 'selected' : '' }}>{{ $w }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mood" class="form-label">Tâm trạng / Trải nghiệm</label>
                                <select class="form-select" id="mood" name="mood">
                                    <option value="">-- Chọn --</option>
                                    @php
                                        $oldMood = old('mood', trim($diary['mood'] ?? ''));
                                        $moods = ['Rất vui','Vui vẻ','Bình thường','Mệt mỏi','Bực mình','Xúc động','Ngạc nhiên','Kỳ diệu'];
                                    @endphp
                                    @foreach($moods as $m)
                                        <option value="{{ $m }}" {{ (string)$oldMood === (string)$m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-section-title">Hình ảnh hiện có (tích để xóa khi lưu)</div>
                    @if(empty($photos))
                        <div class="text-muted small py-3 text-center bg-light rounded">Chưa có ảnh nào. Bạn có thể thêm ảnh phía dưới.</div>
                    @else
                        <div class="row g-2 mb-3">
                            @foreach($photos as $idx => $p)
                                <div class="col-6 col-md-3">
                                    <div class="photo-wrap">
                                        <img src="{{ file_url($p) }}" alt="Ảnh {{ $idx + 1 }}">
                                        <label class="photo-check">
                                            <input type="checkbox" name="delete_photos[]" value="{{ $p }}">
                                            <span class="small text-danger ms-1">Xóa</span>
                                        </label>
                                    </div>
                                    <div class="photo-caption">Ảnh {{ $idx + 1 }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-muted small mb-3"><i class="bi bi-info-circle"></i> Tích vào ô "Xóa" và nhấn "Cập nhật" để xóa ảnh khỏi nhật ký.</div>
                    @endif

                    <div class="mb-3">
                        <label for="photos" class="form-label">Thêm ảnh mới (chọn nhiều ảnh cùng lúc)</label>
                        <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*" multiple>
                        <div class="form-text">Ảnh mới sẽ được thêm vào album hiện có.</div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Xem nhật ký
                    </a>
                    <a href="{{ route('admin/tour-diaries') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
(function () {
    var dept = document.getElementById('departure_id');
    var diaryDate = document.getElementById('diary_date');
    var hint = document.getElementById('diary_date_hint');

    function applyDepartureBounds() {
        var opt = dept.options[dept.selectedIndex];
        if (!opt || !opt.value) {
            diaryDate.removeAttribute('min');
            diaryDate.removeAttribute('max');
            if (hint) hint.textContent = 'Vui lòng chọn chuyến khởi hành trước để giới hạn khoảng ngày hợp lệ.';
            return;
        }
        var minD = opt.getAttribute('data-departure-date');
        var maxD = opt.getAttribute('data-return-date');
        if (minD) diaryDate.min = minD;
        else diaryDate.removeAttribute('min');
        if (maxD) diaryDate.max = maxD;
        else diaryDate.removeAttribute('max');

        if (minD && maxD) {
            hint.textContent = 'Ngày nhật ký phải từ ' + fmt(minD) + ' đến ' + fmt(maxD) + '.';
        } else if (minD) {
            hint.textContent = 'Ngày nhật ký phải từ ' + fmt(minD) + ' trở đi.';
        } else {
            hint.textContent = 'Chuyến khởi hành chưa có lịch, hãy chọn ngày phù hợp.';
        }

        if (diaryDate.value && diaryDate.min && diaryDate.value < diaryDate.min) {
            diaryDate.value = diaryDate.min;
        }
        if (diaryDate.value && diaryDate.max && diaryDate.value > diaryDate.max) {
            diaryDate.value = diaryDate.max;
        }
    }
    function fmt(iso) {
        if (!iso) return '';
        var p = iso.split('-');
        return p[2] + '/' + p[1] + '/' + p[0];
    }
    dept.addEventListener('change', applyDepartureBounds);
    document.addEventListener('DOMContentLoaded', applyDepartureBounds);
    if (document.readyState === 'interactive' || document.readyState === 'complete') applyDepartureBounds();
})();
</script>
@endsection
