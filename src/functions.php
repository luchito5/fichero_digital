<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';

function today_fichaje(int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT * FROM fichajes
         WHERE id_usuario = :id AND fecha = CURDATE()
         ORDER BY id_fichaje DESC LIMIT 1'
    );
    $stmt->execute(['id' => $userId]);

    return $stmt->fetch() ?: null;
}

function today_fichajes(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT id_fichaje, fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin
         FROM fichajes
         WHERE id_usuario = :id AND fecha = CURDATE()
         ORDER BY id_fichaje ASC'
    );
    $stmt->execute(['id' => $userId]);

    return $stmt->fetchAll();
}

function register_entry(int $userId): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            'SELECT id_fichaje, hora_entrada, hora_salida
             FROM fichajes
             WHERE id_usuario = :id AND fecha = CURDATE()
             ORDER BY id_fichaje DESC
             LIMIT 1
             FOR UPDATE'
        );
        $stmt->execute(['id' => $userId]);
        $existing = $stmt->fetch();

        if ($existing && !empty($existing['hora_entrada']) && empty($existing['hora_salida'])) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Ya existe una entrada abierta para hoy.'];
        }

        $stmt = $pdo->prepare(
            'INSERT INTO fichajes
             (id_usuario, fecha, hora_entrada, validado_admin)
             VALUES (:id, CURDATE(), CURTIME(), 0)'
        );
        $stmt->execute(['id' => $userId]);
        $idFichaje = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT TIME_FORMAT(hora_entrada, '%H:%i') FROM fichajes WHERE id_fichaje = :id");
        $stmt->execute(['id' => $idFichaje]);
        $time = (string)$stmt->fetchColumn();

        $pdo->commit();

        return ['ok' => true, 'time' => $time];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo registrar la entrada.'];
    }
}

function register_exit(int $userId): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            'SELECT id_fichaje, hora_entrada
             FROM fichajes
             WHERE id_usuario = :id
               AND fecha = CURDATE()
               AND hora_salida IS NULL
             ORDER BY id_fichaje DESC
             LIMIT 1
             FOR UPDATE'
        );
        $stmt->execute(['id' => $userId]);
        $fichaje = $stmt->fetch();

        if (!$fichaje) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'No hay una entrada abierta para registrar la salida.'];
        }

        $stmt = $pdo->prepare(
            'UPDATE fichajes
             SET hora_salida = CURTIME(),
                 horas_trabajadas = ROUND(TIME_TO_SEC(TIMEDIFF(CURTIME(), hora_entrada)) / 3600, 2)
             WHERE id_fichaje = :fichaje'
        );
        $stmt->execute(['fichaje' => $fichaje['id_fichaje']]);

        $stmt = $pdo->prepare("SELECT TIME_FORMAT(hora_salida, '%H:%i') FROM fichajes WHERE id_fichaje = :id");
        $stmt->execute(['id' => $fichaje['id_fichaje']]);
        $time = (string)$stmt->fetchColumn();

        $pdo->commit();

        return ['ok' => true, 'time' => $time];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo registrar la salida.'];
    }
}

function month_hours(int $userId): float
{
    $stmt = db()->prepare(
        'SELECT COALESCE(SUM(horas_trabajadas), 0) AS total
         FROM fichajes
         WHERE id_usuario = :id
           AND fecha >= DATE_FORMAT(CURDATE(), "%Y-%m-01")
           AND fecha <= LAST_DAY(CURDATE())'
    );
    $stmt->execute(['id' => $userId]);

    return (float)$stmt->fetchColumn();
}

function recent_fichajes(int $userId, int $limit = 10): array
{
    $limit = max(1, min($limit, 50));

    $stmt = db()->prepare(
        "SELECT fecha, hora_entrada, hora_salida, horas_trabajadas, validado_admin
         FROM fichajes
         WHERE id_usuario = :id
         ORDER BY fecha DESC, id_fichaje DESC
         LIMIT $limit"
    );
    $stmt->execute(['id' => $userId]);

    return $stmt->fetchAll();
}

