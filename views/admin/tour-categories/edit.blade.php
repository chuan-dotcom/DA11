@extends('layouts.admin')

@section('title', $title)

@section('content')                 
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/tour-categories/update/' . $category['id']) }}" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $category['name'] }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ $category['description'] }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin/tour-categories') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>
@endsection