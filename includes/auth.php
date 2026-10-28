<?php
/**
 * Funções de login/sessão e a barra de navegação da conta.
 * Usa as funções de includes/funcoes.php (e(), redirecionar()).
 */

/** O usuário está logado? */
function usuario_logado()
{
    return !empty($_SESSION['usuario']['id']);
}

/** Dados do usuário logado (id, nome, email) ou null. */
function usuario_atual()
{
    return usuario_logado() ? $_SESSION['usuario'] : null;
}

/**
 * Páginas restritas chamam isto no início.
 * Quem não está logado é enviado para login.php (com um aviso opcional).
 */
function exigir_login($aviso = null)
{
    if (!usuario_logado()) {
        if ($aviso !== null) {
            $_SESSION['aviso'] = $aviso;
        }
        redirecionar('login.php');
    }
}

/** Lê uma mensagem guardada na sessão e a apaga (aparece uma vez só). */
function mensagem_unica($chave)
{
    $mensagem = isset($_SESSION[$chave]) ? $_SESSION[$chave] : null;
    unset($_SESSION[$chave]);
    return $mensagem;
}

/** Registra o usuário como logado (e troca o id da sessão, por segurança). */
function entrar($id, $nome, $email)
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = ['id' => (int) $id, 'nome' => $nome, 'email' => $email];
}

/** Barra com os links da conta, mostrada no topo das páginas. */
function barra_usuario()
{
    ?>
  <nav class="nav-conta" aria-label="Conta">
    <?php if (usuario_logado()): ?>
      <a href="index.php">Início</a>
      <a href="minha_conta.php">Minha conta</a>
      <span class="nav-ola">Olá, <?= e($_SESSION['usuario']['nome']) ?></span>
      <a href="logout.php">Sair</a>
    <?php else: ?>
      <a href="login.php">Entrar</a>
      <a href="cadastro.php">Criar conta</a>
    <?php endif; ?>
  </nav>
<?php
}
