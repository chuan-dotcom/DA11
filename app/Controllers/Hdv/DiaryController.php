<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\TourDiary;
use App\Models\Departure;
use App\Models\Staff;
use App\Support\Auth;
use Rakit\Validation\Validator;

class DiaryController extends Controller
{
    private $modelDiary;
    private $modelDeparture;
    private $modelStaff;
    private $validator;

    public function __construct()
    {
        $this->modelDiary = new TourDiary();
        $this->modelDeparture = new Departure();
        $this->modelStaff = new Staff();
        $this->validator = new Validator();
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

    public function index()
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $departures = $this->getAssignedDepartures($hdvId);
        $departureIds = array_column($departures, 'id');

        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        $db = (new \App\Model())->getConnection();

        $diaries = [];
        if (!empty($departureIds)) {
            $qb = $db->createQueryBuilder();
            $qb->select(
                'td.*',
                'd.group_name as departure_group_name',
                'd.departure_date as tour_departure_date',
                'd.return_date as tour_return_date',
                't.id as tour_id',
                't.name as tour_name',
                'tc.name as category_name'
            )
                ->from('tour_diaries', 'td')
                ->innerJoin('td', 'departures', 'd', 'd.id = td.departure_id')
                ->innerJoin('d', 'tours', 't', 't.id = d.tour_id')
                ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
                ->where('td.departure_id IN (:departure_ids)')
                ->setParameter('departure_ids', $departureIds, \Doctrine\DBAL\ArrayParameterType::INTEGER);

            if ($selectedDepartureId) {
                $qb->andWhere('td.departure_id = :selected_dep')
                    ->setParameter('selected_dep', $selectedDepartureId);
            }

            $qb->orderBy('td.diary_date', 'DESC')
                ->addOrderBy('td.id', 'DESC');

            $diaries = $qb->fetchAllAssociative();
        }

        $totalDiaries = count($diaries);
        $title = 'Nhật ký tour';

        return view('hdv.diaries.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'diaries',
            'departures',
            'selectedDepartureId',
            'totalDiaries'
        ));
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

        $title = 'Viết nhật ký tour mới';

        return view('hdv.diaries.create', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'departures',
            'selectedDepartureId',
            'selectedDeparture'
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
        $title = 'Chỉnh sửa nhật ký tour';

        return view('hdv.diaries.edit', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'diary',
            'departures',
            'photos'
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
