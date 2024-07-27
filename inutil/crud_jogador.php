<?php
include '../../../config/conexao.php';
session_start(); // Iniciar a sessão para validação CSRF e autenticação

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

    // Validar o token e obter o ID do jogador
    $sql = "SELECT id FROM jogadores WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
        // Excluir o jogador com o ID correspondente
        $deleteSql = "DELETE FROM jogadores WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Redirecionar após exclusão com sucesso
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            // Redirecionar após erro de exclusão
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }
}

// Gerar token CSRF para uso no formulário
$_SESSION['csrf_token'] = gerarTokenCSRF();

// Consulta SQL para obter todos os jogadores
$sql = "SELECT j.id, j.nome, j.posicao, j.numero, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, j.token, t.nome AS time_nome 
        FROM jogadores j 
        JOIN times t ON j.time_id = t.id 
        ORDER BY t.nome, j.nome";
$result = $conn->query($sql);

$players = [];
while ($row = $result->fetch_assoc()) {
    $players[$row['time_nome']][] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Jogadores</title>
    <!-- Adicionando o Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Seu estilo CSS */
        /* Seu estilo CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            text-align: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        
        h1 {
            font-size: 60px;
            text-align: center;
            margin-top: 5%;
        }
        /* .container {
            margin-top: 3%;
            padding: 20px;
            border: 3px solid rgba(31, 38, 135, 0.37);
            border-radius: 10px;

            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            width: 100%;
            overflow-x: auto;
            text-align: center;
            margin-bottom: 10%;
        } */
        .time-wrapper {
            display: flex;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        .time-container {
            flex: 0 0 auto;
            /* margin-right: 1.6%; */
            display: flex;
            flex-direction: column;
            width: 350px; /* Ajuste conforme necessário */
            margin-left: 1%;
            text-align: center;
        }
        .time-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        @keyframes borderColorChange {
            0% {
                border-color: borderColorChange; /* Usa a cor da borda definida no inline CSS */
            }
            50% {
                border-color: #FFFFFF; /* Cor intermediária (branco) */
            }
            100% {
                border-color: borderColorChange; /* Volta à cor inicial */
            }
        }
        .player-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Transições suaves para a transformação e sombra */
        }
        .player-card:hover {
            animation: borderColorChange 2s infinite; /* Aplica a animação quando o mouse passa por cima */
            /* box-shadow: 0 0 40px rgba(0, 0, 0, 0.4); Aumenta o efeito de sombra */
            box-shadow: 0 0 40px rgba(255, 0, 0, 0.4); /* Sombra vermelha com opacidade de 0.4 */

            transform: scale(1.05); /* Aumenta o tamanho da caixa em 10% */
            margin: 3%;
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
            border: 3px solid; /* A cor da borda será definida via CSS inline */
        }
        .player-image {
            width: 100px; /* Defina o tamanho desejado */
            height: auto; /* Defina o tamanho desejado */
            border-radius: 5px;
            margin-right: 20px;
        }
        .player-details {
            flex: 1;
            margin-right: 20px;
            text-align: left;
        }
        .player-actions {
            margin-top: 10px;
        }
        .player-actions button {
            margin-right: 10px;
        }

/* Responsividade para dispositivos móveis */
@media (max-width: 1440px) {
    .container {
        width: 95%;
        padding: 10px;
    }

    h1 {
        font-size: 80px; /* Define o tamanho da fonte */
            margin-top: 5%; /* Define a margem superior */
            margin-bottom: 10px; /* Define a margem inferior */
            text-align: center; /* Alinha o texto ao centro */
            text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5); /* Adiciona uma sombra ao texto */
    }

    .time-title {
        font-size: 40px;
    }

    .player-card {
        padding: 10px;
    }

    .player-image {
        width: 60px;
    }

    .player-details {
        margin-right: 5px;
    }
}

@media (max-width: 768px) {
    .container {
        width: 100%;
        padding: 5px;
    }
    .time-container {
        margin-bottom: 30px;
        margin-top: 30px;
            flex: 0 0 auto;
            /* margin-right: 1.6%; */
            display: flex;
            flex-direction: column;
            width: 250px; /* Ajuste conforme necessário */
            margin-left: 2%;
            text-align: center;
        }
    h1 {
        font-size: 1em;
    }

    .time-title {
        font-size: 16px;
    }

    .player-card {
        width: 100%;
        padding: 8px;
        flex-direction: center;
        align-items: center;
        justify-content: center;
    }

    .player-image {
        width: 50px;
    }

    .player-details {
        margin-right: 0;
        text-align: left;
        font-size: 10px;
    }
    .btn {
        padding: 3px;
    }
}
    </style>
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
                    <a href="../../cadastro_adm/login.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
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
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/estatistica.png" alt="Trophy Icon"></a>
                    <span>Editar jogadores</span>
                </div>
            </nav>
            <button onclick="toggleDarkMode()">Modo Escuro/Claro</button>
            <script>
                function toggleDarkMode() {
                    var element = document.body;
                    element.classList.toggle("dark-mode");
                }
            </script>
        </div>

    </header>
<h1>EDITAR JOGADORES</h1>
    <div class="container">

        <div class="time-wrapper">
            <?php
            $borderColors = ['#FF5733', '#33FF57', '#3357FF', '#F333FF', '#FF33A0', '#33F0FF', '#FFBF00', '#8CFF33']; // Defina as cores que você deseja usar
            $colorIndex = 0;
            ?>
            <?php foreach ($players as $timeNome => $playersList): ?>
                <div class="time-container">
                    <h2 class="time-title"><?php echo htmlspecialchars($timeNome); ?></h2>
                    <?php foreach ($playersList as $player): ?>
                        <div class="player-card" style="border-color: <?php echo $borderColors[$colorIndex]; ?>;">
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
                                <!-- Botão de exclusão -->
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-token="<?php echo htmlspecialchars($player['token']); ?>">
                                    Excluir
                                </button>
                            </div>
                        </div>
                        <?php
                        $colorIndex = ($colorIndex + 1) % count($borderColors);
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
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

    <!-- Adicionando o Bootstrap JS e dependências -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // JavaScript para configurar o formulário de exclusão no modal
        $('#confirmDeleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botão que acionou o modal
            var token = button.data('token'); // Extrai o token do atributo data-token

            var modal = $(this);
            modal.find('#deleteToken').val(token); // Define o valor do campo hidden no formulário
            modal.find('#deleteForm').attr('action', '?delete_token=' + encodeURIComponent(token)); // Define a URL de ação do formulário
        });
    </script>
</body>
</html>
