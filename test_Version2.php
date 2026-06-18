<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "TEST PHP OK<br>";

echo "PHP version: " . PHP_VERSION . "<br>";

echo "Loaded extensions:<br>";
echo in_array('pdo_mysql', get_loaded_extensions(), true) ? "pdo_mysql OK<br>" : "pdo_mysql MANQUANT<br>";

echo "File exists includes/auth.php: " . (file_exists(__DIR__ . '/includes/auth.php') ? 'YES' : 'NO') . "<br>";
echo "File exists config/database.php: " . (file_exists(__DIR__ . '/config/database.php') ? 'YES' : 'NO') . "<br>";

require_once __DIR__ . '/config/database.php';
echo "database.php chargé<br>";

$db = get_db();
echo "Connexion DB OK<br>";

echo "FIN TEST<br>";