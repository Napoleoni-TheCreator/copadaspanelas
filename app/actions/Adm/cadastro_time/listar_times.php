<?php
include '../../../config/conexao.php';
session_start(); // Iniciar a sessão para validação CSRF e autenticação

// Função para gerar token
function gerarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Função para lidar com a exclusão de um time
if (isset($_GET['delete'])) {
    // Verificação do token CSRF
    if (!isset($_SESSION['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token inválido");
    }

    $token = $_GET['delete'];

    // Desativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Validar o token e obter o ID do time
    $sql = "SELECT id FROM times WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
        // Excluir o time com o ID correspondente
        $deleteSql = "DELETE FROM times WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Redireciona para a lista de times com uma mensagem de sucesso
                header("Location: listar_times.php?status=success");
                exit();
            } else {
                echo "<script>alert('Nenhum time encontrado com o ID fornecido.'); window.location.href='listar_times.php';</script>";
            }
        } else {
            echo "<script>alert('Erro ao excluir time: " . $stmt->error . "'); window.location.href='listar_times.php';</script>";
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }

    // Reativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
}

// Consulta SQL para obter todos os times
$sql = "SELECT t.id, t.nome, t.logo, t.token, g.nome AS grupo_nome FROM times t JOIN grupos g ON t.grupo_id = g.id ORDER BY g.nome, t.nome";
$result = $conn->query($sql);

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[] = $row;
}

$conn->close();

// Gerar token CSRF para uso no formulário
$csrf_token = gerarToken();
$_SESSION['csrf_token'] = $csrf_token;
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
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">Time excluído com sucesso!</div>
    <?php endif; ?>
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
                <a href="editar_time.php?token=<?php echo $time['token']; ?>">Editar</a>
                <a href="?delete=<?php echo $time['token']; ?>&csrf_token=<?php echo $csrf_token; ?>" onclick="return confirm('Tem certeza que deseja excluir este time?')">Excluir</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
