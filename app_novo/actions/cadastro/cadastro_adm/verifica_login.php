<?php
session_start();
$usuario_valido= "admin";
$email_valido = "edu@gmail.com";
$senha_valida= "123";
$usuario= $_POST['nome'];
$senha= $_POST['senha'];
$email = $_POST['email'];

if ($usuario === $usuario_valido && $senha=== $senha_valida && $email === $email_valido) {
$_SESSION['nome_usuario'] = $usuario;
header("Location: protected.php");
} else {
$erro= "Nome de usuário ou senha inválidos!";
$_SESSION['erro'] = $erro;
header("Location: login.php");
}