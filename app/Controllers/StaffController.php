<?php
namespace App\Controllers;

use App\Controller;
use App\Models\Staff;
use Rakit\Validation\Validator;

class StaffController extends Controller
{
    private $modelStaff;
    private $validator;

    public function __construct()
    {
        $this->modelStaff = new Staff();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Danh sách Hướng dẫn viên (Nhân sự)';
        $staffs = $this->modelStaff->getAll();
        return view('Staff.index', compact('title', 'staffs'));
    }

    public function create()
    {
        $title = 'Thêm mới Hướng dẫn viên';
        return view('Staff.create', compact('title'));
    }

    public function store()
    {
        $data = [
            'Hoten'            => $_POST['Hoten'] ?? '',
            'Ngaysinh'         => $_POST['Ngaysinh'] ?? null,
            'Gioitinh'         => $_POST['Gioitinh'] ?? '',
            'Lienhe'           => $_POST['Lienhe'] ?? '',
            'Ngonngu'          => $_POST['Ngonngu'] ?? '',
            'Diachi'           => $_POST['Diachi'] ?? '',
            'chungchiHDV'      => $_POST['chungchiHDV'] ?? '',
            'Kinhnghiem'       => $_POST['Kinhnghiem'] ?? 0,
            'Ngaybatdaulam'    => $_POST['Ngaybatdaulam'] ?? null,
            'Trangthaisuckhoe' => $_POST['Trangthaisuckhoe'] ?? '',
            'Ghichunoibo'      => $_POST['Ghichunoibo'] ?? '',
            'Diemdanhgia'      => $_POST['Diemdanhgia'] ?? 0,
            'Nhanxetdanhgia'   => $_POST['Nhanxetdanhgia'] ?? '',
            'HDV_group_id'     => $_POST['HDV_group_id'] ?? null,
            'Status'           => $_POST['Status'] ?? 'active',
        ];

        $rules = [
            'Hoten'  => 'required|max:50',
            'Status' => 'required|in:active,inactive,on_leave',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/staff/create');
        }

        $this->modelStaff->insert($data);
        setFlash('success', 'Thêm mới Hướng dẫn viên thành công!');
        return redirect('admin/staff');
    }

    public function edit($id)
    {
        $title = 'Cập nhật Hướng dẫn viên';
        $staff = $this->modelStaff->findById($id);
        if (!$staff) {
            setFlash('error', 'Hướng dẫn viên không tồn tại');
            redirect('admin/staff');
        }
        return view('Staff.edit', compact('title', 'staff'));
    }

    public function update($id)
    {
        $staff = $this->modelStaff->findById($id);
        if (!$staff) {
            setFlash('error', 'Hướng dẫn viên không tồn tại');
            redirect('admin/staff');
        }

        $data = [
            'Hoten'            => $_POST['Hoten'] ?? '',
            'Ngaysinh'         => $_POST['Ngaysinh'] ?? null,
            'Gioitinh'         => $_POST['Gioitinh'] ?? '',
            'Lienhe'           => $_POST['Lienhe'] ?? '',
            'Ngonngu'          => $_POST['Ngonngu'] ?? '',
            'Diachi'           => $_POST['Diachi'] ?? '',
            'chungchiHDV'      => $_POST['chungchiHDV'] ?? '',
            'Kinhnghiem'       => $_POST['Kinhnghiem'] ?? 0,
            'Ngaybatdaulam'    => $_POST['Ngaybatdaulam'] ?? null,
            'Trangthaisuckhoe' => $_POST['Trangthaisuckhoe'] ?? '',
            'Ghichunoibo'      => $_POST['Ghichunoibo'] ?? '',
            'Diemdanhgia'      => $_POST['Diemdanhgia'] ?? 0,
            'Nhanxetdanhgia'   => $_POST['Nhanxetdanhgia'] ?? '',
            'HDV_group_id'     => $_POST['HDV_group_id'] ?? null,
            'Status'           => $_POST['Status'] ?? 'active',
        ];

        $rules = [
            'Hoten'  => 'required|max:50',
            'Status' => 'required|in:active,inactive,on_leave',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/staff/edit/' . $id);
        }

        $this->modelStaff->update($id, $data);
        setFlash('success', 'Cập nhật Hướng dẫn viên thành công!');
        return redirect('admin/staff');
    }

    public function delete($id)
    {
        $staff = $this->modelStaff->findById($id);
        if (!$staff) {
            setFlash('error', 'Hướng dẫn viên không tồn tại');
            redirect('admin/staff');
        }

        $this->modelStaff->delete($id);
        setFlash('success', 'Xóa Hướng dẫn viên thành công!');
        return redirect('admin/staff');
    }

    public function show($id)
    {
        $title = 'Chi tiết Hướng dẫn viên';
        $staff = $this->modelStaff->findById($id);

        if (!$staff) {
            setFlash('error', 'Hướng dẫn viên không tồn tại');
            return redirect('admin/staff');
        }

        return view('Staff.show', compact('title', 'staff'));
    }
}
