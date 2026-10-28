<?php
/* cadastro.php — cria uma conta (nome, e-mail e senha). */
require __DIR__ . '/includes/funcoes.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/conexao.php';
iniciar_sessao();

if (usuario_logado()) {
    redirecionar('index.php');
}

$erros = [];
$nome  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha     = isset($_POST['senha']) ? (string) $_POST['senha'] : '';
    $confirmar = isset($_POST['confirmar']) ? (string) $_POST['confirmar'] : '';

    // Validações
    if (($erro_nome = validar_nome($nome)) !== null) {
        $erros[] = $erro_nome;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email, 'UTF-8') > 120) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (mb_strlen($senha, 'UTF-8') < 8) {
        $erros[] = 'A senha precisa ter pelo menos 8 caracteres.';
    }
    if ($senha !== $confirmar) {
        $erros[] = 'A confirmação da senha não confere.';
    }

    // Grava no banco
    if (!$erros) {
        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $erros[] = 'Já existe uma conta com esse e-mail. Tente entrar.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);   // nunca guardar a senha pura
                $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)')
                    ->execute([$nome, $email, $hash]);

                entrar($pdo->lastInsertId(), $nome, $email);
                $_SESSION['sucesso'] = 'Conta criada com sucesso. Agora é só fazer o teste.';
                redirecionar('index.php');
            }
        } catch (PDOException $ex) {
            $erros[] = 'Não foi possível acessar o banco de dados. Confira o MySQL e o arquivo conexao.php.' . detalhe_erro_db($ex);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar conta — Teste de perfil</title>
  <link rel="stylesheet" href="css/estilo.css">
  <link rel="stylesheet" href="css/conta.css">
</head>
<body>
<main class="container">
  <?php barra_usuario(); ?>

  <h1 class="titulo-pagina">Criar conta</h1>
  <p>Para fazer o teste é preciso ter uma conta. Seus resultados ficam guardados em “Minha conta”.</p>

  <section class="painel">
    <?php if ($erros): ?>
      <div class="alerta" role="alert">
        <strong>Corrija para continuar:</strong>
        <?php foreach ($erros as $erro): ?>
          <?= e($erro) ?><br>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="cadastro.php" method="post">
      <label class="campo" for="nome">Nome</label>
      <input type="text" id="nome" name="nome" maxlength="60" autocomplete="name" value="<?= e($nome) ?>">

      <label class="campo" for="email">E-mail</label>
      <input type="email" id="email" name="email" maxlength="120" autocomplete="email" value="<?= e($email) ?>">

      <label class="campo" for="senha">Senha</label>
      <input type="password" id="senha" name="senha" autocomplete="new-password">
      <span class="dica">Mínimo de 8 caracteres.</span>

      <label class="campo" for="confirmar">Confirmar senha</label>
      <input type="password" id="confirmar" name="confirmar" autocomplete="new-password">

      <button class="btn" type="submit">Criar conta</button>
    </form>
    <p class="rodape-form">Já tem conta? <a href="login.php">Entrar</a></p>
  </section>
</main>
</body>
</html>
