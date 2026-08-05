@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .user-avatar {                
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
    }
    .user-avatar-sm {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
    }
    .role-badge-admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .role-badge-user {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .role-badge-hdv {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
    }
    .table th,
    .table td {
        vertical-align: middle;
    }
    .table th:first-child,
    .table td:first-child {
        width: 40px;
    }
    .table td .btn {
        min-width: 88px;
    }
    .bulk-actions {
        justify-content: space-between;
    }
    .bulk-actions .btn {
        white-space: nowrap;
    }
</style>

<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-people"></i> {{ $title }}
            </h2>
        </div>
        <div class="d-flex flex-wrap gap-2 bulk-actions w-100 w-md-auto">
            <a href="{{ route('admin/users/create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Thêm mới tài khoản
            </a>
            <button type="submit" form="bulkDeleteForm" class="btn btn-danger">
                <i class="bi bi-trash"></i> Xóa tài khoản đã chọn
            </button>
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('admin/users/delete-multiple') }}" method="POST">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <colgroup>
                            <col style="width:40px">
                            <col style="width:60px">
                            <col style="width:80px">
                            <col>
                            <col style="width:220px">
                            <col style="width:120px">
                            <col style="width:140px">
                            <col style="width:120px">
                            <col style="width:220px">
                        </colgroup>
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center align-middle">
                                    <input type="checkbox" id="selectAllUsers" />
                                </th>
                                <th>ID</th>
                                <th>Ảnh đại diện</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($users))
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Chưa có tài khoản nào
                                    </td>
                                </tr>
                            @else
                                @foreach($users as $user)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="ids[]" value="{{ $user['id'] }}" />
                                        </td>
                                        <td class="fw-semibold text-center">{{ $user['id'] }}</td>
                                        <td class="text-center">
                                            @if($user['avatar'])
                                                <img src="{{ file_url($user['avatar']) }}" alt="{{ $user['name'] }}" class="user-avatar-sm">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center user-avatar-sm bg-light text-muted">
                                                    <i class="bi bi-person fs-5"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin/users/show/' . $user['id']) }}" class="text-decoration-none fw-semibold text-dark">
                                                {{ $user['name'] }}
                                            </a>
                                        </td>
                                        <td class="text-muted">{{ $user['email'] }}</td>
                                        <td>{{ $user['phone'] ?: '<span class="text-muted">—</span>' }}</td>
                                        <td>
                                            @if($user['role'] == 'admin')
                                                <span class="badge role-badge-admin px-3 py-2">
                                                    <i class="bi bi-shield-check me-1"></i>Admin
                                                </span>
                                            @elseif($user['role'] == 'hdv')
                                                <span class="badge role-badge-hdv px-3 py-2">
                                                    <i class="bi bi-person-badge me-1"></i>HDV
                                                </span>
                                            @else
                                                <span class="badge role-badge-user px-3 py-2">
                                                    <i class="bi bi-person me-1"></i>User
                                                </span>
                                            @endif
                                            @if($user['role'] == 'hdv' && !empty($user['hdv_name']))
                                                <div class="small text-muted mt-1">Gắn với: {{ $user['hdv_name'] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user['status'] == 1)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>Hoạt động
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">
                                                    <i class="bi bi-x-circle me-1"></i>Khóa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('admin/users/show/' . $user['id']) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                            <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i> Sửa
                                            </a>
                                            <a href="{{ route('admin/users/delete/' . $user['id']) }}"
                                               class="btn btn-sm btn-danger"
                                               title="Xóa"
                                               onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')">
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
                    Tổng số tài khoản: <strong>{{ count($users) }}</strong>
                </small>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.getElementById('selectAllUsers')?.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input[name="ids[]"]')?.forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    });
</script>
@endsection

                                            <img src="{{ file_url($user['avatar']) }}" alt="{{ $user['name'] }}" class="user-avatar-sm">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center user-avatar-sm bg-light text-muted">
                                                <i class="bi bi-person fs-5"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/users/show/' . $user['id']) }}" class="text-decoration-none fw-semibold text-dark">
                                            {{ $user['name'] }}
                                        </a>
                                    </td>
                                    <td class="text-muted">{{ $user['email'] }}</td>
                                    <td>{{ $user['phone'] ?: '<span class="text-muted">—</span>' }}</td>
                                    <td>
                                        @if($user['role'] == 'admin')
                                            <span class="badge role-badge-admin px-3 py-2">
                                                <i class="bi bi-shield-check me-1"></i>Admin
                                            </span>
                                        @elseif($user['role'] == 'hdv')
                                            <span class="badge role-badge-hdv px-3 py-2">
                                                <i class="bi bi-person-badge me-1"></i>HDV
                                            </span>
                                        @else
                                            <span class="badge role-badge-user px-3 py-2">
                                                <i class="bi bi-person me-1"></i>User
                                            </span>
                                        @endif
                                        @if($user['role'] == 'hdv' && !empty($user['hdv_name']))
                                            <div class="small text-muted mt-1">Gắn với: {{ $user['hdv_name'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user['status'] == 1)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>Hoạt động
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i>Khóa
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/users/show/' . $user['id']) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin/users/edit/' . $user['id']) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="{{ route('admin/users/delete/' . $user['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác!')">
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
                Tổng số tài khoản: <strong>{{ count($users) }}</strong>
            </small>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.getElementById('selectAllUsers')?.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input[name="ids[]"]').forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    });
</script>
@endsection
