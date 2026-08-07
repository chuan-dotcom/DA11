<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\TourDiary;
use App\Models\Departure;
use App\Models\Staff;
use App\Models\TourLog;
use App\Support\Auth;
use Rakit\Validation\Validator;

class DiaryController extends Controller
{
    private $modelDiary;
    private $modelDeparture;
    private $modelStaff;
    private $validator;
    private $modelTourLog;

    public function __construct()
    {
        $this->modelDiary = new TourDiary();
        $this->modelDeparture = new Departure();
        $this->modelStaff = new Staff();
        $this->validator = new Validator();
        $this->modelTourLog = new TourLog();
    }

    private function getActiveHdvId()
    {
        if (Auth::hasBoundHdv()) {
            $_SESSION['hdv_id'] = (int) (Auth::user()['hdv_id'] ?? 0);
            return $_SESSION['hdv_id'];
        }
        if (isset($_GET['hdv_id']) && (int)$_GET['hdv_id'] > 0) {
            $_SESSION['hdv_id'] = (int)$_GET['hdv_id'];
        }
        if (!isset($_SESSION['hdv_id'])) {
            $allStaff = $this->modelStaff->getAll();
            $_SESSION['hdv_id'] = !empty($allStaff) ? (int)$allStaff[0]['HDV_id'] : 1;
        }
        return $_SESSION['hdv_id'];
    }

    private function getAssignedDepartures($hdvId)
    {
        $db = (new \App\Model())->getConnection();
        $sql = "
            SELECT 
                d.id, 
                d.group_name, 
                d.departure_date, 
                d.return_date, 
                d.meeting_point,
                d.meeting_time,
                d.incurred_cost,
                d.incurred_cost_note,
                t.id AS tour_id,
                t.name AS tour_name,
                tc.name AS category_name
            FROM staff_assignments sa
            INNER JOIN departures d ON d.id = sa.departure_id
            INNER JOIN tours t ON t.id = d.tour_id
            LEFT JOIN tour_categories tc ON tc.id = t.category_id
            WHERE sa.staff_id = :hdv_id
            ORDER BY d.departure_date DESC
        ";
        return $db->fetchAllAssociative($sql, ['hdv_id' => $hdvId]);
    }

    private function cleanupUploadedPhotos(array $photos): void
    {
        foreach ($photos as $photo) {
            if (is_string($photo) && $photo !== '' && file_exists($photo)) {
                unlink($photo);
            }
        }
    }

    private function findAssignedDepartureById(array $departures, int $departureId): ?array
    {
        foreach ($departures as $departure) {
            if ((int) ($departure['id'] ?? 0) === $departureId) {
                return $departure;
            }
        }

        return null;
    }

    private function validateDiaryDateForDeparture(array $departure, string $diaryDate): ?string
    {
        if ($diaryDate === '') {
            return 'Vui lòng chọn ngày ghi nhật ký.';
        }

        $diaryTs = strtotime($diaryDate);
        if ($diaryTs === false) {
            return 'Ngày ghi nhật ký không hợp lệ.';
        }

        if (!empty($departure['departure_date'])) {
            $departTs = strtotime($departure['departure_date']);
            if ($departTs !== false && $diaryTs < $departTs) {
                return 'Ngày nhật ký không thể sớm hơn ngày khởi hành (' . date('d/m/Y', $departTs) . ').';
            }
        }

        if (!empty($departure['return_date'])) {
            $returnTs = strtotime($departure['return_date']);
            if ($returnTs !== false && $diaryTs > $returnTs) {
                return 'Ngày nhật ký không thể muộn hơn ngày kết thúc tour (' . date('d/m/Y', $returnTs) . ').';
            }
        }

        return null;
    }

    private function getTimelineLogsForDepartures(array $departures): array
    {
        $logs = [];
        foreach ($departures as $departure) {
            foreach ($this->modelTourLog->getByDepartureId((int) $departure['id']) as $log) {
                $logs[] = $log;
            }
        }
        return $logs;
    }

    private function validateTimelineLog(int $tourLogId, int $departureId): ?string
    {
        if ($tourLogId <= 0) {
            return null;
        }

        $log = $this->modelTourLog->findById($tourLogId);
        if (!$log || (int) ($log['departure_id'] ?? 0) !== $departureId) {
            return 'Mốc hoạt động được chọn không thuộc chuyến tour này.';
        }

        return null;
    }

