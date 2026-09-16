<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';

function admin_dashboard_stats(): array
{
    $pdo = db();
    return [
        'personal_total' => (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo=1")->fetchColumn(),
        'presentes_hoy' => (int)$pdo->query("SELECT COUNT(DISTINCT id_usuario) FROM fichajes WHERE fecha=CURDATE() AND hora_entrada IS NOT NULL AND (hora_salida IS NULL OR hora_salida > CURTIME())")->fetchColumn(),
        'cirugias_hoy' => (int)$pdo->query("SELECT COUNT(*) FROM cirugias WHERE fecha=CURDATE()")->fetchColumn(),
        'alquileres_hoy' => (int)$pdo->query("SELECT COUNT(*) FROM alquileres WHERE fecha=CURDATE()")->fetchColumn()
    ];
}

function present_employees_today(): array
{
    $sql = "SELECT u.id_usuario,u.nombre,u.apellido,u.username,u.especialidad,u.tipo_contrato,
                   f.hora_entrada,f.hora_salida
            FROM fichajes f
            INNER JOIN usuarios u ON u.id_usuario=f.id_usuario
            WHERE f.fecha=CURDATE() AND f.hora_entrada IS NOT NULL
              AND (f.hora_salida IS NULL OR f.hora_salida > CURTIME())
              AND u.activo=1
            ORDER BY f.hora_entrada ASC";
    return db()->query($sql)->fetchAll();
}

function fichajes_sin_salida(): array
{
    $sql = "SELECT f.id_fichaje, f.id_usuario, u.nombre, u.apellido, u.dni, u.tipo_contrato,
                   f.fecha, TIME_FORMAT(f.hora_entrada, '%H:%i') AS hora_entrada,
                   ROUND(TIMESTAMPDIFF(MINUTE, TIMESTAMP(f.fecha, f.hora_entrada), NOW()) / 60, 1) AS horas_abiertas,
                   (TIMESTAMPDIFF(MINUTE, TIMESTAMP(f.fecha, f.hora_entrada), NOW()) > 720) AS demorado,
                   f.validado_admin
            FROM fichajes f
            INNER JOIN usuarios u ON u.id_usuario = f.id_usuario
            WHERE f.hora_entrada IS NOT NULL AND f.hora_salida IS NULL
              AND f.validado_admin = 0
            ORDER BY TIMESTAMP(f.fecha, f.hora_entrada) ASC";
    return db()->query($sql)->fetchAll();
}

function fichajes_demorados_count(): int
{
    return (int)db()->query(
        "SELECT COUNT(*)
         FROM fichajes
         WHERE hora_entrada IS NOT NULL AND hora_salida IS NULL
           AND validado_admin = 0
           AND TIMESTAMPDIFF(MINUTE, TIMESTAMP(fecha, hora_entrada), NOW()) > 720"
    )->fetchColumn();
}

function validar_fichaje(int $idFichaje, int $idAdmin): array
{
    if ($idFichaje <= 0) return ['ok' => false, 'message' => 'Fichaje inválido.'];
    try {
        db()->prepare(
            'UPDATE fichajes
             SET validado_admin = 1, validado_por = :adm,
                 obs_validacion = COALESCE(NULLIF(obs_validacion, ""), "Validado por administración")
             WHERE id_fichaje = :id'
        )->execute(['adm' => $idAdmin, 'id' => $idFichaje]);
        registrar_auditoria($idAdmin, 'fichaje_validado', 'ID ' . $idFichaje);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo validar el fichaje.'];
    }
}

function corregir_fichaje(int $idFichaje, string $entrada, string $salida, string $obs, int $idAdmin): array
{
    if ($idFichaje <= 0) return ['ok' => false, 'message' => 'Fichaje inválido.'];
    $entrada = trim($entrada);
    $salida = trim($salida);
    if (!preg_match('/^\d{2}:\d{2}$/', $entrada) || !preg_match('/^\d{2}:\d{2}$/', $salida)) {
        return ['ok' => false, 'message' => 'Completá las horas de entrada y salida (HH:MM).'];
    }
    $difMin = (strtotime('1970-01-01 ' . $salida) - strtotime('1970-01-01 ' . $entrada)) / 60;
    if ($difMin <= 0) $difMin += 1440;
    if ($difMin > 1440) {
        return ['ok' => false, 'message' => 'El registro no puede superar las 24 horas.'];
    }
    $obs = mb_substr(trim($obs), 0, 255);
    try {
        db()->prepare(
            'UPDATE fichajes
             SET hora_entrada = :e, hora_salida = :s,
                 horas_trabajadas = :hs, validado_admin = 1, validado_por = :adm,
                 obs_validacion = NULLIF(:obs, "")
             WHERE id_fichaje = :id'
        )->execute(['e' => $entrada, 's' => $salida, 'hs' => round($difMin / 60, 2), 'obs' => $obs, 'adm' => $idAdmin, 'id' => $idFichaje]);
        registrar_auditoria($idAdmin, 'fichaje_corregido', 'ID ' . $idFichaje);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo corregir el fichaje.'];
    }
}

function corregir_entrada(int $idFichaje, string $entrada, string $obs, int $idAdmin): array
{
    if ($idFichaje <= 0) return ['ok' => false, 'message' => 'Fichaje inválido.'];
    $entrada = trim($entrada);
    if (!preg_match('/^\d{2}:\d{2}$/', $entrada)) {
        return ['ok' => false, 'message' => 'Completá la hora de entrada (HH:MM).'];
    }
    $obs = mb_substr(trim($obs), 0, 255);
    try {
        db()->prepare(
            'UPDATE fichajes
             SET hora_entrada = :e, validado_admin = 1, validado_por = :adm,
                 obs_validacion = NULLIF(:obs, "")
             WHERE id_fichaje = :id'
        )->execute(['e' => $entrada, 'obs' => $obs, 'adm' => $idAdmin, 'id' => $idFichaje]);
        registrar_auditoria($idAdmin, 'fichaje_entrada_corregida', 'ID ' . $idFichaje);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo corregir la entrada.'];
    }
}

function list_employees(string $search = '', string $contract = ''): array
{
    $sql = "SELECT u.id_usuario,u.nombre,u.apellido,u.dni,u.especialidad,u.tipo_contrato,u.username,u.activo,
                   tp.nombre AS tipo_personal
            FROM usuarios u
            LEFT JOIN tipos_personal tp ON tp.id_tipo=u.id_tipo_personal
            WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $sql .= " AND (u.nombre LIKE :search1 OR u.apellido LIKE :search2 OR u.username LIKE :search3 OR u.dni LIKE :search4)";
        
        $value = '%' . $search . '%';
        $params['search1'] = $value;
        $params['search2'] = $value;
        $params['search3'] = $value;
        $params['search4'] = $value;
    }

    if ($contract !== '') {
        $sql .= " AND u.tipo_contrato=:contract";
        $params['contract'] = $contract;
    }
    $sql .= " ORDER BY u.apellido,u.nombre";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function personal_types(): array
{
    return db()->query("SELECT id_tipo,nombre FROM tipos_personal ORDER BY nombre")->fetchAll();
}

function find_employee(int $id): ?array
{
    $stmt = db()->prepare("SELECT * FROM usuarios WHERE id_usuario=:id LIMIT 1");
    $stmt->execute(['id'=>$id]);
    return $stmt->fetch() ?: null;
}

function create_employee(array $data): array
{
    $required = ['nombre','apellido','dni','id_tipo_personal','tipo_contrato','password'];
    foreach ($required as $key) if (trim((string)($data[$key] ?? '')) === '') return ['ok'=>false,'message'=>'Completá todos los campos obligatorios.'];

    $contract = trim((string)$data['tipo_contrato']);
    $allowed = ['Fijo','Por Hora','Por Cirugía','Alquiler','Admin'];
    if (!in_array($contract,$allowed,true)) return ['ok'=>false,'message'=>'Tipo de contrato no válido.'];

    if (strlen((string)$data['password']) < 8) return ['ok'=>false,'message'=>'La contraseña temporal debe tener al menos 8 caracteres.'];

    $stmt = db()->prepare('SELECT nombre FROM tipos_personal WHERE id_tipo=:id LIMIT 1');
    $stmt->execute(['id'=>(int)$data['id_tipo_personal']]);
    $especialidad = $stmt->fetchColumn();
    if ($especialidad === false) return ['ok'=>false,'message'=>'Rol / especialidad no válido.'];

    $pdo=db();
    try {
        $dni = trim((string)$data['dni']);
        $stmt=$pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE dni=:dni OR username=:username');
        $stmt->execute(['dni'=>$dni,'username'=>$dni]);
        if ((int)$stmt->fetchColumn()>0) return ['ok'=>false,'message'=>'El DNI ya existe.'];

            $stmd=$pdo->prepare('INSERT INTO usuarios (nombre,apellido,dni,especialidad,id_tipo_personal,tipo_contrato,username,password_hash) VALUES (:nombre, :apellido, :dni, :especialidad, :tipo, :contrato, :username, :password)');
            $stmd->execute([
            'nombre' => trim($data['nombre']),
            'apellido' => trim($data['apellido']),
            'dni' => $dni,
            'especialidad' => $especialidad,
            'tipo' => (int)$data['id_tipo_personal'],
            'contrato' => $contract,
            'username' => $dni,
            'password' => password_hash(trim($data['password']), PASSWORD_DEFAULT)
    ]);
    
    $idNuevo = (int)$pdo->lastInsertId();
    registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'empleado_creado', 'ID ' . $idNuevo . ' - ' . trim($data['nombre']) . ' ' . trim($data['apellido']));
    return ['ok'=>true, 'id'=>$idNuevo];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok'=>false,'message'=>'No se pudo crear el empleado.'];
    }
}

