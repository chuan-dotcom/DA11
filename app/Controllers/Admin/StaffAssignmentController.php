<?php

namespace App\Controllers\Admin;
  
use App\Controller;
use App\Models\StaffAssignment;
use App\Models\Departure;
use App\Models\Staff;
use Rakit\Validation\Validator;

class StaffAssignmentController extends Controller
{
    private $modelAssignment;
    private $modelDeparture;
    private $modelStaff;
    private $validator;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelStaff = new Staff();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Phân bổ nhân sự';
        $assignments = $this->modelAssignment->getAll();
        $totalAssignments = $this->modelAssignment->getTotalAssignments();
        return view('admin.staff-assignments.index', compact('title', 'assignments', 'totalAssignments'));
    }

    public function create()
    {
        $title = 'Phân bổ nhân sự mới';
        $departures = $this->modelDeparture->getUpcomingDepartures(50);
        $staffList = $this->modelStaff->getAll();

        $departureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;
        $availableStaff = [];
        if ($departureId) {
            $availableStaff = $this->modelAssignment->getAvailableStaff($departureId);
        }

        $returnDepartureId = $departureId;

        return view('admin.staff-assignments.create', compact('title', 'departures', 'staffList', 'departureId', 'availableStaff', 'returnDepartureId'));
    }

    public function store()
    {
        $data = [
            'departure_id'     => $_POST['departure_id'],
            'staff_id'         => $_POST['staff_id'],
            'role'             => $_POST['role'] ?? 'other',
            'responsibilities' => $_POST['responsibilities'] ?? null,
            'notes'            => $_POST['notes'] ?? null,
            'status'           => $_POST['status'] ?? 'assigned',
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'staff_id'     => 'required|integer',
            'role'         => 'required',
            'status'       => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/staff-assignments/create');
        }

        $departure = $this->modelDeparture->findById($data['departure_id']);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/staff-assignments/create');
        }

        $staff = $this->modelStaff->findById($data['staff_id']);
        if (!$staff) {
            setFlash('error', 'Nhân viên không tồn tại!');
            return redirect('admin/staff-assignments/create');
        }

        if (!$this->modelAssignment->checkStaffAvailability(
            $data['staff_id'],
            $departure['departure_date'],
            $departure['return_date']
        )) {
            $conflicts = $this->modelAssignment->getConflictingAssignments(
                $data['staff_id'],
                $departure['departure_date'],
                $departure['return_date']
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
            setFlash('error', 'Nhân sự ' . ($staff['Hoten'] ?? 'đã chọn') . ' đã bị trùng lịch với ' . $conflictText . '.');
            return redirect('admin/staff-assignments/create');
        }

        try {
            $this->modelAssignment->insert($data);
            setFlash('success', 'Phân bổ nhân sự thành công!');
            $redirectDepartureId = (int)$data['departure_id'];
            if ($redirectDepartureId > 0) {
                return redirect('admin/departures/edit/' . $redirectDepartureId);
            }
            return redirect('admin/staff-assignments');
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, '1062 Duplicate entry') !== false || strpos($errorMsg, 'unique') !== false) {
                setFlash('error', 'Nhân viên này đã được phân bổ cho chuyến khởi hành này rồi!');
            } else {
                setFlash('error', 'Lỗi: ' . $errorMsg);
            }
            return redirect('admin/staff-assignments/create');
        }
    }

    public function edit($id)
    {
        $title = 'Cập nhật phân bổ nhân sự';
        $assignment = $this->modelAssignment->findById($id);

        if (!$assignment) {
            setFlash('error', 'Phân bổ nhân sự không tồn tại!');
            return redirect('admin/staff-assignments');
        }

        $departures = $this->modelDeparture->getUpcomingDepartures(50);
        $currentDeparture = $this->modelDeparture->findById($assignment['departure_id']);
        if ($currentDeparture) {
            $departureExists = false;
            foreach ($departures as $departure) {
                if ((int) $departure['id'] === (int) $currentDeparture['id']) {
                    $departureExists = true;
                    break;
                }
            }

            if (!$departureExists) {
                array_unshift($departures, $currentDeparture);
            }
        }
        $staffList = $this->modelStaff->getAll();
        $returnDepartureId = (int)$assignment['departure_id'];

        return view('admin.staff-assignments.edit', compact('title', 'assignment', 'departures', 'staffList', 'returnDepartureId'));
    }

    public function update($id)
    {
        $assignment = $this->modelAssignment->findById($id);
        if (!$assignment) {
            setFlash('error', 'Phân bổ nhân sự không tồn tại!');
            return redirect('admin/staff-assignments');
        }

        $data = [
            'departure_id'     => $_POST['departure_id'],
            'staff_id'         => $_POST['staff_id'],
            'role'             => $_POST['role'] ?? 'other',
            'responsibilities' => $_POST['responsibilities'] ?? null,
            'notes'            => $_POST['notes'] ?? null,
            'status'           => $_POST['status'] ?? 'assigned',
        ];

        $rules = [
            'departure_id' => 'required|integer',
            'staff_id'     => 'required|integer',
            'role'         => 'required',
            'status'       => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/staff-assignments/edit/' . $id);
        }

        $departure = $this->modelDeparture->findById($data['departure_id']);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/staff-assignments/edit/' . $id);
        }

        $staffChanged = $data['staff_id'] != $assignment['staff_id'];
        $departureChanged = $data['departure_id'] != $assignment['departure_id'];

        if ($staffChanged || $departureChanged) {
            if (!$this->modelAssignment->checkStaffAvailability(
                $data['staff_id'],
                $departure['departure_date'],
                $departure['return_date'],
                $id
            )) {
                $conflicts = $this->modelAssignment->getConflictingAssignments(
                    $data['staff_id'],
                    $departure['departure_date'],
                    $departure['return_date'],
                    $id
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
                setFlash('error', 'Nhân sự ' . ($staff['Hoten'] ?? 'đã chọn') . ' đã bị trùng lịch với ' . $conflictText . '.');
                return redirect('admin/staff-assignments/edit/' . $id);
            }
        }

        $this->modelAssignment->update($id, $data);
        setFlash('success', 'Cập nhật phân bổ nhân sự thành công!');
        $redirectDepartureId = (int)$data['departure_id'];
        if ($redirectDepartureId > 0) {
            return redirect('admin/departures/edit/' . $redirectDepartureId);
        }
        return redirect('admin/staff-assignments');
    }

    public function delete($id)
    {
        $assignment = $this->modelAssignment->findById($id);
        if (!$assignment) {
            setFlash('error', 'Phân bổ nhân sự không tồn tại!');
            return redirect('admin/staff-assignments');
        }
        $redirectDepartureId = (int)$assignment['departure_id'];

        $this->modelAssignment->delete($id);
        setFlash('success', 'Xóa phân bổ nhân sự thành công!');
        if ($redirectDepartureId > 0) {
            return redirect('admin/departures/edit/' . $redirectDepartureId);
        }
        return redirect('admin/staff-assignments');
    }

    public function show($id)
    {
        $title = 'Chi tiết phân bổ nhân sự';
        $assignment = $this->modelAssignment->findById($id);

        if (!$assignment) {
            setFlash('error', 'Phân bổ nhân sự không tồn tại!');
            return redirect('admin/staff-assignments');
        }

        return view('admin.staff-assignments.show', compact('title', 'assignment'));
    }
}

