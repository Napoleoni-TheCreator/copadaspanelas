<?php
// Inclua o arquivo de conexão com o banco de dados
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

// Função para obter e classificar os times
function getClassificados($conn) {
    // Obtém a configuração atual da fase final
    $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
    $resultConfig = $conn->query($sqlConfig);
    if (!$resultConfig) {
        die("Erro na consulta de configuração: " . $conn->error);
    }
    $faseFinal = 'oitavas'; // Default
    if ($resultConfig->num_rows > 0) {
        $rowConfig = $resultConfig->fetch_assoc();
        $faseFinal = $rowConfig['fase_final'];
    }

    // Define a quantidade de times a serem classificados com base na fase final
    $totalClassificados = ($faseFinal == 'oitavas') ? 16 : 8;

    // Obtém a quantidade de grupos e seus nomes
    $sqlGrupos = "SELECT id, nome FROM grupos";
    $resultGrupos = $conn->query($sqlGrupos);
    if (!$resultGrupos) {
        die("Erro na consulta de grupos: " . $conn->error);
    }
    $grupos = [];
    while ($rowGrupos = $resultGrupos->fetch_assoc()) {
        $grupos[$rowGrupos['id']] = $rowGrupos['nome'];
    }
    $numGrupos = count($grupos);

    // Calcula o número de times classificados por grupo
    $classificadosPorGrupo = intval($totalClassificados / $numGrupos);

    // Obtém os times classificados de cada grupo
    $classificados = [];
    foreach ($grupos as $grupoId => $grupoNome) {
        $sqlTimes = "SELECT t.id, t.nome, t.logo, t.pts, t.gm, t.gc, t.sg, t.grupo_id,
                            (t.vitorias + t.empates) AS vitorias, t.empates, t.derrotas
                     FROM times t
                     WHERE t.grupo_id = $grupoId
                     ORDER BY t.pts DESC, t.gm DESC, t.gc ASC, t.sg DESC
                     LIMIT $classificadosPorGrupo";
        $resultTimes = $conn->query($sqlTimes);
        if (!$resultTimes) {
            die("Erro na consulta de times do grupo $grupoNome: " . $conn->error);
        }

        while ($row = $resultTimes->fetch_assoc()) {
            $classificados[$grupoNome][] = $row;
        }
    }

    return $classificados;
}

function inserirClassificadosAutomatica($conn) {
    // Obtém a configuração atual da fase final
    $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
    $resultConfig = $conn->query($sqlConfig);
    if (!$resultConfig) {
        die("Erro na consulta de configuração: " . $conn->error);
    }
    $faseFinal = 'quartas'; // Define a fase final como quartas para esta função

    // Define a quantidade de times a serem classificados com base na fase final
    $totalClassificados = ($faseFinal == 'oitavas') ? 16 : 8;

    // Obtém a quantidade de grupos e seus nomes
    $sqlGrupos = "SELECT id, nome FROM grupos";
    $resultGrupos = $conn->query($sqlGrupos);
    if (!$resultGrupos) {
        die("Erro na consulta de grupos: " . $conn->error);
    }
    $grupos = [];
    while ($rowGrupos = $resultGrupos->fetch_assoc()) {
        $grupos[$rowGrupos['id']] = $rowGrupos['nome'];
    }
    $numGrupos = count($grupos);

    // Calcula o número de times classificados por grupo
    $classificadosPorGrupo = intval($totalClassificados / $numGrupos);

    // Obtém os times classificados de cada grupo
    $classificados = [];
    foreach ($grupos as $grupoId => $grupoNome) {
        $sqlTimes = "SELECT t.id, t.nome, t.logo, t.pts, t.gm, t.gc, t.sg, t.grupo_id,
                            (t.vitorias + t.empates) AS vitorias, t.empates, t.derrotas
                     FROM times t
                     WHERE t.grupo_id = $grupoId
                     ORDER BY t.pts DESC, t.gm DESC, t.gc ASC, t.sg DESC
                     LIMIT $classificadosPorGrupo";
        $resultTimes = $conn->query($sqlTimes);
        if (!$resultTimes) {
            die("Erro na consulta de times do grupo $grupoNome: " . $conn->error);
        }

        while ($row = $resultTimes->fetch_assoc()) {
            $classificados[$grupoNome][] = $row;
        }
    }

    // Define a tabela de destino como quartas de final
    $tabelaDestino = 'quartas_de_final';

    // Limpa a tabela de destino antes de inserir novos dados
    $sqlTruncate = "TRUNCATE TABLE $tabelaDestino";
    if (!$conn->query($sqlTruncate)) {
        die("Erro ao limpar tabela $tabelaDestino: " . $conn->error);
    }

    // Prepara a consulta de inserção na tabela de fase final
    $stmtFase = $conn->prepare("INSERT INTO $tabelaDestino (time_id, time_nome, grupo_nome) VALUES (?, ?, ?)");

    if (!$stmtFase) {
        die("Erro na preparação da consulta de fase final: " . $conn->error);
    }

    // Processa cada time
    foreach ($classificados as $grupoNome => $times) {
        foreach ($times as $time) {
            // Insere o time na tabela de fase final
            $stmtFase->bind_param(
                'iss',
                $time['id'],         // ID do time
                $time['nome'],       // Nome do time
                $grupoNome           // Nome do grupo
            );
            if (!$stmtFase->execute()) {
                die("Erro ao inserir time {$time['nome']} na tabela $tabelaDestino: " . $stmtFase->error);
            }
        }
    }

    $stmtFase->close();
}

// Chama a função para inserir os times automaticamente
inserirClassificadosAutomatica($conn);
?>
