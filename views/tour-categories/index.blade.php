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
        <a href="{{ route('admin/tour-categories/create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Thêm mới danh mục
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($categories))
                            <tr>
                                <td colspan="4" class="text-center">Chưa có danh mục nào</td>
                            </tr>
                        @else
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category['id'] }}</td>
                                    <td>{{ $category['name'] }}</td>
                                    <td>
                                        @php
                                            $desc = $category['description'];
                                            echo (strlen($desc) > 100) ? substr($desc, 0, 100) . '...' : $desc;
                                        @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/tour-categories/edit/' . $category['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="{{ route('admin/tour-categories/delete/' . $category['id']) }}" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
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
@endsection