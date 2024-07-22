<?php
session_start();
include("../../config/conexao.php");

// Gerar um token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função para processar o login
function processarLogin($conn, $cod_adm, $senha) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE cod_adm = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $cod_adm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_id'] = $admin['cod_adm'];
                $_SESSION['admin_nome'] = $admin['nome'];

                // Redirecionar para a URL de referência ou para uma página padrão
                $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '../../pages/HomePage.php';
                unset($_SESSION['redirect_url']); // Limpar a URL de redirecionamento após login
                header("Location: $redirect_url");
                exit();
            } else {
                echo "<p style='color: red;'>Senha incorreta.</p>";
            }
        } else {
            echo "<p style='color: red;'>Administrador não encontrado.</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Erro na preparação da declaração: " . $conn->error . "</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('<p style="color: red;">Token CSRF inválido.</p>');
    }
    
    $cod_adm = $_POST['cod_adm'];
    $senha = $_POST['senha'];

    processarLogin($conn, $cod_adm, $senha);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            height: 100vh;
            background-size: cover;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        form {
            max-width: 400px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 15px;
            font-size: 18px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #c60909;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #a50707;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="cod_adm">Código do Administrador:</label>
            <input type="text" id="cod_adm" name="cod_adm" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
