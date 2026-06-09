<?php

$pageTitle = 'Configuration Utilisateur';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireGroup(['admin']);

$usersList = readJSON('users.json');
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$account = null;
$error = '';

if ($id) {
    foreach ($usersList as $u) {
        if ($u['id'] === $id) {
            $account = $u;
            break;
        }
    }
    if (!$account) {
        redirectWithFlash(BASE_URL . '/admin/users.php', 'danger', 'Utilisateur introuvable.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nom      = trim($_POST['nom'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $fonction = trim($_POST['fonction'] ?? '');
    $bio      = trim($_POST['bio'] ?? '');
    $groupes  = $_POST['groupes'] ?? []; 

    
    if ($username === '' || $nom === '' || $prenom === '' || $email === '' || empty($groupes)) {
        $error = 'Veuillez remplir tous les champs obligatoires et cocher au moins un groupe.';
    } elseif (!$id && $password === '') {
        $error = 'Le mot de passe est obligatoire pour un nouveau compte.';
    } else {
       
        $usernameExists = false;
        foreach ($usersList as $u) {
            if ($u['username'] === $username && $u['id'] !== $id) {
                $usernameExists = true;
                break;
            }
        }

        if ($usernameExists) {
            $error = 'Cet identifiant est déjà utilisé par un autre utilisateur.';
        } else {
            if ($id) {
                // MODE EDITION
                foreach ($usersList as &$u) {
                    if ($u['id'] === $id) {
                        $u['username'] = $username;
                        $u['nom']      = $nom;
                        $u['prenom']   = $prenom;
                        $u['email']    = $email;
                        $u['fonction'] = $fonction;
                        $u['bio']      = $bio;
                        $u['groupes']  = $groupes;
                        
                        // Si un nouveau mot de passe a été écrit, on le crypte
                        if ($password !== '') {
                            $u['password'] = password_hash($password, PASSWORD_BCRYPT);
                        }
                        break;
                    }
                }
                $msg = 'Le compte utilisateur a été mis à jour.';
            } else {
                // MODE CREATION
                $newId = nextId($usersList);
                $usersList[] = [
                    'id'         => $newId,
                    'username'   => $username,
                    'password'   => password_hash($password, PASSWORD_BCRYPT),
                    'nom'        => $nom,
                    'prenom'     => $prenom,
                    'email'      => $email,
                    'fonction'   => $fonction,
                    'groupes'    => $groupes,
                    'photo'      => 'assets/photos/default.jpg',
                    'bio'        => $bio,
                    'created_at' => date('Y-m-d')
                ];
                $msg = 'Le nouveau compte utilisateur a été créé.';
            }

            if (writeJSON('users.json', $usersList)) {
                redirectWithFlash(BASE_URL . '/admin/users.php', 'success', $msg);
            } else {
                $error = 'Erreur système : Impossible d\'écrire dans users.json.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-outline-secondary btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="mb-0 h3"><?= $id ? 'Modifier le compte utilisateur' : 'Créer un accès intranet' ?></h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= h($error) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form method="POST" action="user_form.php<?= $id ? '?id='.$id : '' ?>">
                
                <h5 class="mb-3 text-muted border-bottom pb-2">Informations de connexion</h5>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Identifiant / Username *</label>
                        <input type="text" name="username" class="form-control" required autocomplete="username"
                               value="<?= h($_POST['username'] ?? $account['username'] ?? '') ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Mot de passe <?= $id ? '(laisser vide si inchangé)' : '*' ?></label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password" <?= $id ? '' : 'required' ?>>
                    </div>
                </div>

                <h5 class="mb-3 text-muted border-bottom pb-2 mt-4">Informations personnelles</h5>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required
                               value="<?= h($_POST['prenom'] ?? $account['prenom'] ?? '') ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nom *</label>
                        <input type="text" name="nom" class="form-control" required
                               value="<?= h($_POST['nom'] ?? $account['nom'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Adresse Email *</label>
                        <input type="email" name="email" class="form-control" required
                               value="<?= h($_POST['email'] ?? $account['email'] ?? '') ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Fonction officielle</label>
                        <input type="text" name="fonction" class="form-control"
                               value="<?= h($_POST['fonction'] ?? $account['fonction'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Biographie</label>
                    <textarea name="bio" class="form-control" rows="3"><?= h($_POST['bio'] ?? $account['bio'] ?? '') ?></textarea>
                </div>

                <h5 class="mb-3 text-muted border-bottom pb-2 mt-4">Attribution des Groupes *</h5>
                <div class="card p-3 bg-light border-0 mb-4">
                    <div class="form-text mb-2 text-dark fw-medium"><i class="bi bi-info-circle me-1"></i>Un utilisateur peut appartenir à plusieurs groupes simultanément.</div>
                    <?php 
                    $roles = ['admin' => 'Administrateur', 'direction' => 'Direction', 'managers' => 'Manager / Responsable', 'salaries' => 'Salarié'];
                    $currentGroups = $account['groupes'] ?? [];
                    foreach ($roles as $key => $label): 
                        $checked = in_array($key, $currentGroups, true) ? 'checked' : '';
                    ?>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="checkbox" name="groupes[]" value="<?= $key ?>" id="group_<?= $key ?>" <?= $checked ?>>
                            <label class="form-check-input-label fw-semibold ms-1" for="group_<?= $key ?>">
                                <?= $label ?> (<code><?= $key ?></code>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-vedda">
                        <i class="bi bi-save me-2"></i><?= $id ? 'Mettre à jour le compte' : 'Créer l\'utilisateur' ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>