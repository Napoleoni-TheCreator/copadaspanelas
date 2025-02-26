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
    $campeonato_nome = $_POST['campeonato_nome'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $salvar_dados = isset($_POST['salvar_dados']) ? $_POST['salvar_dados'] : array();

    // Insere o novo campeonato na tabela de campeonatos
    $stmt = $conn->prepare("INSERT INTO campeonatos (nome, data_inicio, data_fim) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $campeonato_nome, $data_inicio, $data_fim);
    $stmt->execute();
    $campeonato_id = $stmt->insert_id;
    $stmt->close();

    // Copia os dados selecionados para o novo campeonato
    foreach ($salvar_dados as $tabela) {
        $sql = "INSERT INTO $tabela SELECT NULL, ";
        $columns = array();
        $result = $conn->query("SHOW COLUMNS FROM $tabela");
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] != 'id') {
                $columns[] = $row['Field'];
            }
        }
        $sql .= implode(", ", $columns) . ", $campeonato_id FROM $tabela WHERE campeonato_id IS NULL";
        $conn->query($sql);
    }

    echo "Novo campeonato criado com sucesso!";
}

// Obtém a lista de tabelas do banco de dados
$tabelas = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tabelas[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Campeonato</title>
    <script>
        function mostrarPopup() {
            document.getElementById('popup').style.display = 'block';
        }

        function fecharPopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</head>
<body>
    <h1>Criar Novo Campeonato</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="campeonato_nome">Nome do Campeonato:</label>
        <input type="text" id="campeonato_nome" name="campeonato_nome" required><br><br>
        <label for="data_inicio">Data de Início:</label>
        <input type="date" id="data_inicio" name="data_inicio" required><br><br>
        <label for="data_fim">Data de Fim:</label>
        <input type="date" id="data_fim" name="data_fim" required><br><br>
        <button type="button" onclick="mostrarPopup()">Selecionar Dados para Salvar</button><br><br>
        <div id="popup" style="display:none;">
            <h2>Selecione os dados para salvar</h2>
            <?php foreach ($tabelas as $tabela): ?>
                <input type="checkbox" name="salvar_dados[]" value="<?php echo $tabela; ?>"> <?php echo $tabela; ?><br>
            <?php endforeach; ?>
            <button type="button" onclick="fecharPopup()">Fechar</button>
        </div>
        <br>
        <input type="submit" value="Criar Campeonato">
    </form>
</body>
</html>