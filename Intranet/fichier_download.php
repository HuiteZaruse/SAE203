<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$filename = basename($_GET['file'] ?? '');
$path = UPLOADS_DIR . '/' . $filename;


if ($filename && file_exists($path) && is_file($path) && preg_match('/\.(txt|csv)$/i', $filename)) {
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
} else {
    redirectWithFlash(BASE_URL . '/pages/fichiers.php', 'danger', 'Fichier introuvable ou non autorisé.');
}