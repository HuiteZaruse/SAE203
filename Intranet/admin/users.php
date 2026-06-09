<?php

$pageTitle = 'Administration — Utilisateurs';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';


requireGroup(['admin']);

$user = currentUser();
$usersList = readJSON('users.json');

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1" style="color: var(--vedda-navy);">
            <i class="bi bi-shield-lock-fill me-2" style="color: var(--vedda-orange);"></i>Console d'Administration
        </h1>
        <p class="text-muted mb-0">Gestion des accès à l'intranet et attribution des rôles (<?= count($usersList) ?> comptes)</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/user_form.php" class="btn btn-vedda">
            <i class="bi bi-person-plus-fill me-2"></i>Créer un utilisateur
        </a>
    </div>
</div>

<?php showFlash(); ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Identifiant</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Fonction</th>
                    <th>Groupes / Rôles</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersList as $u): ?>
                    <tr>
                        <td class="ps-4 text-muted small">#<?= $u['id'] ?></td>
                        <td><code class="text-dark fw-bold"><?= h($u['username']) ?></code></td>
                        <td><strong><?= h($u['prenom']) ?></strong> <?= h($u['nom']) ?></td>
                        <td class="small"><?= h($u['email']) ?></td>
                        <td class="text-muted small"><?= h($u['fonction']) ?></td>
                        <td>
                            <?php 
                            foreach ($u['groupes'] as $g) {
                                echo groupeBadge($g) . ' ';
                            }
                            ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="<?= BASE_URL ?>/admin/user_form.php?id=<?= $u['id'] ?>" 
                                   class="btn btn-sm btn-outline-secondary" title="Modifier l'utilisateur">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if ($u['id'] !== $user['id']): ?>
                                    <a href="<?= BASE_URL ?>/admin/user_delete.php?id=<?= $u['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Supprimer définitivement le compte de <?= h($u['prenom']) ?> ? Il perdra tous ses accès.');"
                                       title="Supprimer l'utilisateur">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light text-muted" disabled title="Vous ne pouvez pas vous supprimer vous-même">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>