    public function index()
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $departures = $this->getAssignedDepartures($hdvId);
        $departureIds = array_column($departures, 'id');

        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        $db = (new \App\Model())->getConnection();

        $allDiaries = [];
        if (!empty($departureIds)) {
            $sql = "
                SELECT 
                    td.*,
                    d.group_name as departure_group_name,
                    d.departure_date as tour_departure_date,
                    d.return_date as tour_return_date,
                    t.id as tour_id,
                    t.name as tour_name,
                    tc.name as category_name
                FROM tour_diaries td
                INNER JOIN departures d ON d.id = td.departure_id
                INNER JOIN tours t ON t.id = d.tour_id
                LEFT JOIN tour_categories tc ON tc.id = t.category_id
                WHERE td.departure_id IN (" . implode(',', array_map('intval', $departureIds)) . ")
            ";
            if ($selectedDepartureId) {
                $sql .= " AND td.departure_id = " . (int) $selectedDepartureId;
            }
            $sql .= " ORDER BY td.diary_date DESC, td.id DESC";

            $allDiaries = $db->fetchAllAssociative($sql);
        }

        // Group diaries into Main Departure Journals
        $groupedJournals = [];
        $filteredDepartures = $selectedDepartureId
            ? array_filter($departures, fn($d) => (int)$d['id'] === $selectedDepartureId)
            : $departures;

        foreach ($filteredDepartures as $dep) {
            $depId = (int) $dep['id'];
            $childDiaries = array_values(array_filter($allDiaries, fn($item) => (int)$item['departure_id'] === $depId));
            $estimatedCost = $this->modelDeparture->getEstimatedCostForDeparture($depId);
            $actualCostSum = array_sum(array_map('floatval', array_column($childDiaries, 'actual_cost')));
            $diariesExpenseSum = array_sum(array_map('floatval', array_column($childDiaries, 'expense_amount')));
            $actualCost = $actualCostSum;
            $incurredCost = $diariesExpenseSum > 0 ? $diariesExpenseSum : (float) ($dep['incurred_cost'] ?? 0);
            $incurredCostNote = $dep['incurred_cost_note'] ?? '';

            $groupedJournals[] = [
                'departure'          => $dep,
                'departure_id'       => $depId,
                'tour_name'          => $dep['tour_name'] ?? '—',
                'group_name'         => $dep['group_name'] ?: ('Chuyến #' . $depId),
                'category_name'      => $dep['category_name'] ?? 'Chưa phân loại',
                'departure_date'     => $dep['departure_date'] ?? null,
                'return_date'        => $dep['return_date'] ?? null,
                'estimated_cost'     => $estimatedCost,
                'actual_cost'        => $actualCost,
                'incurred_cost'      => $incurredCost,
                'incurred_cost_note' => $incurredCostNote,
                'diaries'            => $childDiaries,
                'total_child_diaries'=> count($childDiaries),
            ];
        }

        $totalDiaries = count($allDiaries);
        $totalJournals = count($groupedJournals);
        $title = 'Nhật ký tour';

