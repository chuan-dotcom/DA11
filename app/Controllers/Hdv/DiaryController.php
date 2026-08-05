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
        if (Auth::isHdv()) {
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
                t.name AS tour_name
            FROM staff_assignments sa
            INNER JOIN departures d ON d.id = sa.departure_id
            INNER JOIN tours t ON t.id = d.tour_id
            WHERE sa.staff_id = :hdv_id
            ORDER BY d.departure_date DESC
        ";
        return $db->fetchAllAssociative($sql, ['hdv_id' => $hdvId]);
    }

    public function index()
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::isAdmin() ? $this->modelStaff->getAll() : [$activeHdv];

        $departures = $this->getAssignedDepartures($hdvId);
        $departureIds = array_column($departures, 'id');

        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        $db = (new \App\Model())->getConnection();

        $diaries = [];
        if (!empty($departureIds)) {
            $qb = $db->createQueryBuilder();
            $qb->select('td.*', 'd.group_name as departure_group_name', 't.name as tour_name')
                ->from('tour_diaries', 'td')
                ->innerJoin('td', 'departures', 'd', 'd.id = td.departure_id')
                ->innerJoin('d', 'tours', 't', 't.id = d.tour_id')
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
        $allHdv = Auth::isAdmin() ? $this->modelStaff->getAll() : [$activeHdv];

        $departures = $this->getAssignedDepartures($hdvId);
        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        $title = 'Viết nhật ký tour mới';

        return view('hdv.diaries.create', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'departures',
            'selectedDepartureId'
        ));
    }

    public function store()
    {
        $hdvId = $this->getActiveHdvId();
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
            $_SESSION['old_input'] = $_POST;
            setFlash('error', reset($errors));
            return redirect('hdv/nhat-ky-tour/create');
        }

        $this->modelDiary->insert($data);
        setFlash('success', 'Đã thêm nhật ký tour thành công!');
        return redirect('hdv/nhat-ky-tour');
    }

    public function show($id)
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::isAdmin() ? $this->modelStaff->getAll() : [$activeHdv];

        $diary = $this->modelDiary->findById($id);

        if (!$diary) {
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
        $allHdv = Auth::isAdmin() ? $this->modelStaff->getAll() : [$activeHdv];

        $diary = $this->modelDiary->findById($id);
        if (!$diary) {
            setFlash('error', 'Nhật ký không tồn tại!');
            return redirect('hdv/nhat-ky-tour');
        }

        $departures = $this->getAssignedDepartures($hdvId);
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
        $diary = $this->modelDiary->findById($id);
        if (!$diary) {
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

        $this->modelDiary->update($id, $data);
        setFlash('success', 'Đã cập nhật nhật ký tour!');
        return redirect('hdv/nhat-ky-tour');
    }

    public function delete($id)
    {
        $diary = $this->modelDiary->findById($id);
        if ($diary) {
            $this->modelDiary->delete($id);
            setFlash('success', 'Đã xóa nhật ký tour thành công!');
        }
        return redirect('hdv/nhat-ky-tour');
    }
}
