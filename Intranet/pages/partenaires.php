<?php
/**
 * partenaires.php — Gestion des Partenaires (Espace Intranet)
 */
$pageTitle = 'Partenaires';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// -------------------------------------------------------------------------
// 1. CONFIGURATION DES CHEMINS
// -------------------------------------------------------------------------
// On définit le dossier où seront stockés les logos (en utilisant le chemin absolu du serveur)
$logo_dir_php = __DIR__ . '/../assets/photos/';

// -------------------------------------------------------------------------
// 2. TRAITEMENT : SUPPRESSION D'UN PARTENAIRE
// -------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // Matrice de droits : Seuls les managers et admins peuvent supprimer
    if (!isManager() && !isDirection() && !isAdmin()) {
        redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'danger', 'Permissions insuffisantes pour supprimer un partenaire.');
    }

    $target_id = (int)$_GET['id'];
    $partners = readJSON('partenaires.json');
    $initialCount = count($partners);
    
    foreach ($partners as $key => $partner) {
        if ($partner['id'] === $target_id) {
            // SÉCURITÉ : On supprime le fichier image physique du serveur s'il ne s'agit pas de l'image par défaut
            $logo_path_physique = __DIR__ . '/../' . ltrim($partner['logo_path'], '/');
            if (!empty($partner['logo_path']) && file_exists($logo_path_physique) && strpos($partner['logo_path'], 'default.jpg') === false) {
                unlink($logo_path_physique);
            }
            
            // On retire le partenaire du tableau
            unset($partners[$key]);
            break;
        }
    }

    if (count($partners) < $initialCount) {
        if (writeJSON('partenaires.json', array_values($partners))) {
            redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'success', 'Le partenaire et son logo ont été supprimés.');
        } else {
            redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'danger', 'Erreur technique lors de l\'écriture du fichier.');
        }
    }
}

// -------------------------------------------------------------------------
// 3. TRAITEMENT : AJOUT D'UN PARTENAIRE (Via formulaire POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_partner'])) {
    
    if (!isManager() && !isDirection() && !isAdmin()) {
        redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'danger', 'Permissions insuffisantes.');
    }

    $nom         = trim($_POST['partner_name'] ?? '');
    $description = trim($_POST['partner_description'] ?? '');
    $logo_web_path = 'assets/photos/default.jpg'; // Image par défaut si erreur
    $error = '';

    // Gestion du fichier image
    if (isset($_FILES['partner_logo']) && $_FILES['partner_logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['partner_logo']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['partner_logo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
            $destination = $logo_dir_php . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                // On enregistre le chemin relatif pour la base JSON
                $logo_web_path = 'assets/photos/' . $new_file_name;
            } else {
                $error = "Erreur de déplacement du fichier. Vérifiez les permissions du dossier assets/photos.";
            }
        } else {
            $error = "Format de fichier non autorisé (JPG, PNG, GIF, WEBP).";
        }
    }

    if (empty($error) && $nom !== '' && $description !== '') {
        $partners = readJSON('partenaires.json');
        
        $partners[] = [
            'id'          => nextId($partners),
            'nom'         => $nom,
            'description' => $description,
            'logo_path'   => $logo_web_path
        ];
        
        if (writeJSON('partenaires.json', $partners)) {
            redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'success', 'Nouveau partenaire ajouté avec succès !');
        } else {
            redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'danger', 'Erreur système lors de la sauvegarde.');
        }
    } elseif ($error) {
        redirectWithFlash(BASE_URL . '/pages/partenaires.php', 'danger', $error);
    }
}

// -------------------------------------------------------------------------
// 4. AFFICHAGE DE LA PAGE
// -------------------------------------------------------------------------
$partners = readJSON('partenaires.json');
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1" style="color: var(--vedda-navy);">
            <i class="bi bi-handshake-fill me-2" style="color: var(--vedda-yellow);"></i>Partenaires
        </h1>
        <p class="text-muted mb-0">Gestion du réseau de fournisseurs et collaborateurs</p>
    </div>
</div>

<?php showFlash(); ?>

<div class="row g-4">
    <?php if (isManager() || isDirection() || isAdmin()): ?>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
            <h5 class="mb-3 fw-bold"><i class="bi bi-building-add me-2 text-primary"></i>Nouveau partenaire</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="partner_name" class="form-label fw-bold small">Nom de l'entreprise *</label>
                    <input type="text" class="form-control" id="partner_name" name="partner_name" required>
                </div>
                <div class="mb-3">
                    <label for="partner_description" class="form-label fw-bold small">Description / Activité *</label>
                    <textarea class="form-control" id="partner_description" name="partner_description" rows="3" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="partner_logo" class="form-label fw-bold small">Logo (Miniature)</label>
                    <input type="file" class="form-control" id="partner_logo" name="partner_logo" accept="image/*">
                    <div class="form-text text-muted" style="font-size: 0.8rem;">Formats: JPG, PNG, GIF, WEBP</div>
                </div>
                <button type="submit" name="add_partner" class="btn btn-vedda w-100">
                    <i class="bi bi-save me-2"></i>Enregistrer
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?= (isManager() || isDirection() || isAdmin()) ? 'col-lg-8' : 'col-12' ?>">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 15%;">Logo</th>
                            <th style="width: 25%;">Nom de l'entreprise</th>
                            <th>Description</th>
                            <?php if (isManager() || isDirection() || isAdmin()): ?>
                            <th class="text-end pe-4" style="width: 15%;">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($partners)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Aucun partenaire enregistré pour le moment.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($partners as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if (!empty($p['logo_path'])): ?>
                                            <img src="<?= BASE_URL ?>/<?= h($p['logo_path']) ?>" alt="Logo" class="shadow-sm" style="height: 50px; width: 50px; object-fit: contain; border-radius: 8px; border: 1px solid #eaeaea; padding: 2px;">
                                        <?php else: ?>
                                            <span class="text-muted small">Aucun logo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong style="color: var(--vedda-navy);"><?= h($p['nom']) ?></strong></td>
                                    <td><p class="mb-0 text-muted small"><?= h($p['description']) ?></p></td>
                                    
                                    <?php if (isManager() || isDirection() || isAdmin()): ?>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>/pages/partenaires.php?action=delete&id=<?= $p['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Voulez-vous vraiment supprimer définitivement ce partenaire et son logo ?');"
                                           title="Supprimer le partenaire">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>