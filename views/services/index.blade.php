@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/services/create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Đặt dịch vụ
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
    @if(isset($_SESSION['success']))
        <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
        @php unset($_SESSION['success']); @endphp
    @endif

    <div class="card mb-3">
        <div class="card-body py-3">
            <form action="{{ route('admin/services') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label visually-hidden">Tour</label>
                    <select name="tour_id" class="form-select form-select-lg">
                        <option value="">Tất cả Tour</option>
                        @foreach($tours as $tour)
                            <option value="{{ $tour['id'] }}" {{ ($tourId ?? '') == $tour['id'] ? 'selected' : '' }}>
                                #{{ $tour['id'] }} - {{ $tour['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label visually-hidden">Loại dịch vụ</label>
                    <select name="service_types" class="form-select form-select-lg">
                        <option value="">Tất cả loại</option>
                        <option value="Tham quan" {{ ($serviceTypes ?? '') == 'Tham quan' ? 'selected' : '' }}>Tham quan</option>
                        <option value="Nhà hàng" {{ ($serviceTypes ?? '') == 'Nhà hàng' ? 'selected' : '' }}>Nhà hàng</option>
                        <option value="Vé máy bay" {{ ($serviceTypes ?? '') == 'Vé máy bay' ? 'selected' : '' }}>Vé máy bay</option>
                        <option value="Khách sạn" {{ ($serviceTypes ?? '') == 'Khách sạn' ? 'selected' : '' }}>Khách sạn</option>
                        <option value="Xe" {{ ($serviceTypes ?? '') == 'Xe' ? 'selected' : '' }}>Xe</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label visually-hidden">Trạng thái</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ (isset($status) && $status === '1') ? 'selected' : '' }}>Xác nhận</option>
                        <option value="0" {{ (isset($status) && $status === '0') ? 'selected' : '' }}>Chờ</option>
                        <option value="2" {{ (isset($status) && $status === '2') ? 'selected' : '' }}>Hoàn tất</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Lọc</button>
                    <a href="{{ route('admin/services') }}" class="btn btn-outline-secondary btn-lg w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th>ID</th>
                            <th>Tour</th>
                            <th>Loại dịch vụ</th>
                            <th>Nhà cung cấp</th>
                            <th>Số lượng</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Ghi chú</th>
                            <th width="130" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($services))
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">Chưa có dữ liệu</td>
                            </tr>
                        @else
                            @foreach($services as $service)
                                <tr style="border-bottom: 1px solid #eef2f7;">
                                    <td>{{ $service['id'] }}</td>
                                    <td class="fw-semibold">
                                        #{{ $service['tour_id'] }} - {{ $service['tour_name'] }}
                                    </td>
                                    <td>{{ $service['service_types'] }}</td>
                                    <td>{{ $service['supplier'] }}</td>
                                    <td>{{ $service['quantity'] }}</td>
                                    <td>
                                        @if($service['status'] == 0)
                                            <span class="badge" style="background: #facc15; color: #78350f; font-size: 0.85rem; padding: 0.4rem 0.9rem; border-radius: 9999px;">Chờ</span>
                                        @elseif($service['status'] == 1)
                                            <span class="badge" style="background: #0ea5e9; color: white; font-size: 0.85rem; padding: 0.4rem 0.9rem; border-radius: 9999px;">Xác nhận</span>
                                        @else
                                            <span class="badge" style="background: #16a34a; color: white; font-size: 0.85rem; padding: 0.4rem 0.9rem; border-radius: 9999px;">Hoàn tất</span>
                                        @endif
                                    </td>
                                    <td style="white-space: normal;">
                                        @if($service['start_time'] && $service['end_time'])
                                            {{ date('Y-m-d H:i:s', strtotime($service['start_time'])) }} -<br>
                                            {{ date('Y-m-d H:i:s', strtotime($service['end_time'])) }}
                                        @elseif($service['start_time'])
                                            {{ date('Y-m-d H:i:s', strtotime($service['start_time'])) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $service['note'] ?: '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin/services/edit/' . $service['id']) }}" class="btn btn-outline-primary btn-sm" title="Sửa" style="width: 40px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-pencil-square" style="font-size: 1.1rem;"></i>
                                            </a>
                                            <a href="{{ route('admin/services/delete/' . $service['id']) }}" class="btn btn-outline-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này?')" style="width: 40px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-trash" style="font-size: 1.1rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
