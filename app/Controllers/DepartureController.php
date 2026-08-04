<?php

namespace App\Controllers;

use App\Controller; 
use App\Models\Departure;
use App\Models\Tour;
use App\Models\StaffAssignment;
use Rakit\Validation\Validator;

class DepartureController extends Controller
{
    private $modelDeparture;
    private $modelTour;
    private $modelAssignment;
    private $validator;

    public function __construct()
    {
        $this->modelDeparture = new Departure();
        $this->modelTour = new Tour();
        $this->modelAssignment = new StaffAssignment();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý khởi hành';
        $departures = $this->modelDeparture->getAll();
        $statusCounts = $this->modelDeparture->getDeparturesByStatus();
        return view('departures.index', compact('title', 'departures', 'statusCounts'));
    }

    public function create()
    {
        $title = 'Thêm chuyến khởi hành mới';
        $tours = $this->modelTour->getAll();
        return view('departures.create', compact('title', 'tours'));
    }

    public function store()
    {
        $data = [
            'tour_id'         => $_POST['tour_id'],
            'departure_date'  => $_POST['departure_date'],
            'return_date'     => $_POST['return_date'] ?? null,
            'max_participants'=> $_POST['max_participants'] ?? 0,
            'meeting_point'   => $_POST['meeting_point'] ?? null,
            'meeting_time'    => $_POST['meeting_time'] ?? null,
            'vehicle'         => $_POST['vehicle'] ?? null,
            'notes'           => $_POST['notes'] ?? null,
            'status'          => $_POST['status'] ?? 'scheduled',
        ];

        $rules = [
            'tour_id'        => 'required|integer',
            'departure_date' => 'required',
            'max_participants' => 'integer',
            'status'         => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/departures/create');
        }

        if (!empty($data['return_date']) && strtotime($data['return_date']) < strtotime($data['departure_date'])) {
            setFlash('error', 'Ngày trở về không thể sớm hơn ngày khởi hành!');
            return redirect('admin/departures/create');
        }

        $this->modelDeparture->insert($data);
        setFlash('success', 'Thêm chuyến khởi hành thành công!');
        return redirect('admin/departures');
    }

    public function edit($id)
    {
        $title = 'Cập nhật chuyến khởi hành';
        $departure = $this->modelDeparture->findById($id);
        $tours = $this->modelTour->getAll();

        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);

        return view('departures.edit', compact('title', 'departure', 'tours', 'assignments'));
    }

    public function update($id)
    {
        $departure = $this->modelDeparture->findById($id);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $data = [
            'tour_id'         => $_POST['tour_id'],
            'departure_date'  => $_POST['departure_date'],
            'return_date'     => $_POST['return_date'] ?? null,
            'max_participants'=> $_POST['max_participants'] ?? 0,
            'meeting_point'   => $_POST['meeting_point'] ?? null,
            'meeting_time'    => $_POST['meeting_time'] ?? null,
            'vehicle'         => $_POST['vehicle'] ?? null,
            'notes'           => $_POST['notes'] ?? null,
            'status'          => $_POST['status'] ?? 'scheduled',
        ];

        $rules = [
            'tour_id'        => 'required|integer',
            'departure_date' => 'required',
            'max_participants' => 'integer',
            'status'         => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/departures/edit/' . $id);
        }

        if (!empty($data['return_date']) && strtotime($data['return_date']) < strtotime($data['departure_date'])) {
            setFlash('error', 'Ngày trở về không thể sớm hơn ngày khởi hành!');
            return redirect('admin/departures/edit/' . $id);
        }

        $this->modelDeparture->update($id, $data);
        setFlash('success', 'Cập nhật chuyến khởi hành thành công!');
        return redirect('admin/departures');
    }

    public function delete($id)
    {
        $departure = $this->modelDeparture->findById($id);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $this->modelDeparture->delete($id);
        setFlash('success', 'Xóa chuyến khởi hành thành công!');
        return redirect('admin/departures');
    }

    public function show($id)
    {
        $title = 'Chi tiết chuyến khởi hành';
        $departure = $this->modelDeparture->findById($id);

        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);

        return view('departures.show', compact('title', 'departure', 'assignments'));
    }
}

