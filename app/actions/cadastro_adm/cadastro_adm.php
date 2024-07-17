<?php
include ("../../config/conexao.php");

function cadastrarAdmin($conn, $cod_adm, $nome, $senha) {
    // Hashing da senha
    $hashed_password = password_hash($senha, PASSWORD_BCRYPT);

    // Declaração preparada para inserir administrador
    $stmt = $conn->prepare("INSERT INTO admin (cod_adm, nome, senha) VALUES (?, ?, ?)");

    if ($stmt) {
        // Vinculação dos parâmetros
        $stmt->bind_param("sss", $cod_adm, $nome, $hashed_password);

        // Execução da declaração preparada
        if ($stmt->execute()) {
            echo "Administrador cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar administrador: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da declaração: " . $conn->error;
    }
}

// Cadastro dos administradores
cadastrarAdmin($conn, '2021ydg03i0040', 'ADMIM1', 'X7m@1cT#4p$');
cadastrarAdmin($conn, '2022zfh04i0048', 'ADMIM2', 'W9k&2dL%8r^');
cadastrarAdmin($conn, '2023ajk05i0056', 'ADMIM3', 'Y3j$6bM!0n@');
cadastrarAdmin($conn, '2025blk06i0064', 'ADMIM4', 'Q5g^7sH&2v#');

// Fechar a conexão
$conn->close();
?>
