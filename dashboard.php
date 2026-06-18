<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$membres    = list_membres();
$page_title = 'ARTE21 – Tableau de bord';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Cartes de membre</h1>
        <a href="create_card.php" class="btn btn--primary">+ Nouvelle carte</a>
    </div>

    <?php if (empty($membres)): ?>
        <div class="empty-state">
            <p>Aucune carte de membre enregistrée pour l'instant.</p>
            <a href="create_card.php" class="btn btn--primary">Créer la première carte</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de naissance</th>
                        <th>Date d'inscription</th>
                        <th>Validité</th>
                        <th>Date d'expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($membres as $m): ?>
                    <tr>
                        <td><code><?= h($m['reference']) ?></code></td>
                        <td><?= h($m['nom']) ?></td>
                        <td><?= h($m['prenom']) ?></td>
                        <td><?= h(fmt_date($m['date_naissance'])) ?></td>
                        <td><?= h(fmt_date($m['date_inscription'])) ?></td>
                        <td><?= h($m['duree_validite']) ?> an<?= $m['duree_validite'] > 1 ? 's' : '' ?></td>
                        <td class="<?= strtotime($m['date_validite']) < time() ? 'expired' : '' ?>">
                            <?= h(fmt_date($m['date_validite'])) ?>
                        </td>
                        <td class="actions">
                            <a href="card_detail.php?id=<?= (int)$m['id'] ?>" class="btn btn--sm">Voir</a>
                            <a href="card_detail.php?id=<?= (int)$m['id'] ?>&print=1"
                               class="btn btn--sm btn--outline" target="_blank">Imprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="table-count"><?= count($membres) ?> carte(s) enregistrée(s)</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
