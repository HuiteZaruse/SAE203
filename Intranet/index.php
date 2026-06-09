<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Déjà connecté → dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $users = readJSON('users.json');
        $found = null;
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $found = $u;
                break;
            }
        }
        if ($found && password_verify($password, $found['password'])) {
            loginUser($found);
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        } else {
            $error = 'Identifiant ou mot de passe incorrect.';
        }
    }
}


if (!$error && isset($_GET['error'])) {
    if ($_GET['error'] === 'not_logged') $error = 'Vous devez être connecté pour accéder à cette page.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/logo.png">
    <title>Connexion — Vedda Interactive Intranet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/theme.css">
</head>
<body class="vedda-login-page">

<div class="container-fluid h-100">
    <div class="row min-vh-100">

        
        <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 vedda-login-brand">
            <div>
                <!-- Logo -->
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="vedda-login-logo">
                        <img src="<?= BASE_URL ?>/assets/logo.png" alt="Logo Vedda" width="40" height="40" style="border-radius: 8px; object-fit: cover;">
                    </div>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:#fff;line-height:1.1">Vedda</div>
                        <div style="font-size:.7rem;color:#FFC85C;font-weight:500;letter-spacing:.12em;text-transform:uppercase">Interactive</div>
                    </div>
                </div>

               
                <h1 style="font-family:'Syne',sans-serif;font-size:2.8rem;font-weight:800;color:#fff;line-height:1.15">
                    Bienvenue sur<br>
                    <span style="color:#FFC85C">l'Intranet</span>
                </h1>
                <p style="color:rgba(255,255,255,.65);font-size:1.05rem;margin-top:1rem;max-width:340px">
                    Portail privé réservé aux collaborateurs de Vedda Interactive. Accédez à vos outils de travail en toute sécurité.
                </p>
            </div>

           
            <div class="row g-3">
                <div class="col-4">
                    <div style="background:rgba(255,255,255,.1);border-radius:12px;padding:1rem;text-align:center">
                        <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#FFC85C">14</div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.6)">Employés</div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="background:rgba(255,255,255,.1);border-radius:12px;padding:1rem;text-align:center">
                        <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#FF653F">32+</div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.6)">Clients</div>
                    </div>
                </div>
                <div class="col-4">
                    <div style="background:rgba(255,255,255,.1);border-radius:12px;padding:1rem;text-align:center">
                        <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#fff">2022</div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.6)">Fondé</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7 d-flex align-items-center justify-content-center p-4">
            <div style="width:100%;max-width:420px">

                
                <div class="d-flex d-lg-none align-items-center gap-3 mb-4 justify-content-center">
                    <div class="vedda-login-logo" style="width:50px;height:50px">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                            <path d="M6 22L11 10L16 18L21 10L26 22" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="16" cy="26" r="2" fill="#FFC85C"/>
                        </svg>
                    </div>
                    <div style="font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--vedda-navy)">Vedda Interactive</div>
                </div>

               
                <div class="vedda-login-card p-4 p-sm-5">
                    <h2 style="font-family:'Syne',sans-serif;font-weight:700;color:var(--vedda-navy);margin-bottom:.25rem">Connexion</h2>
                    <p class="text-muted mb-4">Accédez à votre espace de travail</p>

                    <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><?= h($error) ?></span>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Identifiant</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="Votre identifiant"
                                       value="<?= h($_POST['username'] ?? '') ?>"
                                       autocomplete="username" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Votre mot de passe"
                                       autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-vedda w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>

                   
                    <div class="mt-4 p-3" style="background:var(--vedda-cream-dark);border-radius:10px;font-size:.82rem">
                        <div class="fw-semibold mb-2" style="color:var(--vedda-navy);font-family:'Syne',sans-serif">
                            <i class="bi bi-info-circle me-1"></i>Comptes de démonstration
                        </div>
                        <div class="row g-1">
                            <div class="col-6"><code>admin</code> / <code>admin123</code></div>
                            <div class="col-6"><code>direction</code> / <code>direction123</code></div>
                            <div class="col-6"><code>manager</code> / <code>manager123</code></div>
                            <div class="col-6"><code>salarie</code> / <code>salarie123</code></div>
                        </div>
                    </div>
                </div>

                <p class="text-center text-muted small mt-3">
                    <i class="bi bi-shield-check"></i> Connexion sécurisée — Intranet privé
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon  = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
});
</script>
</body>
</html>