<?php
/** Histórico de resultados guardado no banco (somente para usuários logados). */
require_once __DIR__ . '/../conexao.php';

/** Grava um resultado calculado pelo PHP. */
function salvar_resultado($usuario_id, $nome, $pontuacao, $predominantes)
{
    $sql = 'INSERT INTO resultados
              (usuario_id, nome_usado, pontos_aguia, pontos_gato, pontos_lobo, pontos_tubarao, predominantes)
            VALUES (?, ?, ?, ?, ?, ?, ?)';
    conectar()->prepare($sql)->execute([
        $usuario_id,
        $nome,
        $pontuacao['aguia'],
        $pontuacao['gato'],
        $pontuacao['lobo'],
        $pontuacao['tubarao'],
        implode(',', $predominantes),
    ]);
}

/** Últimos resultados do usuário, do mais novo para o mais antigo. */
function listar_resultados($usuario_id, $limite = 20)
{
    $stmt = conectar()->prepare(
        'SELECT * FROM resultados WHERE usuario_id = ? ORDER BY criado_em DESC, id DESC LIMIT ' . (int) $limite
    );
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}
