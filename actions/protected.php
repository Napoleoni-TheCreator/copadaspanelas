<?php
session_start();
if (!isset($_SESSION['nome_usuario'])) {header("Location: login.php");}?>
<!DOCTYPE html>
<html>
    <head>
        <title>Página Protegida</title>
    </head>
    <body>
        <h2>Página Protegida</h2>
        <p>Bem-vindo, 
            <?php echo $_SESSION['nome_usuario']; ?>! 
        </p><a href="app/pages/tabelaJogadores.php">Logout</a>
    </body>
</html>