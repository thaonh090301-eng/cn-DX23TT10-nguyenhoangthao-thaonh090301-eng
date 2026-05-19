<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function consumeFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flash;
    }

    protected function view(string $view, array $data = []): string
    {
        $viewPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';

        if (!is_file($viewPath)) {
            http_response_code(500);

            return 'View not found.';
        }

        $e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        $currentUser = $data['currentUser'] ?? $this->currentUser();

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;

        return (string) ob_get_clean();
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    protected function requireAuth(): void
    {
        if ($this->currentUserId() !== null) {
            return;
        }

        $_SESSION['intended_path'] = $_SERVER['REQUEST_URI'] ?? '/dashboard';
        $this->redirect('/login');
    }

    protected function currentUserId(): ?int
    {
        $userId = $_SESSION['user_id'] ?? null;

        return is_numeric($userId) ? (int) $userId : null;
    }

    protected function currentUser(): ?array
    {
        $userId = $this->currentUserId();

        if ($userId === null) {
            return null;
        }

        return [
            'id' => $userId,
            'name' => (string) ($_SESSION['user_name'] ?? 'Demo User'),
            'email' => (string) ($_SESSION['user_email'] ?? ''),
        ];
    }

    protected function authUserId(): int
    {
        $this->requireAuth();

        return (int) $this->currentUserId();
    }
}
