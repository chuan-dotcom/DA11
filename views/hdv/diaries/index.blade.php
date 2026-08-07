@extends('layouts.hdv')

@section('title', $title)

@section('content')
<style>
    .journal-main-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.75rem;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .journal-main-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    }
    .journal-main-header {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
    }
    .cost-summary-box {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }
    .cost-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 0.85rem 1.15rem;
        border: 1px solid #cbd5e1;
    }
    .cost-card.estimated {
        border-left: 4px solid #0ea5e9;
    }
    .cost-card.incurred {
        border-left: 4px solid #f59e0b;
    }
    .diary-thumb {
        width: 70px;
        height: 52px;
        object-fit: cover;
        border-radius: 8px;
    }
    .diary-title-link {
        color: #1e293b;
        text-decoration: none;
        font-weight: 700;
    }
    .diary-title-link:hover {
        color: #0284c7;
    }
    .stat-box {
        background: linear-gradient(135deg, #00bcd4, #00acc1);
        color: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
    }
</style>

<div class="hdv-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-journal-album text-info me-2"></i> {{ $title }}</h4>
            <p class="text-muted small mb-0">Tất cả Nhật ký chuyến đi được nhóm theo Chuyến khởi hành của hướng dẫn viên {{ $activeHdv['Hoten'] ?? '' }}.</p>
        </div>
        <a href="{{ route('hdv/nhat-ky-tour/create') }}{{ !empty($selectedDepartureId) ? '?departure_id=' . $selectedDepartureId : '' }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Viết nhật ký mới
        </a>
    </div>
</div>

@if(isset($_SESSION['flash']['success']))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['flash']['success'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @php unset($_SESSION['flash']['success']); @endphp
@endif

@if(isset($_SESSION['flash']['error']))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $_SESSION['flash']['error'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @php unset($_SESSION['flash']['error']); @endphp
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-box shadow-sm">
            <div class="small opacity-75 mb-1">Tổng số Chuyến đi công tác</div>
            <div class="fs-2 fw-bold">{{ $totalJournals ?? count($groupedJournals) }} chuyến</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box shadow-sm">
            <div class="small opacity-75 mb-1">Bài viết nhật ký chính</div>
            <div class="fs-2 fw-bold">{{ $totalJournals ?? count($groupedJournals) }} bài chính</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box shadow-sm">
            <div class="small opacity-75 mb-1">Chuyến đang lọc</div>
            <div class="fs-5 fw-bold text-truncate">{{ !empty($selectedDepartureId) ? ('Chuyến #' . $selectedDepartureId) : 'Tất cả chuyến công tác' }}</div>
        </div>
    </div>
</div>

<!-- Filter by Departure -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('hdv/nhat-ky-tour') }}" class="row g-2 align-items-end">
            <div class="col-md-7">
                <label class="form-label small text-muted mb-1"><i class="bi bi-funnel me-1"></i> Lọc theo chuyến công tác của bạn</label>
                <select class="form-select form-select-sm fw-semibold" name="departure_id" onchange="this.form.submit()">
                    <option value="">-- Tất cả các chuyến khởi hành đã tham gia --</option>
                    @foreach($departures as $d)
                        <option value="{{ $d['id'] }}" {{ !empty($selectedDepartureId) && $selectedDepartureId == $d['id'] ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['tour_name'] }} ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if(!empty($selectedDepartureId))
                <div class="col-md-auto">
                    <a href="{{ route('hdv/nhat-ky-tour') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Bỏ lọc
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- List of Main Tour Journals -->
@if(empty($groupedJournals))
    <div class="hdv-card text-center text-muted py-5">
        <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-50"></i>
        <h5>Chưa có dữ liệu nhật ký chuyến đi nào</h5>
        <p class="small text-muted mb-3">Bạn chưa có bài viết nhật ký hoặc chưa được phân công chuyến đi nào.</p>
        <a href="{{ route('hdv/nhat-ky-tour/create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Viết nhật ký đầu tiên
        </a>
    </div>
@else
    @foreach($groupedJournals as $journal)
        <div class="journal-main-card">
            <!-- Header of Main Tour Journal -->
            <div class="journal-main-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-light text-primary fw-bold px-2 py-1">Chuyến #{{ $journal['departure_id'] }}</span>
                        <span class="badge bg-info-subtle text-light border border-light-subtle px-2 py-1">{{ $journal['category_name'] }}</span>
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1">
                            <i class="bi bi-journal-bookmark me-1"></i> {{ $journal['total_child_diaries'] }} bài nhật ký
                        </span>
                    </div>
                    <h5 class="fw-bold text-white mb-1"><i class="bi bi-geo-alt-fill me-1"></i> {{ $journal['tour_name'] }}</h5>
                    <div class="small opacity-90">
                        <i class="bi bi-people-fill me-1"></i> {{ $journal['group_name'] }}
                        @if(!empty($journal['departure_date']))
                            <span class="ms-2"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/Y', strtotime($journal['departure_date'])) }} {{ !empty($journal['return_date']) ? (' - ' . date('d/m/Y', strtotime($journal['return_date']))) : '' }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('hdv/nhat-ky-tour/create') }}?departure_id={{ $journal['departure_id'] }}" class="btn btn-sm btn-light text-primary rounded-pill px-3 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Viết bài mới cho chuyến này
                    </a>
                </div>
            </div>

            <!-- Cost Summary Box (Outside Main Info) -->
            <div class="cost-summary-box">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="cost-card estimated shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-muted"><i class="bi bi-calculator me-1 text-info"></i> Chi phí dự kiến</span>
                                <span class="badge bg-info-subtle text-info-emphasis px-2 py-0.5" style="font-size:10px;">Dữ liệu hệ thống</span>
                            </div>
                            <div class="fs-5 fw-bold text-primary">
                                {{ number_format($journal['estimated_cost'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="cost-card estimated shadow-sm" style="border-left: 4px solid #0d6efd;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-muted"><i class="bi bi-cash-coin me-1 text-primary"></i> Chi phí thực tế</span>
                                <span class="badge bg-primary-subtle text-primary-emphasis px-2 py-0.5" style="font-size:10px;">Tự động tính từ nhật ký</span>
                            </div>
                            <div class="fs-5 fw-bold text-primary">
                                {{ number_format($journal['actual_cost'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="cost-card incurred shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold text-muted"><i class="bi bi-lightning-charge me-1 text-warning"></i> Chi phí phát sinh</span>
                                <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-0.5" style="font-size:10px;">HDV cập nhật</span>
                            </div>
                            <div class="fs-5 fw-bold text-warning-emphasis">
                                {{ number_format($journal['incurred_cost'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Child Diary Posts Section (5 bài nhỏ trong bài chính) -->
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <span class="fw-bold text-secondary small">
                        <i class="bi bi-list-stars me-1"></i> DANH SÁCH BÀI NHẬT KÝ CON ({{ $journal['total_child_diaries'] }})
                    </span>
                    <button class="btn btn-sm btn-link text-decoration-none text-muted p-0 small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseList{{ $journal['departure_id'] }}">
                        <i class="bi bi-chevron-down me-1"></i> Ẩn / Hiện danh sách
                    </button>
                </div>

                <div class="collapse show" id="collapseList{{ $journal['departure_id'] }}">
                    @if(empty($journal['diaries']))
                        <div class="bg-light rounded-3 text-center py-4 text-muted small">
                            <i class="bi bi-journal-text me-1"></i> Chuyến đi này chưa có bài viết nhật ký con nào. Nhấn "Viết bài mới cho chuyến này" để thêm bài đầu tiên.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th style="width: 80px;">Hình ảnh</th>
                                        <th>Tiêu đề bài viết nhỏ</th>
                                        <th style="width: 140px;">Chi phí thực tế</th>
                                        <th style="width: 140px;">Chi phí phát sinh</th>
                                        <th>Ngày viết</th>
                                        <th>Thời tiết / Cảm xúc</th>
                                        <th class="text-center" style="width: 130px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($journal['diaries'] as $index => $diary)
                                        <tr>
                                            <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
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
                                                <a href="{{ route('hdv/nhat-ky-tour/show/' . $diary['id']) }}" class="diary-title-link">
                                                    {{ $diary['title'] }}
                                                </a>
                                                <div class="text-muted small text-truncate" style="max-width: 280px;">
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
                                                <div class="fw-semibold text-secondary small">
                                                    <i class="bi bi-calendar-event me-1"></i> {{ !empty($diary['diary_date']) ? date('d/m/Y', strtotime($diary['diary_date'])) : '—' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1 align-items-start">
                                                    @if(!empty($diary['weather']))
                                                        <span class="badge bg-info-subtle text-info-emphasis px-2 py-0.5"><i class="bi bi-cloud-sun me-1"></i> {{ $diary['weather'] }}</span>
                                                    @endif
                                                    @if(!empty($diary['mood']))
                                                        <span class="badge bg-warning-subtle text-dark px-2 py-0.5"><i class="bi bi-emoji-smile me-1"></i> {{ $diary['mood'] }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="{{ route('hdv/nhat-ky-tour/show/' . $diary['id']) }}" class="btn btn-sm btn-info text-white rounded-circle" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('hdv/nhat-ky-tour/edit/' . $diary['id']) }}" class="btn btn-sm btn-warning rounded-circle" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('hdv/nhat-ky-tour/delete/' . $diary['id']) }}"
                                                   class="btn btn-sm btn-danger rounded-circle"
                                                   title="Xóa"
                                                   onclick="return confirm('Bạn có chắc muốn xóa nhật ký nhỏ này?')">
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

        <!-- Modal Update Incurred Cost for departure -->
        <div class="modal fade" id="costModal{{ $journal['departure_id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 shadow">
                    <form method="POST" action="{{ route('hdv/nhat-ky-tour/update-cost') }}">
                        <input type="hidden" name="departure_id" value="{{ $journal['departure_id'] }}">
                        <div class="modal-header bg-warning text-dark rounded-top-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-1"></i> Cập nhật chi phí phát sinh</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small text-muted mb-3">
                                <strong>Chuyến đi:</strong> #{{ $journal['departure_id'] }} - {{ $journal['tour_name'] }}
                            </p>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chi phí dự kiến (hệ thống tự tính):</label>
                                <input type="text" class="form-control bg-light" value="{{ number_format($journal['estimated_cost'], 0, ',', '.') }} VNĐ" disabled readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-warning-emphasis">Chi phí phát sinh thực tế (VNĐ):</label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control fw-bold" name="incurred_cost" value="{{ (int) $journal['incurred_cost'] }}" required placeholder="Nhập số tiền phát sinh (ví dụ: 1500000)">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lý do / Ghi chú chi phí phát sinh:</label>
                                <textarea class="form-control" name="incurred_cost_note" rows="3" placeholder="Ví dụ: Mua thêm nước uống cho đoàn, phát sinh tiền vé thắng cảnh thêm ngoài chương trình...">{{ $journal['incurred_cost_note'] }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Lưu chi phí phát sinh</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection
