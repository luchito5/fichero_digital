<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/admin_functions.php';
$admin=require_admin(); require_post();
if(!verify_csrf($_POST['csrf_token']??'')){ http_response_code(403); exit('Solicitud no válida.'); }
$id=(int)($_POST['id']??0);
if($id===(int)$admin['id']){ header('Location: empleados.php?error=propio'); exit; }
$result=delete_employee($id);
header('Location: empleados.php?' . ($result['ok']?'eliminado=1':'error=eliminar'));
exit;
