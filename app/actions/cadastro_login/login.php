<?php
session_start();
include "../../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Determinar se o usuário está utilizando email ou nome de usuário
    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
    } else {
        $sql = "SELECT * FROM usuarios WHERE username = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verificar a senha
        if (password_verify($password, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<p>Login realizado com sucesso!</p>";
            // Redirecionar para a página principal ou dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p>Senha incorreta. Tente novamente.</p>";
        }
    } else {
        echo "<p>Nome de usuário ou email não encontrado. Tente novamente.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
