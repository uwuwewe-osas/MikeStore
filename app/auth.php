<?php

declare(strict_types=1);

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user && (int) $user['id'] === (int) $_SESSION['user_id']) {
        return $user;
    }

    $user = fetch_one('SELECT id, name, email, role, is_active, created_at FROM users WHERE id = ?', [$_SESSION['user_id']]);
    return $user;
}

function attempt_login(string $email, string $password): bool
{
    $user = fetch_one('SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1', [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    session_regenerate_id(true);
    return true;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function require_auth(): void
{
    if (!current_user()) {
        flash('error', 'Inicia sesion para continuar.');
        redirect_to('/?page=login');
    }
}

function require_role(array $roles): void
{
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('No autorizado.');
    }
}
