<?php
/* ==========================================================
   index.php — Página inicial
   - lê a mensagem de erro de validação do nome (guardada na sessão)
   - envia o nome por POST para teste.php
   ========================================================== */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
iniciar_sessao();
exigir_login();   // sem conta, não há como chegar ao teste

// Erro e nome digitado vindos da validação feita em teste.php / resultado.php
$erro_nome     = isset($_SESSION['erro_nome']) ? $_SESSION['erro_nome'] : null;
$nome_digitado = isset($_SESSION['nome_digitado']) ? $_SESSION['nome_digitado'] : '';
unset($_SESSION['erro_nome'], $_SESSION['nome_digitado']);
$sucesso = mensagem_unica('sucesso');

// Quem está logado já vê o nome da conta preenchido
if ($nome_digitado === '' && usuario_logado()) {
    $nome_digitado = $_SESSION['usuario']['nome'];
}

// Voltar ao início começa uma nova tentativa do zero
limpar_tentativa();

$perfis = require __DIR__ . '/dados/perfis.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Qual é o seu perfil? — Águia, Gato, Lobo ou Tubarão</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <div class="capa" aria-hidden="true">
    <div class="aguia">Águia</div><div class="gato">Gato</div>
    <div class="lobo">Lobo</div><div class="tubarao">Tubarão</div>
  </div>

  <h1 class="titulo-pagina">Qual animal é o seu perfil?</h1>
  <p>São 24 perguntas rápidas com quatro alternativas cada. Escolha a que mais combina com você. No final, você descobre seu perfil predominante.</p>

  <section class="painel">
    <?php if ($sucesso): ?>
      <div class="sucesso" role="status"><strong><?= e($sucesso) ?></strong></div>
    <?php endif; ?>
    <?php if ($erro_nome): ?>
      <div class="alerta" role="alert">
        <strong><?= e($erro_nome) ?></strong>
      </div>
    <?php endif; ?>

    <form action="teste.php" method="post">
      <label class="campo" for="nome">Seu nome</label>
      <input type="text" id="nome" name="nome" placeholder="Digite seu nome" autocomplete="name"
             maxlength="60" value="<?= e($nome_digitado) ?>">
      <button class="btn" type="submit">Começar o teste</button>
    </form>

    <ul class="perfis-lista">
      <?php foreach ($perfis as $chave => $perfil): ?>
        <li class="<?= e($chave) ?>"><strong><?= e($perfil['nome']) ?></strong> — <?= e($perfil['titulo']) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>

</main>
</body>
</html>
