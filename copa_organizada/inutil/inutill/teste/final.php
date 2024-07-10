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

// Verifica se a fase final é 'semifinais'
if ($faseFinal !== 'semifinais') {
    die("A fase final não é semifinais.");
}

// Obtém os vencedores das semifinais
$sqlVencedoresSemifinais = "SELECT id, timeA_nome, timeB_nome, gols_marcados_timeA, gols_marcados_timeB
                            FROM semifinais_confrontos";
$resultVencedoresSemifinais = $conn->query($sqlVencedoresSemifinais);
if (!$resultVencedoresSemifinais) {
    die("Erro na consulta de vencedores das semifinais: " . $conn->error);
}

$vencedoresSemifinais = [];
while ($row = $resultVencedoresSemifinais->fetch_assoc()) {
    // Determina o time vencedor baseado nos gols marcados
    if ($row['gols_marcados_timeA'] > $row['gols_marcados_timeB']) {
        $vencedoresSemifinais[] = $row['timeA_nome'];
    } else {
        $vencedoresSemifinais[] = $row['timeB_nome'];
    }
}

// Verifica se temos 2 times classificados
if (count($vencedoresSemifinais) !== 2) {
    die("Número de times classificados para a final está incorreto.");
}

// Define o confronto da final
$finalConfronto = [
    [$vencedoresSemifinais[0], $vencedoresSemifinais[1]],
];

// Limpa a tabela final_confrontos antes de inserir novos dados
$sqlDelete = "DELETE FROM final_confrontos";
if (!$conn->query($sqlDelete)) {
    die("Erro ao limpar tabela final_confrontos: " . $conn->error);
}

// Insere o confronto da final na tabela final_confrontos
$sqlInsert = "INSERT INTO final_confrontos (timeA_nome, timeB_nome) VALUES (?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
if (!$stmtInsert) {
    die("Erro na preparação da consulta de inserção: " . $conn->error);
}

foreach ($finalConfronto as $confronto) {
    $stmtInsert->bind_param('ss', $confronto[0], $confronto[1]);
    if (!$stmtInsert->execute()) {
        die("Erro ao inserir confronto: " . $stmtInsert->error);
    }
}
$stmtInsert->close();

// Obtém o confronto da final para exibir
$sqlFinal = "SELECT timeA_nome, timeB_nome FROM final_confrontos";
$resultFinal = $conn->query($sqlFinal);
if (!$resultFinal) {
    die("Erro na consulta de final: " . $conn->error);
}

$final = [];
while ($row = $resultFinal->fetch_assoc()) {
    $final[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Final</title>
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
        #final-wrapper {
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
        .final {
            margin-bottom: 20px;
        }
        .final-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .final-teams {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div id="final-wrapper">
        <h1>Final</h1>
        <div class="final">
            <div class="final-header">Confronto da Final</div>
            <?php foreach ($final as $index => $confronto): ?>
                <div class="final-teams">
                    <div><?php echo htmlspecialchars($confronto['timeA_nome']); ?></div>
                    <div>x</div>
                    <div><?php echo htmlspecialchars($confronto['timeB_nome']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>
