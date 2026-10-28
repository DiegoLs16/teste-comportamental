<?php
/**
 * Funções do sistema: sessão, validação e regra de negócio.
 * Toda a classificação é feita aqui, no servidor (PHP).
 */

/** Inicia a sessão uma única vez. */
function iniciar_sessao()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/** Escapa texto para exibir com segurança no HTML. */
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** Redireciona e encerra o script. */
function redirecionar($url)
{
    header('Location: ' . $url);
    exit;
}

/** Limpa tudo que pertence a uma tentativa do teste. */
function limpar_tentativa()
{
    unset(
        $_SESSION['nome'],
        $_SESSION['ordem'],
        $_SESSION['respostas'],
        $_SESSION['faltando'],
        $_SESSION['resultado'],
        $_SESSION['historico_salvo']
    );
}

/**
 * Valida o nome do participante.
 * Retorna uma mensagem de erro ou null se estiver tudo certo.
 */
function validar_nome($nome)
{
    $nome = trim($nome);

    if ($nome === '') {
        return 'Informe seu nome para começar.';
    }
    $tamanho = mb_strlen($nome, 'UTF-8');
    if ($tamanho < 2) {
        return 'O nome precisa ter pelo menos 2 letras.';
    }
    if ($tamanho > 60) {
        return 'O nome pode ter no máximo 60 caracteres.';
    }
    if (!preg_match('/^[\p{L}\p{M}][\p{L}\p{M}\s.\'-]*$/u', $nome)) {
        return 'Use apenas letras, espaços, ponto, apóstrofo ou hífen no nome.';
    }
    return null;
}

/**
 * Sorteia, para cada pergunta, a ordem em que as alternativas serão exibidas.
 * Exemplo: ordem[0] = [2, 0, 3, 1] mostra primeiro a alternativa de índice 2,
 * depois a 0, a 3 e a 1. Cada alternativa continua levando o seu 'perfil'.
 * A ordem fica na sessão para não mudar se a página for reexibida.
 */
function embaralhar_ordem($perguntas)
{
    $ordem = [];
    foreach ($perguntas as $pergunta) {
        $indices = range(0, count($pergunta['alternativas']) - 1);
        shuffle($indices);
        $ordem[] = $indices;
    }
    return $ordem;
}

/**
 * Confere as respostas recebidas por POST.
 * Cada resposta deve ser uma das chaves de perfil (aguia, gato, lobo, tubarao).
 * Retorna [respostas válidas, índices das perguntas sem resposta válida].
 */
function validar_respostas($total_perguntas, $enviado, $perfis)
{
    $validas  = [];
    $faltando = [];

    for ($i = 0; $i < $total_perguntas; $i++) {
        $valor = isset($enviado[$i]) ? $enviado[$i] : null;

        if (is_string($valor) && isset($perfis[$valor])) {
            $validas[$i] = $valor;
        } else {
            $faltando[] = $i;
        }
    }
    return [$validas, $faltando];
}

/** Soma 1 ponto para o perfil de cada resposta. */
function calcular_pontuacao($respostas, $perfis)
{
    $pontuacao = [];
    foreach (array_keys($perfis) as $chave) {
        $pontuacao[$chave] = 0;
    }

    foreach ($respostas as $perfil) {
        if (isset($pontuacao[$perfil])) {
            $pontuacao[$perfil]++;
        }
    }
    return $pontuacao;
}

/** Retorna a lista de perfis com a maior pontuação (mais de um = empate). */
function perfis_predominantes($pontuacao)
{
    $maior = max($pontuacao);
    $predominantes = [];

    foreach ($pontuacao as $perfil => $pontos) {
        if ($pontos == $maior) {
            $predominantes[] = $perfil;
        }
    }
    return $predominantes;
}

/** Percentual de cada perfil: pontos ÷ total de perguntas × 100. */
function calcular_percentuais($pontuacao, $total_perguntas)
{
    $percentuais = [];
    foreach ($pontuacao as $perfil => $pontos) {
        $percentuais[$perfil] = round(($pontos / $total_perguntas) * 100, 2);
    }
    return $percentuais;
}

/** Texto para exibir: 33,33% */
function formatar_percentual($valor)
{
    return number_format($valor, 2, ',', '.') . '%';
}

/** Número com ponto decimal, para usar em style="width:..%" (independe do idioma). */
function largura_css($valor)
{
    return number_format($valor, 2, '.', '');
}

/** Título do resultado, tratando o caso de empate. */
function titulo_resultado($predominantes, $perfis)
{
    $nomes = [];
    foreach ($predominantes as $chave) {
        $nomes[] = $perfis[$chave]['nome'];
    }
    return implode(' + ', $nomes);
}
