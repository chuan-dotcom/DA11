<?php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Staff;
use App\Models\User;
use Rakit\Validation\Validator;

class UserController extends Controller
{
    private User $userModel;
    private Staff $staffModel;
    private Validator $validator;

    public function __construct()
    {
        $this->userModel = new User();
        $this->staffModel = new Staff();
        $this->validator = new Validator();
    }

    public function index()
    {
        $pageTitle = 'Quản lý tài khoản';
        $title = 'Danh sách tài khoản';
        $users = $this->userModel->getAll();
        $stats = [
            'total' => count($users),
            'active' => count(array_filter($users, static fn ($u) => ($u['status'] ?? 0) == 1)),
            'admin' => count(array_filter($users, static fn ($u) => ($u['role'] ?? '') === 'admin')),
            'hdv' => count(array_filter($users, static fn ($u) => ($u['role'] ?? '') === 'hdv')),
        ];

        return view('admin.users.index', compact('pageTitle', 'title', 'users', 'stats'));
    }

    public function create()
    {
        $pageTitle = 'Quản lý tài khoản';
        $title = 'Thêm mới tài khoản';
        $staffs = $this->staffModel->getAll();

        $this->keepOldInput();

        return view('admin.users.create', compact('pageTitle', 'title', 'staffs'));
    }

    public function store()
    {
        $input = $this->normalizeInput([
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => $_POST['role'] ?? 'user',
            'hdv_id' => $_POST['hdv_id'] ?? null,
            'status' => $_POST['status'] ?? '1',
        ]);

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:admin,user,hdv',
            'status' => 'required|in:0,1',
        ];

        $errors = $this->validate($this->validator, $input, $rules);
        if (!empty($errors)) {
            $this->flashOldInput($input);
            setFlash('error', reset($errors));
            return redirect('admin/users/create');
        }

        if ($input['role'] !== 'hdv') {
            $input['hdv_id'] = null;
        } elseif (!empty($input['hdv_id']) && !$this->staffModel->findById((int) $input['hdv_id'])) {
            $this->flashOldInput($input);
            setFlash('error', 'Hướng dẫn viên được chọn không tồn tại.');
            return redirect('admin/users/create');
        }

        if ($this->userModel->emailExists($input['email'])) {
            $this->flashOldInput($input);
            setFlash('error', 'Email đã tồn tại!');
            return redirect('admin/users/create');
        }

        $input['avatar'] = null;
        if (is_upload('avatar')) {
            try {
                $input['avatar'] = $this->uploadFile($_FILES['avatar'], 'users');
            } catch (\Throwable $e) {
                $this->logError('Upload user avatar failed: ' . $e->getMessage());
                $this->flashOldInput($input);
                setFlash('error', 'Không thể tải ảnh đại diện, vui lòng thử lại.');
                return redirect('admin/users/create');
            }
        }

        $this->userModel->insert($input);
        setFlash('success', 'Thêm mới tài khoản thành công!');

        return redirect('admin/users');
    }

    public function edit($id)
    {
        $pageTitle = 'Quản lý tài khoản';
        $title = 'Cập nhật tài khoản';
        $user = $this->userModel->findById($id);
        $staffs = $this->staffModel->getAll();

        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }

        $this->keepOldInput();

        return view('admin.users.edit', compact('pageTitle', 'title', 'user', 'staffs'));
    }

    public function update($id)
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }

        $input = $this->normalizeInput([
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => $_POST['role'] ?? 'user',
            'hdv_id' => $_POST['hdv_id'] ?? null,
            'status' => $_POST['status'] ?? '1',
        ]);

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|in:admin,user,hdv',
            'status' => 'required|in:0,1',
        ];

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';
        if ($password !== '' || $passwordConfirm !== '') {
            $input['password'] = $password;
            $input['password_confirmation'] = $passwordConfirm;
            $rules['password'] = 'min:6';
            $rules['password_confirmation'] = 'same:password';
        }

        $errors = $this->validate($this->validator, $input, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/users/edit/' . $id);
        }

        if ($input['role'] !== 'hdv') {
            $input['hdv_id'] = null;
        } elseif (!empty($input['hdv_id']) && !$this->staffModel->findById((int) $input['hdv_id'])) {
            setFlash('error', 'Hướng dẫn viên được chọn không tồn tại.');
            return redirect('admin/users/edit/' . $id);
        }

        if ($this->userModel->emailExists($input['email'], $id)) {
            setFlash('error', 'Email đã tồn tại!');
            return redirect('admin/users/edit/' . $id);
        }

        $input['avatar'] = $user['avatar'];
        if (is_upload('avatar')) {
            try {
                if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                $input['avatar'] = $this->uploadFile($_FILES['avatar'], 'users');
            } catch (\Throwable $e) {
                $this->logError('Update user avatar failed: ' . $e->getMessage());
                setFlash('error', 'Không thể tải ảnh đại diện, vui lòng thử lại.');
                return redirect('admin/users/edit/' . $id);
            }
        }

        $this->userModel->update($id, $input);
        setFlash('success', 'Cập nhật tài khoản thành công!');

        return redirect('admin/users');
    }

    public function delete($id)
    {
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            redirect('admin/users');
        }

        if ($this->isCurrentUser($id)) {
            setFlash('error', 'Không thể xóa tài khoản đang đăng nhập.');
            return redirect('admin/users');
        }

        $this->userModel->delete($id);
        setFlash('success', 'Xóa tài khoản thành công!');

        return redirect('admin/users');
    }

    public function bulkDelete()
    {
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            setFlash('error', 'Vui lòng chọn ít nhất 1 tài khoản để xóa.');
            return redirect('admin/users');
        }

        foreach ($ids as $id) {
            if ($this->isCurrentUser($id)) {
                setFlash('error', 'Không thể xóa tài khoản đang đăng nhập.');
                return redirect('admin/users');
            }
        }

        $deletedCount = $this->userModel->deleteMultiple($ids);
        if ($deletedCount > 0) {
            setFlash('success', "Xóa thành công {$deletedCount} tài khoản!");
        } else {
            setFlash('error', 'Không có tài khoản nào bị xóa.');
        }

        return redirect('admin/users');
    }

    public function show($id)
    {
        $pageTitle = 'Quản lý tài khoản';
        $title = 'Chi tiết tài khoản';
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlash('error', 'Tài khoản không tồn tại');
            return redirect('admin/users');
        }

        return view('admin.users.show', compact('pageTitle', 'title', 'user'));
    }

    private function normalizeInput(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = $value === '' ? null : preg_replace('/\s+/u', ' ', $value);
            }
        }

        return $input;
    }

    private function flashOldInput(array $input): void
    {
        $safe = [];
        foreach ($input as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $safe[$key] = $value;
            }
        }
        $safe = array_diff_key($safe, array_flip(['password', 'password_confirmation']));
        $_SESSION['old_input'] = $safe;
    }

    private function keepOldInput(): void
    {
        // Keep for one more render, then clear.
        if (isset($_SESSION['old_input'])) {
            // leave it intact so old() works on the form, then the next request cleans up.
        }
    }

    private function isCurrentUser($id): bool
    {
        $currentUserId = $_SESSION['auth']['id'] ?? null;
        return $currentUserId !== null && (int) $currentUserId === (int) $id;
    }
}
