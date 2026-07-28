@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">
        <i class="bi bi-person-plus-fill"></i> {{ $title }}
    </h2>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $_SESSION['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $_SESSION['flash']['error'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin/staff/store') }}" method="POST">
                <h5 class="text-primary mb-3"><i class="bi bi-person-vcard"></i> Thông tin cơ bản</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Hoten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="Hoten" name="Hoten" placeholder="Nhập họ và tên" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Lienhe" class="form-label">Thông tin liên hệ (SĐT / Email)</label>
                            <input type="text" class="form-control" id="Lienhe" name="Lienhe" placeholder="SĐT hoặc Email">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Ngaysinh" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="Ngaysinh" name="Ngaysinh">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Gioitinh" class="form-label">Giới tính</label>
                            <select class="form-select" id="Gioitinh" name="Gioitinh">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Status" class="form-label">Trạng thái làm việc <span class="text-danger">*</span></label>
                            <select class="form-select" id="Status" name="Status" required>
                                <option value="active">Active (Đang làm)</option>
                                <option value="inactive">Inactive (Đã nghỉ)</option>
                                <option value="on_leave">On Leave (Nghỉ phép)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Diachi" class="form-label">Địa chỉ cư trú</label>
                    <input type="text" class="form-control" id="Diachi" name="Diachi" placeholder="Nhập địa chỉ cư trú">
                </div>

                <hr class="my-4">
                <h5 class="text-primary mb-3"><i class="bi bi-award"></i> Kỹ năng & Chuyên môn</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Ngonngu" class="form-label">Ngôn ngữ hướng dẫn</label>
                            <input type="text" class="form-control" id="Ngonngu" name="Ngonngu" placeholder="VD: Tiếng Anh, Tiếng Nhật">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="chungchiHDV" class="form-label">Chứng chỉ</label>
                            <input type="text" class="form-control" id="chungchiHDV" name="chungchiHDV" placeholder="Số/Loại chứng chỉ">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Kinhnghiem" class="form-label">Kinh nghiệm (Số năm)</label>
                            <input type="number" class="form-control" id="Kinhnghiem" name="Kinhnghiem" placeholder="VD: 5" min="0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Ngaybatdaulam" class="form-label">Ngày bắt đầu làm việc</label>
                            <input type="date" class="form-control" id="Ngaybatdaulam" name="Ngaybatdaulam">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Diemdanhgia" class="form-label">Điểm đánh giá (0 - 5.0)</label>
                            <input type="number" step="0.1" max="5.0" min="0" class="form-control" id="Diemdanhgia" name="Diemdanhgia" placeholder="VD: 4.8">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="HDV_group_id" class="form-label">Nhóm</label>
                            <input type="number" class="form-control" id="HDV_group_id" name="HDV_group_id" placeholder="VD: 1">
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="text-primary mb-3"><i class="bi bi-file-text"></i> Ghi chú & Đánh giá</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Trangthaisuckhoe" class="form-label">Tình trạng sức khỏe</label>
                            <textarea class="form-control" id="Trangthaisuckhoe" name="Trangthaisuckhoe" rows="3" placeholder="Nhập tình trạng sức khỏe"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Nhanxetdanhgia" class="form-label">Nhận xét đánh giá</label>
                            <textarea class="form-control" id="Nhanxetdanhgia" name="Nhanxetdanhgia" rows="3" placeholder="Nhận xét chi tiết về quá trình làm việc"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="Ghichunoibo" class="form-label">Ghi chú nội bộ</label>
                            <textarea class="form-control" id="Ghichunoibo" name="Ghichunoibo" rows="3" placeholder="Ghi chú nội bộ cho quản lý"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu Hướng dẫn viên
                    </button>
                    <a href="{{ route('admin/staff') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
