<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php?error=not_logged');
        exit;
    }
}


function requireGroup(array $groupes): void {
    requireLogin();
    if (!hasGroup($groupes)) {
        header('Location: ' . BASE_URL . '/dashboard.php?error=forbidden');
        exit;
    }
}


function hasGroup(array $groupes): bool {
    if (!isset($_SESSION['groupes'])) return false;
    foreach ($groupes as $g) {
        if (in_array($g, $_SESSION['groupes'], true)) return true;
    }
    return false;
}


function isAdmin(): bool {
    return hasGroup(['admin']);
}


function isDirection(): bool {
    return hasGroup(['direction', 'admin']);
}


function isManager(): bool {
    return hasGroup(['managers', 'direction', 'admin']);
}

/**
 * Retourne les infos de session de l'utilisateur courant
 */
function currentUser(): array {
    return [
        'id'       => $_SESSION['user_id']       ?? null,
        'username' => $_SESSION['username']       ?? '',
        'nom'      => $_SESSION['nom']            ?? '',
        'prenom'   => $_SESSION['prenom']         ?? '',
        'fonction' => $_SESSION['fonction']       ?? '',
        'groupes'  => $_SESSION['groupes']        ?? [],
    ];
}


function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['nom']       = $user['nom'];
    $_SESSION['prenom']    = $user['prenom'];
    $_SESSION['fonction']  = $user['fonction'];
    $_SESSION['groupes']   = $user['groupes'];
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}