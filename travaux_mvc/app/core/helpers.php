<?php
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }

function asset(string $path): string { return BASE_URL . '/' . ltrim($path, '/'); }

function money($n): string { return number_format((float)$n, 2, ',', ' ') . ' DH'; }

function dateFr(?string $d): string { return $d ? date('d/m/Y', strtotime($d)) : ''; }

function flash(?string $msg = null, string $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function statusLabel(string $s): string {
    $map = [
        'en_attente' => 'En attente', 'acceptee' => 'Acceptée', 'affectee' => 'Ouvrier affecté',
        'en_cours' => 'En cours', 'terminee' => 'Terminée', 'refusee' => 'Refusée', 'annulee' => 'Annulée',
        'planifiee' => 'Planifiée', 'a_remplacer' => 'À remplacer', 'a_payer' => 'À payer', 'paye' => 'Payé',
    ];
    return $map[$s] ?? $s;
}

// Liste des dates entre deux jours (inclus)
function datesBetween(string $start, string $end): array {
    $out = [];
    $d = new DateTime($start);
    $e = new DateTime($end);
    while ($d <= $e) { $out[] = $d->format('Y-m-d'); $d->modify('+1 day'); }
    return $out;
}

// Petit formulaire POST avec jeton CSRF (bouton d'action)
function postBtn(string $path, string $label, string $class = 'btn small', string $confirm = '', array $hidden = []): string {
    $h = '<form class="inline" method="post" action="' . e(url($path)) . '"'
       . ($confirm !== '' ? ' onsubmit="return confirm(\'' . e(addslashes($confirm)) . '\')"' : '') . '>' . Csrf::field();
    foreach ($hidden as $k => $v) $h .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    return $h . '<button class="' . e($class) . '" type="submit">' . e($label) . '</button></form>';
}

function badge(string $s): string { return '<span class="badge ' . e($s) . '">' . e(statusLabel($s)) . '</span>'; }

function monthName(int $m): string {
    return ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'][$m] ?? '';
}
