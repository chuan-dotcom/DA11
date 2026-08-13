@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Đăng ký tài khoản</h1>
        <p class="auth-muted mb-0">Tạo tài khoản người dùng mới để sử dụng hệ thống.</p>
    </div>                                                

    <form action="{{ route('auth/register') }}" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Họ và tên</label>
            <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" class="form-control form-control-lg" id="phone" name="phone" value="{{ old('phone') }}" placeholder="09xxxxxxxx">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Tối thiểu 6 ký tự" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu</label>
            <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
        </div>

        <button type="submit" class="btn btn-info text-white w-100 btn-lg fw-semibold">
            Đăng ký
        </button>
    </form>

    <p class="text-center auth-muted mt-4 mb-0">
        Đã có tài khoản?
        <a href="{{ route('auth/login') }}" class="fw-semibold text-decoration-none">Đăng nhập</a>
    </p>
@endsection
