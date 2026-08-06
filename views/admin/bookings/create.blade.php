@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

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
            <form action="{{ route('admin/bookings/store') }}" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Tour du lịch <span class="text-danger">*</span></label>
                            <select name="tour_id" id="tour_id" class="form-select" required>
                                <option value="">-- Chọn Tour --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour['id'] }}" data-price="{{ $tour['price'] ?? 0 }}" {{ (isset($preTourId) && (int)$preTourId === (int)$tour['id']) ? 'selected' : '' }}>
                                        {{ $tour['name'] }} ({{ number_format($tour['price']) }} VNĐ)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Chuyến khởi hành <span class="text-muted small">(tùy chọn)</span></label>
                            <select name="departure_id" id="departure_id" class="form-select">
                                <option value="">-- Không gắn vào đoàn --</option>
                                @foreach($departures as $d)
                                    <option value="{{ $d['id'] }}"
                                            data-tour="{{ (int)($d['tour_id'] ?? 0) }}"
                                            data-departure="{{ !empty($d['departure_date']) ? date('Y-m-d', strtotime($d['departure_date'])) : '' }}"
                                            data-return="{{ !empty($d['return_date']) ? date('Y-m-d', strtotime($d['return_date'])) : '' }}"
                                            data-meeting="{{ !empty($d['meeting_point']) ? htmlspecialchars($d['meeting_point'], ENT_QUOTES) : '' }}"
                                            {{ (isset($preDepartureId) && (int)$preDepartureId === (int)$d['id']) ? 'selected' : '' }}>
                                        #{{ $d['id'] }} - {{ $d['group_name'] ?? ('Đoàn ' . ($d['tour_name'] ?? 'Tour')) }} ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small opacity-75">Chọn Tour ở trên sẽ tự lọc các chuyến khởi hành phù hợp. Nếu chọn chuyến → tự động gắn ngày khởi hành &amp; địa chỉ đón (nếu chưa điền).</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Họ tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ 
                        isset($_POST['customer_name']) ? htmlentities($_POST['customer_name']) : '' }}" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" value="{{ 
                                isset($_POST['customer_email']) ? htmlentities($_POST['customer_email']) : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="{{ 
                                isset($_POST['customer_phone']) ? htmlentities($_POST['customer_phone']) : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Địa chỉ đón khách hàng</label>
                    <input type="text" name="pickup_address" id="pickup_address" class="form-control" value="{{ 
                        isset($_POST['pickup_address']) ? htmlentities($_POST['pickup_address']) : '' }}" placeholder="Ví dụ: Số 123 đường Nguyễn Huệ, Q.1, TP.HCM">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Số người</label>
                            <input type="number" name="num_people" id="num_people" class="form-control" value="{{ 
                                isset($_POST['num_people']) ? (int)$_POST['num_people'] : 1 }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Ngày đặt Tour</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" value="{{ 
                                isset($_POST['booking_date']) ? htmlentities($_POST['booking_date']) : date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="0">Chờ xác nhận</option>
                        <option value="1">Đã xác nhận</option>
                        <option value="2">Đã hủy</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : '' }}</textarea>
                </div>

                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Tổng tiền sẽ được hệ thống tự động tính bằng <strong>Giá Tour × Số người</strong>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu Booking</button>
                    <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tourSel = document.getElementById('tour_id');
    const depSel = document.getElementById('departure_id');
    const bookingDate = document.getElementById('booking_date');
    const pickupAddr = document.getElementById('pickup_address');
    if (!tourSel || !depSel) return;

    const depOpts = Array.from(depSel.options).filter(o => o.value !== '');

    function filterDepartures() {
        const tourVal = tourSel.value ? parseInt(tourSel.value, 10) : 0;
        const prev = depSel.value;
        depSel.innerHTML = '';
        const allOpt = document.createElement('option');
        allOpt.value = '';
        allOpt.textContent = '-- Không gắn vào đoàn --';
        depSel.appendChild(allOpt);
        depOpts.forEach(o => {
            const t = parseInt(o.getAttribute('data-tour') || '0', 10);
            if (!tourVal || tourVal === t) {
                const copy = o.cloneNode(true);
                depSel.appendChild(copy);
            }
        });
        if (prev && depSel.querySelector('option[value="' + prev + '"]')) {
            depSel.value = prev;
        }
    }

    function applyDeparture(opt) {
        if (!opt || !opt.dataset) return;
        const depDate = opt.getAttribute('data-departure') || '';
        const meet = opt.getAttribute('data-meeting') || '';
        if (depDate && (!bookingDate.value || bookingDate.value === '{{ date('Y-m-d') }}')) {
            bookingDate.value = depDate;
        }
        if (meet && !pickupAddr.value) {
            pickupAddr.value = meet;
        }
    }

    tourSel.addEventListener('change', filterDepartures);
    depSel.addEventListener('change', function() {
        const sel = this.options[this.selectedIndex];
        if (sel && sel.value) applyDeparture(sel);
    });
    filterDepartures();
    const initSel = depSel.options[depSel.selectedIndex];
    if (initSel && initSel.value) applyDeparture(initSel);
});
</script>
@endsection
