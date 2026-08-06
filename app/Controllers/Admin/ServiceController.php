<?php

namespace App\Controllers\Admin;
  
use App\Controller;
use App\Models\Service;
use App\Models\Tour;
use App\Models\Departure;
use Rakit\Validation\Validator;

class ServiceController extends Controller
{
    private $modelService;
    private $modelTour;
    private $modelDeparture;
    private $validator;

    public function __construct()
    {
        $this->modelService = new Service();
        $this->modelTour = new Tour();
        $this->modelDeparture = new Departure();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý dịch vụ đoàn';

        $tourId = $_GET['tour_id'] ?? null;
        $departureId = $_GET['departure_id'] ?? null;
        $serviceTypes = $_GET['service_types'] ?? null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

        if ($tourId || $departureId || $serviceTypes || $status !== null) {
            $services = $this->modelService->filter($tourId, $departureId, $serviceTypes, $status);
        } else {
            $services = $this->modelService->getAll();
        }

        $tours = $this->modelTour->getAll();
        $departures = $this->modelDeparture->getAll();

        return view(
            'admin.services.index',
            compact('title', 'services', 'tours', 'departures', 'tourId', 'departureId', 'serviceTypes', 'status')
        );
    }

    public function create()
    {
        $title = 'Đặt dịch vụ';

        $tours = $this->modelTour->getAll();
        $departures = $this->modelDeparture->getAll();

        $preTourId      = $_GET['tour_id']      ?? null;
        $preDepartureId = $_GET['departure_id'] ?? null;
        $preQty         = $_GET['quantity']     ?? null;

        return view(
            'admin.services.create',
            compact('title', 'tours', 'departures', 'preTourId', 'preDepartureId', 'preQty')
        );
    }

    public function store()
    {
        $serviceTypes = isset($_POST['service_types']) && is_array($_POST['service_types'])
            ? implode(', ', $_POST['service_types'])
            : ($_POST['service_types_text'] ?? '');

        $departureId = !empty($_POST['departure_id']) ? (int)$_POST['departure_id'] : null;

        $startTime = $_POST['start_time'] ?? null;
        $endTime   = $_POST['end_time']   ?? null;

        if ($departureId && (empty($startTime) || empty($endTime))) {
            $dep = $this->modelDeparture->findById($departureId);
            if ($dep) {
                if (!empty($dep['departure_date']) && empty($startTime)) {
                    $startTime = date('Y-m-d H:i:s', strtotime($dep['departure_date'] . ' 06:00:00'));
                }
                if (!empty($dep['return_date']) && empty($endTime)) {
                    $endTime = date('Y-m-d H:i:s', strtotime($dep['return_date'] . ' 20:00:00'));
                }
            }
        }

        $data = [
            'tour_id'       => $_POST['tour_id'],
            'departure_id'  => $departureId,
            'service_types' => $serviceTypes,
            'supplier'      => $_POST['supplier'],
            'quantity'      => $_POST['quantity'],
            'status'        => $_POST['status'],
            'start_time'    => $startTime,
            'end_time'      => $endTime,
            'note'          => $_POST['note'] ?? ''
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

        if ($departureId) {
            $dep = $this->modelDeparture->findById($departureId);
            if (!$dep) {
                setFlash('error', 'Chuyến khởi hành được chọn không tồn tại');
                return redirect('admin/services/create');
            }
            if ((int)$dep['tour_id'] !== (int)$data['tour_id']) {
                setFlash('error', 'Chuyến khởi hành không thuộc về tour đã chọn');
                return redirect('admin/services/create');
            }
        }

        $this->modelService->insert($data);

        setFlash(
            'success',
            'Thêm dịch vụ đoàn thành công!' . ($departureId ? ' (Đã gắn vào chuyến khởi hành)' : '')
        );

        if ($departureId) {
            return redirect('admin/services?departure_id=' . $departureId);
        }
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
        $departures = $this->modelDeparture->getAll();

        $selectedTypes = array_map('trim', explode(',', $service['service_types']));

        return view(
            'admin.services.edit',
            compact(
                'title',
                'service',
                'tours',
                'departures',
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

        $departureId = !empty($_POST['departure_id']) ? (int)$_POST['departure_id'] : null;

        $data = [
            'tour_id'       => $_POST['tour_id'],
            'departure_id'  => $departureId,
            'service_types' => $serviceTypes,
            'supplier'      => $_POST['supplier'],
            'quantity'      => $_POST['quantity'],
            'status'        => $_POST['status'],
            'start_time'    => $_POST['start_time'] ?: null,
            'end_time'      => $_POST['end_time'] ?: null,
            'note'          => $_POST['note'] ?? ''
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

        if ($departureId) {
            $dep = $this->modelDeparture->findById($departureId);
            if (!$dep) {
                setFlash('error', 'Chuyến khởi hành được chọn không tồn tại');
                return redirect('admin/services/edit/' . $id);
            }
            if ((int)$dep['tour_id'] !== (int)$data['tour_id']) {
                setFlash('error', 'Chuyến khởi hành không thuộc về tour đã chọn');
                return redirect('admin/services/edit/' . $id);
            }
        }

        $this->modelService->update($id, $data);

        setFlash(
            'success',
            'Cập nhật dịch vụ đoàn thành công!' . ($departureId ? ' (Đã gắn vào chuyến khởi hành)' : '')
        );

        if ($departureId) {
            return redirect('admin/services?departure_id=' . $departureId);
        }
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

        $departureId = !empty($service['departure_id']) ? (int)$service['departure_id'] : null;

        $this->modelService->delete($id);

        setFlash(
            'success',
            'Xóa dịch vụ đoàn thành công!'
        );

        if ($departureId) {
            return redirect('admin/services?departure_id=' . $departureId);
        }
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
            'admin.services.show',
            compact(
                'title',
                'service'
            )
        );
    }
}
