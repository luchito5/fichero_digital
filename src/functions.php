<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';

/**
 * Cantidad de fichajes (entradas + salidas) del usuario en el día de hoy.
 */
function today_fichaje_event_count(int $userId): int
{
    $stmt = db()->prepare(
        'SELECT COALESCE(SUM(hora_entrada IS NOT NULL), 0)
              + COALESCE(SUM(hora_salida IS NOT NULL), 0) AS eventos
         FROM fichajes
         WHERE id_usuario = :id AND fecha = CURDATE()
           AND (hora_entrada IS NOT NULL OR hora_salida IS NOT NULL)'
    );
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();

    return $row ? (int)$row['eventos'] : 0;
}

/**
 * Seguridad anti-spam: al alcanzar un múltiplo de FICHAJE_EVENTOS_POR_CORTE fichajes
 * del día, el fichaje queda cortado durante FICHAJE_CORTE_SEGUNDOS desde el último registro.
 * Ej.: con 10 fichajes el corte dura 3 minutos; el próximo corte es en la fichada 20, 30, etc.
 *
 * Devuelve los segundos que faltan para poder fichar (0 si está permitido).
 */
function fichaje_cooldown_restante(int $userId): int
{
    $stmt = db()->prepare(
        'SELECT COALESCE(SUM(hora_entrada IS NOT NULL), 0)
              + COALESCE(SUM(hora_salida IS NOT NULL), 0) AS eventos,
                UNIX_TIMESTAMP(MAX(TIMESTAMP(fecha, COALESCE(hora_salida, hora_entrada)))) AS ultimo_ts,
                UNIX_TIMESTAMP(NOW()) AS ahora_ts
         FROM fichajes
         WHERE id_usuario = :id AND fecha = CURDATE()
           AND (hora_entrada IS NOT NULL OR hora_salida IS NOT NULL)'
    );
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();

    $eventos = $row ? (int)$row['eventos'] : 0;
    if ($eventos <= 0 || $eventos % FICHAJE_EVENTOS_POR_CORTE !== 0) {
        return 0;
    }

    $ultimo = $row ? (int)$row['ultimo_ts'] : 0;
    if ($ultimo <= 0) {
        return 0;
    }

    $restante = $ultimo + FICHAJE_CORTE_SEGUNDOS - (int)$row['ahora_ts'];

    return $restante > 0 ? $restante : 0;
}

function fichaje_cooldown_mensaje(int $restante): string
{
    $restante = max(1, $restante);
    $min = (int)floor($restante / 60);
    $seg = $restante - ($min * 60);

    return 'Alcanzaste el límite de ' . FICHAJE_EVENTOS_POR_CORTE
        . ' fichajes por día. Podés volver a fichar en ' . $min . ':' . str_pad((string)$seg, 2, '0', STR_PAD_LEFT) . '.';
}

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

        $cooldown = fichaje_cooldown_restante($userId);
        if ($cooldown > 0) {
            $pdo->rollBack();
            return ['ok' => false, 'cooldown' => true, 'retry_after' => $cooldown, 'message' => fichaje_cooldown_mensaje($cooldown)];
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

        $cooldown = fichaje_cooldown_restante($userId);
        if ($cooldown > 0) {
            $pdo->rollBack();
            return ['ok' => false, 'cooldown' => true, 'retry_after' => $cooldown, 'message' => fichaje_cooldown_mensaje($cooldown)];
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
