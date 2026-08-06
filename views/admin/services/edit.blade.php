@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/services') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>                    

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            @if(isset($returnDepartureId) && (int)$returnDepartureId > 0)
                <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div class="small">
                        Dịch vụ này thuộc chuyến khởi hành #{{ (int)$returnDepartureId }}. Sau khi lưu sẽ tự động quay lại trang chi tiết đoàn.
                        <a href="{{ route('admin/departures/edit/' . (int)$returnDepartureId) }}" class="text-decoration-none ms-2">(Quay lại ngay)</a>
                    </div>
                </div>
            @endif
            <form action="{{ route('admin/services/update/' . $service['id']) }}" method="POST">
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tour du lịch <span class="text-danger">*</span></label>
                            <select name="tour_id" id="tour_id" class="form-select" required>
                                <option value="">-- Chọn Tour --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour['id'] }}" {{ ((isset($_POST['tour_id']) ? $_POST['tour_id'] : $service['tour_id']) == $tour['id']) ? 'selected' : '' }}>
                                        #{{ $tour['id'] }} - {{ $tour['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Chuyến khởi hành (tùy chọn)</label>
                            <select name="departure_id" id="departure_id" class="form-select">
                                <option value="">-- Không gắn vào chuyến --</option>
                                @if(!empty($departures))
                                    @foreach($departures as $d)
                                        @php
                                            $dpDate = !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : '';
                                            $rtDate = !empty($d['return_date']) ? date('d/m/Y', strtotime($d['return_date'])) : '';
                                        @endphp
                                        <option value="{{ $d['id'] }}"
                                            data-tour="{{ $d['tour_id'] }}"
                                            data-departure="{{ !empty($d['departure_date']) ? date('Y-m-d', strtotime($d['departure_date'])) : '' }}"
                                            data-return="{{ !empty($d['return_date']) ? date('Y-m-d', strtotime($d['return_date'])) : '' }}"
                                            {{ ((isset($_POST['departure_id']) ? $_POST['departure_id'] : $service['departure_id']) == $d['id']) ? 'selected' : '' }}>
                                            #{{ $d['id'] }} · {{ $d['group_name'] ?: ('Chuyến '.$d['id']) }}
                                            @if($dpDate)
                                                ({{ $dpDate }}@if($rtDate && $rtDate !== $dpDate) → {{ $rtDate }} @endif)
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(!empty($service['departure_id']))
                                <div class="form-text small text-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    Dịch vụ đang gắn vào <strong>{{ $service['departure_group_name'] ?: ('#'.$service['departure_id']) }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Loại dịch vụ <span class="text-danger">*</span></label>
                    <div class="row g-2 mb-2">
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                @php
                                    $val = isset($_POST['service_types']) && is_array($_POST['service_types']) ? $_POST['service_types'] : $selectedTypes;
                                @endphp
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_thamquan" value="Tham quan"
                                    {{ in_array('Tham quan', $val) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_thamquan">Tham quan</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_nhahang" value="Nhà hàng"
                                    {{ in_array('Nhà hàng', $val) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_nhahang">Nhà hàng</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_vemaybay" value="Vé máy bay"
                                    {{ in_array('Vé máy bay', $val) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_vemaybay">Vé máy bay</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_khachsan" value="Khách sạn"
                                    {{ in_array('Khách sạn', $val) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_khachsan">Khách sạn</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_xe" value="Xe"
                                    {{ in_array('Xe', $val) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_xe">Xe</label>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="service_types_text" class="form-control" placeholder="Hoặc nhập loại dịch vụ khác (nếu có)" value="{{ isset($_POST['service_types_text']) ? htmlentities($_POST['service_types_text']) : '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                    <input type="text" name="supplier" class="form-control" value="{{ isset($_POST['supplier']) ? htmlentities($_POST['supplier']) : htmlentities($service['supplier']) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="{{ isset($_POST['quantity']) ? (int)$_POST['quantity'] : (int)$service['quantity'] }}" min="1" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Thời gian bắt đầu</label>
                        <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="{{ isset($_POST['start_time']) ? htmlentities($_POST['start_time']) : ($service['start_time'] ? date('Y-m-d\TH:i', strtotime($service['start_time'])) : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="{{ isset($_POST['end_time']) ? htmlentities($_POST['end_time']) : ($service['end_time'] ? date('Y-m-d\TH:i', strtotime($service['end_time'])) : '') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="0" {{ (isset($_POST['status']) ? $_POST['status'] : $service['status']) == '0' ? 'selected' : '' }}>Chờ</option>
                        <option value="1" {{ (isset($_POST['status']) ? $_POST['status'] : $service['status']) == '1' ? 'selected' : '' }}>Xác nhận</option>
                        <option value="2" {{ (isset($_POST['status']) ? $_POST['status'] : $service['status']) == '2' ? 'selected' : '' }}>Hoàn tất</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : htmlentities($service['note']) }}</textarea>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Cập nhật</button>
                    @if(isset($returnDepartureId) && (int)$returnDepartureId > 0)
                        <a href="{{ route('admin/departures/edit/' . (int)$returnDepartureId) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại đoàn
                        </a>
                    @else
                        <a href="{{ route('admin/services') }}" class="btn btn-secondary">Hủy</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tourSel    = document.getElementById('tour_id');
    const depSel     = document.getElementById('departure_id');
    const startInput = document.getElementById('start_time');
    const endInput   = document.getElementById('end_time');
    const depMap     = {};

    Array.from(depSel.options).forEach(opt => {
        if (opt.value === '') return;
        depMap[opt.value] = {
            tour:      opt.dataset.tour      || '',
            departure: opt.dataset.departure || '',
            return:    opt.dataset.return    || ''
        };
    });

    function filterDepartures() {
        const tourVal = tourSel.value;
        for (let i = 0; i < depSel.options.length; i++) {
            const opt = depSel.options[i];
            if (opt.value === '') { opt.style.display = ''; continue; }
            const t = depMap[opt.value] ? depMap[opt.value].tour : '';
            opt.style.display = (tourVal === '' || t === '' || String(t) === String(tourVal)) ? '' : 'none';
        }
        if (depSel.value !== '') {
            const cur = depMap[depSel.value];
            if (cur && tourSel.value !== '' && String(cur.tour) !== String(tourSel.value)) {
                depSel.value = '';
            }
        }
    }

    function applyDeparture() {
        const val = depSel.value;
        if (!val || !depMap[val]) return;
        const cur = depMap[val];
        if (cur.tour) {
            for (let i = 0; i < tourSel.options.length; i++) {
                if (String(tourSel.options[i].value) === String(cur.tour)) {
                    tourSel.selectedIndex = i;
                    break;
                }
            }
        }
        if (!startInput.value && cur.departure) {
            startInput.value = cur.departure + 'T06:00';
        }
        if (!endInput.value && cur.return) {
            endInput.value = cur.return + 'T20:00';
        } else if (!endInput.value && cur.departure) {
            endInput.value = cur.departure + 'T20:00';
        }
    }

    tourSel.addEventListener('change', filterDepartures);
    depSel.addEventListener('change', function () {
        filterDepartures();
        applyDeparture();
    });

    filterDepartures();
});
</script>
@endsection
