<?php
/* diagnostico_sessao.php — página de apoio para achar problemas de sessão e banco.
   Remova este arquivo antes de publicar o sistema em um servidor real. */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/conexao.php';
iniciar_sessao();

// Teste da conexão com o banco
$banco_ok   = false;
$banco_info = '';
try {
    conectar()->query('SELECT 1');
    $banco_ok   = true;
    $banco_info = 'Conectado ao banco "' . DB_NOME . '".';
} catch (PDOException $ex) {
    $banco_info = 'Falha na conexão: ' . $ex->getMessage();
}

$cookie = session_get_cookie_params();
$usuario = usuario_atual();

$linhas = [
    ['Versão do PHP', PHP_VERSION, version_compare(PHP_VERSION, '7.4.0', '>=')],
    ['Extensão pdo_mysql', extension_loaded('pdo_mysql') ? 'ativada' : 'desativada', extension_loaded('pdo_mysql')],
    ['Extensão mbstring', extension_loaded('mbstring') ? 'ativada' : 'desativada', extension_loaded('mbstring')],
    ['Sessão ativa', session_status() === PHP_SESSION_ACTIVE ? 'sim' : 'não', session_status() === PHP_SESSION_ACTIVE],
    ['ID da sessão (início)', substr(session_id(), 0, 8) . '…', session_id() !== ''],
    ['Cookie da sessão enviado pelo navegador', isset($_COOKIE[session_name()]) ? 'sim' : 'não (recarregue a página)', isset($_COOKIE[session_name()])],
    ['Pasta onde a sessão é salva', session_save_path() !== '' ? session_save_path() : '(padrão do PHP)', true],
    ['Caminho do cookie', $cookie['path'], true],
    ['Usuário logado', $usuario ? $usuario['nome'] . ' (' . $usuario['email'] . ')' : 'ninguém', true],
    ['Tentativa de teste em andamento', !empty($_SESSION['nome']) ? 'sim' : 'não', true],
    ['Resultado guardado na sessão', !empty($_SESSION['resultado']) ? 'sim' : 'não', true],
    ['Banco de dados', $banco_info, $banco_ok],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Diagnóstico — Teste de perfil</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <h1 class="titulo-pagina">Diagnóstico da sessão</h1>
  <p>Use esta página para conferir se a sessão e o banco estão funcionando.</p>

  <section class="painel">
    <div class="tabela-rolagem">
      <table class="historico diag">
        <tbody>
          <?php foreach ($linhas as $l): ?>
            <tr>
              <td><?= e($l[0]) ?></td>
              <td class="<?= $l[2] ? 'ok' : 'falha' ?>"><?= e($l[1]) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="nota-conta">Remova este arquivo antes de publicar o sistema em um servidor real.</p>
  </section>
</main>
</body>
</html>
