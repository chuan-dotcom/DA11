@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>                 
    .tour-name-link {
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 600;
    }
    .tour-name-link:hover {
        color: #0d6efd;
        text-decoration: underline;
    }
    .tour-thumb {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
        transition: opacity .2s;
    }
    .tour-thumb:hover {
        opacity: .85;
    }
    #qrcode-box {
        display: flex;
        justify-content: center;
        padding: 12px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        min-height: 220px;
        align-items: center;
    }
    #qrcode-box img, #qrcode-box canvas {
        max-width: 200px;
        height: auto;
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

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

    <div class="mb-3">
        <a href="{{ route('admin/tours/create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Thêm mới tour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên tour</th>
                            <th>Địa điểm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Thời gian</th>
                            <th>Số người (tối đa)</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($tours))
                            <tr>
                                <td colspan="10" class="text-center">Chưa có tour nào</td>
                            </tr>
                        @else
                            @foreach($tours as $tour)
                                @php $detailUrl = absolute_url('tour/' . $tour['id']); @endphp
                                <tr>
                                    <td>{{ $tour['id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin/tours/show/' . $tour['id']) }}" title="Xem chi tiết">
                                            @if($tour['image'])
                                                <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" class="tour-thumb" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/tours/show/' . $tour['id']) }}" class="tour-name-link" title="Xem mô tả tour">
                                            {{ $tour['name'] }}
                                        </a>
                                    </td>
                                    <td>
                                        @if(!empty($tour['location']))
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $tour['location'] }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $tour['category_name'] }}</td>
                                    <td class="text-danger">{{ number_format($tour['price']) }} VNĐ</td>
                                    <td>{{ $tour['duration'] }}</td>
                                    <td>
                                        @if(!empty($tour['max_participants']))
                                            <span class="badge bg-primary">
                                                <i class="bi bi-people me-1"></i>{{ number_format($tour['max_participants']) }}
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-infinity me-1"></i>Không giới hạn
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tour['status'] == 1)
                                            <span class="badge bg-success">Hiển thị</span>
                                        @else
                                            <span class="badge bg-secondary">Ẩn</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/tours/participants/' . $tour['id']) }}" class="btn btn-sm btn-success" title="Xem người tham gia">
                                            <i class="bi bi-people"></i> Người tham gia
                                        </a>
                                        <a href="{{ route('admin/tours/show/' . $tour['id']) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-dark btn-show-qr"
                                                title="Mã QR chi tiết tour"
                                                data-bs-toggle="modal"
                                                data-bs-target="#qrModal"
                                                data-tour-name="{{ $tour['name'] }}"
                                                data-tour-url="{{ $detailUrl }}">
                                            <i class="bi bi-qr-code"></i> QR
                                        </button>
                                        <a href="{{ route('admin/tours/edit/' . $tour['id']) }}" class="btn btn-sm btn-warning" title="Sửa tour">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="{{ route('admin/tours/delete/' . $tour['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           title="Xóa tour"
                                           onclick="return confirm('Bạn có chắc muốn xóa tour này?')">
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
    </div>
</div>

{{-- Modal QR --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="bi bi-qr-code"></i> Mã QR tour
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-semibold mb-2" id="qr-tour-name"></p>
                <p class="text-muted small mb-3">Quét mã QR để mở ngay trang chi tiết tour</p>
                <div id="qrcode-box" class="mx-auto mb-3" style="width:220px;"></div>
                <div class="small text-break text-muted" id="qr-tour-url"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <a href="#" id="qr-open-link" class="btn btn-outline-primary btn-sm" target="_blank">
                    Mở link chi tiết
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('qrModal');
        const qrBox = document.getElementById('qrcode-box');
        let qrInstance = null;

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-tour-name');
            const url = button.getAttribute('data-tour-url');

            document.getElementById('qr-tour-name').textContent = name;
            document.getElementById('qr-tour-url').textContent = url;
            document.getElementById('qr-open-link').href = url;

            qrBox.innerHTML = '';
            qrInstance = new QRCode(qrBox, {
                text: url,
                width: 200,
                height: 200,
                colorDark: '#111827',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        });

        modal.addEventListener('hidden.bs.modal', function () {
            qrBox.innerHTML = '';
            qrInstance = null;
        });
    });
</script>
@endsection
