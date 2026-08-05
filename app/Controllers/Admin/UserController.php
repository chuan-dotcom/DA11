<?php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Staff;
use App\Models\User;
use Rakit\Validation\Validator;

class UserController extends Controller
{
    private $modelUser;
    private $modelStaff;
    private $validator;

    public function __construct()
    {
        $this->modelUser = new User();
        $this->modelStaff = new Staff();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Danh sách tài khoản';
        $users = $this->modelUser->getAll();
        return view('admin.users.index', compact('title', 'users'));
    }

    public function create()
    {
        $title = 'Thêm mới tài khoản';
        $staffs = $this->modelStaff->getAll();
        return view('admin.users.create', compact('title', 'staffs'));
    }

    public function store()
    {
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'password_confirmation' => $_POST['password_confirmation'],
            'phone' => $_POST['phone'] ?? '',
            'role' => $_POST['role'],
            'hdv_id' => $_POST['hdv_id'] ?? null,
            'status' => $_POST['status'],
        ];

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:admin,user,hdv',
            'status' => 'required|in:0,1',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/users/create');
        }

        if ($data['role'] !== 'hdv') {
            $data['hdv_id'] = null;
        } elseif (!empty($data['hdv_id']) && !$this->modelStaff->findById((int) $data['hdv_id'])) {
            setFlash('error', 'Hướng dẫn viên được chọn không tồn tại.');
            return redirect('admin/users/create');
        }

        if ($this->modelUser->emailExists($data['email'])) {
            setFlash('error', 'Email đã tồn tại!');
            return redirect('admin/users/create');
        }

        $data['avatar'] = null;
        if (is_upload('avatar')) {
            $data['avatar'] = $this->uploadFile($_FILES['avatar'], 'users');
        }

        $this->modelUser->insert($data);
        setFlash('success', 'Thêm mới tài khoản thành công!');
        return redirect('admin/users');
    }

    public function edit($id)
    {
        $title = 'Cập nhật tài khoản';
        $user = $this->modelUser->findById($id);
        $staffs = $this->modelStaff->getAll();
        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }
        return view('admin.users.edit', compact('title', 'user', 'staffs'));
    }

    public function update($id)
    {
        $user = $this->modelUser->findById($id);
        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'role' => $_POST['role'],
            'hdv_id' => $_POST['hdv_id'] ?? null,
            'status' => $_POST['status'],
        ];

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,user,hdv',
            'status' => 'required|in:0,1',
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
            $data['password_confirmation'] = $_POST['password_confirmation'];
            $rules['password'] = 'min:6';
            $rules['password_confirmation'] = 'same:password';
        }

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/users/edit/' . $id);
        }

        if ($data['role'] !== 'hdv') {
            $data['hdv_id'] = null;
        } elseif (!empty($data['hdv_id']) && !$this->modelStaff->findById((int) $data['hdv_id'])) {
            setFlash('error', 'Hướng dẫn viên được chọn không tồn tại.');
            return redirect('admin/users/edit/' . $id);
        }

        if ($this->modelUser->emailExists($data['email'], $id)) {
            setFlash('error', 'Email đã tồn tại!');
            return redirect('admin/users/edit/' . $id);
        }

        $data['avatar'] = $user['avatar'];
        if (is_upload('avatar')) {
            if ($user['avatar'] && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
            $data['avatar'] = $this->uploadFile($_FILES['avatar'], 'users');
        }

        $this->modelUser->update($id, $data);
        setFlash('success', 'Cập nhật tài khoản thành công!');
        return redirect('admin/users');
    }

    public function delete($id)
    {
        $user = $this->modelUser->findById($id);
        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }

        $this->modelUser->delete($id);
        setFlash('success', 'Xóa tài khoản thành công!');
        return redirect('admin/users');
    }

    public function bulkDelete()
    {
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) {
            setFlash('error', 'Vui lòng chọn ít nhất 1 tài khoản để xóa.');
            return redirect('admin/users');
        }

        $currentUserId = $_SESSION['auth']['id'] ?? null;
        if ($currentUserId !== null && in_array($currentUserId, $ids, true)) {
            setFlash('error', 'Không thể xóa tài khoản đang đăng nhập.');
            return redirect('admin/users');
        }

        $deletedCount = $this->modelUser->deleteMultiple($ids);
        if ($deletedCount > 0) {
            setFlash('success', "Xóa thành công {$deletedCount} tài khoản!");
        } else {
            setFlash('error', 'Không có tài khoản nào bị xóa.');
        }

        return redirect('admin/users');
    }

    public function show($id)
    {
        $title = 'Chi tiết tài khoản';
        $user = $this->modelUser->findById($id);

        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            return redirect('admin/users');
        }

        return view('admin.users.show', compact('title', 'user'));
    }
}
