<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$id     = (int) ($_GET['id'] ?? 0);
$membre = $id > 0 ? get_membre($id) : null;

if (!$membre) {
    http_response_code(404);
    $page_title = 'ARTE21 – Carte introuvable';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container"><div class="alert alert--error">Carte introuvable.</div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$print_mode = isset($_GET['print']) && $_GET['print'] === '1';
$page_title = 'ARTE21 – Carte ' . $membre['reference'];
$base_path  = '';

if ($print_mode) {
    // En mode impression : page minimale
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte – <?= h($membre['reference']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/print.css" media="print">
</head>
<body class="print-body" onload="window.print()">
    <?php include __DIR__ . '/includes/card_template.php'; ?>
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn--primary">Imprimer</button>
        <a href="card_detail.php?id=<?= $id ?>" class="btn btn--outline">Retour</a>
    </div>
<script src="assets/js/app.js"></script>
</body>
</html>
    <?php
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Carte de membre – <?= h($membre['reference']) ?></h1>
        <div class="page-header__actions">
            <a href="card_detail.php?id=<?= $id ?>&print=1" class="btn btn--outline" target="_blank">Imprimer</a>
            <a href="dashboard.php" class="btn btn--outline">← Retour</a>
        </div>
    </div>

    <?php include __DIR__ . '/includes/card_template.php'; ?>

    <div class="member-details">
        <h2>Informations enregistrées</h2>
        <dl class="detail-list">
            <dt>Référence</dt>  <dd><?= h($membre['reference']) ?></dd>
            <dt>Nom</dt>        <dd><?= h($membre['nom']) ?></dd>
            <dt>Prénom</dt>     <dd><?= h($membre['prenom']) ?></dd>
            <dt>Date de naissance</dt>
            <dd><?= h(fmt_date($membre['date_naissance'])) ?></dd>
            <dt>Date d'inscription</dt>
            <dd><?= h(fmt_date($membre['date_inscription'])) ?></dd>
            <dt>Durée de validité</dt>
            <dd><?= h($membre['duree_validite']) ?> an<?= $membre['duree_validite'] > 1 ? 's' : '' ?></dd>
            <dt>Date d'expiration</dt>
            <dd class="<?= strtotime($membre['date_validite']) < time() ? 'expired' : '' ?>">
                <?= h(fmt_date($membre['date_validite'])) ?>
                <?= strtotime($membre['date_validite']) < time() ? '<span class="badge badge--expired">Expirée</span>' : '<span class="badge badge--valid">Valide</span>' ?>
            </dd>
            <dt>Créée le</dt>
            <dd><?= h(date('d/m/Y à H:i', strtotime($membre['created_at']))) ?></dd>
        </dl>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
