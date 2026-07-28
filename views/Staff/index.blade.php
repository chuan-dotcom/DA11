@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">
        <i class="bi bi-people-fill"></i> {{ $title }}
    </h2>

    @if(isset($_SESSION['success']))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>{{ $_SESSION['success'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['success']); @endphp
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $_SESSION['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>{{ $_SESSION['flash']['success'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $_SESSION['flash']['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="mb-3">
        <a href="{{ route('admin/staff/create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Thêm mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">ID</th>
                            <th>Họ tên</th>
                            <th>Liên hệ</th>
                            <th>Ngôn ngữ</th>
                            <th>Chứng chỉ</th>
                            <th>Kinh nghiệm</th>
                            <th>Điểm ĐG</th>
                            <th>Trạng thái</th>
                            <th width="200">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($staffs))
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Chưa có dữ liệu Hướng dẫn viên
                                </td>
                            </tr>
                        @else
                            @foreach($staffs as $s)
                                <tr>
                                    <td class="fw-semibold text-center">{{ $s['HDV_id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin/staff/show/' . $s['HDV_id']) }}" class="text-decoration-none fw-semibold text-dark">
                                            {{ $s['Hoten'] }}
                                        </a>
                                        @if($s['Gioitinh'])
                                            <small class="text-muted d-block">({{ $s['Gioitinh'] }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $s['Lienhe'] ?: '<span class="text-muted">—</span>' }}</td>
                                    <td>
                                        @if($s['Ngonngu'])
                                            <span class="badge bg-info text-dark">{{ $s['Ngonngu'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $s['chungchiHDV'] ?: '<span class="text-muted">—</span>' }}</td>
                                    <td class="text-center">
                                        {{ $s['Kinhnghiem'] ? $s['Kinhnghiem'] . ' năm' : '0 năm' }}
                                    </td>
                                    <td class="text-center">
                                        @if($s['Diemdanhgia'])
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-star-fill me-1"></i>{{ number_format($s['Diemdanhgia'], 1) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($s['Status'] == 'active')
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>Đang làm việc (Active)
                                            </span>
                                        @elseif($s['Status'] == 'on_leave')
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="bi bi-clock-history me-1"></i>Nghỉ phép (On Leave)
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i>Đã nghỉ (Inactive)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/staff/show/' . $s['HDV_id']) }}" class="btn btn-sm btn-info text-white" title="Xem">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin/staff/edit/' . $s['HDV_id']) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="{{ route('admin/staff/delete/' . $s['HDV_id']) }}"
                                           class="btn btn-sm btn-danger"
                                           title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa Hướng dẫn viên này?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light py-2">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Tổng số Hướng dẫn viên: <strong>{{ count($staffs) }}</strong>
            </small>
        </div>
    </div>
</div>
@endsection