function update_employee(int $id,array $data): array
{
    $employee=find_employee($id);
    if (!$employee) return ['ok'=>false,'message'=>'Empleado no encontrado.'];
    $allowed=['Fijo','Por Hora','Por Cirugía','Alquiler','Admin'];
    if (!in_array((string)$data['tipo_contrato'],$allowed,true)) return ['ok'=>false,'message'=>'Tipo de contrato no válido.'];
    $tipo = (int)$data['id_tipo_personal'];
    $stmt=db()->prepare('SELECT nombre FROM tipos_personal WHERE id_tipo=:id LIMIT 1');
    $stmt->execute(['id'=>$tipo]);
    $especialidad = $stmt->fetchColumn();
    if ($especialidad === false) return ['ok'=>false,'message'=>'Rol / especialidad no válido.'];
    $dni = trim((string)$data['dni']);
    if ($dni === '') return ['ok'=>false,'message'=>'Completá el DNI.'];
    try {
        $stmt=db()->prepare('UPDATE usuarios SET nombre=:nombre,apellido=:apellido,dni=:dni,especialidad=:especialidad,id_tipo_personal=:tipo,tipo_contrato=:contrato,username=:username,activo=:activo WHERE id_usuario=:id');
        $stmt->execute([
            'nombre'=>trim($data['nombre']),'apellido'=>trim($data['apellido']),'dni'=>$dni,
            'especialidad'=>$especialidad,'tipo'=>$tipo,'contrato'=>$data['tipo_contrato'],
            'username'=>$dni,'activo'=>(int)($data['activo'] ?? $employee['activo']),'id'=>$id
        ]);
        if (trim((string)($data['password']??'')) !== '') {
            if (strlen($data['password'])<8) return ['ok'=>false,'message'=>'La nueva contraseña debe tener al menos 8 caracteres.'];
            $stmt=db()->prepare('UPDATE usuarios SET password_hash=:password WHERE id_usuario=:id');
            $stmt->execute(['password' => password_hash(trim($data['password']), PASSWORD_DEFAULT), 'id' => $id]);
        }
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'empleado_editado', 'ID ' . $id);
        return ['ok'=>true];
    } catch(PDOException $e){ error_log($e->getMessage()); return ['ok'=>false,'message'=>'No se pudo actualizar el empleado.']; }
}

