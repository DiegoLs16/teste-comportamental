<?php
/**
 * conexao.php — conexão com o MySQL usando PDO.
 * Ajuste as constantes abaixo se o seu servidor usar outros dados.
 * (XAMPP e Laragon: usuário "root", senha vazia, porta 3306.)
 */
const DB_HOST    = 'localhost';
const DB_PORTA   = 3306;
const DB_NOME    = 'teste_comportamental';
const DB_USUARIO = 'root';
const DB_SENHA   = '';

// true = mostra o detalhe técnico do erro de banco na tela (útil enquanto você configura).
// Coloque false na versão final.
const DB_DEBUG = true;

/** Opções padrão do PDO. */
function opcoes_pdo()
{
    return [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
}

/**
 * Devolve a conexão (criada uma única vez). Lança PDOException se falhar.
 * Se o banco ainda não existir, ele é criado a partir do banco.sql.
 */
function conectar()
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORTA . ';dbname=' . DB_NOME . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, DB_USUARIO, DB_SENHA, opcoes_pdo());
    } catch (PDOException $ex) {
        // Erro 1049 = "Unknown database": cria o banco e as tabelas e tenta de novo.
        if ((int) $ex->getCode() === 1049 || strpos($ex->getMessage(), '1049') !== false) {
            criar_banco();
            $pdo = new PDO($dsn, DB_USUARIO, DB_SENHA, opcoes_pdo());
        } else {
            throw $ex;
        }
    }
    return $pdo;
}

/** Executa o banco.sql (cria o banco e as tabelas usuarios e resultados). */
function criar_banco()
{
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORTA . ';charset=utf8mb4',
        DB_USUARIO,
        DB_SENHA,
        opcoes_pdo()
    );

    $sql = file_get_contents(__DIR__ . '/banco.sql');
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);   // remove linhas de comentário

    foreach (explode(';', $sql) as $comando) {
        $comando = trim($comando);
        if ($comando !== '') {
            $pdo->exec($comando);
        }
    }
}

/** Texto técnico do erro, mostrado só quando DB_DEBUG = true. */
function detalhe_erro_db($ex)
{
    return DB_DEBUG ? ' Detalhe técnico: ' . $ex->getMessage() : '';
}
