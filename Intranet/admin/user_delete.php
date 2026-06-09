<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireGroup(['admin']);

$currentUser = currentUser();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    
    if ($id === $currentUser['id']) {
        redirectWithFlash(BASE_URL . '/admin/users.php', 'danger', 'Action impossible : vous ne pouvez pas révoquer votre propre compte administrateur.');
    }

    $usersList = readJSON('users.json');
    $initialCount = count($usersList);
    
    $usersList = array_filter($usersList, function($u) use ($id) {
        return $u['id'] !== $id;
    });
    
    if (count($usersList) < $initialCount) {
        if (writeJSON('users.json', array_values($usersList))) {
            redirectWithFlash(BASE_URL . '/admin/users.php', 'success', 'Le compte utilisateur a été supprimé avec succès.');
        } else {
            redirectWithFlash(BASE_URL . '/admin/users.php', 'danger', 'Erreur système lors de l\'écriture du fichier.');
        }
    } else {
        redirectWithFlash(BASE_URL . '/admin/users.php', 'warning', 'Utilisateur introuvable.');
    }
} else {
    redirectWithFlash(BASE_URL . '/admin/users.php', 'danger', 'ID utilisateur invalide.');
}