function toggle_employee_status(int $id): array
{
    $employee=find_employee($id);
    if (!$employee) return ['ok'=>false,'message'=>'Empleado no encontrado.'];
    if ((int)$employee['es_admin']===1) return ['ok'=>false,'message'=>'No se puede desactivar un administrador desde este módulo.'];
    try {
        db()->prepare('UPDATE usuarios SET activo = 1 - activo WHERE id_usuario=:id')->execute(['id'=>$id]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'empleado_estado', 'ID ' . $id . ' -> ' . ((int)$employee['activo']===1?'inactivo':'activo'));
        return ['ok'=>true];
    } catch(PDOException $e){ error_log($e->getMessage()); return ['ok'=>false,'message'=>'No se pudo cambiar el estado.']; }
}

function delete_employee(int $id): array
{
    $employee=find_employee($id);
    if (!$employee) return ['ok'=>false,'message'=>'Empleado no encontrado.'];
    if ((int)$employee['es_admin']===1) return ['ok'=>false,'message'=>'No se puede eliminar un administrador desde este módulo.'];
    try {
        $pdo=db();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM fichajes WHERE id_usuario=:id OR validado_por=:id2')->execute(['id'=>$id,'id2'=>$id]);
        $pdo->prepare('DELETE FROM novedades WHERE id_usuario=:id OR registrado_por=:id2')->execute(['id'=>$id,'id2'=>$id]);
        $pdo->prepare('DELETE FROM cirugia_personal WHERE id_usuario=:id')->execute(['id'=>$id]);
        $pdo->prepare('DELETE FROM cirugias WHERE registrado_por=:id')->execute(['id'=>$id]);
        $pdo->prepare('DELETE FROM alquileres WHERE id_usuario=:id OR registrado_por=:id2')->execute(['id'=>$id,'id2'=>$id]);
        $pdo->prepare('DELETE FROM usuarios WHERE id_usuario=:id')->execute(['id'=>$id]);
        $pdo->commit();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'empleado_eliminado', 'ID ' . $id);
        return ['ok'=>true];
    } catch(PDOException $e){ if($pdo->inTransaction()) $pdo->rollBack(); $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); error_log($e->getMessage()); return ['ok'=>false,'message'=>'No se pudo eliminar el empleado.']; }
}

function contract_class(?string $contract): string
{
    return match ($contract) {
        'Por Hora' => 'hour',
        'Por Cirugía' => 'surgery',
        'Alquiler' => 'rental',
        'Admin' => 'admin',
        default => 'fixed',
    };
}

