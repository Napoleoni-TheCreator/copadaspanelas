<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/login.css">
    <title>Login do Usuario</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($_SESSION['erro'])) { echo "<p style='color:red;'>{$_SESSION['erro']}</p>"; } ?>
    <form method="post" action="verifica_login.php">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br>
        <br>
        <input type="submit" value="Login">
    </form>


</body>
</html>