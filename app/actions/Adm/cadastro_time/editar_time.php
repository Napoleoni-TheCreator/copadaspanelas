<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../config/conexao.php";

// Exibe erros para depuração
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verifica se o ID do time foi passado via GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Processa o envio do formulário (atualização de dados)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $grupo_id = $_POST['grupo_id'];
        $pts = $_POST['pts'];
        $vitorias = $_POST['vitorias'];
        $empates = $_POST['empates'];
        $derrotas = $_POST['derrotas'];
        $gm = $_POST['gm'];
        $gc = $_POST['gc'];
        $sg = $_POST['sg'];

        // Consulta para obter a imagem atual
        $sql = "SELECT logo FROM times WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $time = $result->fetch_assoc();
        $logo_atual = $time['logo'];
        $stmt->close();

        // Verifica se foi enviada uma nova imagem
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
            $logo = file_get_contents($_FILES['logo']['tmp_name']);
        } else {
            $logo = $logo_atual;
        }

        $sql = "UPDATE times SET nome = ?, logo = ?, grupo_id = ?, pts = ?, vitorias = ?, empates = ?, derrotas = ?, gm = ?, gc = ?, sg = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiiiiiiiii', $nome, $logo, $grupo_id, $pts, $vitorias, $empates, $derrotas, $gm, $gc, $sg, $id);

        if ($stmt->execute()) {
            header("Location: listar_times.php");
            exit();
        } else {
            echo "<script>alert('Erro ao atualizar time: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    // Consulta para obter os dados do time pelo ID
    $sql = "SELECT * FROM times WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $time = $result->fetch_assoc();
    $stmt->close();
} else {
    die("ID do time não especificado.");
}

// Consulta para obter todos os grupos
$sql = "SELECT id, nome FROM grupos";
$grupos = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Time</title>
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
        .form-container {
            margin-top: 20px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input, select {
            margin-bottom: 10px;
        }
        input[type="file"] {
            padding: 8px;
        }
        input[type="submit"] {
            padding: 10px;
            border: none;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .imagem-preview {
            max-width: 300px;
            margin-top: 10px;
        }
        .imagem-preview img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Editar Time</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($time['id']); ?>">

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($time['nome']); ?>" maxlength="30" required>

                <label for="grupo_id">Grupo:</label>
                <select id="grupo_id" name="grupo_id" required>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo $grupo['id']; ?>" <?php echo ($grupo['id'] == $time['grupo_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grupo['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="pts">Pontos:</label>
                <input type="number" id="pts" name="pts" value="<?php echo htmlspecialchars($time['pts']); ?>" max="1000" required>

                <label for="vitorias">Vitórias:</label>
                <input type="number" id="vitorias" name="vitorias" value="<?php echo htmlspecialchars($time['vitorias']); ?>" max="100" required>

                <label for="empates">Empates:</label>
                <input type="number" id="empates" name="empates" value="<?php echo htmlspecialchars($time['empates']); ?>" max="100" required>

                <label for="derrotas">Derrotas:</label>
                <input type="number" id="derrotas" name="derrotas" value="<?php echo htmlspecialchars($time['derrotas']); ?>" max="100" required>

                <label for="gm">Gols Marcados:</label>
                <input type="number" id="gm" name="gm" value="<?php echo htmlspecialchars($time['gm']); ?>" max="1000" required>

                <label for="gc">Gols Sofridos:</label>
                <input type="number" id="gc" name="gc" value="<?php echo htmlspecialchars($time['gc']); ?>" max="1000" required>

                <label for="sg">Saldo de Gols:</label>
                <input type="number" id="sg" name="sg" value="<?php echo htmlspecialchars($time['sg']); ?>" max="1000" required>

                <?php if (!empty($time['logo'])): ?>
                    <label>Logo Atual:</label>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($time['logo']); ?>" alt="Imagem do Time" class="imagem-preview">
                <?php endif; ?>

                <label for="logo">Nova Imagem do Time (opcional):</label>
                <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(event)">

                <div id="imagem-preview" class="imagem-preview"></div>

                <input type="submit" value="Atualizar Time">
            </form>
        </div>
    </div>
    <script>
        function previewImage(event) {
            const previewContainer = document.getElementById('imagem-preview');
            previewContainer.innerHTML = ''; // Limpa a visualização anterior

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