function active_employees(): array
{
    return db()->query(
        "SELECT u.id_usuario, u.nombre, u.apellido, u.especialidad, u.tipo_contrato
         FROM usuarios u
         WHERE u.activo = 1
         ORDER BY u.apellido, u.nombre"
    )->fetchAll();
}

function employees_by_role(array $keywords): array
{
    $like = [];
    $params = [];
    $i = 1;
    foreach ($keywords as $kw) {
        $like[] = 'COALESCE(u.especialidad,\'\') LIKE :k' . $i;
        $like[] = 'COALESCE(tp.nombre,\'\') LIKE :t' . $i;
        $params['k' . $i] = '%' . $kw . '%';
        $params['t' . $i] = '%' . $kw . '%';
        $i++;
    }
    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.especialidad, u.tipo_contrato
            FROM usuarios u
            LEFT JOIN tipos_personal tp ON tp.id_tipo = u.id_tipo_personal
            WHERE u.activo = 1 AND (" . implode(' OR ', $like) . ")
            ORDER BY u.apellido, u.nombre";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function employees_other(array $exclude): array
{
    $not = [];
    $params = [];
    $i = 1;
    foreach ($exclude as $kw) {
        $not[] = 'COALESCE(u.especialidad,\'\') NOT LIKE :k' . $i;
        $not[] = 'COALESCE(tp.nombre,\'\') NOT LIKE :t' . $i;
        $params['k' . $i] = '%' . $kw . '%';
        $params['t' . $i] = '%' . $kw . '%';
        $i++;
    }
    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.especialidad, u.tipo_contrato
            FROM usuarios u
            LEFT JOIN tipos_personal tp ON tp.id_tipo = u.id_tipo_personal
            WHERE u.activo = 1 AND (" . implode(' AND ', $not) . ")
            ORDER BY u.apellido, u.nombre";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function procedure_types(): array
{
    return db()->query(
        "SELECT id_tipo_proc, nombre FROM tipos_procedimiento WHERE activo = 1 ORDER BY nombre"
    )->fetchAll();
}

function create_procedure_type(string $nombre): array
{
    $nombre = trim($nombre);
    if ($nombre === '') return ['ok' => false, 'message' => 'Ingresá un nombre.'];
    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM tipos_procedimiento WHERE nombre = :n');
        $stmt->execute(['n' => $nombre]);
        if ((int)$stmt->fetchColumn() > 0) return ['ok' => false, 'message' => 'Ese tipo de procedimiento ya existe.'];
        db()->prepare('INSERT INTO tipos_procedimiento (nombre) VALUES (:n)')->execute(['n' => $nombre]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'procedimiento_creado', $nombre);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo crear el tipo de procedimiento.'];
    }
}

function delete_procedure_type(int $id): array
{
    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM cirugias WHERE id_tipo_procedimiento = :id');
        $stmt->execute(['id' => $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            return ['ok' => false, 'message' => 'No se puede eliminar: hay cirugías registradas con este tipo de procedimiento.'];
        }
        db()->prepare('DELETE FROM tipos_procedimiento WHERE id_tipo_proc = :id')->execute(['id' => $id]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'procedimiento_eliminado', 'ID ' . $id);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo eliminar el tipo de procedimiento.'];
    }
}

function list_cirugias(?string $mes = null): array
{
    $mes = $mes && preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $sql = "SELECT c.id_cirugia, c.fecha, TIME_FORMAT(c.hora_inicio, '%H:%i') AS hora_inicio,
                   c.observaciones, tp.nombre AS procedimiento,
                   GROUP_CONCAT(CONCAT(u.nombre, ' ', u.apellido, ' (', COALESCE(cp.rol_en_cirugia, ''), ')') SEPARATOR ', ') AS personal
            FROM cirugias c
            INNER JOIN tipos_procedimiento tp ON tp.id_tipo_proc = c.id_tipo_procedimiento
            LEFT JOIN cirugia_personal cp ON cp.id_cirugia = c.id_cirugia
            LEFT JOIN usuarios u ON u.id_usuario = cp.id_usuario
            WHERE DATE_FORMAT(c.fecha, '%Y-%m') = :mes
            GROUP BY c.id_cirugia, c.fecha, c.hora_inicio, c.observaciones, tp.nombre
            ORDER BY c.fecha DESC, c.hora_inicio DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute(['mes' => $mes]);
    return $stmt->fetchAll();
}

function create_cirugia(array $data, array $personal): array
{
    $required = ['fecha', 'hora_inicio', 'tipo_procedimiento'];
    foreach ($required as $key) if (trim((string)($data[$key] ?? '')) === '') return ['ok' => false, 'message' => 'Completá fecha, hora y tipo de procedimiento.'];

    $personal = array_values(array_filter($personal, fn($row) => !empty($row['id_usuario'])));
    if (!$personal) return ['ok' => false, 'message' => 'Seleccioná al menos un participante.'];

    $pdo = db();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO cirugias (fecha, hora_inicio, id_tipo_procedimiento, observaciones, registrado_por) VALUES (:fecha, :hora, :tipo, :obs, :admin)');
        $stmt->execute([
            'fecha' => trim($data['fecha']),
            'hora' => trim($data['hora_inicio']),
            'tipo' => (int)$data['tipo_procedimiento'],
            'obs' => trim((string)($data['observaciones'] ?? '')),
            'admin' => (int)($_SESSION['user']['id'] ?? 0),
        ]);
        $idCirugia = (int)$pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO cirugia_personal (id_cirugia, id_usuario, rol_en_cirugia) VALUES (:c, :u, :rol)');
        foreach ($personal as $row) {
            $stmt->execute([
                'c' => $idCirugia,
                'u' => (int)$row['id_usuario'],
                'rol' => trim((string)($row['rol'] ?? 'Participante')),
            ]);
        }
        $pdo->commit();
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'cirugia_creada', 'ID ' . $idCirugia);
        return ['ok' => true, 'id' => $idCirugia];
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo registrar la cirugía.'];
    }
}

