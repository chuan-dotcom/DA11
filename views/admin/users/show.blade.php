@extends('layouts.admin')

@section('title', ($pageTitle ?? 'Quản lý tài khoản') . ' - ' . $title)

@section('content')
<style>                                              
    .us-page {
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .us-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }

    .us-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .us-card-head {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, rgba(248,250,252,0.8) 0%, #ffffff 100%);
    }

    .us-card-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .us-card-body {
        padding: 1.5rem;
    }

    .us-avatar {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.1);
        background: #fff;
    }

    .us-avatar-ph {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.1);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
    }

    .us-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .us-role-admin { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .us-role-hdv { background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); color: #fff; }
    .us-role-user { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; }
    .us-status-active { background: rgba(22,163,74,0.12); color: #15803d; }
    .us-status-locked { background: #e2e8f0; color: #334155; }

    .us-info-label {
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .us-info-value {
        color: #0f172a;
        font-weight: 700;
        font-size: 0.98rem;
        word-break: break-word;
    }

    .us-info-muted { color: #64748b; font-weight: 600; font-size: 0.95rem; }

    .us-grid {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 1.75rem;
    }

    .us-side {
        border-right: 1px solid #f1f5f9;
        padding-right: 1.5rem;
    }

    @media (max-width: 991.98px) {
        .us-grid {
            grid-template-columns: 1fr;
        }
        .us-side {
            border-right: none;
            border-bottom: 1px solid #f1f5f9;
            padding-right: 0;
            padding-bottom: 1.5rem;
        }
    }
</style>

<div class="mt-4 us-page">
    <nav aria-label="breadcrumb" class="mb-3 us-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin/users') }}">
                    <i class="bi bi-people me-1"></i>{{ $pageTitle ?? 'Quản lý tài khoản' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-person me-1"></i>{{ $title }}
            </li>
        </ol>
    </nav>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success rounded-4">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger rounded-4">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif
    @if(isset($_SESSION['success']))
        <div class="alert alert-success rounded-4">{{ $_SESSION['success'] }}</div>
        @php unset($_SESSION['success']); @endphp
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger rounded-4">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif

    <div class="us-card">
        <div class="us-card-head">
            <h3 class="us-card-title">
                <i class="bi bi-person-badge me-1"></i>
                Thông tin tài khoản
                <span class="badge rounded-pill bg-light text-dark ms-2 border border-secondary-subtle">
                    #{{ $user['id'] }}
                </span>
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-warning rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin/users') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="us-card-body">
            <div class="us-grid">
                <div class="us-side text-center">
                    @if(!empty($user['avatar']))
                        <img src="{{ file_url($user['avatar']) }}" alt="{{ $user['name'] }}" class="us-avatar mx-auto d-block">
                    @else
                        <div class="us-avatar-ph mx-auto">
                            {{ strtoupper(mb_substr($user['name'], 0, 1)) }}
                        </div>
                    @endif

                    <h4 class="mt-3 mb-1 fw-bold">{{ $user['name'] }}</h4>
                    <div class="text-muted small">{{ $user['email'] }}</div>

                    <div class="mt-3 d-flex flex-wrap justify-content-center gap-2">
                        @php
                            $roleClass = 'us-role-user';
                            $roleIcon = 'bi-person';
                            $roleLabel = 'Người dùng';
                            if ($user['role'] === 'admin') {
                                $roleClass = 'us-role-admin';
                                $roleIcon = 'bi-shield-check';
                                $roleLabel = 'Quản trị viên';
                            } elseif ($user['role'] === 'hdv') {
                                $roleClass = 'us-role-hdv';
                                $roleIcon = 'bi-person-badge';
                                $roleLabel = 'Hướng dẫn viên';
                            }
                        @endphp
                        <span class="us-badge {{ $roleClass }}">
                            <i class="bi {{ $roleIcon }}"></i> {{ $roleLabel }}
                        </span>

                        @if($user['status'] == 1)
                            <span class="us-badge us-status-active">
                                <i class="bi bi-check-circle"></i> Hoạt động
                            </span>
                        @else
                            <span class="us-badge us-status-locked">
                                <i class="bi bi-lock"></i> Đã khóa
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-key me-1"></i>ID</div>
                            <div class="us-info-value">#{{ $user['id'] }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-person me-1"></i>Họ và tên</div>
                            <div class="us-info-value">{{ $user['name'] }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-envelope me-1"></i>Email</div>
                            <div class="us-info-value">{{ $user['email'] }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-telephone me-1"></i>Số điện thoại</div>
                            <div class="us-info-value">
                                {{ !empty($user['phone']) ? $user['phone'] : '<span class="us-info-muted">—</span>' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-person-badge me-1"></i>Vai trò</div>
                            <div class="us-info-value">{{ $roleLabel }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-person-vcard me-1"></i>Hồ sơ HDV</div>
                            <div class="us-info-value">
                                {{ !empty($user['hdv_name']) ? $user['hdv_name'] : '<span class="us-info-muted">—</span>' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-calendar-plus me-1"></i>Ngày tạo</div>
                            <div class="us-info-value">
                                {{ !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : '<span class="us-info-muted">—</span>' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="us-info-label"><i class="bi bi-calendar2-check me-1"></i>Cập nhật lần cuối</div>
                            <div class="us-info-value">
                                {{ !empty($user['updated_at']) ? date('d/m/Y H:i', strtotime($user['updated_at'])) : '<span class="us-info-muted">—</span>' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0" style="background:#f8fafc;border-radius:14px;">
                                <div class="card-body py-3 px-4">
                                    <div class="us-info-label mb-1">
                                        <i class="bi bi-toggle-on me-1"></i> Trạng thái hoạt động
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if($user['status'] == 1)
                                            <span class="text-success fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Tài khoản đang hoạt động, có thể truy cập hệ thống.
                                            </span>
                                        @else
                                            <span class="text-danger fw-semibold">
                                                <i class="bi bi-x-circle-fill me-1"></i>
                                                Tài khoản đã bị khóa, tạm thời không thể truy cập.
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer py-3 border-top" style="background:#f8fafc;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    <i class="bi bi-clock-history me-1"></i>
                    Xem chi tiết lúc: {{ date('d/m/Y H:i') }}
                </small>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-warning rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin/users/delete/' . $user['id']) }}"
                       class="btn btn-danger rounded-pill"
                       onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')">
                        <i class="bi bi-trash me-1"></i> Xóa tài khoản
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
