@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Đăng nhập</h1>
        <p class="auth-muted mb-0">Đăng nhập để truy cập hệ thống quản lý tour.</p>
    </div>

    <form action="{{ route('auth/login') }}" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <button type="submit" class="btn btn-info text-white w-100 btn-lg fw-semibold">
            Đăng nhập
        </button>
    </form>

    <p class="text-center auth-muted mt-4 mb-0">
        Chưa có tài khoản?
        <a href="{{ route('auth/register') }}" class="fw-semibold text-decoration-none">Đăng ký ngay</a>
    </p>
@endsection
