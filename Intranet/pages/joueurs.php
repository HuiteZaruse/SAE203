<?php

$pageTitle = 'Gestion des Joueurs';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$user = currentUser();
$joueurs = readJSON('joueurs.json');

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1" style="color: var(--vedda-navy);">
            <i class="bi bi-controller me-2" style="color: var(--vedda-orange);"></i>Base de Données Joueurs
        </h1>
        <p class="text-muted mb-0">Liste des profils enregistrés sur l'intranet (<?= count($joueurs) ?> comptes)</p>
    </div>
</div>

<?php showFlash(); ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Pseudo</th>
                    <th>Identité</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Date d'inscription</th>
                    <th class="text-center pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($joueurs)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Aucun joueur enregistré.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($joueurs as $j): ?>
                        <tr>
                            <td class="ps-4 text-muted small">#<?= $j['id'] ?></td>
                            <td>
                                <span class="badge bg-dark font-monospace px-2 py-1.5"><?= h($j['pseudo']) ?></span>
                            </td>
                            <td><strong><?= h($j['prenom']) ?></strong> <?= h($j['nom']) ?></td>
                            <td><?= h($j['email']) ?></td>
                            <td><?= h($j['telephone']) ?></td>
                            <td class="small text-muted"><?= formatDate($j['date_inscription']) ?></td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?= BASE_URL ?>/pages/joueur_fiche.php?id=<?= $j['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Fiche
                                    </a>
                                    
                                    <?php if (isManager() || isDirection() || isAdmin()): ?>
                                        <a href="<?= BASE_URL ?>/pages/joueur_ban.php?id=<?= $j['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('⚠️ Êtes-vous sûr de vouloir BANNIR le joueur « <?= h($j['pseudo']) ?> » ?');">
                                            <i class="bi bi-shield-x"></i> Bannir
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>