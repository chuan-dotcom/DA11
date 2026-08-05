@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Tài khoản của tôi</h1>
        <p class="auth-muted mb-0">Bạn đã đăng nhập thành công vào hệ thống.</p>
    </div>

    <div class="bg-light rounded-4 p-4 mb-4">
        <div class="mb-3">
            <div class="text-muted small">Họ và tên</div>
            <div class="fw-semibold">{{ $user['name'] ?? '' }}</div>
        </div>
        <div class="mb-3">
            <div class="text-muted small">Email</div>
            <div class="fw-semibold">{{ $user['email'] ?? '' }}</div>
        </div>
        <div>
            <div class="text-muted small">Vai trò</div>
            <div class="fw-semibold">
                {{ ($user['role'] ?? '') === 'admin' ? 'Quản trị viên' : 'Người dùng' }}
            </div>
        </div>
    </div>

    <div class="d-grid gap-2">
        @if(($user['role'] ?? '') === 'admin')
            <a href="{{ route('admin/dashboard') }}" class="btn btn-info text-white btn-lg fw-semibold">Vào trang quản trị</a>
        @endif
        <a href="{{ route('auth/logout') }}" class="btn btn-outline-danger btn-lg fw-semibold">Đăng xuất</a>
    </div>
@endsection
