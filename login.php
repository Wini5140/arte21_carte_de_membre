<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php'; // <- nécessaire pour h()

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Veuillez saisir votre identifiant et votre mot de passe.';
    } elseif (!login($username, $password)) {
        // Délai anti-bruteforce
        sleep(1);
        $error = 'Identifiant ou mot de passe incorrect.';
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

$page_title = 'ARTE21 – Connexion';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <span class="logo-text">ARTE<span class="logo-accent">21</span></span>
        </div>
        <h1 class="login-title">Accès sécurisé</h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert--error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username"
                       value="<?= h($_POST['username'] ?? '') ?>"
                       required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn--primary btn--full">Se connecter</button>
        </form>
    </div>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>