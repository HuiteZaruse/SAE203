<?php
/**
 * annuaire.php — Annuaire des employés de l'entreprise
 */
$pageTitle = 'Annuaire de l\'entreprise';

// On remonte d'un dossier (..) car on est dans /pages/
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Récupération des données
$user = currentUser();
$employees = readJSON('employees.json');

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1" style="color: var(--vedda-navy);">
            <i class="bi bi-people-fill me-2" style="color: var(--vedda-purple);"></i>Annuaire
        </h1>
        <p class="text-muted mb-0">Découvrez l'équipe de Vedda Interactive (<?= count($employees) ?> collaborateurs)</p>
    </div>
    
    <?php 
    // Seuls les admins et la direction peuvent gérer l'annuaire
    if (isAdmin() || isDirection()): 
    ?>
    <div>
        <a href="<?= BASE_URL ?>/pages/employee_form.php" class="btn btn-vedda">
            <i class="bi bi-person-plus-fill me-2"></i>Ajouter un membre
        </a>
    </div>
    <?php endif; ?>
</div>

<?php showFlash(); // Pour afficher les futurs messages de succès/erreur ?>

<div class="row g-4">
    <?php if (empty($employees)): ?>
        <div class="col-12">
            <div class="alert alert-info">Aucun employé n'a été trouvé dans la base de données.</div>
        </div>
    <?php else: ?>
        <?php foreach ($employees as $emp): ?>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                    
                    <div class="text-center pt-4 pb-2" style="background-color: var(--vedda-cream);">
                        <?php 
                        $photoPath = BASE_URL . '/assets/photos/' . h($emp['photo']);
                        ?>
                        <img src="<?= $photoPath ?>" alt="Photo de <?= h($emp['prenom']) ?>" 
                             class="rounded-circle object-fit-cover shadow-sm" 
                             style="width: 100px; height: 100px; border: 4px solid white;">
                    </div>
                    
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1" style="font-family: 'Syne', sans-serif; font-weight: 700; color: var(--vedda-navy);">
                            <?= h($emp['prenom']) ?> <?= h($emp['nom']) ?>
                        </h5>
                        <p class="small mb-3" style="color: var(--vedda-orange); font-weight: 600;">
                            <?= h($emp['fonction']) ?>
                        </p>
                        <p class="card-text text-muted small">
                            <?= h($emp['bio']) ?>
                        </p>
                    </div>

                    <?php if (isAdmin() || isDirection()): ?>
                    <div class="card-footer bg-white border-top-0 d-flex gap-2 justify-content-center pb-3">
                        <a href="<?= BASE_URL ?>/pages/employee_form.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Modifier">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/pages/employee_delete.php?id=<?= $emp['id'] ?>" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?= h($emp['prenom']) ?> ?');"
                           title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>