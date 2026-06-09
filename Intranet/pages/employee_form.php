<?php
/**
 * employee_form.php — Ajout ou modification d'un employé
 */
$pageTitle = 'Gestion Collaborateur';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Sécurité des droits
if (!isAdmin() && !isDirection()) {
    redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'danger', 'Accès refusé.');
}

$employees = readJSON('employees.json');
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$employee = null;
$error = '';

// Si on est en mode "Modification", on cherche l'employé
if ($id) {
    foreach ($employees as $emp) {
        if ($emp['id'] === $id) {
            $employee = $emp;
            break;
        }
    }
    if (!$employee) {
        redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'danger', 'Collaborateur introuvable.');
    }
}

// Traitement du formulaire quand on clique sur "Enregistrer"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $fonction = trim($_POST['fonction'] ?? '');
    $bio      = trim($_POST['bio'] ?? '');
    $photo    = 'default.jpg'; // Simplification : on force la photo par défaut pour le moment

    if ($nom === '' || $prenom === '' || $fonction === '') {
        $error = 'Veuillez remplir les champs obligatoires (Nom, Prénom, Fonction).';
    } else {
        if ($id) {
            // MODE MODIFICATION
            foreach ($employees as &$emp) {
                if ($emp['id'] === $id) {
                    $emp['nom']      = $nom;
                    $emp['prenom']   = $prenom;
                    $emp['fonction'] = $fonction;
                    $emp['bio']      = $bio;
                    break;
                }
            }
            $msg = 'Le profil a été mis à jour.';
        } else {
            // MODE AJOUT
            $newId = nextId($employees); // Utilise ta super fonction pour trouver le prochain ID libre !
            $employees[] = [
                'id'       => $newId,
                'nom'      => $nom,
                'prenom'   => $prenom,
                'fonction' => $fonction,
                'photo'    => $photo,
                'bio'      => $bio
            ];
            $msg = 'Nouveau collaborateur ajouté à l\'annuaire.';
        }

        // Sauvegarde dans le JSON et redirection
        if (writeJSON('employees.json', $employees)) {
            redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'success', $msg);
        } else {
            $error = 'Erreur technique lors de la sauvegarde.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= BASE_URL ?>/pages/annuaire.php" class="btn btn-outline-secondary btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="mb-0 h3"><?= $id ? 'Modifier le profil' : 'Ajouter un collaborateur' ?></h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= h($error) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form method="POST" action="">
                
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required 
                               value="<?= h($_POST['prenom'] ?? $employee['prenom'] ?? '') ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nom *</label>
                        <input type="text" name="nom" class="form-control" required 
                               value="<?= h($_POST['nom'] ?? $employee['nom'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Fonction au sein du studio *</label>
                    <input type="text" name="fonction" class="form-control" required 
                           placeholder="Ex: Level Designer, Testeur QA..."
                           value="<?= h($_POST['fonction'] ?? $employee['fonction'] ?? '') ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Courte biographie</label>
                    <textarea name="bio" class="form-control" rows="4"><?= h($_POST['bio'] ?? $employee['bio'] ?? '') ?></textarea>
                    <div class="form-text">Présentation visible sur l'annuaire.</div>
                </div>

                <hr class="mb-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/pages/annuaire.php" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-vedda">
                        <i class="bi bi-save me-2"></i><?= $id ? 'Enregistrer les modifications' : 'Ajouter à l\'équipe' ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>