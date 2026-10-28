<?php
/* verificar_login.php — confere e-mail e senha. Não mostra nada: só redireciona. */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/conexao.php';
iniciar_sessao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('login.php');
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? (string) $_POST['senha'] : '';

if ($email === '' || $senha === '') {
    $_SESSION['erro_login']     = 'Informe e-mail e senha.';
    $_SESSION['email_digitado'] = $email;
    redirecionar('login.php');
}

try {
    $stmt = conectar()->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
} catch (PDOException $ex) {
    $_SESSION['erro_login'] = 'Não foi possível acessar o banco de dados. Confira o MySQL e o arquivo conexao.php.' . detalhe_erro_db($ex);
    redirecionar('login.php');
}

// Mensagem igual para e-mail inexistente e senha errada (não revela qual dos dois falhou)
if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    $_SESSION['erro_login']     = 'E-mail ou senha incorretos.';
    $_SESSION['email_digitado'] = $email;
    redirecionar('login.php');
}

entrar($usuario['id'], $usuario['nome'], $usuario['email']);
redirecionar('index.php');
