<?php

include '../../config/conexao.php';

// Função para verificar se o número máximo de grupos foi atingido (8 grupos)
function numeroMaximoGruposAtingido($conn) {
    $sql = "SELECT COUNT(*) as total FROM grupos";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalGrupos = $row['total'];
        
        return $totalGrupos >= 8; // Retorna true se o número máximo de grupos (8) foi atingido
    }

    return false;
}

// Obtém a próxima letra disponível para o nome do grupo (A a H)
function obterProximaLetra($conn) {
    $letras = range('A', 'H'); // Limitado a A-H para até 8 grupos

    foreach ($letras as $letra) {
        $sql = "SELECT * FROM grupos WHERE nome = 'Grupo $letra'";
        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            return $letra;
        }
    }

    return null; // Caso todos os grupos de A-H estejam preenchidos
}

// Adiciona um novo grupo no banco de dados
function adicionarGrupo($conn) {
    if (numeroMaximoGruposAtingido($conn)) {
        return "Todos os grupos de A a H já estão preenchidos!";
    }

    $proximaLetra = obterProximaLetra($conn);
    if ($proximaLetra === null) {
        return "Todos os grupos de A a H já estão preenchidos!";
    }

    $nomeGrupo = "Grupo $proximaLetra";

    $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";

    if ($conn->query($sql) === TRUE) {
        return "Grupo $proximaLetra adicionado com sucesso!";
    } else {
        return "Erro ao adicionar grupo: " . $conn->error;
    }
}

// Se o script for chamado diretamente (para testes), adiciona o grupo
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo adicionarGrupo($conn);
}

$conn->close();

?>
