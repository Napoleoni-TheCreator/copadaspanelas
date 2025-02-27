<?php
// Habilitar a exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$database = "copa";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Processar o formulário de criação de campeonato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST); // Verifica se os dados do formulário estão sendo enviados

    // Captura os dados do formulário
    $nomeCampeonato = $_POST['nome_campeonato'];
    $dataInicio = $_POST['data_inicio'];
    $dataFinal = $_POST['data_final'];

    // Validação dos campos
    if (empty($nomeCampeonato)) {
        $erro = "O nome do campeonato é obrigatório.";
    } elseif (empty($dataInicio)) {
        $erro = "A data de início é obrigatória.";
    } elseif (empty($dataFinal)) {
        $erro = "A data final é obrigatória.";
    } elseif ($dataFinal < $dataInicio) {
        $erro = "A data final não pode ser anterior à data de início.";
    } else {
        // Prepara a query SQL
        $sql = "INSERT INTO campeonatos (nome, ativo, data_inicio, data_final) VALUES (?, FALSE, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Erro ao preparar a query: " . $conn->error);
        }

        // Vincula os parâmetros e executa a query
        $stmt->bind_param("sss", $nomeCampeonato, $dataInicio, $dataFinal);
        $stmt->execute();

        // Verifica se a inserção foi bem-sucedida
        if ($stmt->affected_rows > 0) {
            $sucesso = "Campeonato criado com sucesso!";
        } else {
            $erro = "Erro ao criar campeonato. Nenhuma linha afetada.";
        }

        // Fecha a declaração
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Campeonato</title>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <style>
        .mensagem {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
        }
        .erro {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<?php 
require_once 'header_classificacao.php'
?>
    <h1>Criar Campeonato</h1>

    <?php if (isset($erro)): ?>
        <div class="mensagem erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <div class="mensagem sucesso"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="nome_campeonato">Nome do Campeonato:</label>
        <input type="text" id="nome_campeonato" name="nome_campeonato" required>

        <label for="data_inicio">Data de Início:</label>
        <input type="date" id="data_inicio" name="data_inicio" required>

        <label for="data_final">Data Final:</label>
        <input type="date" id="data_final" name="data_final" required>

        <button type="submit">Criar Campeonato</button>
    </form>

    <br>
    <a href="ativar_campeonato.php">Gerenciar Campeonatos</a> | 
    <a href="adicionar_grupos_times.php">Adicionar Grupos e Times</a>
    <?php include '../footer.php' ?>
</body>
</html>