<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Xác thực tài khoản')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f9ff 0%, #ecfeff 100%);
        }

        .auth-shell {
            min-height: 100vh;
        }

        .auth-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        }

        .auth-brand {
            display: flex;
            justify-content: center;
            text-decoration: none;
        }

        .auth-brand-logo {
            width: 120px;
            max-width: 100%;
            height: auto;
            display: block;
        }

        .auth-muted {
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="container auth-shell d-flex align-items-center justify-content-center py-5">
    <div class="row justify-content-center w-100">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card auth-card">
                <div class="card-body p-4 p-md-5">
                    <a href="{{ route('auth/login') }}" class="auth-brand mb-3">
                        <svg class="auth-brand-logo" viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Travel Company logo">
                            <defs>
                                <linearGradient id="authSunsetFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#ff7b16"/>
                                    <stop offset="100%" stop-color="#ffd780"/>
                                </linearGradient>
                            </defs>
                            <circle cx="160" cy="118" r="87" fill="url(#authSunsetFill)" stroke="#08284a" stroke-width="4"/>
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
                    </a>

                    @if(isset($_SESSION['flash']['success']))
                        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
                        @php unset($_SESSION['flash']['success']); @endphp
                    @endif

                    @if(isset($_SESSION['flash']['error']))
                        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
                        @php unset($_SESSION['flash']['error']); @endphp
                    @endif

                    @yield('content')
                    @php unset($_SESSION['old_input']); @endphp
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
