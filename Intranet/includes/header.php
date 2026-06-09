<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
requireLogin();

$user    = currentUser();
$initials = initiales($user['prenom'], $user['nom']);


$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/logo.png">
    <title><?= isset($pageTitle) ? h($pageTitle) . ' — ' : '' ?>Vedda Interactive Intranet</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/theme.css">
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark vedda-navbar sticky-top">
    <div class="container-fluid px-4">

      
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/dashboard.php">
            <span class="vedda-logo-icon">
                <img src="<?= BASE_URL ?>/assets/logo.png" alt="Logo Vedda" width="32" height="32" style="border-radius: 8px; object-fit: cover;">
            </span>
            <span class="vedda-brand-text">
                <span class="brand-main">Vedda</span>
                <span class="brand-sub">Interactive</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

      
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/dashboard.php">
                        <i class="bi bi-grid-1x2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'annuaire.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/annuaire.php">
                        <i class="bi bi-people"></i> Annuaire
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'joueurs.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/joueurs.php">
                        <i class="bi bi-briefcase"></i> Joueurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'partenaires.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/partenaires.php">
                        <i class="bi bi-handshake"></i> Partenaires
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'fichiers.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pages/fichiers.php">
                        <i class="bi bi-folder2-open"></i> Fichiers
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentDir === 'admin' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/admin/users.php">
                        <i class="bi bi-shield-lock"></i> Administration
                    </a>
                </li>
                <?php endif; ?>
            </ul>

           
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="vedda-avatar-sm"><?= h($initials) ?></span>
                        <span class="d-none d-md-inline"><?= h($user['prenom']) ?> <?= h($user['nom']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                <?= h($user['fonction']) ?>
                            </span>
                        </li>
                        <li>
                            <span class="dropdown-item-text small">
                                <?php foreach ($user['groupes'] as $g) echo groupeBadge($g) . ' '; ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>


<main class="container-fluid px-4 py-4">