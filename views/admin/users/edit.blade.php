@extends('layouts.admin')

@section('title', ($pageTitle ?? 'Quản lý tài khoản') . ' - ' . $title)

@section('content')
<style>
    .uf-page {                                                              
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .uf-breadcrumb a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }

    .uf-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .uf-card-head {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, rgba(248,250,252,0.8) 0%, #ffffff 100%);
    }

    .uf-card-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .uf-card-body {
        padding: 1.5rem 1.5rem 1.25rem;
    }

    .uf-avatar-box {
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: #f8fafc;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.25rem;
    }

    .uf-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .uf-avatar-ph {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        font-weight: 800;
    }

    .uf-form-label {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.9rem;
    }

    .uf-form-control,
    .uf-form-select {
        border-radius: 0.85rem;
        border-color: #e2e8f0;
    }

    .uf-form-control:focus,
    .uf-form-select:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15);
    }

    .uf-actions {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .uf-hint {
        color: #64748b;
        font-size: 0.82rem;
    }

    .uf-info-pill {
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #e0e7ff;
        border-radius: 999px;
        padding: 0.3rem 0.7rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    @media (max-width: 991.98px) {
        .uf-card-body { padding: 1.25rem 1rem; }
    }
</style>

<div class="mt-4 uf-page">
    <nav aria-label="breadcrumb" class="mb-3 uf-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin/users') }}">
                    <i class="bi bi-people me-1"></i>{{ $pageTitle ?? 'Quản lý tài khoản' }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin/users/show/' . $user['id']) }}" class="text-decoration-none">
                    <i class="bi bi-person me-1"></i>{{ $user['name'] }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-pencil-square me-1"></i>{{ $title }}
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
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger rounded-4">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif

    <form action="{{ route('admin/users/update/' . $user['id']) }}" method="POST" enctype="multipart/form-data">
        <div class="uf-card">
            <div class="uf-card-head">
                <h3 class="uf-card-title">
                    <i class="bi bi-pencil-square me-1"></i>
                    Cập nhật thông tin tài khoản
                    <span class="uf-info-pill ms-2">ID #{{ $user['id'] }}</span>
                </h3>
                <div class="uf-actions">
                    <a href="{{ route('admin/users/show/' . $user['id']) }}" class="btn btn-outline-info rounded-pill">
                        <i class="bi bi-eye me-1"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('admin/users') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-save-fill me-1"></i> Cập nhật
                    </button>
                </div>
            </div>

            <div class="uf-card-body">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label uf-form-label">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control uf-form-control"
                                       id="name"
                                       name="name"
                                       placeholder="Nhập họ và tên"
                                       required
                                       value="{{ old('name') ?? $user['name'] }}">
                            </div>

                            <div class="col-md-8">
                                <label for="email" class="form-label uf-form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control uf-form-control"
                                       id="email"
                                       name="email"
                                       placeholder="example@email.com"
                                       required
                                       value="{{ old('email') ?? $user['email'] }}">
                            </div>

                            <div class="col-md-4">
                                <label for="phone" class="form-label uf-form-label">Số điện thoại</label>
                                <input type="tel"
                                       class="form-control uf-form-control"
                                       id="phone"
                                       name="phone"
                                       placeholder="09xxxxxxx"
                                       value="{{ old('phone') ?? $user['phone'] }}">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info py-2 small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Để trống các ô mật khẩu bên dưới nếu không muốn thay đổi mật khẩu.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label uf-form-label">Mật khẩu mới</label>
                                <input type="password"
                                       class="form-control uf-form-control"
                                       id="password"
                                       name="password"
                                       placeholder="Tối thiểu 6 ký tự (nếu đổi)"
                                       minlength="6"
                                       autocomplete="new-password">
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label uf-form-label">Xác nhận mật khẩu mới</label>
                                <input type="password"
                                       class="form-control uf-form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Nhập lại mật khẩu mới"
                                       minlength="6"
                                       autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label for="avatar" class="form-label uf-form-label">Ảnh đại diện</label>
                        <input type="file"
                               class="form-control uf-form-control"
                               id="avatar"
                               name="avatar"
                               accept="image/*">

                        <div class="uf-avatar-box">
                            <div id="avatarWrap">
                                @if(!empty($user['avatar']))
                                    <img id="avatarCurrent"
                                         src="{{ file_url($user['avatar']) }}"
                                         alt="{{ $user['name'] }}"
                                         class="uf-avatar">
                                @else
                                    <div class="uf-avatar-ph" id="avatarPh">
                                        {{ strtoupper(mb_substr($user['name'], 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="role" class="form-label uf-form-label">
                                Vai trò <span class="text-danger">*</span>
                            </label>
                            <select class="form-select uf-form-select" id="role" name="role" required>
                                <option value="user" {{ (old('role') ?? $user['role']) === 'user' ? 'selected' : '' }}>Người dùng</option>
                                <option value="hdv" {{ (old('role') ?? $user['role']) === 'hdv' ? 'selected' : '' }}>Hướng dẫn viên</option>
                                <option value="admin" {{ (old('role') ?? $user['role']) === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                        </div>

                        <div class="mt-3 {{ (old('role') ?? $user['role']) === 'hdv' ? '' : 'd-none' }}" id="hdvWrap">
                            <label for="hdv_id" class="form-label uf-form-label">Gắn với hồ sơ HDV</label>
                            <div class="uf-hint mb-2">Để trống nếu muốn dùng làm tài khoản HDV chung.</div>
                            <select class="form-select uf-form-select" id="hdv_id" name="hdv_id">
                                <option value="">Tài khoản HDV chung</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff['HDV_id'] }}"
                                        {{ (string) ((string) old('hdv_id') !== '' ? old('hdv_id') : ($user['hdv_id'] ?? '')) === (string) $staff['HDV_id'] ? 'selected' : '' }}>
                                        #{{ $staff['HDV_id'] }} - {{ $staff['Hoten'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-3">
                            <label for="status" class="form-label uf-form-label">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select uf-form-select" id="status" name="status" required>
                                <option value="1" {{ (string) (old('status') ?? $user['status']) === '1' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ (string) (old('status') ?? $user['status']) === '0' ? 'selected' : '' }}>Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="uf-actions">
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-save-fill me-1"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin/users') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const avatarInput = document.getElementById('avatar');
        const wrap = document.getElementById('avatarWrap');

        avatarInput?.addEventListener('change', function (e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                wrap.innerHTML = '<img src="' + ev.target.result + '" alt="avatar" class="uf-avatar">';
            };
            reader.readAsDataURL(file);
        });

        const role = document.getElementById('role');
        const hdvWrap = document.getElementById('hdvWrap');
        const hdv = document.getElementById('hdv_id');

        function syncRole() {
            const isHdv = role.value === 'hdv';
            hdvWrap.classList.toggle('d-none', !isHdv);
            if (!isHdv) hdv.value = '';
        }
        role?.addEventListener('change', syncRole);
        syncRole();
    })();
</script>
@endsection
