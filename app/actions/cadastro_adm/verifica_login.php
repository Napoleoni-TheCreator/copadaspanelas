<?php
session_start(); // Inicia a sessão

// Verifica se todos os campos foram preenchidos
if (empty($_POST['nome']) || empty($_POST['senha'])) {
    header("location: ../cadastro_adm/login.php?msgLogin=Preencha todos os campos!");
    exit();

}

// Inclui o arquivo de conexão
include_once("../../config/conexao.php");

$nome = $_POST['nome'];
$senha = $_POST['senha'];

// Consulta na tabela admin usando prepared statement
$query_admin = "SELECT cod_adm, nome, senha FROM admin WHERE nome = ?";
$stmt_admin = $conn->prepare($query_admin);
$stmt_admin->bind_param("s", $nome);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();

if ($result_admin->num_rows == 1) {
    // Usuário admin encontrado, continua com a autenticação
    $admin = $result_admin->fetch_assoc();

    // Verifica a senha usando password_verify
    if (password_verify($senha, $admin['senha'])) {
        // Senha correta, inicia a sessão para admin
        $_SESSION['usuario'] = $admin['nome'];
        $_SESSION['tipo'] = 'admin';
        $_SESSION['id'] = $admin['cod_adm'];
        header("location: ../../pages/HomePage.php"); // Redireciona para o painel do admin
        exit();
    } else {
        // Senha incorreta para admin
        header("location: ../cadastro_adm/login.php?msgLogin=Usuário ou senha incorretos!");
        exit();
    }
}

// Consulta na tabela tecnico usando prepared statement
$query_tecnico = "SELECT nome, senha FROM tecnico WHERE nome = ?";
$stmt_tecnico = $conn->prepare($query_tecnico);
$stmt_tecnico->bind_param("s", $nome);
$stmt_tecnico->execute();
$result_tecnico = $stmt_tecnico->get_result();

if ($result_tecnico->num_rows == 1) {
    // Usuário técnico encontrado, continua com a autenticação
    $tecnico = $result_tecnico->fetch_assoc();

    // Verifica a senha usando password_verify
    if (password_verify($senha, $tecnico['senha'])) {
        // Senha correta, inicia a sessão para técnico
        $_SESSION['usuario'] = $tecnico['nome'];
        $_SESSION['tipo'] = 'tecnico';
        header("location: ../../pages/HomePage.php"); // Redireciona para o painel do técnico
        exit();
    } else {
        // Senha incorreta para técnico
        header("location: ../cadastro_adm/login.php?msgLogin=Usuário ou senha incorretos!");
        exit();
    }
}

// Se nenhum usuário foi encontrado
header("location: ../cadastro_adm/login.php?msgLogin=Nem um usuario encontrado no banco de dados");
exit();

// Fecha as consultas e a conexão
$stmt_admin->close();
$stmt_tecnico->close();
$conn->close();
?>
