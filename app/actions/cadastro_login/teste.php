<?php
session_start();
include("session_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Área Restrita</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_nome']); ?>!</h1>
    <p>Esta é uma área restrita. Apenas administradores podem ver este conteúdo.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
