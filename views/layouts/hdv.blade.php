<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kênh Hướng Dẫn Viên')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --hdv-teal: #00bcd4;
            --hdv-teal-dark: #00acc1;
            --hdv-teal-light: #e0f7fa;
            --hdv-bg: #f4f7fa;
        }

        body {
            background-color: var(--hdv-bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .hdv-sidebar {
            background-color: var(--hdv-teal);
            min-height: 100vh;
            color: white;
            padding: 1.25rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hdv-logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }

        .hdv-logo-area .sidebar-logo-svg {
            width: 100%;
            max-width: 190px;
            height: auto;
            display: block;
            filter: drop-shadow(0 3px 6px rgba(255, 255, 255, 0.18)) drop-shadow(0 10px 18px rgba(9, 70, 110, 0.24)) contrast(1.08) saturate(1.08);
            margin: 0 auto;
        }

        .hdv-logo-area .hdv-brand-title {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            line-height: 1.4;
            margin-top: 0.75rem;
        }

        .hdv-logo-area .hdv-brand-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .hdv-nav-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 25px;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .hdv-nav-item i {
            margin-right: 0.65rem;
            font-size: 1.15rem;
        }

        .hdv-nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .hdv-nav-item.active {
            background: rgba(255, 255, 255, 0.35);
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .hdv-logout-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-weight: 700;
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: auto;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
            transition: background-color 0.2s ease;
        }

        .hdv-logout-btn:hover {
            background-color: #dc2626;
            color: white;
        }

        .hdv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 1rem;
        }

        .hdv-header-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
        }

        .hdv-top-logout {
            background: #f1f5f9;
            color: #475569;
            border-radius: 20px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
        }

        .hdv-top-logout:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Tabs custom design matching screenshots */
        .hdv-tabs {
            border-bottom: none;
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .hdv-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            background: transparent;
            font-size: 0.95rem;
            position: relative;
        }

        .hdv-tabs .nav-link.active {
            color: #0284c7;
            font-weight: 700;
            background: transparent;
        }

        .hdv-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #0284c7;
            border-radius: 2px;
        }

        /* Card custom styling */
        .hdv-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-pill {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        .badge-confirmed {
            background-color: #dcfce7;
            color: #15803d;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        .table-hdv th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.9rem;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }

        .table-hdv td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar HDV -->
        <div class="col-12 col-md-3 col-lg-2 hdv-sidebar">
            <div>
                <div class="hdv-logo-area">
                    <svg class="sidebar-logo-svg" viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Du lich logo">
                        <defs>
                            <linearGradient id="sunsetFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#ff7b16"/>
                                <stop offset="100%" stop-color="#ffd780"/>
                            </linearGradient>
                        </defs>
                        <circle cx="160" cy="118" r="87" fill="url(#sunsetFill)" stroke="#08284a" stroke-width="4"/>
                        <path d="M77 156c22-20 40-34 62-49 13 12 21 29 24 53-32 4-58 3-86-4z" fill="#0a2448"/>
                        <path d="M120 156l39-43 22 31 33-24 44 40-138 0z" fill="#0d2e56"/>
                        <path d="M158 122l18 22-11 0-7-22z" fill="#f4f0da"/>
                        <path d="M74 173c37-6 73-2 105 10 36 14 70 18 95 18-21 12-49 18-85 18-48 0-84-15-115-46z" fill="#56b7bb"/>
                        <path d="M67 186c43-8 82-4 116 8 31 11 61 16 90 17-27 16-63 22-108 16-38-5-71-18-98-41z" fill="#2d8b93"/>
                        <path d="M63 201c42-10 78-8 109 0 27 7 53 9 81 7-25 18-60 28-103 24-36-3-66-13-87-31z" fill="#135a72"/>
                        <path d="M96 157c8-34 25-58 47-69-9 15-12 34-8 56-12 6-25 10-39 13z" fill="#06182f"/>
                        <path d="M91 153c-2-25 9-47 28-62 14 16 20 38 18 61-15 0-30 1-46 1z" fill="#081e3a"/>
                        <path d="M95 168c13-4 25-4 37 0-7 3-12 9-15 18-8-5-15-11-22-18z" fill="#06182f"/>
                        <path d="M183 144c6-22 20-38 39-48-8 12-12 28-10 46-10 1-19 2-29 2z" fill="#0a2448"/>
                        <path d="M181 140c1-18 10-34 24-46 12 12 18 29 17 46-14-1-27-1-41 0z" fill="#0c2a4e"/>
                        <path d="M191 154c12-3 23-3 34 2-7 3-12 9-14 18-8-7-14-14-20-20z" fill="#081e3a"/>
                        <path d="M228 86c19-21 45-31 68-28-16 3-31 13-44 27-7 0-15 1-24 1z" fill="#06182f"/>
                        <path d="M198 89l48 23 14-4 18 8-14 4-3 15-11-9-11 2 2-11-12-6 10-4-41-17z" fill="#06182f"/>
                        <path d="M149 181c19-10 42-12 70-8-24 2-44 7-61 15-18 8-31 18-39 29-7 0-13-2-19-4 13-14 29-25 49-32z" fill="#e7f3f6"/>
                        <path d="M63 224c33 6 63 5 91-1 26-6 52-9 78-7-41 18-89 26-145 8-9-3-17-7-24-13z" fill="#0a2448"/>
                        <path d="M143 269l5-35h10l5 35h-20z" fill="#06182f"/>
                        <text x="160" y="288" text-anchor="middle" font-size="48" font-weight="900" letter-spacing="7" fill="#06182f">TRAVEL</text>
                        <text x="160" y="312" text-anchor="middle" font-size="17" font-weight="700" letter-spacing="8" fill="#06182f">COMPANY</text>
                    </svg>
                    
                </div>

                @php
                    $uri = $_SERVER['REQUEST_URI'] ?? '';
                @endphp

                <nav class="nav flex-column">
                    <a href="{{ route('hdv/thong-tin-tour') }}" class="hdv-nav-item {{ str_contains($uri, 'thong-tin-tour') || $uri == '/hdv' ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Thông tin tour
                    </a>
                    <a href="{{ route('hdv/tour-phan-cong') }}" class="hdv-nav-item {{ str_contains($uri, 'tour-phan-cong') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Tour được phân công
                    </a>
                    <a href="{{ route('hdv/lich-trinh') }}" class="hdv-nav-item {{ str_contains($uri, 'lich-trinh') ? 'active' : '' }}">
                        <i class="bi bi-calendar3"></i> Lịch trình tour
                    </a>
                    <a href="{{ route('hdv/nhat-ky-tour') }}" class="hdv-nav-item {{ str_contains($uri, 'nhat-ky-tour') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Nhật ký tour
                    </a>
                </nav>
            </div>

            <div>
                <!-- HDV Selector Switcher -->
                @if(isset($allHdv) && !empty($allHdv))
                    <div class="mb-3 px-1">
                        <label class="form-label small text-white-50 mb-1"><i class="bi bi-person-circle"></i> Đang xem với tư cách:</label>
                        <select class="form-select form-select-sm bg-light border-0 fw-semibold text-dark" onchange="window.location.href='?hdv_id='+this.value">
                            @foreach($allHdv as $h)
                                <option value="{{ $h['HDV_id'] }}" {{ (isset($hdvId) && $hdvId == $h['HDV_id']) ? 'selected' : '' }}>
                                    {{ $h['Hoten'] }} (HDV #{{ $h['HDV_id'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="px-1 text-white-50 small mb-2">
                    Đăng nhập: <strong class="text-white">{{ $_SESSION['auth']['name'] ?? 'Người dùng' }}</strong>
                </div>
                <a href="{{ route('auth/logout') }}" class="hdv-logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-12 col-md-9 col-lg-10 p-4">
            <div class="hdv-header">
                <div class="hdv-header-title">
                    {{ $currentTourDetail['tour_name'] ?? 'Kênh Quản Lý Hướng Dẫn Viên' }}
                </div>
                <a href="{{ route('auth/account') }}" class="hdv-top-logout">
                    <i class="bi bi-person-circle me-1"></i> {{ $_SESSION['auth']['name'] ?? 'Tài khoản' }}
                </a>
            </div>

            @if(isset($_SESSION['flash']['success']))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['flash']['success'] }}
                    @php unset($_SESSION['flash']['success']); @endphp
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(isset($_SESSION['flash']['error']))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $_SESSION['flash']['error'] }}
                    @php unset($_SESSION['flash']['error']); @endphp
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
