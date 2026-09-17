<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

if (current_user()) {
    header('Location: ' . ((int)current_user()['es_admin'] === 1 ? 'admin/panel.php' : 'dashboard.php'));
    exit;
}

$error = '';
$errorField = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginIp = 'login:ip:' . cliente_ip();
    $loginDni = 'login:dni:' . ($_POST['dni'] ?? '');

    if (!rate_limit_consume($loginIp, 5, 900, 900)
        || !rate_limit_consume($loginDni, 5, 900, 900)) {
        $error = 'Demasiados intentos fallidos. Esperá unos minutos e intentá nuevamente.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'La sesión del formulario expiró. Intentá nuevamente.';
    } else {
        $dni = $_POST['dni'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = login_user($dni, $password);

        if ($result === 'ok') {
            rate_limit_reset($loginIp);
            rate_limit_reset($loginDni);
            registrar_auditoria((int)current_user()['id'], 'login', 'Ingreso correcto');
            header('Location: ' . ((int)current_user()['es_admin'] === 1 ? 'admin/panel.php' : 'dashboard.php'));
            exit;
        }

        registrar_auditoria(0, 'login_fallido', 'DNI: ' . $dni);
        $errorField = $result === 'dni' ? 'dni' : 'password';
        $error = $result === 'dni'
            ? 'El DNI ingresado no existe o está inactivo.'
            : 'La contraseña es incorrecta.';
    }
}

$dniValue = (string)($_POST['dni'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-compat.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="brand">
            <img src="assets/img/amemt_logo.jpg" alt="AMEMT" class="logo">
            <h1>AMEMT</h1>
            <p>Fichero Digital</p>
        </div>

        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="dni">DNI</label>
            <input id="dni" name="dni" type="text" inputmode="numeric"
                   maxlength="20" required autocomplete="username" value="<?= e($dniValue) ?>"
                   class="<?= $errorField === 'dni' ? 'field-error' : '' ?>"
                   <?= $errorField === 'dni' ? 'aria-invalid="true" autofocus' : '' ?>>
            <?php if ($error && $errorField === 'dni'): ?>
                <p class="field-message"><?= e($error) ?></p>
            <?php endif; ?>

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   class="<?= $errorField === 'password' ? 'field-error' : '' ?>"
                   <?= $errorField === 'password' ? 'aria-invalid="true" autofocus' : '' ?>>
            <?php if ($error && $errorField === 'password'): ?>
                <p class="field-message"><?= e($error) ?></p>
            <?php endif; ?>

            <?php if ($error && $errorField === ''): ?>
                <p class="alert error"><?= e($error) ?></p>
            <?php endif; ?>

            <button class="btn primary" type="submit"><?= icon('login', 18) ?>Ingresar</button>
        </form>

        <p class="help">¿Olvidaste tu contraseña? Contactá a recepción.</p>
    </main>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
