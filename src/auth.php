<?php
declare(strict_types=1);

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../config/database.php';

start_secure_session();

function login_user(string $dni, string $password): string
{
    $dni = dni_solo_digitos($dni);
    if ($dni === '' || $password === '') return 'dni';
    if (!dni_es_valido($dni)) return 'dni';

    $stmt = db()->prepare('SELECT id_usuario,nombre,apellido,dni,password_hash,es_admin,activo FROM usuarios WHERE dni=:dni AND activo=1 LIMIT 1');
    $stmt->execute(['dni' => $dni]);
    $user = $stmt->fetch();

    if (!$user) {
        return 'dni';
    }

    if (!password_verify($password, $user['password_hash'])) {
        return 'password';
    }

    session_regenerate_id(true);
    $token = bin2hex(random_bytes(32));
    db()->prepare('UPDATE usuarios SET sesion_token = :t WHERE id_usuario = :id')
        ->execute(['t' => $token, 'id' => $user['id_usuario']]);

    $_SESSION['user'] = [
        'id' => (int)$user['id_usuario'],
        'nombre' => $user['nombre'],
        'apellido' => $user['apellido'],
        'dni' => $user['dni'],
        'es_admin' => (int)$user['es_admin'],
    ];
    $_SESSION['sesion_token'] = $token;
    $_SESSION['created_at'] = time();
    return 'ok';
}

function current_user(): ?array
{
    $user = $_SESSION['user'] ?? null;
    if (!$user) return null;

    $token = $_SESSION['sesion_token'] ?? '';
    if ($token === '') {
        unset($_SESSION['user']);
        return null;
    }

    try {
        $stmt = db()->prepare('SELECT sesion_token, es_admin FROM usuarios WHERE id_usuario = :id AND activo = 1 LIMIT 1');
        $stmt->execute(['id' => $user['id']]);
        $row = $stmt->fetch();
        $dbToken = is_array($row) ? $row['sesion_token'] : false;
        if (!is_string($dbToken) || !hash_equals($dbToken, $token)) {
            unset($_SESSION['user'], $_SESSION['sesion_token']);
            return null;
        }
        if (is_array($row) && (int)$row['es_admin'] !== (int)$user['es_admin']) {
            $user['es_admin'] = (int)$row['es_admin'];
            $_SESSION['user'] = $user;
        }
        return $user;
    } catch (Throwable $e) {
        return $user;
    }
}

function require_login(): array
{
    $user = current_user();
    if (!$user) { header('Location: index.php'); exit; }
    return $user;
}

function require_admin(): array
{
    $user = current_user();
    if (!$user) { header('Location: index.php'); exit; }
    if ((int)$user['es_admin'] !== 1) { header('Location: dashboard.php'); exit; }
    return $user;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
    }
    session_destroy();
}