function my_month_fichajes(int $userId, string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $stmt = db()->prepare(
        "SELECT id_fichaje, fecha,
                TIME_FORMAT(hora_entrada, '%H:%i') AS entrada,
                TIME_FORMAT(hora_salida, '%H:%i') AS salida,
                horas_trabajadas, validado_admin, obs_validacion
         FROM fichajes
         WHERE id_usuario = :id AND DATE_FORMAT(fecha, '%Y-%m') = :mes
         ORDER BY fecha DESC, id_fichaje DESC"
    );
    $stmt->execute(['id' => $userId, 'mes' => $mes]);

    return $stmt->fetchAll();
}

function my_month_cirugias(int $userId, string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $stmt = db()->prepare(
        "SELECT c.id_cirugia, c.fecha, TIME_FORMAT(c.hora_inicio, '%H:%i') AS hora_inicio,
                COALESCE(cp.rol_en_cirugia, '') AS rol, tp.nombre AS procedimiento,
                c.observaciones
         FROM cirugia_personal cp
         INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia
         INNER JOIN tipos_procedimiento tp ON tp.id_tipo_proc = c.id_tipo_procedimiento
         WHERE cp.id_usuario = :id AND DATE_FORMAT(c.fecha, '%Y-%m') = :mes
         ORDER BY c.fecha DESC, c.hora_inicio DESC"
    );
    $stmt->execute(['id' => $userId, 'mes' => $mes]);

    return $stmt->fetchAll();
}

function my_month_novedades(int $userId, string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $inicio = $mes . '-01';
    $fin = date('Y-m-t', strtotime($inicio));
    $stmt = db()->prepare(
        'SELECT tipo, fecha_desde, fecha_hasta, observaciones
         FROM novedades
         WHERE id_usuario = :id
           AND fecha_desde <= :fin
           AND (fecha_hasta IS NULL OR fecha_hasta >= :inicio)
         ORDER BY fecha_desde DESC'
    );
    $stmt->execute(['id' => $userId, 'fin' => $fin, 'inicio' => $inicio]);

    return $stmt->fetchAll();
}

function my_month_stats(int $userId, string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $stmt = db()->prepare(
        'SELECT COUNT(*) AS fichajes, COUNT(DISTINCT fecha) AS dias,
                COALESCE(SUM(horas_trabajadas), 0) AS horas
         FROM fichajes
         WHERE id_usuario = :id AND DATE_FORMAT(fecha, "%Y-%m") = :mes'
    );
    $stmt->execute(['id' => $userId, 'mes' => $mes]);
    $f = $stmt->fetch();

    $stmt = db()->prepare(
        'SELECT COUNT(*)
         FROM cirugia_personal cp
         INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia
         WHERE cp.id_usuario = :id AND DATE_FORMAT(c.fecha, "%Y-%m") = :mes'
    );
    $stmt->execute(['id' => $userId, 'mes' => $mes]);
    $cirugias = (int)$stmt->fetchColumn();

    $inicio = $mes . '-01';
    $fin = date('Y-m-t', strtotime($inicio));
    $stmt = db()->prepare(
        'SELECT COUNT(*)
         FROM novedades
         WHERE id_usuario = :id AND fecha_desde <= :fin
           AND (fecha_hasta IS NULL OR fecha_hasta >= :inicio)'
    );
    $stmt->execute(['id' => $userId, 'fin' => $fin, 'inicio' => $inicio]);
    $novedades = (int)$stmt->fetchColumn();

    return [
        'fichajes' => (int)$f['fichajes'],
        'dias' => (int)$f['dias'],
        'horas' => (float)$f['horas'],
        'cirugias' => $cirugias,
        'novedades' => $novedades,
    ];
}

function today_cirurgies(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT c.fecha, c.hora_inicio, tp.nombre AS procedimiento, cp.rol_en_cirugia
         FROM cirugia_personal cp
         INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia
         INNER JOIN tipos_procedimiento tp ON tp.id_tipo_proc = c.id_tipo_procedimiento
         WHERE cp.id_usuario = :id AND c.fecha = CURDATE()
         ORDER BY c.hora_inicio'
    );
    $stmt->execute(['id' => $userId]);

    return $stmt->fetchAll();
}
