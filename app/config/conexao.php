<?php

$servername = "localhost";  // Endereço do servidor MySQL (geralmente 'localhost')
$username = "root";  // Nome de usuário do MySQL
$password = "";    
$database = "copa"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}else{
    // echo"conexao";
}
// // Configuração segura do cookie de sessão
// session_set_cookie_params([
//     'lifetime' => 0, // Sessão padrão
//     'path' => '/',
//     'domain' => '', // Coloque o domínio se necessário
//     'secure' => true, // Só transmita cookies via HTTPS
//     'httponly' => true, // Não acessível via JavaScript
//     'samesite' => 'Strict' // Protege contra CSRF
// ]);
// session_start();
?>