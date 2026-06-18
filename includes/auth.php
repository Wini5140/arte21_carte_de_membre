<?php
/**
 * Authentification – ARTE21
 */

require_once __DIR__ . '/../config/database.php';

// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

/**
 * Vérifie que l'utilisateur est connecté.
 * Redirige vers login.php sinon.
 */
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Tente de connecter l'utilisateur.
 * Retourne true en cas de succès.
 */
function login(string $username, string $password): bool
{
    $db   = get_db();
    $stmt = $db->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row  = $stmt->fetch();

    if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $row['id'];
        $_SESSION['username'] = $username;
        return true;
    }

    return false;
}

/**
 * Déconnecte l'utilisateur.
 */
function logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $p['path'],
            $p['domain'],
            $p['secure'],
            $p['httponly']
        );
    }

    session_destroy();
}

/**
 * Génère ou retourne le token CSRF de la session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Valide le token CSRF soumis.
 */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Requête invalide (CSRF).');
    }
}