<?php

$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user      = currentUser();
$employees = readJSON('employees.json');
$joueurs   = readJSON('joueurs.json');
$partners  = readJSON('partners.json');
$users     = readJSON('users.json');

$sharedFiles = [];
if (is_dir(UPLOADS_DIR)) {
    $sharedFiles = array_filter(scandir(UPLOADS_DIR), function($f) {
        return $f !== '.' && $f !== '..' && preg_match('/\.(txt|csv)$/i', $f);
    });
}

require_once __DIR__ . '/includes/header.php';
?>

<?php showFlash(); ?>

<div class="vedda-page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1">
            <i class="bi bi-grid-1x2 me-2"></i>Tableau de bord
        </h1>
        <p class="text-muted mb-0 small">
            Bonjour, <strong style="color:#FF653F"><?= h($user['prenom']) ?> <?= h($user['nom']) ?></strong>
            — <?= h($user['fonction']) ?>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php foreach ($user['groupes'] as $g) echo groupeBadge($g); ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="stat-number"><?= count($employees) ?></div>
            <div class="stat-label">Employés</div>
            <div style="width:40px;height:3px;background:var(--vedda-orange);border-radius:2px;margin:.5rem auto 0"></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="stat-number"><?= count($joueurs) ?></div>
            <div class="stat-label">Joueurs</div>
            <div style="width:40px;height:3px;background:var(--vedda-orange);border-radius:2px;margin:.5rem auto 0"></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="stat-number"><?= count($partners) ?></div>
            <div class="stat-label">Partenaires</div>
            <div style="width:40px;height:3px;background:var(--vedda-orange);border-radius:2px;margin:.5rem auto 0"></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center">
            <div class="stat-number"><?= count($sharedFiles) ?></div>
            <div class="stat-label">Fichiers</div>
            <div style="width:40px;height:3px;background:var(--vedda-navy);border-radius:2px;margin:.5rem auto 0"></div>
        </div>
    </div>
</div>

<h2 class="mb-3" style="font-size:1.2rem">Modules disponibles</h2>

<div class="row g-4">
    <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/pages/annuaire.php" class="vedda-module-card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="vedda-module-icon icon-purple">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h5 class="mb-0">Annuaire</h5>
                    <small class="text-muted">Équipe Vedda Interactive</small>
                </div>
            </div>
            <p class="text-muted small mb-2">Consultez les fiches de tous les collaborateurs du studio.</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:var(--vedda-cream-dark);color:var(--vedda-navy)">
                    <?= count($employees) ?> personnes
                </span>
                <i class="bi bi-arrow-right ms-auto" style="color:var(--vedda-orange)"></i>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/pages/joueurs.php" class="vedda-module-card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="vedda-module-icon icon-orange">
                    <i class="bi bi-controller"></i>
                </div>
                <div>
                    <h5 class="mb-0">Joueurs</h5>
                    <small class="text-muted">Base de données réseau</small>
                </div>
            </div>
            <p class="text-muted small mb-2">Gérez la communauté de joueurs et téléchargez leurs fiches.</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:var(--vedda-cream-dark);color:var(--vedda-navy)">
                    <?= count($joueurs) ?> joueurs
                </span>
                <i class="bi bi-arrow-right ms-auto" style="color:var(--vedda-orange)"></i>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/pages/partenaires.php" class="vedda-module-card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="vedda-module-icon icon-yellow">
                    <i class="bi bi-handshake-fill"></i>
                </div>
                <div>
                    <h5 class="mb-0">Partenaires</h5>
                    <small class="text-muted">Fournisseurs & partenaires</small>
                </div>
            </div>
            <p class="text-muted small mb-2">Consultez et gérez les partenaires de Vedda Interactive.</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:var(--vedda-cream-dark);color:var(--vedda-navy)">
                    <?= count($partners) ?> partenaires
                </span>
                <i class="bi bi-arrow-right ms-auto" style="color:var(--vedda-orange)"></i>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/pages/fichiers.php" class="vedda-module-card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="vedda-module-icon icon-navy">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h5 class="mb-0">Fichiers</h5>
                    <small class="text-muted">Espace fichiers partagés</small>
                </div>
            </div>
            <p class="text-muted small mb-2">Partagez et téléchargez des fichiers .txt et .csv.</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:var(--vedda-cream-dark);color:var(--vedda-navy)">
                    <?= count($sharedFiles) ?> fichiers
                </span>
                <i class="bi bi-arrow-right ms-auto" style="color:var(--vedda-orange)"></i>
            </div>
        </a>
    </div>

    <?php if (isAdmin()): ?>
    <div class="col-sm-6 col-lg-4">
        <a href="<?= BASE_URL ?>/admin/users.php" class="vedda-module-card p-4" style="border-top: 3px solid var(--vedda-orange)">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="vedda-module-icon icon-danger">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h5 class="mb-0">Administration</h5>
                    <small class="text-muted">Gestion des utilisateurs</small>
                </div>
            </div>
            <p class="text-muted small mb-2">Ajoutez, modifiez et gérez les comptes utilisateurs.</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background:var(--vedda-cream-dark);color:var(--vedda-navy)">
                    <?= count($users) ?> utilisateurs
                </span>
                <span class="badge bg-danger ms-1">Admin</span>
                <i class="bi bi-arrow-right ms-auto" style="color:var(--vedda-orange)"></i>
            </div>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>