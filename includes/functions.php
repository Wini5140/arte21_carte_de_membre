<?php
/**
 * Fonctions utilitaires – ARTE21
 */

require_once __DIR__ . '/../config/database.php';

define('UPLOAD_DIR',      __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 Mo
define('ALLOWED_MIME',    ['image/jpeg', 'image/png']);
define('ALLOWED_EXT',     ['jpg', 'jpeg', 'png']);

/**
 * Échappe une valeur pour l'affichage HTML.
 */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Retourne le prochain numéro de série pour une année donnée.
 */
function next_serial(int $year): int
{
    $db   = get_db();
    $stmt = $db->prepare(
        'SELECT COALESCE(MAX(numero_serie), 0) + 1 FROM membres WHERE annee_creation = ?'
    );
    $stmt->execute([$year]);
    return (int) $stmt->fetchColumn();
}

/**
 * Génère la référence ARTE21/AAAA/NNNN.
 */
function build_reference(int $year, int $serial): string
{
    return sprintf('ARTE21/%d/%04d', $year, $serial);
}

/**
 * Calcule la date de validité à partir de la date d'inscription et de la durée.
 * @param string $date_inscription  Format Y-m-d
 * @param int    $duree_annees
 * @return string Format Y-m-d
 */
function compute_validite(string $date_inscription, int $duree_annees): string
{
    $dt = new DateTimeImmutable($date_inscription);
    return $dt->modify("+{$duree_annees} years")->format('Y-m-d');
}

/**
 * Valide et enregistre la photo uploadée.
 * Retourne le chemin relatif ou lève une exception.
 */
function save_photo(array $file): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erreur lors du téléchargement de la photo (code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('La photo dépasse la taille maximale autorisée (2 Mo).');
    }

    // Vérification MIME réelle (pas seulement l'extension)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_MIME, true)) {
        throw new RuntimeException('Format de photo non autorisé. Utilisez JPEG ou PNG.');
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT, true)) {
        throw new RuntimeException('Extension de fichier non autorisée.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0750, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . ($mimeType === 'image/png' ? 'png' : 'jpg');
    $dest     = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Impossible de sauvegarder la photo.');
    }

    return 'uploads/' . $filename;
}

/**
 * Crée un nouveau membre en base de données.
 * Retourne l'ID inséré.
 */
function create_membre(array $data): int
{
    $db   = get_db();
    $year = (int) date('Y', strtotime($data['date_inscription']));

    // Sérialisation transactionnelle pour éviter les doublons de numéro de série
    $db->beginTransaction();
    try {
        $serial    = next_serial($year);
        $reference = build_reference($year, $serial);
        $validite  = compute_validite($data['date_inscription'], (int) $data['duree_validite']);

        $stmt = $db->prepare(
            'INSERT INTO membres
             (reference, numero_serie, annee_creation, nom, prenom,
              date_naissance, date_inscription, duree_validite, date_validite, photo_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $reference,
            $serial,
            $year,
            trim($data['nom']),
            trim($data['prenom']),
            $data['date_naissance'],
            $data['date_inscription'],
            (int) $data['duree_validite'],
            $validite,
            $data['photo_path'] ?? null,
        ]);
        $id = (int) $db->lastInsertId();
        $db->commit();
        return $id;
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}

/**
 * Retourne tous les membres (sans la photo) pour la liste.
 */
function list_membres(): array
{
    $db   = get_db();
    $stmt = $db->query(
        'SELECT id, reference, nom, prenom, date_naissance,
                date_inscription, duree_validite, date_validite, created_at
         FROM membres
         ORDER BY created_at DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Retourne un membre par son ID.
 */
function get_membre(int $id): ?array
{
    $db   = get_db();
    $stmt = $db->prepare('SELECT * FROM membres WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Formate une date Y-m-d en d/m/Y pour l'affichage.
 */
function fmt_date(string $date): string
{
    return date('d/m/Y', strtotime($date));
}
