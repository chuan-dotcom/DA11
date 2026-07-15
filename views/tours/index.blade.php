@extends('layouts.admin')

@section('title', $title)

@section('content')
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

    <div class="mb-3">
        <a href="{{ route('admin/tours/create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Thêm mới tour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên tour</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($tours))
                            <tr>
                                <td colspan="8" class="text-center">Chưa có tour nào</td>
                            </tr>
                        @else
                            @foreach($tours as $tour)
                                <tr>
                                    <td>{{ $tour['id'] }}</td>
                                    <td>
                                        @if($tour['image'])
                                            <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" style="width: 100px; height: 70px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $tour['name'] }}</td>
                                    <td>{{ $tour['category_name'] }}</td>
                                    <td class="text-danger">{{ number_format($tour['price']) }} VNĐ</td>
                                    <td>{{ $tour['duration'] }}</td>
                                    <td>
                                        @if($tour['status'] == 1)
                                            <span class="badge bg-success">Hiển thị</span>
                                        @else
                                            <span class="badge bg-secondary">Ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/tours/show/' . $tour['id']) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/tours/edit/' . $tour['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/tours/delete/' . $tour['id']) }}" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc muốn xóa tour này?')">
                                            <i class="bi bi-trash"></i>
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
@endsection