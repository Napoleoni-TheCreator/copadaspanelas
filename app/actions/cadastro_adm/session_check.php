<?php
// session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../../pages/adm/login.php");
    exit();
}
?>
