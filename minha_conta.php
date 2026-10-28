<?php
/* minha_conta.php — dados da conta e histórico de resultados (somente logado). */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/historico.php';
iniciar_sessao();
exigir_login();

$perfis   = require __DIR__ . '/dados/perfis.php';
$usuario  = usuario_atual();
$sucesso  = mensagem_unica('sucesso');
$erro_db  = null;
$lista    = [];

try {
    $lista = listar_resultados($usuario['id']);
} catch (PDOException $ex) {
    $erro_db = 'Não foi possível carregar seu histórico agora.';
}

/** Transforma "aguia,lobo" em "Águia + Lobo". */
function nomes_dos_perfis($texto, $perfis)
{
    $nomes = [];
    foreach (explode(',', $texto) as $chave) {
        $nomes[] = isset($perfis[$chave]) ? $perfis[$chave]['nome'] : $chave;
    }
    return implode(' + ', $nomes);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Minha conta — Teste de perfil</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <h1 class="titulo-pagina">Minha conta</h1>

  <?php if ($sucesso): ?>
    <div class="sucesso" role="status"><strong><?= e($sucesso) ?></strong></div>
  <?php endif; ?>

  <section class="painel">
    <dl class="dados-conta">
      <dt>Nome</dt><dd><?= e($usuario['nome']) ?></dd>
      <dt>E-mail</dt><dd><?= e($usuario['email']) ?></dd>
    </dl>
    <div class="acoes" style="margin-top:0">
      <a class="btn" href="index.php">Fazer o teste</a>
      <a class="btn secundario" href="logout.php">Sair</a>
    </div>
  </section>

  <section class="painel" style="margin-top:1.25rem">
    <h2>Meus resultados</h2>

    <?php if ($erro_db): ?>
      <div class="alerta" role="alert"><strong><?= e($erro_db) ?></strong></div>
    <?php elseif (!$lista): ?>
      <p>Você ainda não fez nenhum teste com esta conta.</p>
    <?php else: ?>
      <div class="tabela-rolagem">
        <table class="historico">
          <thead>
            <tr><th>Data</th><th>Perfil</th><th>Águia</th><th>Gato</th><th>Lobo</th><th>Tubarão</th></tr>
          </thead>
          <tbody>
            <?php foreach ($lista as $linha): ?>
              <tr>
                <td><?= e(date('d/m/Y H:i', strtotime($linha['criado_em']))) ?></td>
                <td><strong><?= e(nomes_dos_perfis($linha['predominantes'], $perfis)) ?></strong></td>
                <td><?= (int) $linha['pontos_aguia'] ?></td>
                <td><?= (int) $linha['pontos_gato'] ?></td>
                <td><?= (int) $linha['pontos_lobo'] ?></td>
                <td><?= (int) $linha['pontos_tubarao'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
