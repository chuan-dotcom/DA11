<?php

namespace App\Controllers;

use App\Controller;
use App\Models\TourLog;
use App\Models\Departure;
use Rakit\Validation\Validator;

class TourLogController extends Controller
{
    private $modelLog;
    private $modelDeparture;
    private $validator;

    public function __construct()
    {
        $this->modelLog = new TourLog();
        $this->modelDeparture = new Departure();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý nhật ký tour';
        $departureId = isset($_GET['departure_id']) ? (int) $_GET['departure_id'] : null;
        $status = $_GET['status'] ?? null;
        $departures = $this->modelDeparture->getAll();
        $logs = $this->modelLog->getAll($departureId, $status);
        $totalLogs = $this->modelLog->getTotalLogs();

        return view('tour-logs.index', compact('title', 'logs', 'departures', 'departureId', 'status', 'totalLogs'));
    }

    public function create()
    {
        $title = 'Ghi nhật ký tour mới';
        $departureId = isset($_GET['departure_id']) ? (int) $_GET['departure_id'] : null;
        $departures = $this->modelDeparture->getAll();

        return view('tour-logs.create', compact('title', 'departures', 'departureId'));
    }

    public function store()
    {
        $data = [
            'departure_id' => $_POST['departure_id'],
            'title'        => trim($_POST['title'] ?? ''),
            'content'      => trim($_POST['content'] ?? ''),
            'log_date'     => $_POST['log_date'] ?? date('Y-m-d H:i:s'),
            'location'     => trim($_POST['location'] ?? ''),
            'weather'      => trim($_POST['weather'] ?? ''),
            'mood'         => trim($_POST['mood'] ?? ''),
            'status'       => $_POST['status'] ?? 'published',
            'author_id'    => $_POST['author_id'] ?? null,
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'log_date'     => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/tour-logs/create');
        }

        if ($data['location'] === '') { $data['location'] = null; }
        if ($data['weather'] === '') { $data['weather'] = null; }
        if ($data['mood'] === '') { $data['mood'] = null; }

        $images = [];
        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $fileCount = count($_FILES['images']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK && $_FILES['images']['size'][$i] > 0) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$i],
                        'type'     => $_FILES['images']['type'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'error'    => $_FILES['images']['error'][$i],
                        'size'     => $_FILES['images']['size'][$i],
                    ];
                    try {
                        $images[] = $this->uploadFile($file, 'tour-logs');
                    } catch (\Throwable $e) {}
                }
            }
        }
        $data['images'] = !empty($images) ? json_encode($images) : null;

        $this->modelLog->insert($data);
        setFlash('success', 'Ghi nhật ký tour thành công!');
        return redirect('admin/tour-logs');
    }

    public function edit($id)
    {
        $title = 'Cập nhật nhật ký tour';
        $log = $this->modelLog->findById($id);

        if (!$log) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-logs');
        }

        $departures = $this->modelDeparture->getAll();
        return view('tour-logs.edit', compact('title', 'log', 'departures'));
    }

    public function update($id)
    {
        $log = $this->modelLog->findById($id);
        if (!$log) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-logs');
        }

        $data = [
            'departure_id' => $_POST['departure_id'],
            'title'        => trim($_POST['title'] ?? ''),
            'content'      => trim($_POST['content'] ?? ''),
            'log_date'     => $_POST['log_date'] ?? date('Y-m-d H:i:s'),
            'location'     => trim($_POST['location'] ?? ''),
            'weather'      => trim($_POST['weather'] ?? ''),
            'mood'         => trim($_POST['mood'] ?? ''),
            'status'       => $_POST['status'] ?? 'published',
            'author_id'    => $_POST['author_id'] ?? null,
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'log_date'     => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/tour-logs/edit/' . $id);
        }

        if ($data['location'] === '') { $data['location'] = null; }
        if ($data['weather'] === '') { $data['weather'] = null; }
        if ($data['mood'] === '') { $data['mood'] = null; }

        $existingImages = json_decode($log['images'] ?? '[]', true);
        if (!is_array($existingImages)) { $existingImages = []; }

        $removedImages = $_POST['removed_images'] ?? [];
        if (!is_array($removedImages)) { $removedImages = []; }
        foreach ($removedImages as $removePath) {
            if (($key = array_search($removePath, $existingImages)) !== false) {
                unset($existingImages[$key]);
                if (file_exists($removePath)) {
                    try { unlink($removePath); } catch (\Throwable $e) {}
                }
            }
        }
        $existingImages = array_values($existingImages);

        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $fileCount = count($_FILES['images']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK && $_FILES['images']['size'][$i] > 0) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$i],
                        'type'     => $_FILES['images']['type'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'error'    => $_FILES['images']['error'][$i],
                        'size'     => $_FILES['images']['size'][$i],
                    ];
                    try {
                        $existingImages[] = $this->uploadFile($file, 'tour-logs');
                    } catch (\Throwable $e) {}
                }
            }
        }
        $data['images'] = !empty($existingImages) ? json_encode($existingImages) : null;

        $this->modelLog->update($id, $data);
        setFlash('success', 'Cập nhật nhật ký tour thành công!');
        return redirect('admin/tour-logs');
    }

    public function delete($id)
    {
        $log = $this->modelLog->findById($id);
        if (!$log) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-logs');
        }

        $this->modelLog->delete($id);
        setFlash('success', 'Xóa nhật ký tour thành công!');
        return redirect('admin/tour-logs');
    }

    public function show($id)
    {
        $title = 'Chi tiết nhật ký tour';
        $log = $this->modelLog->findById($id);

        if (!$log) {
            setFlash('error', 'Nhật ký tour không tồn tại!');
            return redirect('admin/tour-logs');
        }

        $images = json_decode($log['images'] ?? '[]', true);
        if (!is_array($images)) { $images = []; }

        $relatedLogs = [];
        if (!empty($log['departure_id'])) {
            $allOfDeparture = $this->modelLog->getByDepartureId($log['departure_id']);
            foreach ($allOfDeparture as $r) {
                if ((int) $r['id'] !== (int) $id) {
                    $relatedLogs[] = $r;
                }
            }
        }

        return view('tour-logs.show', compact('title', 'log', 'images', 'relatedLogs'));
    }
}
