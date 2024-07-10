<?php

$servername = "localhost";  // Endereço do servidor MySQL (geralmente 'localhost')
$username = "root";  // Nome de usuário do MySQL
$password = "";    // Senha do MySQL
$database = "gerenciador_grupos"; // Nome do banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}else
    echo"conexao sucesso"
?>