<?php
include '../../../config/conexao.php';

// Função para lidar com a exclusão de um jogador
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM jogadores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Jogador excluído com sucesso!";
    } else {
        echo "Erro ao excluir jogador: " . $conn->error;
    }
    $stmt->close();
}

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
    <link rel="stylesheet" href="../../../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .time-container {
            margin-bottom: 30px;
        }
        .time-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .player-card {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .player-image {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
        }
        .player-details {
            flex: 1;
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
        .player-actions a:hover {
            text-decoration: underline;
        }
        .form-container {
            margin-top: 20px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            background-color: #c60909;
            color: #fff;
            border: none;
            padding: 15px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CRUD de Jogadores</h1>

        <?php foreach ($players as $timeNome => $playersList): ?>
            <div class="time-container">
                <h2 class="time-title"><?php echo htmlspecialchars($timeNome); ?></h2>
                <?php foreach ($playersList as $player): ?>
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
                            <a href="editar_jogador.php?id=<?php echo $player['id']; ?>">Editar</a>
                            <a href="?delete=<?php echo $player['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este jogador?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
