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
            <form action="{{ route('admin/staff-assignments/update/' . $assignment['id']) }}" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="departure_id" class="form-label">Chuyến khởi hành <span class="text-danger">*</span></label>
                            <select class="form-select" id="departure_id" name="departure_id" required>
                                <option value="">-- Chọn chuyến khởi hành --</option>
                                @foreach($departures as $d)
                                    <option value="{{ $d['id'] }}" {{ $assignment['departure_id'] == $d['id'] ? 'selected' : '' }}>
                                        {{ $d['tour_name'] }} - {{ date('d/m/Y', strtotime($d['departure_date'])) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select class="form-select" id="staff_id" name="staff_id" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($staffList as $s)
                                    <option value="{{ $s['HDV_id'] }}" {{ $assignment['staff_id'] == $s['HDV_id'] ? 'selected' : '' }}>
                                        {{ $s['Hoten'] }} - {{ $s['Ngonngu'] ?? 'Tiếng Việt' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="lead_guide" {{ $assignment['role'] == 'lead_guide' ? 'selected' : '' }}>HDV chính</option>
                                <option value="assistant_guide" {{ $assignment['role'] == 'assistant_guide' ? 'selected' : '' }}>HDV phụ</option>
                                <option value="driver" {{ $assignment['role'] == 'driver' ? 'selected' : '' }}>Lái xe</option>
                                <option value="photographer" {{ $assignment['role'] == 'photographer' ? 'selected' : '' }}>Nhiếp ảnh</option>
                                <option value="other" {{ $assignment['role'] == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="assigned" {{ $assignment['status'] == 'assigned' ? 'selected' : '' }}>Đã phân bổ</option>
                                <option value="confirmed" {{ $assignment['status'] == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="completed" {{ $assignment['status'] == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="rejected" {{ $assignment['status'] == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="responsibilities" class="form-label">Trách nhiệm chính</label>
                            <textarea class="form-control" id="responsibilities" name="responsibilities" rows="3">{{ $assignment['responsibilities'] }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ $assignment['notes'] }}</textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin/staff-assignments') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
