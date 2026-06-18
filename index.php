<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "A: index start<br>";

require_once __DIR__ . '/includes/auth.php';
echo "B: auth loaded<br>";

echo "SESSION user_id = ";
var_dump($_SESSION['user_id'] ?? null);

if (!empty($_SESSION['user_id'])) {
    echo "C: redirect dashboard<br>";
    // header('Location: dashboard.php');
    exit;
}

echo "D: redirect login<br>";
// header('Location: login.php');
exit;