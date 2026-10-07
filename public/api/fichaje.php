<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/functions.php';

header('Content-Type: application/json; charset=utf-8');

require_post();

$user = current_user();

if (!$user) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Sesión expirada.']);
    exit;
}

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if (!verify_csrf($csrf)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Solicitud no válida.']);
    exit;
}

$rateKey = 'fichaje:usuario:' . $user['id'];
if (rate_limit_blocked($rateKey)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'message' => 'Demasiadas solicitudes. Intentá más tarde.']);
    exit;
}
if (!rate_limit_consume($rateKey, 10, 3600, 180)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'message' => 'Demasiadas solicitudes. Intentá más tarde.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'entrada') {
    $result = register_entry($user['id']);
} elseif ($action === 'salida') {
    $result = register_exit($user['id']);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Acción no válida.']);
    exit;
}

if (!empty($result['cooldown'])) {
    http_response_code(429);
    registrar_auditoria($user['id'], 'fichaje_bloqueado', ($action === 'entrada' ? 'Entrada' : 'Salida') . ' - espera: ' . (int)$result['retry_after'] . 's');
    echo json_encode($result);
    exit;
}

if (empty($result['ok'])) {
    registrar_auditoria($user['id'], 'fichaje_rechazado', ($action === 'entrada' ? 'Entrada' : 'Salida') . ' - ' . ($result['message'] ?? ''));
    echo json_encode($result);
    exit;
}

registrar_auditoria($user['id'], 'fichaje_' . $action, 'Hora: ' . ($result['time'] ?? ''));

echo json_encode($result);
