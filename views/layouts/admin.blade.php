<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {               
            background: #f4f7fb;
        }

        .admin-sidebar {
            background: #18bfd4;
        }

        .admin-sidebar-logo {
            min-height: 158px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.15rem 0.25rem;
        }

        .admin-sidebar-logo span {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            line-height: 1.4;
        }

        .admin-sidebar-logo img {
            max-width: 100%;
            max-height: 135px;
            object-fit: contain;
            display: block;
        }

        .sidebar-logo-svg {
            width: 100%;
            max-width: 190px;
            height: auto;
            display: block;
            filter: drop-shadow(0 3px 6px rgba(255, 255, 255, 0.18)) drop-shadow(0 10px 18px rgba(9, 70, 110, 0.24)) contrast(1.08) saturate(1.08);
        }

        .admin-sidebar .sidebar-section-title {
            color: rgba(255, 255, 255, 0.72) !important;
            font-size: 0.85rem;
        }

        .admin-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.88);
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.7rem 0.85rem;
            transition: background-color 0.2s ease, color 0.2s ease, font-weight 0.2s ease;
        }

        .admin-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .admin-sidebar .nav-link.is-active {
            background: transparent;
            color: #fff;
            font-weight: 800;
        }

        .admin-sidebar .nav-link i {
            margin-right: 0.35rem;
        }

        .admin-topbar {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            margin-bottom: 1.25rem;
        }

        .admin-user-meta {
            color: #475569;
            font-size: 0.95rem;
        }
    </style>
    
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-3 col-lg-2 admin-sidebar min-vh-100 p-3">
            @include('admin.partials.aside')
        </div>

        <div class="col-12 col-md-9 col-lg-10 p-3">
            <div class="admin-topbar d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="fw-bold text-dark">Xin chào, {{ $_SESSION['auth']['name'] ?? 'Người dùng' }}</div>
                    <div class="admin-user-meta">
                        {{ $_SESSION['auth']['email'] ?? '' }}
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('auth/account') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-circle"></i> Tài khoản
                    </a>
                    <a href="{{ route('auth/logout') }}" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</div>

@include('admin.partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