function delete_cirugia(int $id): array
{
    try {
        db()->prepare('DELETE FROM cirugias WHERE id_cirugia = :id')->execute(['id' => $id]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'cirugia_eliminada', 'ID ' . $id);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo eliminar la cirugía.'];
    }
}

function list_novedades(): array
{
    return db()->query(
        "SELECT n.id_novedad, n.tipo, n.fecha_desde, n.fecha_hasta, n.observaciones,
                u.nombre, u.apellido
         FROM novedades n
         INNER JOIN usuarios u ON u.id_usuario = n.id_usuario
         ORDER BY n.fecha_desde DESC, n.id_novedad DESC"
    )->fetchAll();
}

function create_novedad(array $data): array
{
    $required = ['id_usuario', 'tipo', 'fecha_desde'];
    foreach ($required as $key) if (trim((string)($data[$key] ?? '')) === '') return ['ok' => false, 'message' => 'Completá empleado, tipo y fecha desde.'];
    try {
        $stmt = db()->prepare('INSERT INTO novedades (id_usuario, tipo, fecha_desde, fecha_hasta, observaciones, registrado_por) VALUES (:u, :tipo, :desde, :hasta, :obs, :admin)');
        $stmt->execute([
            'u' => (int)$data['id_usuario'],
            'tipo' => trim((string)$data['tipo']),
            'desde' => trim((string)$data['fecha_desde']),
            'hasta' => trim((string)($data['fecha_hasta'] ?? '')) !== '' ? trim((string)$data['fecha_hasta']) : null,
            'obs' => trim((string)($data['observaciones'] ?? '')),
            'admin' => (int)($_SESSION['user']['id'] ?? 0),
        ]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'novedad_creada', $data['tipo']);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo registrar la novedad.'];
    }
}

function delete_novedad(int $id): array
{
    try {
        db()->prepare('DELETE FROM novedades WHERE id_novedad = :id')->execute(['id' => $id]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'novedad_eliminada', 'ID ' . $id);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo eliminar la novedad.'];
    }
}

function list_alquileres(): array
{
    return db()->query(
        "SELECT a.id_alquiler, a.fecha, TIME_FORMAT(a.hora_entrada, '%H:%i') AS hora_entrada,
                TIME_FORMAT(a.hora_salida, '%H:%i') AS hora_salida, a.horas_uso, a.dato_facturacion,
                COALESCE(CONCAT(u.nombre, ' ', u.apellido), a.nombre_responsable) AS responsable,
                a.institucion
         FROM alquileres a
         LEFT JOIN usuarios u ON u.id_usuario = a.id_usuario
         ORDER BY a.fecha DESC, a.id_alquiler DESC"
    )->fetchAll();
}

