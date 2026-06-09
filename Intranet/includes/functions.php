<?php


define('BASE_URL', '/Intranet');
define('DATA_DIR', __DIR__ . '/../data');
define('UPLOADS_DIR', __DIR__ . '/../shared_files');


function readJSON(string $filename): array {
    $path = DATA_DIR . '/' . $filename;
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    if ($content === false) return [];
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}


function writeJSON(string $filename, array $data): bool {
    $path = DATA_DIR . '/' . $filename;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($path, $json) !== false;
}


function nextId(array $items): int {
    if (empty($items)) return 1;
    return max(array_column($items, 'id')) + 1;
}


function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}


function groupeBadge(string $groupe): string {
    $map = [
        'admin'     => 'danger',
        'direction' => 'dark',
        'managers'  => 'warning',
        'salaries'  => 'primary',
    ];
    $color = $map[$groupe] ?? 'secondary';
    $labels = [
        'admin'     => 'Admin',
        'direction' => 'Direction',
        'managers'  => 'Manager',
        'salaries'  => 'Salarié',
    ];
    $label = $labels[$groupe] ?? ucfirst($groupe);
    return '<span class="badge bg-' . $color . '">' . h($label) . '</span>';
}


function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}


function showFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type = in_array($flash['type'], ['success','danger','warning','info']) ? $flash['type'] : 'info';
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo h($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}


function redirectWithFlash(string $url, string $type, string $message): void {
    setFlash($type, $message);
    header('Location: ' . $url);
    exit;
}


function formatDate(string $date): string {
    if (!$date) return '—';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d) return h($date);
    $mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    return $d->format('d') . ' ' . $mois[(int)$d->format('m') - 1] . ' ' . $d->format('Y');
}


function initiales(string $prenom, string $nom): string {
    return mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}