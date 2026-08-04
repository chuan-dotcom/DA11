@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">                  
    <h2 class="mb-4">
        <i class="bi bi-pencil-square"></i> {{ $title }}
    </h2>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin/users/update/' . $user['id']) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user['name'] }}" placeholder="Nhập họ và tên" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user['email'] }}" placeholder="example@email.com" required>
                        </div>

                        <div class="alert alert-info py-2">
                            <small><i class="bi bi-info-circle me-1"></i> Để trống nếu không muốn thay đổi mật khẩu</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Tối thiểu 6 ký tự" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" minlength="6">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ $user['phone'] }}" placeholder="09xxxxxxx">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <div class="mt-2 d-flex justify-content-center">
                                <div id="avatar-preview-container" class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted" style="width: 140px; height: 140px;">
                                    @if($user['avatar'])
                                        <img id="avatar-current" src="{{ file_url($user['avatar']) }}" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 2px solid #e5e7eb;">
                                    @else
                                        <i id="avatar-icon" class="bi bi-person fs-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user" {{ $user['role'] == 'user' ? 'selected' : '' }}>
                                    <i class="bi bi-person"></i> User
                                </option>
                                <option value="admin" {{ $user['role'] == 'admin' ? 'selected' : '' }}>
                                    <i class="bi bi-shield-check"></i> Admin
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1" {{ $user['status'] == 1 ? 'selected' : '' }}>
                                    <i class="bi bi-check-circle"></i> Hoạt động
                                </option>
                                <option value="0" {{ $user['status'] == 0 ? 'selected' : '' }}>
                                    <i class="bi bi-x-circle"></i> Khóa
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin/users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const container = document.getElementById('avatar-preview-container');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                container.innerHTML = '<img src="' + e.target.result + '" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 2px solid #e5e7eb;">';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
