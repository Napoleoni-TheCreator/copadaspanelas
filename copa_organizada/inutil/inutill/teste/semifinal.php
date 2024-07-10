<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

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

// Verifica se a fase final é 'quartas'
if ($faseFinal !== 'quartas') {
    die("A fase final não é quartas de finais.");
}

// Obtém os vencedores das quartas de finais
$sqlVencedoresQuartas = "SELECT id, timeA_nome, timeB_nome, gols_marcados_timeA, gols_marcados_timeB
                         FROM quartas_de_final_confrontos";
$resultVencedoresQuartas = $conn->query($sqlVencedoresQuartas);
if (!$resultVencedoresQuartas) {
    die("Erro na consulta de vencedores das quartas de finais: " . $conn->error);
}

$vencedoresQuartas = [];
while ($row = $resultVencedoresQuartas->fetch_assoc()) {
    // Determina o time vencedor baseado nos gols marcados
    if ($row['gols_marcados_timeA'] > $row['gols_marcados_timeB']) {
        $vencedoresQuartas[] = $row['timeA_nome'];
    } else {
        $vencedoresQuartas[] = $row['timeB_nome'];
    }
}

// Verifica se temos 4 times classificados
if (count($vencedoresQuartas) !== 4) {
    die("Número de times classificados para as semifinais está incorreto.");
}

// Define os confrontos das semifinais
$semifinalConfrontos = [
    [$vencedoresQuartas[0], $vencedoresQuartas[1]],
    [$vencedoresQuartas[2], $vencedoresQuartas[3]],
];

// Limpa a tabela semifinais_confrontos antes de inserir novos dados
$sqlDelete = "DELETE FROM semifinais_confrontos";
if (!$conn->query($sqlDelete)) {
    die("Erro ao limpar tabela semifinais_confrontos: " . $conn->error);
}

// Insere os confrontos das semifinais na tabela semifinais_confrontos
$sqlInsert = "INSERT INTO semifinais_confrontos (timeA_nome, timeB_nome) VALUES (?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
if (!$stmtInsert) {
    die("Erro na preparação da consulta de inserção: " . $conn->error);
}

foreach ($semifinalConfrontos as $confronto) {
    $stmtInsert->bind_param('ss', $confronto[0], $confronto[1]);
    if (!$stmtInsert->execute()) {
        die("Erro ao inserir confronto: " . $stmtInsert->error);
    }
}
$stmtInsert->close();

// Obtém os confrontos das semifinais para exibir
$sqlSemifinais = "SELECT timeA_nome, timeB_nome FROM semifinais_confrontos";
$resultSemifinais = $conn->query($sqlSemifinais);
if (!$resultSemifinais) {
    die("Erro na consulta de semifinais: " . $conn->error);
}

$semifinais = [];
while ($row = $resultSemifinais->fetch_assoc()) {
    $semifinais[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Semifinais</title>
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
        #semifinal-wrapper {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .semifinal {
            margin-bottom: 20px;
        }
        .semifinal-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .semifinal-teams {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div id="semifinal-wrapper">
        <h1>Semifinais</h1>
        <div class="semifinal">
            <div class="semifinal-header">Confrontos das Semifinais</div>
            <?php foreach ($semifinais as $index => $confronto): ?>
                <div class="semifinal-teams">
                    <div><?php echo htmlspecialchars($confronto['timeA_nome']); ?></div>
                    <div>x</div>
                    <div><?php echo htmlspecialchars($confronto['timeB_nome']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
