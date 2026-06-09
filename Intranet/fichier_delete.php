<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();


if (!isManager() && !isDirection() && !isAdmin()) {
    redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'danger', 'Vous n\'avez pas les droits pour supprimer un fichier.');
}

$filename = basename($_GET['file'] ?? '');
$path = UPLOADS_DIR . '/' . $filename;

if ($filename && file_exists($path) && is_file($path)) {
    
    if (unlink($path)) {
        redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'success', 'Le fichier a été supprimé du serveur.');
    } else {
        redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'danger', 'Erreur système : Impossible de supprimer le fichier.');
    }
} else {
    redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'warning', 'Fichier introuvable.');
}