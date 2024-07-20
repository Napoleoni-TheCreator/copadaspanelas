<?php
include '../../../config/conexao.php';

// Função para lidar com a exclusão de um time
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Desativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    $sql = "DELETE FROM times WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
            
                header("Location: listar_times.php");
                exit();
            } else {
                echo "<script>alert('Nenhum time encontrado com o ID fornecido.'); window.location.href='index.php';</script>";
            }
        } else {
            echo "<script>alert('Erro ao excluir time: " . $stmt->error . "'); window.location.href='index.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "'); window.location.href='index.php';</script>";
    }

    // Reativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
}

// Consulta SQL para obter todos os times
$sql = "SELECT t.id, t.nome, t.logo, g.nome AS grupo_nome FROM times t JOIN grupos g ON t.grupo_id = g.id ORDER BY g.nome, t.nome";
$result = $conn->query($sql);

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Times</title>
    <style>
        /* Adicione seu estilo aqui */
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
        .time-card {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            border: 2px solid #ccc;
            position: relative;
        }
        .time-image {
            width: 100px; /* Defina o tamanho desejado */
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
        }
        .time-details {
            flex: 1;
            margin-right: 20px;
            text-align: left;
        }
        .time-actions {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        .time-actions a {
            display: inline-block;
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .time-actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>LISTAR TIMES</h1>
    <?php foreach ($times as $time): ?>
        <div class="time-card">
            <?php if ($time['logo']): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($time['logo']); ?>" class="time-image" alt="Logo do Time">
            <?php else: ?>
                <img src="../../../../public/images/default-team.png" class="time-image" alt="Logo do Time">
            <?php endif; ?>
            <div class="time-details">
                <strong>Nome:</strong> <?php echo htmlspecialchars($time['nome']); ?><br>
                <strong>Grupo:</strong> <?php echo htmlspecialchars($time['grupo_nome']); ?><br>
            </div>
            <div class="time-actions">
                <a href="editar_time.php?id=<?php echo $time['id']; ?>">Editar</a>
                <a href="?delete=<?php echo $time['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este time?')">Excluir</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
