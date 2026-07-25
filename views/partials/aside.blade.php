<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('') }}">
            <i class="bi bi-speedometer2"></i> Báo cáo thống kê
        </a>
    </li>
    
    <li class="nav-item mt-3">
        <h6 class="text-muted px-3 mb-1">Quản lý Tour</h6>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/tours') }}">
            <i class="bi bi-airplane"></i> Danh sách Tour
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/tour-categories') }}">
            <i class="bi bi-list-ul"></i> Danh mục Tour
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin/bookings') }}" class="nav-link">
            <i class="bi bi-calendar-check"></i>
            Quản lý Booking
        </a>
    </li>
</ul>
