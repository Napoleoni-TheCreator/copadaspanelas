<?php
// Inclui a conexão com o banco de dados
include_once "../config/conexao.php";

// Função para obter os códigos do Instagram do banco de dados
function getCodes($conn) {
    $codes = [];

    $stmt = $conn->prepare("SELECT codinsta, linklive FROM linkinstagram WHERE codinsta IN (1, 2, 3)");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $codes[$row['codinsta']] = $row['linklive'];
        }

        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $conn->error;
    }

    return $codes;
}

// Obtém todos os códigos
$codes = getCodes($conn);

// Armazena os códigos em variáveis
$code1 = $codes[1] ?? null;
$code2 = $codes[2] ?? null;
$code3 = $codes[3] ?? null;

?>
