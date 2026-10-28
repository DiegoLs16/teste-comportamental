<?php
/* ==========================================================
   resultado.php — Tela do resultado
   1. recebe $_POST, valida o nome e as 24 respostas
      (se inválido, volta ao teste ou ao início)
   2. soma os pontos de cada perfil
   3. descobre o maior valor e os perfis predominantes (trata EMPATE)
   4. calcula o percentual: pontos / total * 100
   O cálculo é feito aqui, em PHP. Nada é calculado em JavaScript.
   ========================================================== */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/historico.php';
iniciar_sessao();
exigir_login('Entre na sua conta para ver o resultado.');

require __DIR__ . '/dados/perguntas.php';          // define $perguntas
$perfis = require __DIR__ . '/dados/perfis.php';
$total  = count($perguntas);

// 1) Recebimento do formulário (POST): valida, calcula e guarda o resultado.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nome
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $erro = validar_nome($nome);
    if ($erro !== null) {
        $_SESSION['erro_nome']     = $erro;
        $_SESSION['nome_digitado'] = $nome;
        redirecionar('index.php');
    }

    // Respostas: cada uma precisa ser um perfil válido
    $enviado = (isset($_POST['respostas']) && is_array($_POST['respostas'])) ? $_POST['respostas'] : [];
    list($validas, $faltando) = validar_respostas($total, $enviado, $perfis);

    // Guarda o que já foi respondido para o participante não perder nada
    $_SESSION['nome']      = $nome;
    $_SESSION['respostas'] = $validas;

    if ($faltando) {
        $_SESSION['faltando'] = $faltando;
        redirecionar('teste.php');
    }

    // Regra de negócio
    $pontuacao     = calcular_pontuacao($validas, $perfis);
    $predominantes = perfis_predominantes($pontuacao);
    $percentuais   = calcular_percentuais($pontuacao, $total);

    $_SESSION['resultado'] = [
        'nome'          => $nome,
        'pontuacao'     => $pontuacao,
        'percentuais'   => $percentuais,
        'predominantes' => $predominantes,
    ];
    // Guarda o resultado no histórico da conta (se o banco falhar, o resultado ainda aparece)
    try {
        salvar_resultado($_SESSION['usuario']['id'], $nome, $pontuacao, $predominantes);
        $_SESSION['historico_salvo'] = true;
    } catch (PDOException $ex) {
        $_SESSION['historico_salvo'] = false;
    }

    redirecionar('resultado.php');   // evita reenvio do formulário ao atualizar a página
}

// 2) Exibição (GET): precisa haver um resultado calculado.
if (empty($_SESSION['resultado'])) {
    redirecionar('index.php');
}

$r             = $_SESSION['resultado'];
$nome          = $r['nome'];
$pontuacao     = $r['pontuacao'];
$percentuais   = $r['percentuais'];
$predominantes = $r['predominantes'];
$empate        = count($predominantes) > 1;
$vencedor      = $predominantes[0];   // usado quando não há empate
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seu resultado — Teste de perfil</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <?php if (!$empate): ?>
    <!-- Perfil único -->
    <section class="destaque <?= e($vencedor) ?>">
      <div class="pequeno"><?= e($nome) ?>, seu perfil predominante é</div>
      <h1><?= e($perfis[$vencedor]['nome']) ?></h1>
      <p><strong><?= e($perfis[$vencedor]['titulo']) ?></strong> — <?= $pontuacao[$vencedor] ?> pontos · <?= formatar_percentual($percentuais[$vencedor]) ?></p>
    </section>
  <?php else: ?>
    <!-- Empate -->
    <section class="destaque empate">
      <div class="pequeno"><?= e($nome) ?>, você teve</div>
      <h1>Resultado combinado: <?= e(titulo_resultado($predominantes, $perfis)) ?></h1>
      <p>Dois ou mais perfis empataram com a maior pontuação (<?= $pontuacao[$vencedor] ?> pontos · <?= formatar_percentual($percentuais[$vencedor]) ?> cada).</p>
    </section>
  <?php endif; ?>

  <section class="painel">
    <h2>Sua pontuação</h2>
    <ul class="placar">
      <?php foreach ($perfis as $chave => $perfil): ?>
        <li class="<?= e($chave) ?>">
          <div class="linha"><span><?= e($perfil['nome']) ?></span><span><?= $pontuacao[$chave] ?> pontos · <?= formatar_percentual($percentuais[$chave]) ?></span></div>
          <div class="trilho"><span style="width:<?= largura_css($percentuais[$chave]) ?>%"></span></div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php foreach ($predominantes as $chave): ?>
      <div class="descricao <?= e($chave) ?>">
        <h3><?= e($perfis[$chave]['nome']) ?> — <?= e($perfis[$chave]['titulo']) ?></h3>
        <p><?= e($perfis[$chave]['descricao']) ?></p>
      </div>
    <?php endforeach; ?>
  </section>

  <?php if (isset($_SESSION['historico_salvo']) && $_SESSION['historico_salvo']): ?>
    <p class="nota-conta">Resultado salvo em <a href="minha_conta.php">Minha conta</a>.</p>
  <?php elseif (isset($_SESSION['historico_salvo'])): ?>
    <p class="nota-conta">Não foi possível salvar este resultado no histórico.</p>
  <?php endif; ?>

  <div class="acoes">
    <a class="btn" href="index.php">Fazer o teste novamente</a>
    <a class="btn secundario" href="teste.php">Corrigir minhas respostas</a>
    <button class="btn secundario" type="button" onclick="window.print()">Imprimir resultado</button>
  </div>

</main>
</body>
</html>
