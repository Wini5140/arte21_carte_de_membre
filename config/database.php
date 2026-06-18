<?php
/**
 * Configuration de la base de données ARTE21
 */

define('DB_HOST',   'localhost');       // Hôte MySQL OVH (ex: sql123.cluster.ovh.net)
define('DB_NAME',   'arte21_membres');  // Nom de la base de données
define('DB_USER',   'arte21_user');     // Utilisateur MySQL
define('DB_PASS',   'VotreMotDePasse'); // Mot de passe MySQL
define('DB_CHARSET','utf8mb4');

/**
 * Retourne une connexion PDO singleton.
 */
function get_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // TEMPORAIRE pour debug (retirer après correction)
        http_response_code(500);
        echo 'Erreur connexion DB : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        exit;
    }

    return $pdo;
}