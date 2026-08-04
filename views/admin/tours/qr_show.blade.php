<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">                
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .tour-page {
            max-width: 720px;
            margin: 0 auto;
            min-height: 100vh;
            background: #fff;
            box-shadow: 0 0 24px rgba(0,0,0,.06);
        }
        .tour-hero {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
            background: #e5e7eb;
        }
        .tour-hero-empty {
            height: 220px;
            background: linear-gradient(135deg, #dbeafe, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 3rem;
        }
        .tour-body {
            padding: 1.25rem 1.25rem 2rem;
        }
        .tour-price {
            color: #dc2626;
            font-size: 1.45rem;
            font-weight: 700;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .85rem;
            margin: 1rem 0 1.25rem;
        }
        .meta-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .75rem .9rem;
        }
        .meta-item .label {
            font-size: .75rem;
            color: #6b7280;
            margin-bottom: .15rem;
        }
        .meta-item .value {
            font-weight: 600;
            color: #111827;
            font-size: .95rem;
        }
        .desc-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.1rem;
            white-space: pre-line;
            line-height: 1.7;
            color: #374151;
        }
        .page-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .75rem;
            font-weight: 600;
            padding: .25rem .6rem;
            border-radius: 999px;
            margin-bottom: .75rem;
        }
    </style>
</head>
<body>
    <div class="tour-page">
        @if(!empty($tour['image']))
            <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" class="tour-hero">
        @else
            <div class="tour-hero-empty">
                <i class="bi bi-image"></i>
            </div>
        @endif

        <div class="tour-body">
            <div class="page-badge">
                <i class="bi bi-qr-code-scan"></i> Chi tiết tour
            </div>

            <h1 class="h3 fw-bold mb-2">{{ $tour['name'] }}</h1>
            <div class="tour-price mb-3">{{ number_format($tour['price']) }} VNĐ</div>

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">Danh mục</div>
                    <div class="value">
                        <i class="bi bi-tag"></i>
                        {{ $tour['category_name'] ?? 'Chưa phân loại' }}
                    </div>
                </div>
                <div class="meta-item">
                    <div class="label">Thời gian</div>
                    <div class="value">
                        <i class="bi bi-clock"></i>
                        {{ $tour['duration'] ?: 'Chưa cập nhật' }}
                    </div>
                </div>
                <div class="meta-item">
                    <div class="label">Trạng thái</div>
                    <div class="value">
                        @if($tour['status'] == 1)
                            <span class="badge bg-success">Đang mở</span>
                        @else
                            <span class="badge bg-secondary">Tạm ẩn</span>
                        @endif
                    </div>
                </div>
                <div class="meta-item">
                    <div class="label">Mã tour</div>
                    <div class="value">#{{ $tour['id'] }}</div>
                </div>
            </div>

            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-info-circle"></i> Mô tả / Giới thiệu
            </h2>
            <div class="desc-box">
                @if(!empty(trim($tour['description'] ?? '')))
                    {{ $tour['description'] }}
                @else
                    <span class="text-muted">Tour này chưa có mô tả giới thiệu.</span>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
