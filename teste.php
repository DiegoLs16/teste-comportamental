<?php
/* ==========================================================
   teste.php — Tela das perguntas
   1. carrega $perguntas de dados/perguntas.php
   2. valida o nome recebido da página inicial
   3. gera os cartões .pergunta com foreach (alternativas embaralhadas)
   4. reexibe o teste com .alerta e .sem-resposta se faltar resposta
   ========================================================== */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
iniciar_sessao();
exigir_login('Entre na sua conta para fazer o teste.');

require __DIR__ . '/dados/perguntas.php';          // define $perguntas
$perfis = require __DIR__ . '/dados/perfis.php';
$total  = count($perguntas);

// 1) Chegada pela página inicial (POST com o nome): valida e prepara a tentativa.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $erro = validar_nome($nome);

    if ($erro !== null) {
        $_SESSION['erro_nome']     = $erro;
        $_SESSION['nome_digitado'] = $nome;
        redirecionar('index.php');
    }

    limpar_tentativa();
    $_SESSION['nome']  = $nome;
    $_SESSION['ordem'] = embaralhar_ordem($perguntas);
    redirecionar('teste.php');   // evita reenvio do formulário ao atualizar a página
}

// 2) Sem nome na sessão: volta ao início.
if (empty($_SESSION['nome'])) {
    redirecionar('index.php');
}

// Garante uma ordem de alternativas válida (ex.: se o número de perguntas mudou)
if (empty($_SESSION['ordem']) || count($_SESSION['ordem']) !== $total) {
    $_SESSION['ordem'] = embaralhar_ordem($perguntas);
}

$nome      = $_SESSION['nome'];
$ordem     = $_SESSION['ordem'];
$respostas = isset($_SESSION['respostas']) ? $_SESSION['respostas'] : [];
$faltando  = isset($_SESSION['faltando']) ? $_SESSION['faltando'] : [];
unset($_SESSION['faltando']);   // o aviso aparece só uma vez

$respondidas = count($respostas);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Teste de perfil — perguntas</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<!-- Barra de progresso (JavaScript no final do arquivo só atualiza a barra; não calcula resultado) -->
<div class="topo-progresso">
  <div class="container" style="padding:0">
    <div class="texto"><span>Olá, <?= e($nome) ?></span><span id="contador"><?= $respondidas ?> de <?= $total ?> respondidas</span></div>
    <div class="barra"><span id="preenchimento"></span></div>
  </div>
</div>

<main class="container">
  <h1 class="titulo-pagina">Escolha uma alternativa por pergunta</h1>

  <?php if ($faltando): ?>
    <div class="alerta" role="alert">
      <strong>Faltam <?= count($faltando) ?> <?= count($faltando) === 1 ? 'pergunta sem resposta' : 'perguntas sem resposta' ?>.</strong>
      Responda todas para ver seu resultado:
      <?php
      $links = [];
      foreach ($faltando as $i) {
          $links[] = '<a href="#q' . ($i + 1) . '">' . ($i + 1) . '</a>';
      }
      echo implode(', ', $links);
      ?>.
    </div>
  <?php endif; ?>

  <form action="resultado.php" method="post" id="form-teste">
    <input type="hidden" name="nome" value="<?= e($nome) ?>">

    <?php foreach ($perguntas as $i => $pergunta): ?>
      <fieldset class="pergunta<?= in_array($i, $faltando, true) ? ' sem-resposta' : '' ?>"
                id="q<?= $i + 1 ?>" style="margin-left:0;margin-right:0">
        <legend class="numero" style="padding:0">Pergunta <?= $i + 1 ?> de <?= $total ?></legend>
        <h2><?= e($pergunta['texto']) ?></h2>

        <?php foreach ($ordem[$i] as $indice): ?>
          <?php $alternativa = $pergunta['alternativas'][$indice]; ?>
          <label class="opcao">
            <input type="radio" name="respostas[<?= $i ?>]" value="<?= e($alternativa['perfil']) ?>"
                   <?= (isset($respostas[$i]) && $respostas[$i] === $alternativa['perfil']) ? 'checked' : '' ?>>
            <span><?= e($alternativa['texto']) ?></span>
          </label>
        <?php endforeach; ?>
      </fieldset>
    <?php endforeach; ?>

    <div class="acoes">
      <button class="btn" type="submit">Ver meu resultado</button>
      <a class="btn secundario" href="index.php">Voltar ao início</a>
    </div>
  </form>
</main>

<script>
/* Apenas visual: atualiza a barra de progresso conforme as respostas marcadas. */
(function () {
  var form = document.getElementById('form-teste');
  var total = <?= $total ?>;
  function atualizar() {
    var nomes = {};
    form.querySelectorAll('input[type=radio]:checked').forEach(function (r) { nomes[r.name] = true; });
    var feitas = Object.keys(nomes).length;
    document.getElementById('contador').textContent = feitas + ' de ' + total + ' respondidas';
    document.getElementById('preenchimento').style.width = (feitas / total * 100) + '%';
  }
  form.addEventListener('change', atualizar);
  atualizar();
})();
</script>
</body>
</html>
