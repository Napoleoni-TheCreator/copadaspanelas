<?php
// conexao.php
$host = 'localhost'; // Endereço do servidor MySQL
$dbname = 'copa';    // Nome do banco de dados
$username = 'root';  // Nome de usuário do MySQL
$password = '';      // Senha do MySQL (vazia no seu caso)

try {
    // Cria a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Desativa a emulação de prepared statements para segurança
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    echo "Conexão bem-sucedida!"; // Mensagem de depuração (remova após testes)
} catch (PDOException $e) {
    // Captura e exibe erros de conexão
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>