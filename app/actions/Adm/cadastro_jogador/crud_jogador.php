<?php
include '../../../config/conexao.php';
session_start();

// Função para gerar token CSRF
function gerarTokenCSRF() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para lidar com a exclusão de um jogador
if (isset($_POST['delete_token'])) {
    // Verificação do token CSRF
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token inválido");
    }

    $token = $_POST['delete_token'];

    $sql = "SELECT id FROM jogadores WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
        $deleteSql = "DELETE FROM jogadores WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }
}

$_SESSION['csrf_token'] = gerarTokenCSRF();

// Obter todos os times
$timesSql = "SELECT id, nome FROM times";
$timesResult = $conn->query($timesSql);

$times = [];
while ($row = $timesResult->fetch_assoc()) {
    $times[] = $row;
}

// Obter jogadores por time se um time for selecionado
$selectedTimeId = isset($_POST['time_id']) ? $_POST['time_id'] : null;
$players = [];
if ($selectedTimeId) {
    $playersSql = "SELECT id, nome, posicao, numero, gols, assistencias, cartoes_amarelos, cartoes_vermelhos, imagem, token 
                   FROM jogadores 
                   WHERE time_id = ?";
    $stmt = $conn->prepare($playersSql);
    $stmt->bind_param("i", $selectedTimeId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $players[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Jogadores</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            text-align: center;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        h1 {
            font-size: 60px;
            text-align: center;
            margin-top: 5%;
            color: inherit;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .player-card {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            transition: background-color 0.3s;
        }
        body.dark-mode .player-card {
            background-color: rgba(33, 33, 33, 0.8);
        }
        .player-image {
            width: 100px;
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
        }
        .player-details {
            flex: 1;
            margin-right: 20px;
            text-align: left;
            color: inherit;
        }
        .player-actions {
            margin-top: 10px;
        }
        .player-actions button {
            margin-right: 10px;
        }
        .modal-content {
            border-radius: 10px;
            background-color: #fefefe;
            color: #000;
        }
        body.dark-mode .modal-content {
            background-color: #333;
            color: #fff;
        }
    </style>
    <script>
        function populatePlayersList(timeId) {
            document.getElementById('selectedTimeId').value = timeId;
            document.getElementById('playersForm').submit();
        }

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
</head>
<body>
<?php include '../../../pages/header_classificacao.php'; ?>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../../../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
            </div>
            <nav class="nav-icons">
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/rodadas_adm.php"><img src="../../../../public/img/header/rodadas.png" alt="Soccer Icon"></a>
                    <span>Rodadas</span>
                </div>
                <div class="nav-item">
                    <a href="../../../actions/cadastro_adm/login.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
                    <span>Classificação</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/listar_times.php"><img src="../../../../public/img/header/classificados.png" alt="Chess Icon"></a>
                    <span>editar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_dados_finais.php"><img src="../../../../public/img/header/oitavas.png" alt="Trophy Icon"></a>
                    <span>editar finais</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/prancheta.svg" alt="Trophy Icon"></a>
                    <span>Editar jogadores</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/grupo.svg" alt="Trophy Icon"></a>
                    <span>Criar grupos</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/adtime.svg" alt="Trophy Icon"></a>
                    <span>Adicionar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/adadm.svg" alt="cadastro novos adm"></a>
                    <span>Adicionar outro adm</span>
                </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()">Modo Escuro</button>
        </div>
    </header>
<h1>EDITAR JOGADORES</h1>
<div class="container">
    <form id="playersForm" method="POST">
        <div class="form-group">
            <label for="timeSelect">Selecione um Time:</label>
            <select class="form-control" id="timeSelect" name="time_id" onchange="populatePlayersList(this.value)">
                <option value="">Escolha um time</option>
                <?php foreach ($times as $time): ?>
                    <option value="<?php echo htmlspecialchars($time['id']); ?>" <?php echo ($selectedTimeId == $time['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($time['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" id="selectedTimeId" name="selected_time_id" value="<?php echo htmlspecialchars($selectedTimeId); ?>">
    </form>
    <?php if ($selectedTimeId): ?>
        <?php if (count($players) > 0): ?>
            <?php foreach ($players as $player): ?>
                <div class="player-card">
                    <?php if ($player['imagem']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($player['imagem']); ?>" class="player-image" alt="Imagem do Jogador">
                    <?php else: ?>
                        <img src="../../../../public/images/default-player.png" class="player-image" alt="Imagem do Jogador">
                    <?php endif; ?>
                    <div class="player-details">
                        <strong>Nome:</strong> <?php echo htmlspecialchars($player['nome']); ?><br>
                        <strong>Posição:</strong> <?php echo htmlspecialchars($player['posicao']); ?><br>
                        <strong>Número:</strong> <?php echo htmlspecialchars($player['numero']); ?><br>
                        <strong>Gols:</strong> <?php echo htmlspecialchars($player['gols']); ?><br>
                        <strong>Assistências:</strong> <?php echo htmlspecialchars($player['assistencias']); ?><br>
                        <strong>Cartões Amarelos:</strong> <?php echo htmlspecialchars($player['cartoes_amarelos']); ?><br>
                        <strong>Cartões Vermelhos:</strong> <?php echo htmlspecialchars($player['cartoes_vermelhos']); ?><br>
                    </div>
                    <div class="player-actions">
                        <a href="editar_jogador.php?token=<?php echo htmlspecialchars($player['token']); ?>" class="btn btn-primary">Editar</a>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-token="<?php echo htmlspecialchars($player['token']); ?>">Excluir</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Não há jogadores para o time selecionado.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este jogador?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="delete_token" id="deleteToken" value="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var token = button.data('token');

        var modal = $(this);
        modal.find('#deleteToken').val(token);
        modal.find('#deleteForm').attr('action', '?delete_token=' + encodeURIComponent(token));
    });
</script>
</body>
</html>
