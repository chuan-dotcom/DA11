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
                <div class="mb-3 p-3 rounded-3 bg-light border d-flex flex-column gap-2">
                    <label class="form-label fw-semibold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-magic text-primary"></i> Tự điền từ khách hàng đã đặt tour
                    </label>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-7">
                            <label for="booking_suggestion" class="form-label small text-muted">Chọn tên khách đại diện</label>
                            <select class="form-select" id="booking_suggestion">
                                <option value="">-- Chọn booking đã xác nhận --</option>
                                @if(!empty($bookingSuggestions))
                                    @foreach($bookingSuggestions as $b)
                                        @php
                                            $meta = [
                                                'tour_id' => (int)$b['tour_id'],
                                                'tour_name' => $b['tour_name'],
                                                'tour_location' => $b['tour_location'] ?? '',
                                                'num_people' => (int)$b['num_people'],
                                                'customer_phone' => $b['customer_phone'],
                                                'customer_email' => $b['customer_email'],
                                                'booking_date' => $b['booking_date'],
                                            ];
                                        @endphp
                                        <option value="{{ $b['id'] }}"
                                            data-meta="{{ htmlentities(json_encode($meta, JSON_HEX_TAG|JSON_HEX_APOS), ENT_QUOTES) }}">
                                            #{{ $b['id'] }} · {{ $b['customer_name'] }}
                                            @if(!empty($b['customer_phone']))
                                                ({{ $b['customer_phone'] }})
                                            @endif
                                            · {{ $b['tour_name'] }} · {{ $b['num_people'] }} người
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div id="booking_summary_box" class="p-2 rounded-2 bg-white border d-none small min-h-0" style="min-height:62px">
                                <div class="d-flex flex-column gap-1">
                                    <div><span class="text-muted">Tour:</span> <strong id="bs_tour_name">—</strong></div>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div><span class="text-muted">Số khách:</span> <strong id="bs_num_people" class="text-primary">—</strong></div>
                                        <div><span class="text-muted">Địa điểm:</span> <strong id="bs_location">—</strong></div>
                                        <div><span class="text-muted">SĐT:</span> <strong id="bs_phone">—</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-text small"><i class="bi bi-info-circle text-primary me-1"></i>Chọn một booking ở trên sẽ tự động <strong>đổi Tour</strong> và <strong>Điền Số khách tối đa</strong>. Bạn có thể chỉnh sửa lại sau khi điền tự động.</div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">Tên đoàn</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" value="{{ $departure['group_name'] ?? '' }}">
                            <div class="form-text">Có thể đổi tên đoàn để dễ quản lý và in danh sách khách.</div>
                        </div>
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
                            <div class="form-text"><i class="bi bi-info-circle text-primary me-1"></i>Giá trị này sẽ được tự động gắn làm <strong>Địa chỉ đón khách hàng</strong> cho tất cả booking trong đoàn này.</div>
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

    <div class="card mt-4 border-secondary">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0"><i class="bi bi-people-fill text-secondary me-2"></i>Nhân sự đã phân bổ</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin/staff-assignments') }}?departure_id={{ $departure['id'] }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel me-1"></i> Lọc ở trang Phân công
                </a>
                <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ $departure['id'] }}" class="btn btn-sm btn-success">
                    <i class="bi bi-person-plus"></i> Thêm nhân sự
                </a>
            </div>
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
                                        <a href="{{ route('hdv/tour-phan-cong') }}?hdv_id={{ (int)$a['staff_id'] }}"
                                           target="_blank" rel="noopener"
                                           class="btn btn-sm btn-outline-info me-1"
                                           title="Mở cổng HDV xem tour được phân công của nhân sự này">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <a href="{{ route('admin/staff-assignments/edit/' . $a['id']) }}" class="btn btn-sm btn-warning me-1">
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
                <div class="mt-2 small text-muted d-flex flex-wrap gap-3 align-items-center">
                    <span><i class="bi bi-info-circle me-1"></i>Tổng: <strong>{{ count($assignments) }}</strong> nhân sự phân bổ</span>
                    @php
                        $countRole = [];
                        foreach ($assignments as $a) {
                            $rr = $a['role'] ?? 'other';
                            if (!isset($countRole[$rr])) { $countRole[$rr] = 0; }
                            $countRole[$rr]++;
                        }
                        $roleLabel = [
                            'lead_guide' => 'HDV chính',
                            'assistant_guide' => 'HDV phụ',
                            'driver' => 'Lái xe',
                            'photographer' => 'Nhiếp ảnh',
                            'other' => 'Khác',
                        ];
                        $roleParts = [];
                        foreach ($countRole as $rr => $c) {
                            $lbl = $roleLabel[$rr] ?? $rr;
                            $roleParts[] = $lbl . ': <strong>' . (int)$c . '</strong>';
                        }
                    @endphp
                    @if(!empty($roleParts))
                        <span>{!! implode(' · ', $roleParts) !!}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4 border-primary">
        <div class="card-header bg-light d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-journal-text text-primary me-2"></i>Danh sách booking thuộc đoàn này
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin/bookings') }}?departure_id={{ $departure['id'] }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i> Lọc ở trang Quản lý booking
                </a>
                <a href="{{ route('admin/bookings/create') }}?tour_id={{ $departure['tour_id'] }}&departure_id={{ $departure['id'] }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tạo booking cho đoàn này
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(empty($bookings))
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-text fs-1 opacity-30 mb-2 d-block"></i>
                    Chuyến khởi hành này chưa có booking nào được gắn. Nhấn nút <strong class="text-primary">"Tạo booking cho đoàn này"</strong> bên trên để thêm mới, hoặc gắn booking có sẵn tại trang Quản lý booking.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Liên hệ</th>
                                <th class="text-center">Số người</th>
                                <th>Ngày đặt</th>
                                <th class="text-center">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                                <th width="180" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $b)
                                <tr>
                                    <td>#{{ $b['id'] }}</td>
                                    <td class="fw-semibold">{{ $b['customer_name'] ?? '-' }}</td>
                                    <td class="small">
                                        @if(!empty($b['customer_phone']))
                                            <div>{{ $b['customer_phone'] }}</div>
                                        @endif
                                        @if(!empty($b['customer_email']))
                                            <div class="text-muted">{{ $b['customer_email'] }}</div>
                                        @endif
                                        @if(empty($b['customer_phone']) && empty($b['customer_email']))
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ (int)$b['num_people'] }}</td>
                                    <td class="small">
                                        @if(!empty($b['booking_date']))
                                            {{ date('d/m/Y', strtotime($b['booking_date'])) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($b['total_price']))
                                            {{ number_format((float)$b['total_price'], 0, ',', '.') }} ₫
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($b['status'])
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-info text-white">Đã xác nhận</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Hoàn thành</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-secondary">Đã hủy</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $b['status'] }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin/bookings/show/' . $b['id']) }}" class="btn btn-sm btn-outline-primary me-1" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/bookings/edit/' . $b['id']) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 small text-muted d-flex flex-wrap gap-3 align-items-center">
                    <span><i class="bi bi-info-circle me-1"></i>Tổng: <strong>{{ count($bookings) }}</strong> booking thuộc đoàn</span>
                    @php
                        $totalPax = 0;
                        $totalAmount = 0;
                        foreach ($bookings as $b) {
                            $totalPax += (int)($b['num_people'] ?? 0);
                            $totalAmount += (float)($b['total_price'] ?? 0);
                        }
                    @endphp
                    <span><i class="bi bi-people me-1"></i>Tổng khách: <strong>{{ $totalPax }}</strong> người</span>
                    <span><i class="bi bi-cash-stack me-1"></i>Tổng giá trị: <strong>{{ number_format($totalAmount, 0, ',', '.') }} ₫</strong></span>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4 border-secondary">
        <div class="card-header bg-light d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-list-task text-dark me-2"></i>Dịch vụ đoàn của chuyến khởi hành
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin/services') }}?departure_id={{ $departure['id'] }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-funnel me-1"></i> Lọc ở trang Quản lý dịch vụ
                </a>
                <a href="{{ route('admin/services/create') }}?tour_id={{ $departure['tour_id'] }}&departure_id={{ $departure['id'] }}&quantity={{ (int)($departure['max_participants'] ?? 0) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Đặt dịch vụ cho đoàn này
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(empty($services))
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-list-task fs-1 opacity-30 mb-2 d-block"></i>
                    Chuyến khởi hành này chưa có dịch vụ nào được đặt. Nhấn nút <strong class="text-success">"Đặt dịch vụ cho đoàn này"</strong> bên trên để thêm.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Loại dịch vụ</th>
                                <th>Nhà cung cấp</th>
                                <th class="text-center">Số lượng</th>
                                <th>Thời gian</th>
                                <th class="text-center">Trạng thái</th>
                                <th width="160" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $sv)
                                <tr>
                                    <td>#{{ $sv['id'] }}</td>
                                    <td class="fw-semibold">{{ $sv['service_types'] }}</td>
                                    <td>{{ $sv['supplier'] }}</td>
                                    <td class="text-center">{{ (int)$sv['quantity'] }}</td>
                                    <td class="small">
                                        @if(!empty($sv['start_time']) && !empty($sv['end_time']))
                                            {{ date('d/m/Y H:i', strtotime($sv['start_time'])) }}
                                            <br>→ {{ date('d/m/Y H:i', strtotime($sv['end_time'])) }}
                                        @elseif(!empty($sv['start_time']))
                                            {{ date('d/m/Y H:i', strtotime($sv['start_time'])) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($sv['status'])
                                            @case(0)
                                                <span class="badge bg-warning text-dark">Chờ</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-info text-white">Xác nhận</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-success">Hoàn tất</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ (int)$sv['status'] }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin/services/edit/' . $sv['id']) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="{{ route('admin/services/delete/' . $sv['id']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này khỏi đoàn?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 small text-muted d-flex flex-wrap gap-3 align-items-center">
                    <span><i class="bi bi-info-circle me-1"></i>Tổng: <strong>{{ count($services) }}</strong> dịch vụ đoàn</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departureDate = document.getElementById('departure_date');
    const returnDate = document.getElementById('return_date');
    const tourSelect = document.getElementById('tour_id');
    const groupName = document.getElementById('group_name');
    const maxParticipants = document.getElementById('max_participants');
    const bookingSuggestion = document.getElementById('booking_suggestion');
    const summaryBox = document.getElementById('booking_summary_box');
    const bsTourName = document.getElementById('bs_tour_name');
    const bsNumPeople = document.getElementById('bs_num_people');
    const bsLocation = document.getElementById('bs_location');
    const bsPhone = document.getElementById('bs_phone');

    function applyBookingSuggestion(opt) {
        if (!opt || !opt.dataset || !opt.dataset.meta) {
            summaryBox.classList.add('d-none');
            return;
        }
        let meta = null;
        try {
            meta = JSON.parse(opt.dataset.meta);
        } catch (e) {
            meta = null;
        }
        if (!meta) return;

        summaryBox.classList.remove('d-none');
        bsTourName.textContent = meta.tour_name || '—';
        bsNumPeople.textContent = (meta.num_people ?? 0) + ' người';
        bsLocation.textContent = meta.tour_location || '—';
        bsPhone.textContent = meta.customer_phone || '—';

        if (meta.tour_id) {
            let found = false;
            for (let i = 0; i < tourSelect.options.length; i++) {
                if (String(tourSelect.options[i].value) === String(meta.tour_id)) {
                    tourSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
        }

        if (meta.num_people) {
            maxParticipants.value = Math.max(1, parseInt(meta.num_people, 10) || 1);
        }
    }

    bookingSuggestion.addEventListener('change', function () {
        applyBookingSuggestion(this.options[this.selectedIndex]);
    });

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
