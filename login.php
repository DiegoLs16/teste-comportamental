<?php
/* login.php — formulário de entrada. Os dados são enviados para verificar_login.php. */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
iniciar_sessao();

if (usuario_logado()) {
    redirecionar('index.php');
}

$erro   = mensagem_unica('erro_login');
$aviso  = mensagem_unica('aviso');
$email  = mensagem_unica('email_digitado');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar — Teste de perfil</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <h1 class="titulo-pagina">Entrar</h1>
  <p>Entre na sua conta para fazer o teste de perfil.</p>

  <section class="painel">
    <?php if ($erro): ?>
      <div class="alerta" role="alert"><strong><?= e($erro) ?></strong></div>
    <?php elseif ($aviso): ?>
      <div class="alerta" role="alert"><strong><?= e($aviso) ?></strong></div>
    <?php endif; ?>

    <form action="verificar_login.php" method="post">
      <label class="campo" for="email">E-mail</label>
      <input type="email" id="email" name="email" maxlength="120" autocomplete="email" value="<?= e($email) ?>">

      <label class="campo" for="senha">Senha</label>
      <input type="password" id="senha" name="senha" autocomplete="current-password">

      <button class="btn" type="submit">Entrar</button>
    </form>
    <p class="rodape-form">Ainda não tem conta? <a href="cadastro.php">Criar conta</a></p>
  </section>
</main>
</body>
</html>
