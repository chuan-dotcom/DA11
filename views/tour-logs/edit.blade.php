@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .existing-image {
        position: relative;
        width: 140px;
        height: 140px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #ddd;
    }
    .existing-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .existing-image .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(220,53,69,0.9);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-preview-item {
        width: 140px;
        height: 140px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    .image-preview-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }
    .mood-option {
        cursor: pointer;
        transition: all 0.2s;
    }
    .mood-option:hover { transform: scale(1.2); }
    .mood-option.selected {
        transform: scale(1.3);
        filter: drop-shadow(0 0 4px rgba(13,110,253,0.5));
    }
</style>

@php
    $existingImages = json_decode($log['images'] ?? '[]', true);
    if (!is_array($existingImages)) { $existingImages = []; }
@endphp

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/tour-logs/update/' . $log['id']) }}" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề nhật ký <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                   value="{{ htmlspecialchars($log['title']) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" required
                                      style="resize: vertical;">{{ htmlspecialchars($log['content']) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện có</label>
                            <div class="d-flex flex-wrap gap-2" id="existingImagesContainer">
                                @if(empty($existingImages))
                                    <div class="text-muted small">Chưa có hình ảnh nào</div>
                                @else
                                    @foreach($existingImages as $idx => $imgPath)
                                        <div class="existing-image" data-path="{{ $imgPath }}">
                                            <img src="{{ file_url($imgPath) }}" alt="Ảnh {{ $idx + 1 }}">
                                            <button type="button" class="remove-btn"
                                                    onclick="removeExistingImage(this, '{{ addslashes($imgPath) }}')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" id="removedImages" name="removed_images[]" value="">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thêm hình ảnh mới</label>
                            <input type="file" class="form-control" id="newImages" name="images[]"
                                   accept="image/*" multiple>
                            <div class="form-text">Chọn thêm ảnh (có thể bỏ qua nếu không muốn thêm).</div>
                            <div class="d-flex flex-wrap gap-2 mt-2" id="newImagePreview"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="departure_id" class="form-label">Chuyến khởi hành <span class="text-danger">*</span></label>
                            <select class="form-select" id="departure_id" name="departure_id" required>
                                <option value="">-- Chọn chuyến khởi hành --</option>
                                @foreach($departures as $d)
                                    <option value="{{ $d['id'] }}"
                                        {{ ((int)$log['departure_id'] === (int)$d['id']) ? 'selected' : '' }}>
                                        #{{ $d['id'] }} - {{ $d['tour_name'] ?? 'Tour' }}
                                        ({{ date('d/m/Y', strtotime($d['departure_date'])) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="log_date" class="form-label">Ngày giờ sự kiện <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="log_date" name="log_date"
                                   value="{{ date('Y-m-d\TH:i', strtotime($log['log_date'])) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Địa điểm</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="{{ htmlspecialchars($log['location'] ?? '') }}"
                                       placeholder="Ví dụ: Hoàn Kiếm, Hà Nội">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="weather" class="form-label">Thời tiết</label>
                            <select class="form-select" id="weather" name="weather">
                                <option value="">-- Chọn --</option>
                                @foreach(['Nắng','Mây','Mưa','Sương mù','Gió','Nóng bức','Lạnh'] as $w)
                                    <option value="{{ $w }}" {{ ($log['weather'] ?? '') === $w ? 'selected' : '' }}>
                                        @php
                                            $icons = ['Nắng'=>'☀️','Mây'=>'☁️','Mưa'=>'🌧️','Sương mù'=>'🌫️','Gió'=>'💨','Nóng bức'=>'🔥','Lạnh'=>'❄️'];
                                        @endphp
                                        {{ $icons[$w] ?? '' }} {{ $w }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tâm trạng</label>
                            <input type="hidden" id="mood" name="mood" value="{{ $log['mood'] ?? '' }}">
                            <div class="d-flex justify-content-around p-3 bg-light rounded">
                                @php
                                    $moodMap = [
                                        'excited' => '🎉', 'happy' => '😊', 'calm' => '😌',
                                        'neutral' => '😐', 'tired' => '😴', 'sad' => '😢'
                                    ];
                                @endphp
                                @foreach($moodMap as $mood => $emoji)
                                    <span class="mood-option fs-1 {{ ($log['mood'] ?? '') === $mood ? 'selected' : '' }}"
                                          data-mood="{{ $mood }}" title="{{ $mood }}">{{ $emoji }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="published" {{ ($log['status'] ?? 'published') === 'published' ? 'selected' : '' }}>
                                    ✅ Công khai
                                </option>
                                <option value="draft" {{ ($log['status'] ?? '') === 'draft' ? 'selected' : '' }}>
                                    📝 Lưu nháp
                                </option>
                            </select>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded">
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-clock"></i> Tạo lúc:
                                {{ $log['created_at'] ? date('d/m/Y H:i', strtotime($log['created_at'])) : '-' }}
                            </small>
                            @if(!empty($log['updated_at']))
                                <small class="text-muted d-block">
                                    <i class="bi bi-pencil-square"></i> Cập nhật:
                                    {{ date('d/m/Y H:i', strtotime($log['updated_at'])) }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil-square"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin/tour-logs/show/' . $log['id']) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('admin/tour-logs') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moodOptions = document.querySelectorAll('.mood-option');
    const moodInput = document.getElementById('mood');
    moodOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            moodOptions.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            moodInput.value = this.dataset.mood;
        });
    });

    const newImagesInput = document.getElementById('newImages');
    const newPreview = document.getElementById('newImagePreview');
    newImagesInput.addEventListener('change', function() {
        newPreview.innerHTML = '';
        const files = this.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = '<img src="' + e.target.result + '">';
                newPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeExistingImage(btn, path) {
    const box = btn.closest('.existing-image');
    box.style.display = 'none';
    const hidden = document.getElementById('removedImages');
    const current = JSON.parse(hidden.value || '[]');
    current.push(path);
    hidden.value = JSON.stringify(current);
}
</script>
@endsection
