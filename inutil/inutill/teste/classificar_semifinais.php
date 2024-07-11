<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
function classificarSemifinais() {
    global $conn;

    // Limpar a tabela de semifinais e confrontos
    $conn->query("TRUNCATE TABLE semifinais");
    $conn->query("TRUNCATE TABLE semifinais_confrontos");

    // Obter os times classificados para as semifinais
    $result = $conn->query("SELECT * FROM quartas_de_final ORDER BY id LIMIT 4");
    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

    if (count($times) != 4) {
        die("Número de times classificados insuficiente para semifinais.");
    }

    // Organizar os confrontos das semifinais
    $confrontos = [
        [$times[0], $times[1]],
        [$times[2], $times[3]]
    ];

    foreach ($confrontos as $confronto) {
        $timeA = $confronto[0];
        $timeB = $confronto[1];

        // Inserir os times classificados para as semifinais
        $stmt = $conn->prepare("INSERT INTO semifinais (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();

        // Inserir os confrontos das semifinais
        $stmt = $conn->prepare("INSERT INTO semifinais_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'semifinais')");
        $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();
    }
}

classificarSemifinais();
?>
