<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

$errors  = [];
$success = false;
$new_id  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // --- Validation des champs texte ---
    $nom             = trim($_POST['nom'] ?? '');
    $prenom          = trim($_POST['prenom'] ?? '');
    $date_naissance  = trim($_POST['date_naissance'] ?? '');
    $date_inscription= trim($_POST['date_inscription'] ?? '');
    $duree_validite  = (int) ($_POST['duree_validite'] ?? 0);

    if ($nom === '')              $errors[] = 'Le nom est obligatoire.';
    if ($prenom === '')           $errors[] = 'Le prénom est obligatoire.';
    if ($date_naissance === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_naissance))
        $errors[] = 'La date de naissance est invalide.';
    if ($date_inscription === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_inscription))
        $errors[] = 'La date d\'inscription est invalide.';
    if ($duree_validite < 1 || $duree_validite > 10)
        $errors[] = 'La durée de validité doit être comprise entre 1 et 10 ans.';

    // --- Gestion de la photo ---
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $photo_path = save_photo($_FILES['photo']);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    // --- Insertion ---
    if (empty($errors)) {
        try {
            $new_id = create_membre([
                'nom'              => $nom,
                'prenom'           => $prenom,
                'date_naissance'   => $date_naissance,
                'date_inscription' => $date_inscription,
                'duree_validite'   => $duree_validite,
                'photo_path'       => $photo_path,
            ]);
            $success = true;
        } catch (Throwable $e) {
            $errors[] = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
        }
    }
}

$page_title = 'ARTE21 – Nouvelle carte de membre';
include __DIR__ . '/includes/header.php';
?>

<div class="container container--narrow">
    <div class="page-header">
        <h1>Nouvelle carte de membre</h1>
        <a href="dashboard.php" class="btn btn--outline">← Retour</a>
    </div>

    <?php if ($success && $new_id): ?>
        <div class="alert alert--success">
            Carte créée avec succès !
            <a href="card_detail.php?id=<?= $new_id ?>">Voir la carte</a> |
            <a href="card_detail.php?id=<?= $new_id ?>&print=1" target="_blank">Imprimer</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="create_card.php" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom"
                       value="<?= h($_POST['nom'] ?? '') ?>"
                       required maxlength="100">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="prenom" name="prenom"
                       value="<?= h($_POST['prenom'] ?? '') ?>"
                       required maxlength="100">
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de naissance <span class="required">*</span></label>
                <input type="date" id="date_naissance" name="date_naissance"
                       value="<?= h($_POST['date_naissance'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="date_inscription">Date d'inscription <span class="required">*</span></label>
                <input type="date" id="date_inscription" name="date_inscription"
                       value="<?= h($_POST['date_inscription'] ?? date('Y-m-d')) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="duree_validite">Durée de validité (années) <span class="required">*</span></label>
                <select id="duree_validite" name="duree_validite" required>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"
                            <?= (isset($_POST['duree_validite']) && (int)$_POST['duree_validite'] === $i) ? 'selected' : ($i === 1 ? 'selected' : '') ?>>
                            <?= $i ?> an<?= $i > 1 ? 's' : '' ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_validite_preview">Date d'expiration (calculée automatiquement)</label>
                <input type="text" id="date_validite_preview" readonly
                       placeholder="Calculée à partir de la date d'inscription">
            </div>

            <div class="form-group form-group--full">
                <label for="photo">Photo du membre (JPEG / PNG – max 2 Mo)</label>
                <div class="file-upload">
                    <input type="file" id="photo" name="photo"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    <div class="file-upload__preview" id="photoPreview">
                        <span>Aucune photo sélectionnée</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Créer la carte</button>
            <a href="dashboard.php" class="btn btn--outline">Annuler</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
