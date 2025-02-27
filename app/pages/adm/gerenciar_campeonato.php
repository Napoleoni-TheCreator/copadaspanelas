<?php
// Habilitar a exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$database = "copa";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Busca todos os campeonatos
$sql_campeonatos = "SELECT id, nome FROM campeonatos";
$result_campeonatos = $conn->query($sql_campeonatos);

if ($result_campeonatos === false) {
    die("Erro ao buscar campeonatos: " . $conn->error);
}

$campeonatos = $result_campeonatos->fetch_all(MYSQLI_ASSOC);

// Verifica se o ID do campeonato foi passado
if (isset($_GET['id'])) {
    $campeonato_id = intval($_GET['id']);

    // Busca os dados do campeonato
    $sql = "SELECT id, nome, data_inicio, data_final FROM campeonatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $campeonato_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Campeonato não encontrado.");
    }

    $campeonato = $result->fetch_assoc();

    // Função para buscar dados relacionados ao campeonato
    function buscarDados($conn, $tabela, $campeonato_id) {
        $sql = "SELECT * FROM $tabela WHERE campeonato_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $campeonato_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Busca grupos, times e jogos
    $grupos = buscarDados($conn, "grupos", $campeonato_id);
    $times = buscarDados($conn, "times", $campeonato_id);
    $jogos = buscarDados($conn, "jogos_fase_grupos", $campeonato_id);

    // Processar formulários de adição
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['adicionar_grupo'])) {
            $nome_grupo = $_POST['nome_grupo'];
            $sql = "INSERT INTO grupos (nome, campeonato_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nome_grupo, $campeonato_id);
            $stmt->execute();
            header("Location: gerenciar_campeonato.php?id=$campeonato_id");
            exit;
        }

        if (isset($_POST['adicionar_time'])) {
            $nome_time = $_POST['nome_time'];
            $grupo_id = $_POST['grupo_id'];
            $sql = "INSERT INTO times (nome, grupo_id, campeonato_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $nome_time, $grupo_id, $campeonato_id);
            $stmt->execute();
            header("Location: gerenciar_campeonato.php?id=$campeonato_id");
            exit;
        }

        if (isset($_POST['adicionar_jogo'])) {
            $timeA_id = $_POST['timeA_id'];
            $timeB_id = $_POST['timeB_id'];
            $data_jogo = $_POST['data_jogo'];
            $sql = "INSERT INTO jogos_fase_grupos (timeA_id, timeB_id, data_jogo, campeonato_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $timeA_id, $timeB_id, $data_jogo, $campeonato_id);
            $stmt->execute();
            header("Location: gerenciar_campeonato.php?id=$campeonato_id");
            exit;
        }
    }

    // Processar exclusões
    if (isset($_GET['excluir'])) {
        $tabela = $_GET['tabela'];
        $id = intval($_GET['id']);
        $sql = "DELETE FROM $tabela WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: gerenciar_campeonato.php?id=$campeonato_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Campeonatos</title>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <style>
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
        }
        .list {
            list-style: none;
            padding: 0;
        }
        .list li {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list a {
            text-decoration: none;
            color: #007BFF;
        }
        .list a:hover {
            text-decoration: underline;
        }
        .form-adicionar {
            margin-top: 20px;
        }
        .form-adicionar input, .form-adicionar select {
            margin-right: 10px;
        }
        .select-campeonato {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php 
require_once 'header_classificacao.php'
?>
    <h1>Gerenciar Campeonatos</h1>

    <!-- Seletor de Campeonato -->
    <div class="select-campeonato">
        <form method="GET" action="gerenciar_campeonato.php">
            <label for="campeonato">Selecione um campeonato:</label>
            <select name="id" id="campeonato" onchange="this.form.submit()" required>
                <option value="">-- Escolha um campeonato --</option>
                <?php foreach ($campeonatos as $camp): ?>
                    <option value="<?= $camp['id'] ?>" <?= isset($campeonato) && $campeonato['id'] == $camp['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($camp['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (isset($campeonato)): ?>
        <!-- Exibe os dados do campeonato selecionado -->
        <h2>Campeonato: <?= htmlspecialchars($campeonato['nome']) ?></h2>
        <p>Data de Início: <?= htmlspecialchars($campeonato['data_inicio']) ?></p>
        <p>Data Final: <?= htmlspecialchars($campeonato['data_final']) ?></p>

        <!-- Seção de Grupos -->
        <div class="section">
            <h2>Grupos</h2>
            <ul class="list">
                <?php foreach ($grupos as $grupo): ?>
                    <li>
                        <?= htmlspecialchars($grupo['nome']) ?>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=grupos&id=<?= $grupo['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="POST" class="form-adicionar">
                <input type="text" name="nome_grupo" placeholder="Nome do Grupo" required>
                <button type="submit" name="adicionar_grupo">Adicionar Grupo</button>
            </form>
        </div>

        <!-- Seção de Times -->
        <div class="section">
            <h2>Times</h2>
            <ul class="list">
                <?php foreach ($times as $time): ?>
                    <li>
                        <?= htmlspecialchars($time['nome']) ?>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=times&id=<?= $time['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="POST" class="form-adicionar">
                <input type="text" name="nome_time" placeholder="Nome do Time" required>
                <select name="grupo_id" required>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= $grupo['id'] ?>"><?= htmlspecialchars($grupo['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="adicionar_time">Adicionar Time</button>
            </form>
        </div>

        <!-- Seção de Jogos -->
        <div class="section">
            <h2>Jogos</h2>
            <ul class="list">
                <?php foreach ($jogos as $jogo): ?>
                    <li>
                        Jogo #<?= $jogo['id'] ?> (<?= $jogo['data_jogo'] ?>)
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=jogos_fase_grupos&id=<?= $jogo['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form method="POST" class="form-adicionar">
                <select name="timeA_id" required>
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time['id'] ?>"><?= htmlspecialchars($time['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="timeB_id" required>
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time['id'] ?>"><?= htmlspecialchars($time['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="datetime-local" name="data_jogo" required>
                <button type="submit" name="adicionar_jogo">Adicionar Jogo</button>
            </form>
        </div>
    <?php endif; ?>
    <?php include '../footer.php' ?>
</body>
</html>