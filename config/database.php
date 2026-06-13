<?php
/**
 * Configuration de la base de données ARTE21
 * Adaptez ces valeurs à votre hébergement OVH.
 */

define('DB_HOST',   'localhost');       // Hôte MySQL OVH (ex: sql123.cluster.ovh.net)
define('DB_NAME',   'arte21_membres');  // Nom de la base de données
define('DB_USER',   'arte21_user');     // Utilisateur MySQL
define('DB_PASS',   'VotreMotDePasse'); // Mot de passe MySQL
define('DB_CHARSET','utf8mb4');

/**
 * Connexion PDO (singleton)
 */
function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
