<?php

namespace App\Controllers;

use App\Controller;
use App\Models\TourDiary;
use App\Models\Departure;
use Rakit\Validation\Validator;

class TourDiaryController extends Controller
{
    private $modelDiary;
    private $modelDeparture;
    private $validator;

    public function __construct()
    {
        $this->modelDiary = new TourDiary();
        $this->modelDeparture = new Departure();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý nhật ký tour';
        $departureId = isset($_GET['departure_id']) ? (int) $_GET['departure_id'] : null;
        $departures = $this->modelDeparture->getAll();
        $diaries = $this->modelDiary->getAll($departureId);
        $totalDiaries = $this->modelDiary->getTotalDiaries();
        if (isset($_SESSION['old_input'])) {
            unset($_SESSION['old_input']);
        }

        return view('tour-diaries.index', compact(
            'title',
            'diaries',
            'departures',
            'departureId',
            'totalDiaries'
        ));
    }

    public function create()
    {
        $title = 'Thêm nhật ký tour mới';
        $departures = $this->modelDeparture->getAll();
        $departureId = isset($_GET['departure_id']) ? (int) $_GET['departure_id'] : null;

        return view('tour-diaries.create', compact(
            'title',
            'departures',
            'departureId'
        ));
    }

    public function store()
    {
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
            foreach ($photos as $p) {
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $_SESSION['old_input'] = $_POST;
            setFlash('error', reset($errors));
            return redirect('admin/tour-diaries/create');
        }

        $departure = $this->modelDeparture->findById((int) $data['departure_id']);
        if (!$departure) {
            foreach ($photos as $p) {
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $_SESSION['old_input'] = $_POST;
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/tour-diaries/create');
        }

        if (!empty($departure['departure_date']) && !empty($departure['return_date'])) {
            $diaryTs = strtotime($data['diary_date']);
            $departTs = strtotime($departure['departure_date']);
            $returnTs = strtotime($departure['return_date']);
            if ($diaryTs < $departTs || $diaryTs > $returnTs) {
                foreach ($photos as $p) {
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
                $_SESSION['old_input'] = $_POST;
                setFlash('error', 'Ngày nhật ký phải nằm trong khoảng từ ngày khởi hành đến ngày trở về (' . date('d/m/Y', $departTs) . ' - ' . date('d/m/Y', $returnTs) . ').');
                return redirect('admin/tour-diaries/create');
            }
        } elseif (!empty($departure['departure_date'])) {
            $diaryTs = strtotime($data['diary_date']);
            $departTs = strtotime($departure['departure_date']);
            if ($diaryTs < $departTs) {
                foreach ($photos as $p) {
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
                $_SESSION['old_input'] = $_POST;
                setFlash('error', 'Ngày nhật ký không thể sớm hơn ngày khởi hành (' . date('d/m/Y', $departTs) . ').');
                return redirect('admin/tour-diaries/create');
            }
        }

        $this->modelDiary->insert($data);
        if (isset($_SESSION['old_input'])) {
            unset($_SESSION['old_input']);
        }
        setFlash('success', 'Thêm nhật ký tour thành công!');
        return redirect('admin/tour-diaries');
    }

    public function edit($id)
    {
        $title = 'Cập nhật nhật ký tour';
        $diary = $this->modelDiary->findById($id);
        $departures = $this->modelDeparture->getAll();

        if (!$diary) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-diaries');
        }

        $photos = [];
        if (!empty($diary['photos'])) {
            $photos = explode(',', $diary['photos']);
        }

        return view('tour-diaries.edit', compact(
            'title',
            'diary',
            'departures',
            'photos'
        ));
    }

    public function update($id)
    {
        $diary = $this->modelDiary->findById($id);
        if (!$diary) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-diaries');
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
            foreach ($newPhotos as $p) {
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $_SESSION['old_input'] = $_POST;
            setFlash('error', reset($errors));
            return redirect('admin/tour-diaries/edit/' . $id);
        }

        $departure = $this->modelDeparture->findById((int) $data['departure_id']);
        if (!$departure) {
            foreach ($newPhotos as $p) {
                if (file_exists($p)) {
                    unlink($p);
                }
            }
            $_SESSION['old_input'] = $_POST;
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/tour-diaries/edit/' . $id);
        }

        if (!empty($departure['departure_date']) && !empty($departure['return_date'])) {
            $diaryTs = strtotime($data['diary_date']);
            $departTs = strtotime($departure['departure_date']);
            $returnTs = strtotime($departure['return_date']);
            if ($diaryTs < $departTs || $diaryTs > $returnTs) {
                foreach ($newPhotos as $p) {
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
                $_SESSION['old_input'] = $_POST;
                setFlash('error', 'Ngày nhật ký phải nằm trong khoảng từ ngày khởi hành đến ngày trở về (' . date('d/m/Y', $departTs) . ' - ' . date('d/m/Y', $returnTs) . ').');
                return redirect('admin/tour-diaries/edit/' . $id);
            }
        } elseif (!empty($departure['departure_date'])) {
            $diaryTs = strtotime($data['diary_date']);
            $departTs = strtotime($departure['departure_date']);
            if ($diaryTs < $departTs) {
                foreach ($newPhotos as $p) {
                    if (file_exists($p)) {
                        unlink($p);
                    }
                }
                $_SESSION['old_input'] = $_POST;
                setFlash('error', 'Ngày nhật ký không thể sớm hơn ngày khởi hành (' . date('d/m/Y', $departTs) . ').');
                return redirect('admin/tour-diaries/edit/' . $id);
            }
        }

        $this->modelDiary->update($id, $data);
        if (isset($_SESSION['old_input'])) {
            unset($_SESSION['old_input']);
        }
        setFlash('success', 'Cập nhật nhật ký tour thành công!');
        return redirect('admin/tour-diaries');
    }

    public function delete($id)
    {
        $diary = $this->modelDiary->findById($id);
        if (!$diary) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-diaries');
        }

        $this->modelDiary->delete($id);
        setFlash('success', 'Xóa nhật ký tour thành công!');
        return redirect('admin/tour-diaries');
    }

    public function show($id)
    {
        $title = 'Chi tiết nhật ký tour';
        $diary = $this->modelDiary->findById($id);

        if (!$diary) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-diaries');
        }

        $photos = [];
        if (!empty($diary['photos'])) {
            $photos = explode(',', $diary['photos']);
        }

        $otherDiaries = $this->modelDiary->getByDepartureId($diary['departure_id']);

        return view('tour-diaries.show', compact(
            'title',
            'diary',
            'photos',
            'otherDiaries'
        ));
    }
}
