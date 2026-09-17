<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    } elseif (time() - $_SESSION['created_at'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool
{
    start_secure_session();

    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Método no permitido.']);
        exit;
    }
}

function ensure_security_tables(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS rate_limits (
            clave VARCHAR(190) NOT NULL PRIMARY KEY,
            intentos INT UNSIGNED NOT NULL DEFAULT 0,
            ventana_inicio DATETIME NOT NULL,
            bloqueado_hasta DATETIME NULL,
            KEY idx_rl_bloqueo (bloqueado_hasta),
            KEY idx_rl_ventana (ventana_inicio)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    db()->exec(
        'CREATE TABLE IF NOT EXISTS auditoria (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            id_usuario INT UNSIGNED NULL,
            accion VARCHAR(100) NOT NULL,
            detalle VARCHAR(255) NULL,
            ip VARCHAR(45) NULL,
            KEY idx_aud_fecha (fecha),
            KEY idx_aud_usuario (id_usuario)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function cliente_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function rate_limit_reset(string $clave): void
{
    try {
        db()->prepare('DELETE FROM rate_limits WHERE clave = :c')
            ->execute(['c' => $clave]);
    } catch (Throwable $e) {
        error_log('rate_limit_reset: ' . $e->getMessage());
    }
}

function rate_limit_blocked(string $clave): bool
{
    try {
        ensure_security_tables();
        $stmt = db()->prepare(
            'SELECT 1 FROM rate_limits
             WHERE clave = :c AND bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW()
             LIMIT 1'
        );
        $stmt->execute(['c' => $clave]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        error_log('rate_limit_blocked: ' . $e->getMessage());
        return false;
    }
}

function rate_limit_consume(string $clave, int $max, int $ventanaSeg, int $bloqueoSeg): bool
{
    $max = max(1, $max);
    $ventanaSeg = max(1, $ventanaSeg);

    try {
        ensure_security_tables();
        $pdo = db();
        $pdo->beginTransaction();

        $ahora = (int)$pdo->query('SELECT UNIX_TIMESTAMP(NOW())')->fetchColumn();

        $stmt = $pdo->prepare(
            'SELECT intentos,
                    UNIX_TIMESTAMP(ventana_inicio) AS ventana_ts,
                    UNIX_TIMESTAMP(bloqueado_hasta) AS bloqueo_ts
             FROM rate_limits WHERE clave = :c FOR UPDATE'
        );
        $stmt->execute(['c' => $clave]);
        $row = $stmt->fetch();

        if ($row && $row['bloqueo_ts'] && (int)$row['bloqueo_ts'] > $ahora) {
            $pdo->rollBack();
            return false;
        }

        if (!$row) {
            $stmt = $pdo->prepare(
                'INSERT INTO rate_limits (clave, intentos, ventana_inicio)
                 VALUES (:c, 1, FROM_UNIXTIME(:t))'
            );
            $stmt->execute(['c' => $clave, 't' => $ahora]);
        } else {
            $inicioVentana = (int)$row['ventana_ts'];
            if ($ahora - $inicioVentana > $ventanaSeg) {
                $stmt = $pdo->prepare(
                    'UPDATE rate_limits
                     SET intentos = 1, ventana_inicio = FROM_UNIXTIME(:t), bloqueado_hasta = NULL
                     WHERE clave = :c'
                );
                $stmt->execute(['t' => $ahora, 'c' => $clave]);
            } else {
                $nuevo = (int)$row['intentos'] + 1;
                if ($nuevo > $max) {
                    $stmt = $pdo->prepare(
                        'UPDATE rate_limits
                         SET intentos = :i, bloqueado_hasta = FROM_UNIXTIME(:b)
                         WHERE clave = :c'
                    );
                    $stmt->execute([
                        'i' => $nuevo,
                        'b' => $ahora + $bloqueoSeg,
                        'c' => $clave,
                    ]);
                    $pdo->commit();
                    return false;
                }
                $stmt = $pdo->prepare(
                    'UPDATE rate_limits SET intentos = :i WHERE clave = :c'
                );
                $stmt->execute(['i' => $nuevo, 'c' => $clave]);
            }
        }

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('rate_limit_consume: ' . $e->getMessage());
        return true;
    }
}

function registrar_auditoria(int $idUsuario, string $accion, string $detalle = ''): void
{
    try {
        ensure_security_tables();
        $stmt = db()->prepare(
            'INSERT INTO auditoria (id_usuario, accion, detalle, ip)
             VALUES (:u, :a, :d, :ip)'
        );
        $stmt->execute([
            'u' => $idUsuario > 0 ? $idUsuario : null,
            'a' => $accion,
            'd' => mb_substr($detalle, 0, 255),
            'ip' => cliente_ip(),
        ]);
    } catch (Throwable $e) {
        error_log('registrar_auditoria: ' . $e->getMessage());
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function icon(string $name, int $size = 14): string
{
    $icons = [
        'edit'      => '<path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>',
        'trash'     => '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>',
        'plus'      => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'check'     => '<polyline points="20 6 9 17 4 12"/>',
        'x'         => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
        'undo'      => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>',
        'save'      => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
        'search'    => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'login'     => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'key'       => '<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>',
        'download'  => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        'printer'   => '<polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
        'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'eye'       => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    ];
    $paths = $icons[$name] ?? '<circle cx="12" cy="12" r="10"/>';
    return '<svg class="ic" xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}
