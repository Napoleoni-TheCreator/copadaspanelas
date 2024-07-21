
<?php
include '../../../config/conexao.php';
session_start(); // Iniciar a sessão para validação CSRF e autenticação

// Função para lidar com a exclusão de um jogador
if (isset($_GET['delete'])) {
    // Verificação do token CSRF
    if (!isset($_SESSION['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token inválido");
    }

    // Validação e sanitização do ID
    $id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    $id = intval($id);

    // Verificação de permissões
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        die("Acesso negado");
    }

    $sql = "DELETE FROM jogadores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Jogador excluído com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao excluir jogador.');</script>";
    }
    $stmt->close();
}

// Gerar token CSRF para uso no formulário
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Consulta SQL para obter todos os jogadores
$sql = "SELECT j.id, j.nome, j.posicao, j.numero, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS time_nome FROM jogadores j JOIN times t ON j.time_id = t.id ORDER BY t.nome, j.nome";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            margin: 4%;
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 70%;
            overflow-x: auto;
            text-align: center;
        }
        .time-wrapper {
            display: flex;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        .time-container {
            flex: 0 0 auto;
            margin-right: 1.6%;
            /* margin-bottom: 30px; */
            display: flex;
            flex-direction: column;
            width: 350px; /* Ajuste conforme necessário */
            margin-left: 1.6%;
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
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.4); /* Aumenta o efeito de sombra */
                transform: scale(1.1); /* Aumenta o tamanho da caixa em 10% */
                margin: 0.5%;
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
            /* object-fit: contain; Ajusta a imagem sem cortar */
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
        .player-actions a {
            display: inline-block;
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        /* .player-actions a:hover {
            text-decoration: underline;
        } */
    </style>
</head>
<body>

    <div class="container">
        <h1>EDITAR JOGADORES</h1>
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
                                <a href="editar_jogador.php?id=<?php echo htmlspecialchars($player['id']); ?>">Editar</a>
                                <a href="?delete=<?php echo htmlspecialchars($player['id']); ?>&csrf_token=<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" onclick="return confirm('Tem certeza que deseja excluir este jogador?')">Excluir</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
                    // Atualize o índice da cor para a próxima
                    $colorIndex = ($colorIndex + 1) % count($borderColors);
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
