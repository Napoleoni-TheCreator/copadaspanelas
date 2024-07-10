<?php session_start(); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <h2>Login</h2>
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