<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

logout();

header('Location: index.php');
exit;
