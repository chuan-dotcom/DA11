<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Service;
use App\Models\Tour;
use Rakit\Validation\Validator;

class ServiceController extends Controller
{
    private $modelService;
    private $modelTour;
    private $validator;

    public function __construct()
    {
        $this->modelService = new Service();
        $this->modelTour = new Tour();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý dịch vụ đoàn';

        $tourId = $_GET['tour_id'] ?? null;
        $serviceTypes = $_GET['service_types'] ?? null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

        if ($tourId || $serviceTypes || $status !== null) {
            $services = $this->modelService->filter($tourId, $serviceTypes, $status);
        } else {
            $services = $this->modelService->getAll();
        }

        $tours = $this->modelTour->getAll();

        return view(
            'services.index',
            compact('title', 'services', 'tours', 'tourId', 'serviceTypes', 'status')
        );
    }

    public function create()
    {
        $title = 'Đặt dịch vụ';

        $tours = $this->modelTour->getAll();

        return view(
            'services.create',
            compact('title', 'tours')
        );
    }

    public function store()
    {
        $serviceTypes = isset($_POST['service_types']) && is_array($_POST['service_types'])
            ? implode(', ', $_POST['service_types'])
            : ($_POST['service_types_text'] ?? '');

        $data = [
            'tour_id'       => $_POST['tour_id'],
            'service_types' => $serviceTypes,
            'supplier'      => $_POST['supplier'],
            'quantity'      => $_POST['quantity'],
            'status'        => $_POST['status'],
            'start_time'    => $_POST['start_time'],
            'end_time'      => $_POST['end_time'],
            'note'          => $_POST['note']
        ];

        $rules = [
            'tour_id'       => 'required|integer',
            'service_types' => 'required|max:255',
            'supplier'      => 'required|max:255',
            'quantity'      => 'required|integer',
            'status'        => 'required|integer'
        ];

        $errors = $this->validate(
            $this->validator,
            $data,
            $rules
        );

        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/services/create');
        }

        $tour = $this->modelService->getTour($data['tour_id']);
        if (!$tour) {
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/services/create');
        }

        $this->modelService->insert($data);

        setFlash(
            'success',
            'Thêm dịch vụ đoàn thành công!'
        );

        return redirect('admin/services');
    }

    public function edit($id)
    {
        $title = 'Cập nhật dịch vụ đoàn';

        $service = $this->modelService->findById($id);

        if (!$service) {
            setFlash(
                'error',
                'Dịch vụ đoàn không tồn tại'
            );
            return redirect('admin/services');
        }

        $tours = $this->modelTour->getAll();

        $selectedTypes = array_map('trim', explode(',', $service['service_types']));

        return view(
            'services.edit',
            compact(
                'title',
                'service',
                'tours',
                'selectedTypes'
            )
        );
    }

    public function update($id)
    {
        $service = $this->modelService->findById($id);

        if (!$service) {
            setFlash(
                'error',
                'Dịch vụ đoàn không tồn tại'
            );
            return redirect('admin/services');
        }

        $serviceTypes = isset($_POST['service_types']) && is_array($_POST['service_types'])
            ? implode(', ', $_POST['service_types'])
            : ($_POST['service_types_text'] ?? '');

        $data = [
            'tour_id'       => $_POST['tour_id'],
            'service_types' => $serviceTypes,
            'supplier'      => $_POST['supplier'],
            'quantity'      => $_POST['quantity'],
            'status'        => $_POST['status'],
            'start_time'    => $_POST['start_time'],
            'end_time'      => $_POST['end_time'],
            'note'          => $_POST['note']
        ];

        $rules = [
            'tour_id'       => 'required|integer',
            'service_types' => 'required|max:255',
            'supplier'      => 'required|max:255',
            'quantity'      => 'required|integer',
            'status'        => 'required|integer'
        ];

        $errors = $this->validate(
            $this->validator,
            $data,
            $rules
        );

        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/services/edit/' . $id);
        }

        $tour = $this->modelService->getTour($data['tour_id']);
        if (!$tour) {
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/services/edit/' . $id);
        }

        $this->modelService->update($id, $data);

        setFlash(
            'success',
            'Cập nhật dịch vụ đoàn thành công!'
        );

        return redirect('admin/services');
    }

    public function delete($id)
    {
        $service = $this->modelService->findById($id);

        if (!$service) {
            setFlash(
                'error',
                'Dịch vụ đoàn không tồn tại'
            );
            return redirect('admin/services');
        }

        $this->modelService->delete($id);

        setFlash(
            'success',
            'Xóa dịch vụ đoàn thành công!'
        );

        return redirect('admin/services');
    }

    public function show($id)
    {
        $title = 'Chi tiết dịch vụ đoàn';

        $service = $this->modelService->findById($id);

        if (!$service) {
            setFlash(
                'error',
                'Dịch vụ đoàn không tồn tại'
            );
            return redirect('admin/services');
        }

        return view(
            'services.show',
            compact(
                'title',
                'service'
            )
        );
    }
}