function alquileres_stats(): array
{
    return [
        'hoy' => (int)db()->query("SELECT COUNT(*) FROM alquileres WHERE fecha = CURDATE()")->fetchColumn(),
        'mes' => (int)db()->query("SELECT COUNT(*) FROM alquileres WHERE DATE_FORMAT(fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn(),
        'hs_mes' => (float)db()->query("SELECT COALESCE(SUM(horas_uso), 0) FROM alquileres WHERE DATE_FORMAT(fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn(),
    ];
}

function create_alquiler(array $data): array
{
    if (trim((string)($data['nombre_responsable'] ?? '')) === '') return ['ok' => false, 'message' => 'Completá el nombre del responsable.'];
    if (trim((string)($data['fecha'] ?? '')) === '') return ['ok' => false, 'message' => 'Completá la fecha.'];

    $entrada = trim((string)($data['hora_entrada'] ?? ''));
    $salida = trim((string)($data['hora_salida'] ?? ''));
    $horas = null;
    if ($entrada !== '' && $salida !== '') {
        if (strtotime($salida) <= strtotime($entrada)) {
            return ['ok' => false, 'message' => 'La hora de salida debe ser posterior a la hora de entrada.'];
        }
        $horas = round((strtotime($salida) - strtotime($entrada)) / 3600, 2);
    }

    try {
        $stmt = db()->prepare('INSERT INTO alquileres (id_usuario, nombre_responsable, institucion, fecha, hora_entrada, hora_salida, horas_uso, dato_facturacion, registrado_por) VALUES (NULL, :nombre, :inst, :fecha, :entrada, :salida, :hs, :fact, :admin)');
        $stmt->execute([
            'nombre' => trim((string)$data['nombre_responsable']),
            'inst' => trim((string)($data['institucion'] ?? '')),
            'fecha' => trim((string)$data['fecha']),
            'entrada' => trim((string)($data['hora_entrada'] ?? '')) !== '' ? trim((string)$data['hora_entrada']) : null,
            'salida' => trim((string)($data['hora_salida'] ?? '')) !== '' ? trim((string)$data['hora_salida']) : null,
            'hs' => $horas,
            'fact' => trim((string)($data['dato_facturacion'] ?? '')),
            'admin' => (int)($_SESSION['user']['id'] ?? 0),
        ]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'alquiler_creado', trim((string)$data['nombre_responsable']));
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo registrar el alquiler.'];
    }
}

function delete_alquiler(int $id): array
{
    try {
        db()->prepare('DELETE FROM alquileres WHERE id_alquiler = :id')->execute(['id' => $id]);
        registrar_auditoria((int)($_SESSION['user']['id'] ?? 0), 'alquiler_eliminado', 'ID ' . $id);
        return ['ok' => true];
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'No se pudo eliminar el alquiler.'];
    }
}

function resumen_mensual(string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    $stmt = db()->prepare(
        "SELECT u.id_usuario, u.nombre, u.apellido, u.tipo_contrato,
                (SELECT COUNT(*) FROM cirugia_personal cp
                 INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia
                 WHERE cp.id_usuario = u.id_usuario
                   AND DATE_FORMAT(c.fecha, '%Y-%m') = :mes1) AS cirugias,
                (SELECT COALESCE(SUM(f.horas_trabajadas), 0) FROM fichajes f
                 WHERE f.id_usuario = u.id_usuario
                   AND DATE_FORMAT(f.fecha, '%Y-%m') = :mes2) AS horas
         FROM usuarios u
         WHERE u.activo = 1
         ORDER BY u.apellido, u.nombre"
    );
    $stmt->execute(['mes1' => $mes, 'mes2' => $mes]);
    return $stmt->fetchAll();
}

function resumen_totales(string $mes): array
{
    $mes = preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    return [
        'mes' => $mes,
        'horas' => (float)db()->query("SELECT COALESCE(SUM(horas_trabajadas),0) FROM fichajes WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'")->fetchColumn(),
        'dias_habiles' => (float)db()->query("SELECT COALESCE(SUM(CASE WHEN DAYOFWEEK(fecha) BETWEEN 2 AND 6 THEN 1 ELSE 0 END),0) FROM fichajes WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'")->fetchColumn(),
        'empleados' => (int)db()->query('SELECT COUNT(*) FROM usuarios WHERE activo = 1')->fetchColumn(),
        'cirugias' => (int)db()->query("SELECT COUNT(*) FROM cirugias WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'")->fetchColumn(),
    ];
}

function system_config(): array
{
    $row = db()->query('SELECT * FROM configuracion_sistema ORDER BY id_config LIMIT 1')->fetch();
    if (!$row) {
        db()->exec('INSERT INTO configuracion_sistema (nombre_institucion) VALUES (NULL)');
        $row = db()->query('SELECT * FROM configuracion_sistema ORDER BY id_config LIMIT 1')->fetch();
    }
    return $row;
}

function invalidate_all_sessions(int $currentUserId): void
{
    try {
        $stmt = db()->query("SELECT COUNT(*) FROM fichajes WHERE fecha = CURDATE() AND hora_entrada IS NOT NULL AND hora_salida IS NULL");
        $abiertos = (int)$stmt->fetchColumn();

        db()->exec('UPDATE usuarios SET sesion_token = NULL');
        if ($abiertos > 0) {
            db()->exec(
                "UPDATE fichajes
                 SET hora_salida = CURTIME(),
                     horas_trabajadas = ROUND(TIME_TO_SEC(TIMEDIFF(CURTIME(), hora_entrada)) / 3600, 2)
                 WHERE fecha = CURDATE() AND hora_entrada IS NOT NULL AND hora_salida IS NULL"
            );
        }
        registrar_auditoria($currentUserId, 'sesiones_invalidadas', 'Todas las sesiones - ' . $abiertos . ' fichajes cerrados');
        if ($currentUserId > 0) {
            $token = bin2hex(random_bytes(32));
            db()->prepare('UPDATE usuarios SET sesion_token = :t WHERE id_usuario = :id')->execute(['t' => $token, 'id' => $currentUserId]);
            $_SESSION['sesion_token'] = $token;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

function empleados_filtro_resumen(string $mes): array
{
    $inicio = $mes . '-01';
    $fin = date('Y-m-t', strtotime($inicio));
    $sql = "SELECT u.id_usuario, CONCAT(u.apellido, ', ', u.nombre) AS nombre
            FROM usuarios u
            WHERE u.activo = 1
               OR EXISTS (SELECT 1 FROM fichajes f WHERE f.id_usuario = u.id_usuario AND DATE_FORMAT(f.fecha, '%Y-%m') = :mes)
               OR EXISTS (SELECT 1 FROM novedades n WHERE n.id_usuario = u.id_usuario AND n.fecha_desde <= :fin AND (n.fecha_hasta IS NULL OR n.fecha_hasta >= :inicio))
               OR EXISTS (SELECT 1 FROM cirugia_personal cp
                          INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia AND DATE_FORMAT(c.fecha, '%Y-%m') = :mes2
                          WHERE cp.id_usuario = u.id_usuario)
            ORDER BY u.apellido, u.nombre";
    $stmt = db()->prepare($sql);
    $stmt->execute(['mes' => $mes, 'fin' => $fin, 'inicio' => $inicio, 'mes2' => $mes]);
    return $stmt->fetchAll();
}

function resumen_por_empleado(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $w = ['1=1'];
    $p = [];
    if ($idUsuario > 0) {
        $w[] = 'u.id_usuario = :id';
        $p['id'] = $idUsuario;
    }
    if ($contrato !== '') {
        $w[] = 'u.tipo_contrato = :c';
        $p['c'] = $contrato;
    }
    $where = ' AND ' . implode(' AND ', $w);

    $activo = $idUsuario > 0 ? '1=1' : 'u.activo = 1';
    $stmt = db()->prepare(
        "SELECT u.id_usuario, u.nombre, u.apellido, u.dni, u.tipo_contrato, COALESCE(u.especialidad, '') AS especialidad
         FROM usuarios u
         WHERE " . $activo . $where . "
         ORDER BY u.apellido, u.nombre"
    );
    $stmt->execute($p);
    $rows = [];
    foreach ($stmt->fetchAll() as $r) {
        $rows[$r['id_usuario']] = $r + ['fichajes' => 0, 'horas' => 0, 'cirugias' => 0, 'novedades' => 0];
    }
    if (!$rows) {
        return [];
    }

    $inicio = $mes . '-01';
    $fin = date('Y-m-t', strtotime($inicio));

    $stmt = db()->prepare(
        "SELECT f.id_usuario, COUNT(*) AS fichajes, COALESCE(SUM(f.horas_trabajadas), 0) AS horas
         FROM fichajes f
         INNER JOIN usuarios u ON u.id_usuario = f.id_usuario
         WHERE DATE_FORMAT(f.fecha, '%Y-%m') = :mes" . $where . "
         GROUP BY f.id_usuario"
    );
    $stmt->execute($p + ['mes' => $mes]);
    foreach ($stmt->fetchAll() as $r) {
        if (isset($rows[$r['id_usuario']])) {
            $rows[$r['id_usuario']]['fichajes'] = (int)$r['fichajes'];
            $rows[$r['id_usuario']]['horas'] = (float)$r['horas'];
        }
    }

    $stmt = db()->prepare(
        "SELECT cp.id_usuario, COUNT(DISTINCT c.id_cirugia) AS cirugias
         FROM cirugia_personal cp
         INNER JOIN cirugias c ON c.id_cirugia = cp.id_cirugia
         INNER JOIN usuarios u ON u.id_usuario = cp.id_usuario
         WHERE DATE_FORMAT(c.fecha, '%Y-%m') = :mes" . $where . "
         GROUP BY cp.id_usuario"
    );
    $stmt->execute($p + ['mes' => $mes]);
    foreach ($stmt->fetchAll() as $r) {
        if (isset($rows[$r['id_usuario']])) {
            $rows[$r['id_usuario']]['cirugias'] = (int)$r['cirugias'];
        }
    }

    $stmt = db()->prepare(
        "SELECT n.id_usuario, COUNT(*) AS novedades
         FROM novedades n
         INNER JOIN usuarios u ON u.id_usuario = n.id_usuario
         WHERE n.fecha_desde <= :fin AND (n.fecha_hasta IS NULL OR n.fecha_hasta >= :inicio)" . $where . "
         GROUP BY n.id_usuario"
    );
    $stmt->execute($p + ['fin' => $fin, 'inicio' => $inicio]);
    foreach ($stmt->fetchAll() as $r) {
        if (isset($rows[$r['id_usuario']])) {
            $rows[$r['id_usuario']]['novedades'] = (int)$r['novedades'];
        }
    }

    return array_values($rows);
}

function resumen_fichajes(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $w = ['1=1'];
    $p = ['mes' => $mes];
    if ($idUsuario > 0) {
        $w[] = 'f.id_usuario = :id';
        $p['id'] = $idUsuario;
    }
    if ($contrato !== '') {
        $w[] = 'u.tipo_contrato = :c';
        $p['c'] = $contrato;
    }
    $sql = "SELECT f.id_fichaje, f.fecha,
                   TIME_FORMAT(f.hora_entrada, '%H:%i') AS entrada,
                   TIME_FORMAT(f.hora_salida, '%H:%i') AS salida,
                   f.horas_trabajadas, f.validado_admin,
                   CONCAT(u.nombre, ' ', u.apellido) AS empleado, u.tipo_contrato
            FROM fichajes f
            INNER JOIN usuarios u ON u.id_usuario = f.id_usuario
            WHERE DATE_FORMAT(f.fecha, '%Y-%m') = :mes AND " . implode(' AND ', $w) . "
            ORDER BY f.fecha DESC, u.apellido, f.hora_entrada";
    $stmt = db()->prepare($sql);
    $stmt->execute($p);
    return $stmt->fetchAll();
}

function resumen_cirugias(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $filters = '';
    $p = ['mes' => $mes];
    if ($idUsuario > 0) {
        $filters .= " AND EXISTS (SELECT 1 FROM cirugia_personal cx WHERE cx.id_cirugia = c.id_cirugia AND cx.id_usuario = :cid)";
        $p['cid'] = $idUsuario;
    }
    if ($contrato !== '') {
        $filters .= " AND EXISTS (SELECT 1 FROM cirugia_personal cx INNER JOIN usuarios cu ON cu.id_usuario = cx.id_usuario WHERE cx.id_cirugia = c.id_cirugia AND cu.tipo_contrato = :cc)";
        $p['cc'] = $contrato;
    }
    $sql = "SELECT c.id_cirugia, c.fecha, TIME_FORMAT(c.hora_inicio, '%H:%i') AS hora_inicio,
                   c.observaciones, tp.nombre AS procedimiento,
                   GROUP_CONCAT(DISTINCT CONCAT(u2.nombre, ' ', u2.apellido, ' (', COALESCE(cp.rol_en_cirugia, ''), ')') SEPARATOR ', ') AS personal
            FROM cirugias c
            INNER JOIN tipos_procedimiento tp ON tp.id_tipo_proc = c.id_tipo_procedimiento
            LEFT JOIN cirugia_personal cp ON cp.id_cirugia = c.id_cirugia
            LEFT JOIN usuarios u2 ON u2.id_usuario = cp.id_usuario
            WHERE DATE_FORMAT(c.fecha, '%Y-%m') = :mes" . $filters . "
            GROUP BY c.id_cirugia, c.fecha, c.hora_inicio, c.observaciones, tp.nombre
            ORDER BY c.fecha DESC, c.hora_inicio DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute($p);
    return $stmt->fetchAll();
}

function resumen_alquileres(string $mes): array
{
    $stmt = db()->prepare(
        "SELECT a.id_alquiler, a.fecha,
                TIME_FORMAT(a.hora_entrada, '%H:%i') AS entrada,
                TIME_FORMAT(a.hora_salida, '%H:%i') AS salida,
                a.horas_uso, a.dato_facturacion, a.institucion,
                COALESCE(CONCAT(u.nombre, ' ', u.apellido), a.nombre_responsable) AS responsable
         FROM alquileres a
         LEFT JOIN usuarios u ON u.id_usuario = a.id_usuario
         WHERE DATE_FORMAT(a.fecha, '%Y-%m') = :mes
         ORDER BY a.fecha DESC, a.id_alquiler DESC"
    );
    $stmt->execute(['mes' => $mes]);
    return $stmt->fetchAll();
}

function resumen_novedades(string $mes, int $idUsuario = 0, string $contrato = ''): array
{
    $inicio = $mes . '-01';
    $fin = date('Y-m-t', strtotime($inicio));
    $w = ['n.fecha_desde <= :fin', '(n.fecha_hasta IS NULL OR n.fecha_hasta >= :inicio)'];
    $p = ['fin' => $fin, 'inicio' => $inicio];
    if ($idUsuario > 0) {
        $w[] = 'n.id_usuario = :id';
        $p['id'] = $idUsuario;
    }
    if ($contrato !== '') {
        $w[] = 'u.tipo_contrato = :c';
        $p['c'] = $contrato;
    }
    $sql = "SELECT n.id_novedad, n.tipo, n.fecha_desde, n.fecha_hasta, n.observaciones,
                   CONCAT(u.nombre, ' ', u.apellido) AS empleado, u.tipo_contrato
            FROM novedades n
            INNER JOIN usuarios u ON u.id_usuario = n.id_usuario
            WHERE " . implode(' AND ', $w) . "
            ORDER BY n.fecha_desde, u.apellido";
    $stmt = db()->prepare($sql);
    $stmt->execute($p);
    return $stmt->fetchAll();
}
