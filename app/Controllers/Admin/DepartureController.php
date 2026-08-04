<?php

namespace App\Controllers\Admin;
  
use App\Controller;
use App\Models\Departure;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\StaffAssignment;
use Rakit\Validation\Validator;

class DepartureController extends Controller
{
    private $modelDeparture;
    private $modelTour;
    private $modelCategory;
    private $modelAssignment;
    private $validator;

    public function __construct()
    {
        $this->modelDeparture = new Departure();
        $this->modelTour = new Tour();
        $this->modelCategory = new TourCategory();
        $this->modelAssignment = new StaffAssignment();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý khởi hành';
        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $categories = $this->modelCategory->getAll();
        $departures = $this->modelDeparture->getAll($categoryId);
        $statusCounts = $this->modelDeparture->getDeparturesByStatus($categoryId);
        return view('admin.departures.index', compact('title', 'departures', 'statusCounts', 'categories', 'categoryId'));
    }

    public function create()
    {
        $title = 'Thêm chuyến khởi hành mới';
        $tours = $this->modelTour->getAll();
        return view('admin.departures.create', compact('title', 'tours'));
    }

    public function store()
    {
        $data = [
            'tour_id'         => $_POST['tour_id'],
            'group_name'      => trim($_POST['group_name'] ?? ''),
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
            'group_name'     => 'max:255',
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

        if ($data['group_name'] === '') {
            $tour = $this->modelTour->findByid((int) $data['tour_id']);
            $data['group_name'] = $tour
                ? ('Đoàn ' . $tour['name'] . ' ' . date('d-m-Y', strtotime($data['departure_date'])))
                : null;
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

        return view('admin.departures.edit', compact('title', 'departure', 'tours', 'assignments'));
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
            'group_name'      => trim($_POST['group_name'] ?? ''),
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
            'group_name'     => 'max:255',
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

        if ($data['group_name'] === '') {
            $tour = $this->modelTour->findByid((int) $data['tour_id']);
            $data['group_name'] = $tour
                ? ('Đoàn ' . $tour['name'] . ' ' . date('d-m-Y', strtotime($data['departure_date'])))
                : ($departure['group_name'] ?? null);
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);
        foreach ($assignments as $assignment) {
            if (!$this->modelAssignment->checkStaffAvailability(
                $assignment['staff_id'],
                $data['departure_date'],
                $data['return_date'],
                $assignment['id']
            )) {
                $conflicts = $this->modelAssignment->getConflictingAssignments(
                    $assignment['staff_id'],
                    $data['departure_date'],
                    $data['return_date'],
                    $assignment['id']
                );
                $conflictSummary = [];
                foreach ($conflicts as $conflict) {
                    $tourName = $conflict['tour_name'] ?? 'chuyến khởi hành';
                    $conflictDate = !empty($conflict['departure_date']) ? date('d/m/Y', strtotime($conflict['departure_date'])) : 'chưa xác định';
                    $conflictSummary[] = $tourName . ' (' . $conflictDate . ')';
                }
                $conflictText = implode(', ', array_slice($conflictSummary, 0, 3));
                if (count($conflictSummary) > 3) {
                    $conflictText .= ', ...';
                }
                setFlash('error', 'Nhân sự ' . ($assignment['staff_name'] ?? 'đã chọn') . ' đã bị trùng lịch với ' . $conflictText . '.');
                return redirect('admin/departures/edit/' . $id);
            }
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

        return view('admin.departures.show', compact('title', 'departure', 'assignments'));
    }
}

