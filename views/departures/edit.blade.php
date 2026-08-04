@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .role-badge { font-size: 0.8rem; }
</style>

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin/departures/update/' . $departure['id']) }}" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn tour --</option>
                                @foreach($tours as $tour)
                                    <option value="{{ $tour['id'] }}" {{ $tour['id'] == $departure['tour_id'] ? 'selected' : '' }}>
                                        {{ $tour['name'] }} ({{ $tour['duration'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="departure_date" class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="departure_date" name="departure_date" required value="{{ $departure['departure_date'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="return_date" class="form-label">Ngày trở về</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" value="{{ $departure['return_date'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="max_participants" class="form-label">Số khách tối đa</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="0" value="{{ $departure['max_participants'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meeting_point" class="form-label">Điểm tập trung</label>
                            <input type="text" class="form-control" id="meeting_point" name="meeting_point" value="{{ $departure['meeting_point'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="meeting_time" class="form-label">Giờ tập trung</label>
                            <input type="time" class="form-control" id="meeting_time" name="meeting_time" value="{{ $departure['meeting_time'] }}">
                        </div>
                        <div class="mb-3">
                            <label for="vehicle" class="form-label">Phương tiện di chuyển</label>
                            <select class="form-select" id="vehicle" name="vehicle">
                                <option value="">-- Chọn phương tiện --</option>
                                <option value="Máy bay" {{ $departure['vehicle'] == 'Máy bay' ? 'selected' : '' }}>Máy bay</option>
                                <option value="Xe khách" {{ $departure['vehicle'] == 'Xe khách' ? 'selected' : '' }}>Xe khách</option>
                                <option value="Tàu hỏa" {{ $departure['vehicle'] == 'Tàu hỏa' ? 'selected' : '' }}>Tàu hỏa</option>
                                <option value="Du thuyền" {{ $departure['vehicle'] == 'Du thuyền' ? 'selected' : '' }}>Du thuyền</option>
                                <option value="Xe máy" {{ $departure['vehicle'] == 'Xe máy' ? 'selected' : '' }}>Xe máy</option>
                                <option value="Ô tô riêng" {{ $departure['vehicle'] == 'Ô tô riêng' ? 'selected' : '' }}>Ô tô riêng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="scheduled" {{ $departure['status'] == 'scheduled' ? 'selected' : '' }}>Lên lịch</option>
                                <option value="in_progress" {{ $departure['status'] == 'in_progress' ? 'selected' : '' }}>Đang diễn ra</option>
                                <option value="completed" {{ $departure['status'] == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $departure['status'] == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4">{{ $departure['notes'] }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin/departures') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Nhân sự đã phân bổ</h5>
            <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ $departure['id'] }}" class="btn btn-sm btn-success">
                <i class="bi bi-person-plus"></i> Thêm nhân sự
            </a>
        </div>
        <div class="card-body">
            @if(empty($assignments))
                <div class="text-center text-muted py-3">Chưa phân bổ nhân sự cho chuyến này</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Vai trò</th>
                                <th>Liên hệ</th>
                                <th>Ngôn ngữ</th>
                                <th>Kinh nghiệm</th>
                                <th>Trách nhiệm</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $roleMap = [
                                    'lead_guide' => ['HDV chính', 'bg-primary'],
                                    'assistant_guide' => ['HDV phụ', 'bg-info'],
                                    'driver' => ['Lái xe', 'bg-warning text-dark'],
                                    'photographer' => ['Nhiếp ảnh', 'bg-success'],
                                    'other' => ['Khác', 'bg-secondary']
                                ];
                            @endphp
                            @foreach($assignments as $a)
                                <tr>
                                    <td class="fw-semibold">{{ $a['staff_name'] }}</td>
                                    <td>
                                        @php $r = $roleMap[$a['role']] ?? [$a['role'], 'bg-secondary']; @endphp
                                        <span class="badge role-badge {{ $r[1] }}">{{ $r[0] }}</span>
                                    </td>
                                    <td>{{ $a['staff_phone'] ?? '-' }}</td>
                                    <td>{{ $a['staff_languages'] ?? '-' }}</td>
                                    <td>{{ $a['staff_experience'] ? $a['staff_experience'] . ' năm' : '-' }}</td>
                                    <td>{{ $a['responsibilities'] ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/staff-assignments/edit/' . $a['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/staff-assignments/delete/' . $a['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bỏ phân bổ nhân viên này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departureDate = document.getElementById('departure_date');
    const returnDate = document.getElementById('return_date');

    function validateDates() {
        if (returnDate.value && departureDate.value && returnDate.value < departureDate.value) {
            returnDate.setCustomValidity('Ngày trở về không thể sớm hơn ngày khởi hành');
        } else {
            returnDate.setCustomValidity('');
        }
    }

    departureDate.addEventListener('change', validateDates);
    returnDate.addEventListener('change', validateDates);
});
</script>
@endsection
