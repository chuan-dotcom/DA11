<?php

namespace App\Controllers;

use App\Controller;
use App\Models\User;
use App\Support\Auth;
use Rakit\Validation\Validator;

class AuthController extends Controller
{
    private $modelUser;
    private $validator;

    public function __construct()
    {
        $this->modelUser = new User();
        $this->validator = new Validator();
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('auth/login');
        }

        return redirect(Auth::redirectPath());
    }

    public function showLogin()
    {
        $title = 'Đăng nhập';
        return view('auth.login', compact('title'));
    }

    public function login()
    {
        $data = [
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];

        $rules = [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            Auth::storeOldInput($data);
            setFlash('error', reset($errors));
            return redirect('auth/login');
        }

        $user = $this->modelUser->findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            Auth::storeOldInput($data);
            setFlash('error', 'Email hoặc mật khẩu không đúng.');
            return redirect('auth/login');
        }

        if ((int) ($user['status'] ?? 0) !== 1) {
            Auth::storeOldInput($data);
            setFlash('error', 'Tài khoản của bạn đang bị khóa.');
            return redirect('auth/login');
        }

        Auth::clearOldInput();
        Auth::login($user);
        setFlash('success', 'Đăng nhập thành công.');

        return redirect(Auth::redirectPath($user));
    }

    public function showRegister()
    {
        $title = 'Đăng ký';
        return view('auth.register', compact('title'));
    }

    public function register()
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'max:20',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            Auth::storeOldInput($data);
            setFlash('error', reset($errors));
            return redirect('auth/register');
        }

        if ($this->modelUser->emailExists($data['email'])) {
            Auth::storeOldInput($data);
            setFlash('error', 'Email đã tồn tại.');
            return redirect('auth/register');
        }

        $insertData = $data;
        $insertData['role'] = 'user';
        $insertData['status'] = 1;

        $this->modelUser->insert($insertData);

        $user = $this->modelUser->findByEmail($data['email']);
        Auth::clearOldInput();
        Auth::login($user);
        setFlash('success', 'Đăng ký tài khoản thành công.');

        return redirect(Auth::redirectPath($user));
    }

    public function logout()
    {
        Auth::logout();
        setFlash('success', 'Bạn đã đăng xuất.');
        return redirect('auth/login');
    }

    public function account()
    {
        $title = 'Tài khoản của tôi';
        $user = Auth::user();

        return view('auth.account', compact('title', 'user'));
    }
}
