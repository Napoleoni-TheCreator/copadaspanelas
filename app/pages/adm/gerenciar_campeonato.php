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
$campeonato_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($campeonato_id) {
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
}

// Processar formulários de adição e edição
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

    if (isset($_POST['editar_grupo'])) {
        $nome_grupo = $_POST['nome_grupo'];
        $grupo_id = $_POST['grupo_id'];
        $sql = "UPDATE grupos SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome_grupo, $grupo_id);
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

    if (isset($_POST['editar_time'])) {
        $nome_time = $_POST['nome_time'];
        $time_id = $_POST['time_id'];
        $grupo_id = $_POST['grupo_id'];
        $sql = "UPDATE times SET nome = ?, grupo_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nome_time, $grupo_id, $time_id);
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

    if (isset($_POST['editar_jogo'])) {
        $timeA_id = $_POST['timeA_id'];
        $timeB_id = $_POST['timeB_id'];
        $data_jogo = $_POST['data_jogo'];
        $jogo_id = $_POST['jogo_id'];
        $sql = "UPDATE jogos_fase_grupos SET timeA_id = ?, timeB_id = ?, data_jogo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $timeA_id, $timeB_id, $data_jogo, $jogo_id);
        $stmt->execute();
        header("Location: gerenciar_campeonato.php?id=$campeonato_id");
        exit;
    }
}

// Processar exclusões
if (isset($_GET['excluir'])) {
    $tabela = $_GET['tabela'];
    $id = intval($_GET['id']);

    // Verifica se o campeonato_id está presente
    if (!isset($_GET['campeonato_id'])) {
        die("Campeonato não encontrado.");
    }

    $campeonato_id = intval($_GET['campeonato_id']);

    // Excluir campeonato e todos os dados relacionados
    if ($tabela === 'campeonatos') {
        // Excluir jogos_fase_grupos primeiro (pois eles dependem de times)
        $conn->query("DELETE FROM jogos_fase_grupos WHERE campeonato_id = $id");

        // Excluir jogadores (pois eles dependem de times)
        $conn->query("DELETE FROM jogadores WHERE campeonato_id = $id");

        // Excluir times (pois eles dependem de grupos)
        $conn->query("DELETE FROM times WHERE campeonato_id = $id");

        // Excluir grupos
        $conn->query("DELETE FROM grupos WHERE campeonato_id = $id");

        // Excluir outros dados relacionados ao campeonato
        $conn->query("DELETE FROM jogos_finais WHERE campeonato_id = $id");
        $conn->query("DELETE FROM configuracoes WHERE campeonato_id = $id");
        $conn->query("DELETE FROM oitavas_de_final WHERE campeonato_id = $id");
        $conn->query("DELETE FROM quartas_de_final WHERE campeonato_id = $id");
        $conn->query("DELETE FROM semifinais WHERE campeonato_id = $id");
        $conn->query("DELETE FROM final WHERE campeonato_id = $id");
        $conn->query("DELETE FROM final_confrontos WHERE campeonato_id = $id");
        $conn->query("DELETE FROM oitavas_de_final_confrontos WHERE campeonato_id = $id");
        $conn->query("DELETE FROM quartas_de_final_confrontos WHERE campeonato_id = $id");
        $conn->query("DELETE FROM semifinais_confrontos WHERE campeonato_id = $id");
        $conn->query("DELETE FROM fase_execucao WHERE campeonato_id = $id");
        $conn->query("DELETE FROM noticias WHERE campeonato_id = $id");
        $conn->query("DELETE FROM posicoes_jogadores WHERE campeonato_id = $id");

        // Excluir o campeonato
        $sql = "DELETE FROM campeonatos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: gerenciar_campeonato.php");
        exit;
    } elseif ($tabela === 'grupos') {
        // Excluir jogos_fase_grupos primeiro (pois eles dependem de times)
        $conn->query("DELETE FROM jogos_fase_grupos WHERE timeA_id IN (SELECT id FROM times WHERE grupo_id = $id) OR timeB_id IN (SELECT id FROM times WHERE grupo_id = $id)");

        // Excluir jogadores (pois eles dependem de times)
        $conn->query("DELETE FROM jogadores WHERE time_id IN (SELECT id FROM times WHERE grupo_id = $id)");

        // Excluir times (pois eles dependem de grupos)
        $conn->query("DELETE FROM times WHERE grupo_id = $id");

        // Excluir o grupo
        $sql = "DELETE FROM grupos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: gerenciar_campeonato.php?id=$campeonato_id");
        exit;
    } elseif ($tabela === 'times') {
        // Excluir jogos_fase_grupos primeiro (pois eles dependem de times)
        $conn->query("DELETE FROM jogos_fase_grupos WHERE timeA_id = $id OR timeB_id = $id");

        // Excluir jogadores (pois eles dependem de times)
        $conn->query("DELETE FROM jogadores WHERE time_id = $id");

        // Excluir o time
        $sql = "DELETE FROM times WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: gerenciar_campeonato.php?id=$campeonato_id");
        exit;
    } else {
        // Excluir outros registros (jogos, etc.)
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

        <!-- Botão para excluir o campeonato -->
        <a href="gerenciar_campeonato.php?excluir=1&tabela=campeonatos&id=<?= $campeonato_id ?>" onclick="return confirm('Tem certeza que deseja excluir este campeonato e todos os dados relacionados?')">
            Excluir Campeonato
        </a>

        <!-- Seção de Grupos -->
        <div class="section">
            <h2>Grupos</h2>
            <ul class="list">
                <?php foreach ($grupos as $grupo): ?>
                    <li>
                        <?= htmlspecialchars($grupo['nome']) ?>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&editar_grupo=1&grupo_id=<?= $grupo['id'] ?>">Editar</a>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=grupos&id=<?= $grupo['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_GET['editar_grupo'])): ?>
                <h2>Editar Grupo</h2>
                <form method="POST" action="gerenciar_campeonato.php?id=<?= $campeonato_id ?>">
                    <input type="hidden" name="editar_grupo" value="1">
                    <input type="hidden" name="grupo_id" value="<?= $_GET['grupo_id'] ?>">
                    <input type="text" name="nome_grupo" value="<?= htmlspecialchars($grupo['nome']) ?>" required>
                    <button type="submit">Salvar Alterações</button>
                </form>
            <?php else: ?>
                <form method="POST" class="form-adicionar">
                    <input type="text" name="nome_grupo" placeholder="Nome do Grupo" required>
                    <button type="submit" name="adicionar_grupo">Adicionar Grupo</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Seção de Times -->
        <div class="section">
            <h2>Times</h2>
            <ul class="list">
                <?php foreach ($times as $time): ?>
                    <li>
                        <?= htmlspecialchars($time['nome']) ?>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&editar_time=1&time_id=<?= $time['id'] ?>">Editar</a>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=times&id=<?= $time['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_GET['editar_time'])): ?>
                <h2>Editar Time</h2>
                <form method="POST" action="gerenciar_campeonato.php?id=<?= $campeonato_id ?>">
                    <input type="hidden" name="editar_time" value="1">
                    <input type="hidden" name="time_id" value="<?= $_GET['time_id'] ?>">
                    <input type="text" name="nome_time" value="<?= htmlspecialchars($time['nome']) ?>" required>
                    <select name="grupo_id" required>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= $grupo['id'] ?>" <?= $time['grupo_id'] == $grupo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grupo['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Salvar Alterações</button>
                </form>
            <?php else: ?>
                <form method="POST" class="form-adicionar">
                    <input type="text" name="nome_time" placeholder="Nome do Time" required>
                    <select name="grupo_id" required>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= $grupo['id'] ?>"><?= htmlspecialchars($grupo['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="adicionar_time">Adicionar Time</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Seção de Jogos -->
        <div class="section">
            <h2>Jogos</h2>
            <ul class="list">
                <?php foreach ($jogos as $jogo): ?>
                    <li>
                        Jogo #<?= $jogo['id'] ?> (<?= $jogo['data_jogo'] ?>)
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&editar_jogo=1&jogo_id=<?= $jogo['id'] ?>">Editar</a>
                        <a href="gerenciar_campeonato.php?id=<?= $campeonato_id ?>&excluir=1&tabela=jogos_fase_grupos&id=<?= $jogo['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_GET['editar_jogo'])): ?>
                <h2>Editar Jogo</h2>
                <form method="POST" action="gerenciar_campeonato.php?id=<?= $campeonato_id ?>">
                    <input type="hidden" name="editar_jogo" value="1">
                    <input type="hidden" name="jogo_id" value="<?= $_GET['jogo_id'] ?>">
                    <select name="timeA_id" required>
                        <?php foreach ($times as $time): ?>
                            <option value="<?= $time['id'] ?>" <?= $jogo['timeA_id'] == $time['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($time['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="timeB_id" required>
                        <?php foreach ($times as $time): ?>
                            <option value="<?= $time['id'] ?>" <?= $jogo['timeB_id'] == $time['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($time['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="datetime-local" name="data_jogo" value="<?= htmlspecialchars($jogo['data_jogo']) ?>" required>
                    <button type="submit">Salvar Alterações</button>
                </form>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>