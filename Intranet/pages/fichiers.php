<?php

$pageTitle = 'Fichiers Partagés';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user = currentUser();
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $file = $_FILES['fichier'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    
    if (!in_array($ext, ['txt', 'csv'])) {
        $error = 'Type de fichier non autorisé. Seuls les .txt et .csv sont acceptés.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du transfert du fichier (Code: ' . $file['error'] . ').';
    } else {
        
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
        $destination = UPLOADS_DIR . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'success', 'Le fichier a été partagé avec succès.');
        } else {
            $error = 'Erreur serveur : Impossible de sauvegarder le fichier. Vérifiez les permissions du dossier shared_files.';
        }
    }
}


$fichiers = [];
if (is_dir(UPLOADS_DIR)) {
    $items = scandir(UPLOADS_DIR);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && preg_match('/\.(txt|csv)$/i', $item)) {
            $path = UPLOADS_DIR . '/' . $item;
            $fichiers[] = [
                'nom'    => $item,
                'taille' => filesize($path),
                'date'   => filemtime($path),
                'ext'    => strtolower(pathinfo($item, PATHINFO_EXTENSION))
            ];
        }
    }
}


function formatBytes($bytes) {
    if ($bytes > 1048576) return round($bytes / 1048576, 2) . ' Mo';
    if ($bytes > 1024) return round($bytes / 1024, 2) . ' Ko';
    return $bytes . ' octets';
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1" style="color: var(--vedda-navy);">
            <i class="bi bi-folder2-open me-2" style="color: var(--vedda-navy);"></i>Espace Fichiers
        </h1>
        <p class="text-muted mb-0">Partagez vos documents textes et tableurs CSV avec l'équipe.</p>
    </div>
</div>

<?php showFlash(); ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= h($error) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
            <h5 class="mb-3 fw-bold"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Ajouter un fichier</h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input class="form-control" type="file" name="fichier" accept=".txt,.csv" required>
                    <div class="form-text mt-2 text-muted">
                        <i class="bi bi-info-circle"></i> Extensions autorisées : <strong>.txt, .csv</strong>
                    </div>
                </div>
                <button type="submit" class="btn btn-vedda w-100">Partager le fichier</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nom du fichier</th>
                            <th>Taille</th>
                            <th>Modifié le</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fichiers)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-light"></i>
                                    Aucun fichier partagé pour le moment.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fichiers as $f): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="fs-4 <?= $f['ext'] === 'csv' ? 'text-success' : 'text-secondary' ?>">
                                                <i class="bi bi-filetype-<?= $f['ext'] ?>"></i>
                                            </div>
                                            <span class="fw-medium text-break"><?= h($f['nom']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= formatBytes($f['taille']) ?></td>
                                    <td class="text-muted small"><?= date('d/m/Y H:i', $f['date']) ?></td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= BASE_URL ?>/pages/fichier_download.php?file=<?= urlencode($f['nom']) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            
                                            <?php if (isManager() || isDirection() || isAdmin()): ?>
                                            <a href="<?= BASE_URL ?>/pages/fichier_delete.php?file=<?= urlencode($f['nom']) ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Supprimer définitivement ce fichier ?');"
                                               title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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