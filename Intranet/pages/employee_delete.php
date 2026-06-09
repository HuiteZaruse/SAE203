<?php
/**
 * employee_delete.php — Suppression d'un employé (Version Corrigée)
 */
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if (!isAdmin() && !isDirection()) {
    redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'danger', 'Vous n\'avez pas les droits pour cette action.');
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $employees = readJSON('employees.json');
    $initialCount = count($employees);
    
    $employees = array_filter($employees, function($emp) use ($id) {
        return $emp['id'] !== $id;
    });
    
    if (count($employees) < $initialCount) {
        // CORRECTION : On vérifie SI l'écriture sur le disque réussit vraiment
        if (writeJSON('employees.json', array_values($employees))) {
            redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'success', 'Le collaborateur a été supprimé.');
        } else {
            redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'danger', 'Erreur système : Impossible d\'écrire dans le fichier JSON. Vérifiez les permissions Linux.');
        }
    } else {
        redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'warning', 'Collaborateur introuvable.');
    }
} else {
    redirectWithFlash(BASE_URL . '/pages/annuaire.php', 'danger', 'ID invalide.');
}