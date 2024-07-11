<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
function classificarQuartas() {
    global $conn;

    // Limpar as tabelas de quartas de final e confrontos
    $conn->query("TRUNCATE TABLE quartas_de_final");
    $conn->query("TRUNCATE TABLE quartas_de_final_confrontos");

    // Obter os times classificados para as quartas de final
    $result = $conn->query("SELECT * FROM oitavas_de_final");
    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

    // Organizar os confrontos das quartas de final
    $confrontos = [
        [$times[0], $times[1]],
        [$times[2], $times[3]],
        [$times[4], $times[5]],
        [$times[6], $times[7]]
    ];

    foreach ($confrontos as $confronto) {
        $timeA = $confronto[0];
        $timeB = $confronto[1];

        // Inserir os times classificados para as quartas de final
        $stmt = $conn->prepare("INSERT INTO quartas_de_final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();

        // Inserir os confrontos das quartas de final
        $stmt = $conn->prepare("INSERT INTO quartas_de_final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'quartas')");
        $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();
    }
}

classificarQuartas();
?>
