<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

include '../../config/conexao.php';

// Verifica se o botão foi clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $queries = [
        "INSERT INTO grupos_historico (id, nome) SELECT id, nome FROM grupos",
        "INSERT INTO times_historico (id, nome, logo, grupo_id, token, pts, vitorias, empates, derrotas, gm, gc, sg) SELECT id, nome, logo, grupo_id, token, pts, vitorias, empates, derrotas, gm, gc, sg FROM times",
        "INSERT INTO jogadores_historico (id, nome, gols, posicao, numero, assistencias, cartoes_amarelos, cartoes_vermelhos, token, imagem, time_id) SELECT id, nome, gols, posicao, numero, assistencias, cartoes_amarelos, cartoes_vermelhos, token, imagem, time_id FROM jogadores",
        "INSERT INTO jogos_historico (id, time_id, resultado, data_jogo) SELECT id, time_id, resultado, data_jogo FROM jogos",
        "INSERT INTO jogos_fase_grupos_historico (id, grupo_id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo, rodada) SELECT id, grupo_id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo, rodada FROM jogos_fase_grupos",
        "INSERT INTO quartas_de_final_historico (id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo) SELECT id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo FROM quartas_de_final",
        "INSERT INTO oitavas_de_final_historico (id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo) SELECT id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo FROM oitavas_de_final",
        "INSERT INTO semifinais_historico (id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo) SELECT id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo FROM semifinais",
        "INSERT INTO final_historico (id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo) SELECT id, timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo FROM final"
    ];

    foreach ($queries as $query) {
        try {
            if (!$conn->query($query)) {
                $errors[] = "Erro ao executar consulta: \"$query\" - " . $conn->error;
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = "Erro ao executar consulta: \"$query\" - " . $e->getMessage();
        }
    }

    // Se não houver erros, redireciona para a página anterior
    if (empty($errors)) {
        $_SESSION['success_message'] = "Dados históricos salvos com sucesso!";
    } else {
        $_SESSION['error_messages'] = $errors;
    }

    // Redirecionar para a página anterior
    $previousPage = $_SERVER['HTTP_REFERER'] ?? 'http://localhost/copadaspanelas/app/pages/adm/adicionar_times.php';
    header("Location: $previousPage");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salvar Histórico</title>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #isso_ai {
            font-size: 30px;
        }
        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<?php require_once 'header_classificacao.php'; ?>
<body>
    <div class="container">
        <h1 id="isso_ai">Salvar Dados Históricos</h1>
        <form method="post">
            <button type="submit" class="btn btn-primary">Salvar Histórico</button>
        </form>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success mt-3">
                <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_messages'])): ?>
            <div class="alert alert-danger mt-3">
                <?= implode("<br>", $_SESSION['error_messages']); unset($_SESSION['error_messages']); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
<?php require_once '../footer.php'; ?>
</html>
