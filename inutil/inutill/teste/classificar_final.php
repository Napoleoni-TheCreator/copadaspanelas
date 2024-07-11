<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
function classificarFinal() {
    global $conn;

    // Limpar a tabela de finais e confrontos
    $conn->query("TRUNCATE TABLE final");
    $conn->query("TRUNCATE TABLE final_confrontos");

    // Obter os times classificados para a final
    $result = $conn->query("SELECT * FROM semifinais ORDER BY id LIMIT 2");
    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

    if (count($times) != 2) {
        die("Número de times classificados insuficiente para final.");
    }

    // Organizar o confronto final
    $timeA = $times[0];
    $timeB = $times[1];

    // Inserir os times classificados para a final
    $stmt = $conn->prepare("INSERT INTO final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
    $stmt->execute();
    $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
    $stmt->execute();
    $stmt->close();

    // Inserir o confronto final
    $stmt = $conn->prepare("INSERT INTO final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'final')");
    $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
    $stmt->execute();
    $stmt->close();
}

classificarFinal();
?>