        return view('hdv.diaries.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'allDiaries',
            'groupedJournals',
            'departures',
            'selectedDepartureId',
            'totalDiaries',
            'totalJournals'
        ));
    }

    public function updateCost()
    {
        $hdvId = $this->getActiveHdvId();
        $departureId = (int) ($_POST['departure_id'] ?? 0);
        $incurredCost = (float) ($_POST['incurred_cost'] ?? 0);
        $incurredCostNote = trim($_POST['incurred_cost_note'] ?? '');

        $departures = $this->getAssignedDepartures($hdvId);
        $selectedDeparture = $this->findAssignedDepartureById($departures, $departureId);

        if (!$selectedDeparture) {
            setFlash('error', 'Bạn chỉ có quyền cập nhật chi phí cho chuyến công tác được phân công.');
            return redirect('hdv/nhat-ky-tour');
        }

        $this->modelDeparture->updateIncurredCost($departureId, $incurredCost, $incurredCostNote);
        setFlash('success', 'Đã cập nhật chi phí phát sinh cho chuyến đi #' . $departureId . ' thành công!');
        return redirect('hdv/nhat-ky-tour' . ($departureId > 0 ? '?departure_id=' . $departureId : ''));
    }

    public function create()
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $departures = $this->getAssignedDepartures($hdvId);
        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;
        if ($selectedDepartureId <= 0 && !empty($_SESSION['old_input']['departure_id'])) {
            $selectedDepartureId = (int) $_SESSION['old_input']['departure_id'];
        }
        $selectedDeparture = $selectedDepartureId > 0
            ? $this->findAssignedDepartureById($departures, $selectedDepartureId)
            : null;
        $timelineLogs = $this->getTimelineLogsForDepartures($departures);

        $title = 'Viết nhật ký tour mới';

        return view('hdv.diaries.create', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'departures',
            'selectedDepartureId',
            'selectedDeparture',
            'timelineLogs'
        ));
    }

    public function store()
    {
        $hdvId = $this->getActiveHdvId();
        $departures = $this->getAssignedDepartures($hdvId);
        $photos = [];
        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
            foreach ($_FILES['photos']['name'] as $index => $fileName) {
                if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK && $_FILES['photos']['size'][$index] > 0) {
                    $file = [
                        'name'     => $fileName,
                        'type'     => $_FILES['photos']['type'][$index],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$index],
                        'error'    => $_FILES['photos']['error'][$index],
                        'size'     => $_FILES['photos']['size'][$index],
                    ];
                    try {
                        $photos[] = $this->uploadFile($file, 'tour-diaries');
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        $data = [
            'departure_id' => $_POST['departure_id'],
            'tour_log_id'  => (int) ($_POST['tour_log_id'] ?? 0),
            'title'        => trim($_POST['title']),
            'content'      => trim($_POST['content']),
            'diary_date'   => $_POST['diary_date'],
            'weather'      => trim($_POST['weather'] ?? ''),
            'mood'         => trim($_POST['mood'] ?? ''),
            'photos'       => $photos,
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'diary_date'   => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            $this->cleanupUploadedPhotos($photos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', reset($errors));
            $redirect = 'hdv/nhat-ky-tour/create';
            if (!empty($data['departure_id'])) {
                $redirect .= '?departure_id=' . (int) $data['departure_id'];
            }
            return redirect($redirect);
        }

        $selectedDeparture = $this->findAssignedDepartureById($departures, (int) $data['departure_id']);
        if (!$selectedDeparture) {
            $this->cleanupUploadedPhotos($photos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', 'Bạn chỉ có thể viết nhật ký cho chuyến được phân công.');
            return redirect('hdv/nhat-ky-tour/create');
        }

        $timelineError = $this->validateTimelineLog((int) $data['tour_log_id'], (int) $data['departure_id']);
        if ($timelineError) {
            $this->cleanupUploadedPhotos($photos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', $timelineError);
            return redirect('hdv/nhat-ky-tour/create?departure_id=' . (int) $data['departure_id']);
        }

        $dateError = $this->validateDiaryDateForDeparture($selectedDeparture, (string) $data['diary_date']);
        if ($dateError) {
            $this->cleanupUploadedPhotos($photos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', $dateError);
            return redirect('hdv/nhat-ky-tour/create?departure_id=' . (int) $data['departure_id']);
        }

        $data['created_by_hdv_id'] = $hdvId;

        $this->modelDiary->insert($data);
        unset($_SESSION['old_input']);
        setFlash('success', 'Đã thêm nhật ký tour thành công!');
        return redirect('hdv/nhat-ky-tour?departure_id=' . (int) $data['departure_id']);
    }

    public function show($id)
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $diary = $this->modelDiary->findById($id);
        $departures = $this->getAssignedDepartures($hdvId);
        $assignedDepartureIds = array_map('intval', array_column($departures, 'id'));

        if (!$diary || !in_array((int) ($diary['departure_id'] ?? 0), $assignedDepartureIds, true)) {
            setFlash('error', 'Nhật ký không tồn tại!');
            return redirect('hdv/nhat-ky-tour');
        }

        $photos = !empty($diary['photos']) ? explode(',', $diary['photos']) : [];
        $title = 'Chi tiết nhật ký tour';

        return view('hdv.diaries.show', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'diary',
            'photos'
        ));
    }

    public function edit($id)
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $diary = $this->modelDiary->findById($id);
        $departures = $this->getAssignedDepartures($hdvId);
        $assignedDepartureIds = array_map('intval', array_column($departures, 'id'));

        if (!$diary || !in_array((int) ($diary['departure_id'] ?? 0), $assignedDepartureIds, true)) {
            setFlash('error', 'Nhật ký không tồn tại!');
            return redirect('hdv/nhat-ky-tour');
        }

        $photos = !empty($diary['photos']) ? explode(',', $diary['photos']) : [];
        $timelineLogs = $this->getTimelineLogsForDepartures($departures);
        $title = 'Chỉnh sửa nhật ký tour';

        return view('hdv.diaries.edit', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'diary',
            'departures',
            'photos',
            'timelineLogs'
        ));
    }

    public function update($id)
    {
        $hdvId = $this->getActiveHdvId();
        $departures = $this->getAssignedDepartures($hdvId);
        $diary = $this->modelDiary->findById($id);
        $assignedDepartureIds = array_map('intval', array_column($departures, 'id'));

        if (!$diary || !in_array((int) ($diary['departure_id'] ?? 0), $assignedDepartureIds, true)) {
            setFlash('error', 'Nhật ký không tồn tại!');
            return redirect('hdv/nhat-ky-tour');
        }

        $newPhotos = [];
        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
            foreach ($_FILES['photos']['name'] as $index => $fileName) {
                if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK && $_FILES['photos']['size'][$index] > 0) {
                    $file = [
                        'name'     => $fileName,
                        'type'     => $_FILES['photos']['type'][$index],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$index],
                        'error'    => $_FILES['photos']['error'][$index],
                        'size'     => $_FILES['photos']['size'][$index],
                    ];
                    try {
                        $newPhotos[] = $this->uploadFile($file, 'tour-diaries');
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        $data = [
            'departure_id' => $_POST['departure_id'],
            'tour_log_id'  => (int) ($_POST['tour_log_id'] ?? 0),
            'title'        => trim($_POST['title']),
            'content'      => trim($_POST['content']),
            'diary_date'   => $_POST['diary_date'],
            'weather'      => trim($_POST['weather'] ?? ''),
            'mood'         => trim($_POST['mood'] ?? ''),
            'photos'       => $newPhotos,
            'delete_photos'=> $_POST['delete_photos'] ?? [],
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'diary_date'   => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            $this->cleanupUploadedPhotos($newPhotos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', reset($errors));
            return redirect('hdv/nhat-ky-tour/edit/' . $id);
        }

        $selectedDeparture = $this->findAssignedDepartureById($departures, (int) $data['departure_id']);
        if (!$selectedDeparture) {
            $this->cleanupUploadedPhotos($newPhotos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', 'Bạn chỉ có thể gắn nhật ký vào chuyến được phân công.');
            return redirect('hdv/nhat-ky-tour/edit/' . $id);
        }

        $timelineError = $this->validateTimelineLog((int) $data['tour_log_id'], (int) $data['departure_id']);
        if ($timelineError) {
            $this->cleanupUploadedPhotos($newPhotos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', $timelineError);
            return redirect('hdv/nhat-ky-tour/edit/' . $id);
        }

        $dateError = $this->validateDiaryDateForDeparture($selectedDeparture, (string) $data['diary_date']);
        if ($dateError) {
            $this->cleanupUploadedPhotos($newPhotos);
            $_SESSION['old_input'] = $_POST;
            setFlash('error', $dateError);
            return redirect('hdv/nhat-ky-tour/edit/' . $id);
        }

        $this->modelDiary->update($id, $data);
        unset($_SESSION['old_input']);
        setFlash('success', 'Đã cập nhật nhật ký tour!');
        return redirect('hdv/nhat-ky-tour/show/' . $id);
    }

    public function delete($id)
    {
        $hdvId = $this->getActiveHdvId();
        $diary = $this->modelDiary->findById($id);
        $departures = $this->getAssignedDepartures($hdvId);
        $assignedDepartureIds = array_map('intval', array_column($departures, 'id'));

        if ($diary && in_array((int) ($diary['departure_id'] ?? 0), $assignedDepartureIds, true)) {
            $departureId = (int) ($diary['departure_id'] ?? 0);
            $this->modelDiary->delete($id);
            setFlash('success', 'Đã xóa nhật ký tour thành công!');
            return redirect('hdv/nhat-ky-tour?departure_id=' . $departureId);
        }

        setFlash('error', 'Nhật ký không tồn tại hoặc bạn không có quyền thao tác.');
        return redirect('hdv/nhat-ky-tour');
    }
}
