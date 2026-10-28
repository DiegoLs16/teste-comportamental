<?php
/* logout.php — encerra a sessão e volta para o início. */
require __DIR__ . '/includes/funcoes.php';
iniciar_sessao();

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

redirecionar('index.php');
