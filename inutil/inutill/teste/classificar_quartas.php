<?php
include '../../../app/config/conexao.php';

function classificarQuartas() {
    global $conn;

    // Limpar as tabelas de quartas de final e confrontos
    $conn->query("TRUNCATE TABLE quartas_de_final");
    $conn->query("TRUNCATE TABLE quartas_de_final_confrontos");

    // Obter todos os times classificados
    $result = $conn->query("SELECT * FROM times");
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
        $stmt->bind_param("iss", $timeA['id'], $timeA['grupo_nome'], $timeA['nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['id'], $timeB['grupo_nome'], $timeB['nome']);
        $stmt->execute();
        $stmt->close();

        // Obter os IDs dos times inseridos
        $timeA_id = $conn->insert_id - 1;
        $timeB_id = $conn->insert_id;

        // Inserir os confrontos das quartas de final
        $stmt = $conn->prepare("INSERT INTO quartas_de_final_confrontos (timeA_id, timeA_nome, timeB_id, timeB_nome, fase) VALUES (?, ?, ?, ?, 'quartas')");
        $stmt->bind_param("issi", $timeA_id, $timeA['nome'], $timeB_id, $timeB['nome']);
        $stmt->execute();
        $stmt->close();
    }
}

classificarQuartas();
?>
