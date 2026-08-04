@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .profile-avatar-lg {
        width: 160px;
        height: 160px;                    
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .profile-avatar-placeholder {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 4rem;
        font-weight: bold;
        border: 4px solid #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .info-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }
</style>

<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin/users') }}" class="text-decoration-none">
                    <i class="bi bi-people"></i> Danh sách tài khoản
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-person"></i> {{ $user['name'] }}
            </li>
        </ol>
    </nav>

    @if(isset($_SESSION['success']))
        <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
        @php unset($_SESSION['success']); @endphp
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0"><i class="bi bi-person-badge"></i> Thông tin chi tiết tài khoản</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin/users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    @if($user['avatar'])
                        <img src="{{ file_url($user['avatar']) }}" alt="{{ $user['name'] }}" class="profile-avatar-lg">
                    @else
                        <div class="profile-avatar-placeholder mx-auto">
                            {{ strtoupper(mb_substr($user['name'], 0, 1)) }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <h4 class="mb-1 fw-bold">{{ $user['name'] }}</h4>
                        <div class="d-flex gap-2 justify-content-center mt-2">
                            @if($user['role'] == 'admin')
                                <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="bi bi-shield-check me-1"></i> Quản trị viên
                                </span>
                            @else
                                <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    <i class="bi bi-person me-1"></i> Người dùng
                                </span>
                            @endif

                            @if($user['status'] == 1)
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Hoạt động
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Đã khóa
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-key me-1"></i> ID tài khoản
                            </div>
                            <div class="info-value">#{{ $user['id'] }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-envelope me-1"></i> Email
                            </div>
                            <div class="info-value">{{ $user['email'] }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-telephone me-1"></i> Số điện thoại
                            </div>
                            <div class="info-value">{{ $user['phone'] ?: '<span class="text-muted">—</span>' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-person-badge me-1"></i> Vai trò
                            </div>
                            <div class="info-value">
                                {{ $user['role'] == 'admin' ? 'Quản trị viên' : 'Người dùng' }}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-calendar-plus me-1"></i> Ngày tạo
                            </div>
                            <div class="info-value">
                                {{ !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : '<span class="text-muted">—</span>' }}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="info-label">
                                <i class="bi bi-calendar2-check me-1"></i> Cập nhật lần cuối
                            </div>
                            <div class="info-value">
                                {{ !empty($user['updated_at']) ? date('d/m/Y H:i', strtotime($user['updated_at'])) : '<span class="text-muted">—</span>' }}
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="info-label">
                                <i class="bi bi-toggle-on me-1"></i> Trạng thái
                            </div>
                            <div class="info-value">
                                @if($user['status'] == 1)
                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Tài khoản đang hoạt động, có thể truy cập hệ thống
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tài khoản đã bị khóa, không thể truy cập
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light py-3">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin/users/delete/' . $user['id']) }}"
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')">
                    <i class="bi bi-trash"></i> Xóa tài khoản
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
