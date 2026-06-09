<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (!isManager() && !isDirection() && !isAdmin()) {
    redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'danger', 'Permissions insuffisantes.');
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $joueurs = readJSON('joueurs.json');
    $initialCount = count($joueurs);
    
    $joueurs = array_filter($joueurs, function($j) use ($id) {
        return $j['id'] !== $id;
    });
    
    if (count($joueurs) < $initialCount) {
        if (writeJSON('joueurs.json', array_values($joueurs))) {
            redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'success', 'Le joueur a été banni et son profil supprimé.');
        } else {
            redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'danger', 'Erreur système lors de la modification du fichier JSON.');
        }
    } else {
        redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'warning', 'Joueur introuvable.');
    }
} else {
    redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'danger', 'ID invalide.');
}