<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "copa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campeonato_id = $_POST['campeonato_id'];

    // Lista de tabelas que possuem a coluna campeonato_id
    $tabelas = [
        'linklive', 'linkinstagram', 'admin', 'grupos', 'times', 'jogos', 'jogos_finais', 
        'configuracoes', 'oitavas_de_final', 'quartas_de_final', 'semifinais', 'final', 
        'final_confrontos', 'oitavas_de_final_confrontos', 'quartas_de_final_confrontos', 
        'semifinais_confrontos', 'fase_execucao', 'noticias', 'jogadores', 'posicoes_jogadores', 
        'jogos_fase_grupos'
    ];

    // Excluir dados das tabelas associadas
    foreach ($tabelas as $tabela) {
        $sql = "DELETE FROM $tabela WHERE campeonato_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro ao preparar a consulta para a tabela $tabela: " . $conn->error);
        }
        $stmt->bind_param("i", $campeonato_id);
        if (!$stmt->execute()) {
            die("Erro ao executar a consulta para a tabela $tabela: " . $stmt->error);
        }
        $stmt->close();
    }

    // Excluir o campeonato
    $sql = "DELETE FROM campeonatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a consulta para a tabela campeonatos: " . $conn->error);
    }
    $stmt->bind_param("i", $campeonato_id);
    if (!$stmt->execute()) {
        die("Erro ao executar a consulta para a tabela campeonatos: " . $stmt->error);
    }
    $stmt->close();

    echo "Campeonato e dados associados apagados com sucesso!";
}

// Obtém a lista de campeonatos
$campeonatos = array();
$result = $conn->query("SELECT id, nome FROM campeonatos");
if ($result === false) {
    die("Erro ao obter a lista de campeonatos: " . $conn->error);
}
while ($row = $result->fetch_assoc()) {
    $campeonatos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Apagar Campeonato</title>
</head>
<body>
    <h1>Apagar Campeonato</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="campeonato_id">Selecione o Campeonato para Apagar:</label>
        <select id="campeonato_id" name="campeonato_id" required>
            <?php foreach ($campeonatos as $campeonato): ?>
                <option value="<?php echo $campeonato['id']; ?>"><?php echo $campeonato['nome']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="submit" value="Apagar Campeonato">
    </form>
</body>
</html>