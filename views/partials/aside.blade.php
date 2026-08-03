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

    <li class="nav-item mt-3">
        <h6 class="text-muted px-3 mb-1">Khởi hành & Nhân sự</h6>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/departures') }}">
            <i class="bi bi-calendar3"></i> Quản lý khởi hành
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/staff-assignments') }}">
            <i class="bi bi-person-workspace"></i> Phân bổ nhân sự
        </a>
    </li>

    <li class="nav-item mt-3">
        <h6 class="text-muted px-3 mb-1">Hệ thống</h6>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/staff') }}">
            <i class="bi bi-people-fill"></i> Quản lý nhân sự
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin/users') }}">
            <i class="bi bi-people"></i> Quản lý tài khoản
        </a>
    </li>
</ul>
