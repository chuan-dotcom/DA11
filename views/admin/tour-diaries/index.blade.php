@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .admin-journal-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .admin-journal-header {
        background: #0f172a;
        color: #ffffff;
        padding: 1rem 1.25rem;
    }
    .cost-summary-admin {
        background: #f1f5f9;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1.25rem;
    }
    .cost-box-read-only {
        background: #ffffff;
        border-radius: 8px;
        padding: 0.65rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .cost-box-read-only.estimated {
        border-left: 4px solid #0284c7;
    }
    .cost-box-read-only.incurred {
        border-left: 4px solid #f59e0b;
    }
    .diary-thumb {
        width: 70px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }
    .diary-title-link {
        color: #1e293b;
        text-decoration: none;
        font-weight: 600;
    }
    .diary-title-link:hover {
        color: #0d6efd;
        text-decoration: underline;
    }
    .stat-box {
        background: linear-gradient(135deg, #18bfd4, #0ea5b7);
        color: white;
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }
</style>

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-journal-album text-primary me-2"></i> {{ $title }}</h3>
        <a href="{{ route('admin/tour-diaries/create') }}{{ !empty($departureId) ? '?departure_id=' . $departureId : '' }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Thêm nhật ký mới
        </a>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">{{ $_SESSION['flash']['success'] }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">{{ $_SESSION['flash']['error'] }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-box shadow-sm">
                <div class="small opacity-75 mb-1">Tổng số Chuyến đi công tác</div>
                <div class="fs-3 fw-bold">{{ count($groupedJournals) }} chuyến</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                <div class="small opacity-75 mb-1">Bài viết nhật ký chính</div>
                <div class="fs-3 fw-bold">{{ count($groupedJournals) }} bài chính</div>
            </div>
        </div>
    </div>

    <!-- Filter by Departure -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin/tour-diaries') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1"><i class="bi bi-funnel me-1"></i> Lọc theo chuyến khởi hành</label>
                    <select class="form-select form-select-sm" name="departure_id" onchange="this.form.submit()">
                        <option value="">-- Tất cả chuyến khởi hành --</option>
                        @foreach($departures as $d)
                            <option value="{{ $d['id'] }}" {{ !empty($departureId) && $departureId == $d['id'] ? 'selected' : '' }}>
                                #{{ $d['id'] }} - {{ $d['group_name'] ?: $d['tour_name'] }} ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : 'chưa xếp lịch' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(!empty($departureId))
                    <div class="col-md-auto">
                        <a href="{{ route('admin/tour-diaries') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-x-lg me-1"></i> Bỏ lọc
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Grouped Main Tour Journals -->
    @if(empty($groupedJournals))
        <div class="card shadow-sm border-0 rounded-3 p-5 text-center text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
            <h5>Chưa có dữ liệu nhật ký tour nào</h5>
            <p class="small text-muted mb-3">Hãy chọn chuyến khởi hành hoặc tạo mới nhật ký tour.</p>
            <div>
                <a href="{{ route('admin/tour-diaries/create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Thêm nhật ký mới
                </a>
            </div>
        </div>
    @else
        @foreach($groupedJournals as $journal)
            <div class="admin-journal-card shadow-sm">
                <!-- Header -->
                <div class="admin-journal-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="badge bg-primary text-white me-2">Chuyến #{{ $journal['departure_id'] }}</span>
                        <span class="badge bg-warning text-dark me-2"><i class="bi bi-journal-text me-1"></i> {{ $journal['total_child_diaries'] }} bài viết con</span>
                        <span class="fw-bold fs-6 text-white">{{ $journal['tour_name'] }}</span>
                        <div class="small opacity-75 mt-0.5">
                            <i class="bi bi-people me-1"></i> {{ $journal['group_name'] }}
                            @if(!empty($journal['departure_date']))
                                <span class="ms-2"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y', strtotime($journal['departure_date'])) }} {{ !empty($journal['return_date']) ? (' - ' . date('d/m/Y', strtotime($journal['return_date']))) : '' }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin/tour-diaries/create') }}?departure_id={{ $journal['departure_id'] }}" class="btn btn-sm btn-light text-dark rounded-pill px-3 fw-bold">
                            <i class="bi bi-plus-lg me-1"></i> Thêm bài viết nhỏ
                        </a>
                    </div>
                </div>

                <!-- Costs Read-Only Banner (Hiển thị ở ngoài ý chính cho Admin) -->
                <div class="cost-summary-admin">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="cost-box-read-only estimated">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-muted"><i class="bi bi-calculator me-1 text-primary"></i> Chi phí dự kiến</span>
                                    <span class="badge bg-info-subtle text-primary border border-info-subtle" style="font-size:10px;">Tính tự động từ DB</span>
                                </div>
                                <div class="fs-5 fw-bold text-primary">
                                    {{ number_format($journal['estimated_cost'], 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="cost-box-read-only estimated" style="border-left: 4px solid #0d6efd;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-muted"><i class="bi bi-cash-coin me-1 text-primary"></i> Chi phí thực tế</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">Tự động tính từ nhật ký</span>
                                </div>
                                <div class="fs-5 fw-bold text-primary">
                                    {{ number_format($journal['actual_cost'], 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="cost-box-read-only incurred">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-muted"><i class="bi bi-lightning-charge me-1 text-warning"></i> Chi phí phát sinh</span>
                                    <span class="badge bg-secondary-subtle text-muted border" style="font-size:10px;"><i class="bi bi-calculator me-1"></i>HDV cập nhật</span>
                                </div>
                                <div class="fs-5 fw-bold text-warning-emphasis">
                                    {{ number_format($journal['incurred_cost'], 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Child Diaries List (Collapsible) -->
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-secondary small">
                            <i class="bi bi-list-nested me-1"></i> BÀI VIẾT NHẬT KÝ CON CHO CHUYẾN NÀY ({{ $journal['total_child_diaries'] }})
                        </span>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdminList{{ $journal['departure_id'] }}">
                            <i class="bi bi-chevron-down me-1"></i> Ẩn / Hiện các bài nhật ký con
                        </button>
                    </div>

                    <div class="collapse show" id="collapseAdminList{{ $journal['departure_id'] }}">
                        @if(empty($journal['diaries']))
                            <div class="text-center text-muted py-3 small bg-light rounded-2">
                                Chưa có bài nhật ký con nào cho chuyến này.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th style="width: 50px;">STT</th>
                                            <th style="width: 80px;">Ảnh</th>
                                            <th>Tiêu đề nhật ký con</th>
                                            <th style="width: 140px;">Chi phí thực tế</th>
                                            <th style="width: 140px;">Chi phí phát sinh</th>
                                            <th>Ngày nhật ký</th>
                                            <th>Thời tiết / Cảm xúc</th>
                                            <th class="text-center" style="width: 120px;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($journal['diaries'] as $index => $diary)
                                            <tr>
                                                <td class="fw-bold text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    @php
                                                        $firstPhoto = null;
                                                        if (!empty($diary['photos'])) {
                                                            $rawP = trim($diary['photos']);
                                                            if (str_starts_with($rawP, '[') && str_ends_with($rawP, ']')) {
                                                                $decoded = json_decode($rawP, true);
                                                                if (is_array($decoded) && !empty($decoded)) {
                                                                    $firstPhoto = $decoded[0];
                                                                }
                                                            }
                                                            if (!$firstPhoto) {
                                                                $photos = explode(',', $rawP);
                                                                $firstPhoto = trim($photos[0] ?? '', "[] \"'\t\n\r");
                                                            }
                                                        }
                                                    @endphp
                                                    @if($firstPhoto)
                                                        <img src="{{ file_url($firstPhoto) }}" alt="{{ $diary['title'] }}" class="diary-thumb" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80';">
                                                    @else
                                                        <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80" alt="Placeholder" class="diary-thumb">
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="diary-title-link">
                                                        {{ $diary['title'] }}
                                                    </a>
                                                    <div class="text-muted small text-truncate" style="max-width: 320px;">
                                                        {{ mb_strimwidth(strip_tags($diary['content']), 0, 90, '...') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(!empty($diary['actual_cost']) && (float)$diary['actual_cost'] > 0)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                                            <i class="bi bi-cash-coin me-1"></i>{{ number_format($diary['actual_cost'], 0, ',', '.') }}đ
                                                        </span>
                                                        @if(!empty($diary['expense_category']))
                                                            <div class="small text-muted mt-1" style="font-size:11px;">
                                                                <i class="bi bi-tag me-1"></i>{{ $diary['expense_category'] }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($diary['expense_amount']) && (float)$diary['expense_amount'] > 0)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold">
                                                            <i class="bi bi-lightning me-1"></i>+{{ number_format($diary['expense_amount'], 0, ',', '.') }}đ
                                                        </span>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-semibold small">{{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}</span>
                                                </td>
                                                <td>
                                                    @if(!empty($diary['weather']))
                                                        <span class="badge bg-info-subtle text-info-emphasis me-1"><i class="bi bi-cloud-sun"></i> {{ $diary['weather'] }}</span>
                                                    @endif
                                                    @if(!empty($diary['mood']))
                                                        <span class="badge bg-warning-subtle text-dark"><i class="bi bi-emoji-smile"></i> {{ $diary['mood'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a href="{{ route('admin/tour-diaries/show/' . $diary['id']) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin/tour-diaries/edit/' . $diary['id']) }}" class="btn btn-sm btn-warning" title="Sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('admin/tour-diaries/delete/' . $diary['id']) }}"
                                                       class="btn btn-sm btn-danger"
                                                       title="Xóa"
                                                       onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
