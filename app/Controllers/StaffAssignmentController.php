<?php

namespace App\Controllers;

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
        return view('staff-assignments.index', compact('title', 'assignments', 'totalAssignments'));
    }

    public function create()
    {
        $title = 'Phân bổ nhân sự mới';
        $departures = $this->modelDeparture->getUpcomingDepartures(50);
        $staffList = $this->modelStaff->getAll();

        $departureId = $_GET['departure_id'] ?? null;
        $availableStaff = [];
        if ($departureId) {
            $availableStaff = $this->modelAssignment->getAvailableStaff($departureId);
        }

        return view('staff-assignments.create', compact('title', 'departures', 'staffList', 'departureId', 'availableStaff'));
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
            setFlash('error', 'Nhân viên này đã được phân bổ cho chuyến khác trong cùng thời gian!');
            return redirect('admin/staff-assignments/create');
        }

        try {
            $this->modelAssignment->insert($data);
            setFlash('success', 'Phân bổ nhân sự thành công!');
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
        $staffList = $this->modelStaff->getAll();

        return view('staff-assignments.edit', compact('title', 'assignment', 'departures', 'staffList'));
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
                setFlash('error', 'Nhân viên này đã được phân bổ cho chuyến khác trong cùng thời gian!');
                return redirect('admin/staff-assignments/edit/' . $id);
            }
        }

        $this->modelAssignment->update($id, $data);
        setFlash('success', 'Cập nhật phân bổ nhân sự thành công!');
        return redirect('admin/staff-assignments');
    }

    public function delete($id)
    {
        $assignment = $this->modelAssignment->findById($id);
        if (!$assignment) {
            setFlash('error', 'Phân bổ nhân sự không tồn tại!');
            return redirect('admin/staff-assignments');
        }

        $this->modelAssignment->delete($id);
        setFlash('success', 'Xóa phân bổ nhân sự thành công!');
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

        return view('staff-assignments.show', compact('title', 'assignment'));
    }
}

