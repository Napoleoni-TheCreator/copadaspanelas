<?php
session_start();
include ("../../config/conexao.php");

function processarLogin($conn, $cod_adm, $senha) {
    // Declaração preparada para selecionar administrador pelo código
    $stmt = $conn->prepare("SELECT * FROM admin WHERE cod_adm = ?");

    if ($stmt) {
        // Vinculação dos parâmetros
        $stmt->bind_param("s", $cod_adm);

        // Execução da declaração preparada
        $stmt->execute();

        // Obter o resultado da consulta
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();

            // Verificar a senha
            if (password_verify($senha, $admin['senha'])) {
                // Autenticação bem-sucedida
                session_start(); // Inicia uma nova sessão ou retoma a existente
                $_SESSION['admin_id'] = $admin['cod_adm'];
                $_SESSION['admin_nome'] = $admin['nome'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Senha incorreta.";
            }
        } else {
            echo "Administrador não encontrado.";
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da declaração: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_adm = $_POST['cod_adm'];
    $senha = $_POST['senha'];

    processarLogin($conn, $cod_adm, $senha);
}

// Fechar a conexão
$conn->close();
?>
