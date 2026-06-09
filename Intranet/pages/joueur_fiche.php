<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$joueurs = readJSON('joueurs.json');
$joueur = null;

foreach ($joueurs as $j) {
    if ($j['id'] === $id) {
        $joueur = $j;
        break;
    }
}

if (!$joueur) {
    redirectWithFlash(BASE_URL . '/pages/joueurs.php', 'danger', 'Joueur introuvable.');
}

$filename = "fiche_joueur_" . $joueur['pseudo'] . ".txt";
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "==================================================\n";
echo "          VEDDA INTERACTIVE - FICHE JOUEUR        \n";
echo "==================================================\n\n";
echo "ID UNIQUE       : #" . $joueur['id'] . "\n";
echo "PSEUDONYME      : " . $joueur['pseudo'] . "\n";
echo "PRENOM          : " . $joueur['prenom'] . "\n";
echo "NOM             : " . $joueur['nom'] . "\n";
echo "EMAIL           : " . $joueur['email'] . "\n";
echo "TELEPHONE       : " . $joueur['telephone'] . "\n";
echo "ADRESSE POSTALE : " . $joueur['adresse'] . "\n";
echo "INSCRIPTION     : " . $joueur['date_inscription'] . "\n\n";
echo "==================================================\n";
echo "Généré le " . date('d/m/Y à H:i') . " par l'Intranet.\n";
exit;