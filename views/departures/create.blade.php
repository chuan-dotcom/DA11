@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/departures/store') }}" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">Tên đoàn</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Ví dụ: Nguyễn Anh Tài - Du lịch Cáp Nhĩ Tân">
                            <div class="form-text">Để trống nếu bạn muốn hệ thống tự sinh tên đoàn theo tour và ngày khởi hành.</div>
                        </div>
                        <div class="mb-3">
                            <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn tour --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour['id'] }}">{{ $tour['name'] }} ({{ $tour['duration'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="departure_date" class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="departure_date" name="departure_date" required value="{{ date('Y-m-d', strtotime('+1 week')) }}">
                        </div>
                        <div class="mb-3">
                            <label for="return_date" class="form-label">Ngày trở về</label>
                            <input type="date" class="form-control" id="return_date" name="return_date">
                        </div>
                        <div class="mb-3">
                            <label for="max_participants" class="form-label">Số khách tối đa</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="0" value="20">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meeting_point" class="form-label">Điểm tập trung</label>
                            <input type="text" class="form-control" id="meeting_point" name="meeting_point" placeholder="Ví dụ: Sân bay Tân Sơn Nhất">
                        </div>
                        <div class="mb-3">
                            <label for="meeting_time" class="form-label">Giờ tập trung</label>
                            <input type="time" class="form-control" id="meeting_time" name="meeting_time" value="06:00">
                        </div>
                        <div class="mb-3">
                            <label for="vehicle" class="form-label">Phương tiện di chuyển</label>
                            <select class="form-select" id="vehicle" name="vehicle">
                                <option value="">-- Chọn phương tiện --</option>
                                <option value="Máy bay">Máy bay</option>
                                <option value="Xe khách">Xe khách</option>
                                <option value="Tàu hỏa">Tàu hỏa</option>
                                <option value="Du thuyền">Du thuyền</option>
                                <option value="Xe máy">Xe máy</option>
                                <option value="Ô tô riêng">Ô tô riêng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="scheduled">Lên lịch</option>
                                <option value="in_progress">Đang diễn ra</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu
                </button>
                <a href="{{ route('admin/departures') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departureDate = document.getElementById('departure_date');
    const returnDate = document.getElementById('return_date');
    const tourSelect = document.getElementById('tour_id');

    function validateDates() {
        if (returnDate.value && departureDate.value && returnDate.value < departureDate.value) {
            returnDate.setCustomValidity('Ngày trở về không thể sớm hơn ngày khởi hành');
        } else {
            returnDate.setCustomValidity('');
        }
    }

    departureDate.addEventListener('change', validateDates);
    returnDate.addEventListener('change', validateDates);

    tourSelect.addEventListener('change', function() {
        const tour = this.options[this.selectedIndex].text;
        const durationMatch = tour.match(/(\d+)\s*ngày/);
        if (durationMatch && departureDate.value) {
            const days = parseInt(durationMatch[1]);
            const d = new Date(departureDate.value);
            d.setDate(d.getDate() + days - 1);
            returnDate.value = d.toISOString().split('T')[0];
        }
    });
});
</script>
@endsection
