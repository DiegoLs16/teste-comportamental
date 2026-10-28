TESTE DE PERFIL COMPORTAMENTAL - ÁGUIA, GATO, LOBO E TUBARÃO
============================================================
Front-end: kit fornecido pelo professor (HTML das páginas e css/estilo.css, sem alterações no CSS).
Backend: PHP desenvolvido pelo aluno.

COMO EXECUTAR (XAMPP / WAMP / Laragon)
1. Copie a pasta "teste-comportamental" para htdocs (XAMPP) ou www (Laragon).
2. Inicie o Apache.
3. Acesse http://localhost/teste-comportamental/
   Ou, pelo terminal, dentro da pasta: php -S localhost:8000  ->  http://localhost:8000/
Requisitos: PHP 7.4 ou superior com mbstring e pdo_mysql (já vêm no XAMPP/Laragon).

BANCO DE DADOS (só para login/cadastro/histórico)
1. Inicie o MySQL (XAMPP/Laragon).
2. Importe o arquivo banco.sql (phpMyAdmin > aba SQL, colar e executar).
   Se você não importar, o sistema tenta criar o banco sozinho na primeira conexão.
3. Se o seu MySQL não for "root" sem senha, ajuste as constantes em conexao.php.
   Se aparecer erro de banco, o detalhe técnico é mostrado na tela (DB_DEBUG em conexao.php):
   [2002] connection refused = MySQL desligado | [1045] access denied = usuário/senha errados
   could not find driver = extensão pdo_mysql desativada | porta diferente = ajuste DB_PORTA
4. Abra diagnostico_sessao.php para conferir se sessão e banco estão funcionando.
O banco é obrigatório: o teste só abre para quem tem conta.

ARQUIVOS
cadastro.php          Cria conta (nome, e-mail, senha com password_hash)
login.php             Formulário de entrada
verificar_login.php   Confere e-mail e senha (password_verify) e abre a sessão
logout.php            Encerra a sessão
minha_conta.php       Dados da conta e histórico de resultados (exige login)
diagnostico_sessao.php Mostra o estado da sessão e do banco (remover antes de publicar)
conexao.php           Conexão PDO com o MySQL
banco.sql             Cria o banco e as tabelas usuarios e resultados
includes/auth.php     Funções de login e barra de navegação
includes/historico.php Grava e lista resultados no banco
css/conta.css         Estilos das páginas de conta (complementa o estilo.css do professor)
index.php             Início: lê o erro do nome e envia o nome por POST
teste.php             Valida o nome, gera as 24 perguntas com foreach (alternativas embaralhadas)
resultado.php         Valida nome e respostas, calcula pontos, empate e percentuais
dados/perguntas.php   $perguntas: as 24 perguntas (cada alternativa tem o campo 'perfil')
dados/perfis.php      Nome, título e descrição de cada perfil
includes/funcoes.php  Regra de negócio: validação, pontuação, empate, percentuais
css/estilo.css        Estilos do professor

FLUXO
index.php -> (POST nome) -> teste.php -> (POST nome + respostas) -> resultado.php
Primeiro acesso: qualquer página protegida leva ao login.php.
cadastro.php ou login.php -> verificar_login.php -> index.php (tela inicial do teste)
index.php, teste.php e resultado.php exigem login (exigir_login() em includes/auth.php).
resultado.php grava o resultado no banco e minha_conta.php mostra o histórico.

COMO FUNCIONA
- Cada <input type="radio"> usa name="respostas[N]" e value = perfil da alternativa.
- O PHP confere se cada valor é um perfil válido e soma 1 ponto por resposta.
- Maior pontuação = perfil predominante. Empate = "Resultado combinado: X + Y".
- Percentual = pontos / 24 x 100 (ex.: 8/24 = 33,33%).
- Se faltar resposta, volta ao teste com .alerta e o cartão .sem-resposta, mantendo o que já foi marcado.
- O JavaScript só atualiza a barra de progresso. Todo o cálculo é em PHP.

DESAFIOS EXTRAS FEITOS
- Alternativas embaralhadas (a ordem fica na sessão; o perfil acompanha o texto).
- Barra de progresso; voltar para corrigir respostas; resultado em $_SESSION.
- Versão imprimível (botão Imprimir); classificação feita no servidor.
- Perguntas, perfis e regras em arquivos PHP separados.

PARA A DEFESA
- Nova pergunta: copiar um bloco em dados/perguntas.php (o total é contado automaticamente).
- Descrição de perfil: dados/perfis.php.
- Regra de pontos: calcular_pontuacao() em includes/funcoes.php.
- Critério de empate: perfis_predominantes() em includes/funcoes.php.
