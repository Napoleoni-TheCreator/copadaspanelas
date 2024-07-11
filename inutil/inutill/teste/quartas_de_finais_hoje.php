<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

// Função para verificar se dois times são do mesmo grupo
function mesmoGrupo($timeA, $timeB) {
    return $timeA['grupo_id'] == $timeB['grupo_id'];
}

// Obtém a fase final configurada
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

// Define a tabela de acordo com a fase final
$tabelaClassificados = '';
$tabelaConfrontos = '';
switch ($faseFinal) {
    case 'oitavas':
        $tabelaClassificados = 'oitavas_de_final';
        $tabelaConfrontos = 'oitavas_de_final_confrontos';
        break;
    case 'quartas':
        $tabelaClassificados = 'quartas_de_final';
        $tabelaConfrontos = 'quartas_de_final_confrontos';
        break;
    case 'semifinais':
        $tabelaClassificados = 'semifinais';
        $tabelaConfrontos = 'semifinais_confrontos';
        break;
    case 'final':
        $tabelaClassificados = 'final';
        $tabelaConfrontos = 'final_confrontos';
        break;
    default:
        die("Fase final desconhecida.");
}

// Obtém os times classificados
$sqlClassificados = "SELECT t.id, t.nome, t.logo, t.grupo_id, t.pts, t.vitorias, t.empates, t.derrotas, t.gm, t.gc, t.sg
                      FROM $tabelaClassificados tf
                      JOIN times t ON tf.time_id = t.id
                      ORDER BY t.pts DESC, t.gm DESC, t.gc ASC, t.sg DESC";
$resultClassificados = $conn->query($sqlClassificados);
if (!$resultClassificados) {
    die("Erro na consulta de times classificados: " . $conn->error);
}

// Organiza os times classificados em um array
$classificados = [];
while ($row = $resultClassificados->fetch_assoc()) {
    $classificados[] = $row;
}

// Função para gerar confrontos com base na fase final
function gerarConfrontos($classificados, $faseFinal) {
    $confrontos = [];
    $numTimes = count($classificados);

    if ($faseFinal == 'oitavas') {
        // Oitavas de Finais: 16 times, 8 confrontos
        for ($i = 0; $i < $numTimes / 2; $i++) {
            $timeA = $classificados[$i];
            $timeB = $classificados[$numTimes - $i - 1];
            $confrontos[] = [$timeA['nome'], $timeB['nome']];
        }
    } elseif ($faseFinal == 'quartas') {
        // Quartas de Finais: 8 times, 4 confrontos
        $meio = $numTimes / 2;
        for ($i = 0; $i < $meio; $i++) {
            $timeA = $classificados[$i];
            $timeB = $classificados[$numTimes - $i - 1];
            // Verifica se os times são do mesmo grupo e ajusta o confronto se necessário
            if (mesmoGrupo($timeA, $timeB)) {
                for ($j = $i + 1; $j < $meio; $j++) {
                    $timeB = $classificados[$numTimes - $j - 1];
                    if (!mesmoGrupo($timeA, $timeB)) {
                        $confrontos[] = [$timeA['nome'], $timeB['nome']];
                        $classificados[$numTimes - $j - 1] = $classificados[$numTimes - $i - 1];
                        $classificados[$numTimes - $i - 1] = $timeB;
                        break;
                    }
                }
            } else {
                $confrontos[] = [$timeA['nome'], $timeB['nome']];
            }
        }
    } elseif ($faseFinal == 'semifinais') {
        // Semifinais: 4 times, 2 confrontos
        for ($i = 0; $i < $numTimes / 2; $i++) {
            $timeA = $classificados[$i];
            $timeB = $classificados[$numTimes - $i - 1];
            $confrontos[] = [$timeA['nome'], $timeB['nome']];
        }
    } elseif ($faseFinal == 'final') {
        // Final: 2 times, 1 confronto
        $timeA = $classificados[0];
        $timeB = $classificados[1];
        $confrontos[] = [$timeA['nome'], $timeB['nome']];
    }

    return $confrontos;
}

// Gera os confrontos
$confrontos = gerarConfrontos($classificados, $faseFinal);

// Limpa a tabela de confrontos
$sqlTruncate = "TRUNCATE TABLE $tabelaConfrontos";
if (!$conn->query($sqlTruncate)) {
    die("Erro ao limpar tabela $tabelaConfrontos: " . $conn->error);
}

// Prepara a consulta de inserção de confrontos
$stmtConfronto = $conn->prepare("INSERT INTO $tabelaConfrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, ?)");
if (!$stmtConfronto) {
    die("Erro na preparação da consulta de confrontos: " . $conn->error);
}

// Insere os confrontos na tabela
foreach ($confrontos as $confronto) {
    $timeA_nome = $confronto[0];
    $timeB_nome = $confronto[1];
    $stmtConfronto->bind_param('sss', $timeA_nome, $timeB_nome, $faseFinal);
    if (!$stmtConfronto->execute()) {
        die("Erro ao inserir confronto entre $timeA_nome e $timeB_nome: " . $stmtConfronto->error);
    }
}

$stmtConfronto->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confrontos das Fases Finais</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        #confrontos-wrapper {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
            overflow-x: auto;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .confronto {
            margin-bottom: 20px;
        }
        .confronto-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .matchup {
            font-size: 18px;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div id="confrontos-wrapper">
        <h1>Confrontos das Fases Finais</h1>
        <div class="confronto">
            <div class="confronto-header">Confrontos</div>
            <?php
            if (!empty($confrontos)) {
                foreach ($confrontos as $confronto) {
                    $timeA_nome = $confronto[0];
                    $timeB_nome = $confronto[1];
                    
                    echo '<div class="matchup">';
                    echo htmlspecialchars($timeA_nome);
                    echo ' x ';
                    echo htmlspecialchars($timeB_nome);
                    echo '</div>';
                }
            } else {
                echo '<div class="matchup">Nenhum confronto disponível.</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
