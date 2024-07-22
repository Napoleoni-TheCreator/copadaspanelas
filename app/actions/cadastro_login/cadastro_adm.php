<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../../config/conexao.php";

    // Capture os dados do formulário
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifique se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        echo "<p>As senhas não coincidem. Tente novamente.</p>";
    } else {
        // Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Verificação de usuário ou email existente
        $sql = "SELECT * FROM usuarios WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<p>Nome de usuário ou email já existente.</p>";
        } else {
            // Inserção no banco de dados
            $sql = "INSERT INTO usuarios (nome, sobrenome, username, email, senha) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nome, $sobrenome, $username, $email, $senha_hash);

            if ($stmt->execute()) {
                echo "<p>Cadastro realizado com sucesso!</p>";
            } else {
                echo "<p>Erro ao cadastrar: " . $conn->error . "</p>";
            }
        }

        $stmt->close();
    }

    $conn->close();
}
?>