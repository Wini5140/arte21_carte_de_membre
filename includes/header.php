<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'ARTE21 – Cartes de membre') ?></title>
    <link rel="stylesheet" href="<?= h($base_path ?? '') ?>assets/css/style.css">
</head>
<body>
<?php if (!empty($_SESSION['user_id'])): ?>
<header class="site-header">
    <div class="site-header__inner">
        <div class="site-header__logo">
            <span class="logo-text">ARTE<span class="logo-accent">21</span></span>
            <span class="logo-sub">Cartes de membre</span>
        </div>
        <nav class="site-nav">
            <a href="<?= h($base_path ?? '') ?>dashboard.php">Tableau de bord</a>
            <a href="<?= h($base_path ?? '') ?>create_card.php">Nouvelle carte</a>
            <a href="<?= h($base_path ?? '') ?>logout.php" class="btn btn--outline">Déconnexion</a>
        </nav>
    </div>
</header>
<?php endif; ?>
<main class="main-content">
