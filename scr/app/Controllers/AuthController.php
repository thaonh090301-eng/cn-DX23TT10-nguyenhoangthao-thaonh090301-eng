<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function login(): string
    {
        if ($this->currentUserId() !== null) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth/login', [
            'title' => __('auth.login_title'),
            'credentials' => ['email' => ''],
            'errors' => [],
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function authenticate(): string
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $user = $email === '' ? null : $this->users->findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            http_response_code(422);

            return $this->view('auth/login', [
                'title' => __('auth.login_title'),
                'credentials' => ['email' => $email],
                'errors' => ['login' => __('validation.invalid_login')],
                'flash' => [],
            ]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = (string) $user['name'];
        $_SESSION['user_email'] = (string) $user['email'];

        $intendedPath = (string) ($_SESSION['intended_path'] ?? '/dashboard');
        unset($_SESSION['intended_path']);

        if ($intendedPath === '/login' || !str_starts_with($intendedPath, '/')) {
            $intendedPath = '/dashboard';
        }

        return $this->redirect($intendedPath);
    }

    public function logout(): string
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
        session_start();
        $this->flash('success', __('flash.logged_out'));

        return $this->redirect('/login');
    }

    public function account(): string
    {
        $this->requireAuth();
        $userId = (int) $this->currentUserId();
        $user = $this->users->findById($userId);

        if ($user === null) {
            return $this->logout();
        }

        return $this->view('auth/account', [
            'title' => __('nav.account'),
            'accountUser' => $user,
            'stats' => $this->users->accountStats($userId),
        ]);
    }
}
