<?php

declare(strict_types=1);

function env_value(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

function app_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [
        'name' => env_value('APP_NAME', 'MikeZapatillas POS'),
        'env' => env_value('APP_ENV', 'local'),
        'db' => [
            'host' => env_value('DB_HOST', '127.0.0.1'),
            'port' => env_value('DB_PORT', '3306'),
            'database' => env_value('DB_DATABASE', 'mikezapatillas_pos'),
            'username' => env_value('DB_USERNAME', 'root'),
            'password' => env_value('DB_PASSWORD', ''),
        ],
    ];

    return $config;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function currency(float|int|string $value): string
{
    return 'S/ ' . number_format((float) $value, 2);
}

function redirect_to(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_post(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Token CSRF invalido.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flashes(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function keep_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function request_value(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function nav_is_active(string $page, string $current): string
{
    return $page === $current ? 'is-active' : '';
}

function badge_class(string $value): string
{
    return match ($value) {
        'open', 'completed', 'cash', 'admin' => 'badge badge-success',
        'closed' => 'badge badge-neutral',
        'cancelled', 'expense' => 'badge badge-danger',
        default => 'badge badge-info',
    };
}
