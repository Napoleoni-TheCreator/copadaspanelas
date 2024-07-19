<?php
include "../../../config/conexao.php";

// Processa a atualização dos dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $posicao = trim($_POST['posicao']);
    $numero = intval($_POST['numero']);
    $gols = intval($_POST['gols']);
    $assistencias = intval($_POST['assistencias']);
    $cartoes_amarelos = intval($_POST['cartoes_amarelos']);
    $cartoes_vermelhos = intval($_POST['cartoes_vermelhos']);

    // Valida o nome (deve ser uma string sem números)
    if (!preg_match("/^[a-zA-Z\s]+$/", $nome)) {
        echo "Nome do jogador deve ser uma string sem números.";
        exit;
    }

    // Processa a imagem, se houver
    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    // Atualiza os dados do jogador no banco de dados
    if ($imagem) {
        $sql = "UPDATE jogadores SET nome=?, posicao=?, numero=?, gols=?, assistencias=?, cartoes_amarelos=?, cartoes_vermelhos=?, imagem=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiiiiisi', $nome, $posicao, $numero, $gols, $assistencias, $cartoes_amarelos, $cartoes_vermelhos, $imagem, $id);
    } else {
        $sql = "UPDATE jogadores SET nome=?, posicao=?, numero=?, gols=?, assistencias=?, cartoes_amarelos=?, cartoes_vermelhos=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiiiiii', $nome, $posicao, $numero, $gols, $assistencias, $cartoes_amarelos, $cartoes_vermelhos, $id);
    }

    if ($stmt->execute()) {
        // Redireciona para a mesma página de edição com o ID do jogador
        // header("Location: editar_jogador.php?id=$id");
        header("Location: crud_jogador.php");
        exit();
    } else {
        // Redireciona para a página de erro
        header("Location: erro.php?error=" . urlencode($stmt->error));
        exit();
    }

    $stmt->close();
}

// Recupera o jogador para exibir o formulário de edição
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM jogadores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $jogador = $result->fetch_assoc();
    } else {
        die("Jogador não encontrado.");
    }
    $stmt->close();
} else {
    die("ID do jogador não especificado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Jogador</title>
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
        input[type="text"], input[type="number"], select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        #imagem-preview {
            max-width: 100px;
            height: auto;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Editar Jogador</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($jogador['id']); ?>">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($jogador['nome']); ?>" required>
                
                <label for="posicao">Posição:</label>
                <select id="posicao" name="posicao" required>
                    <option value="">Selecione a posição</option>
                    <option value="Fixo" <?php echo ($jogador['posicao'] == 'Fixo') ? 'selected' : ''; ?>>Fixo</option>
                    <option value="Ala Direita" <?php echo ($jogador['posicao'] == 'Ala Direita') ? 'selected' : ''; ?>>Ala Direita</option>
                    <option value="Ala Esquerda" <?php echo ($jogador['posicao'] == 'Ala Esquerda') ? 'selected' : ''; ?>>Ala Esquerda</option>
                    <option value="Pivô" <?php echo ($jogador['posicao'] == 'Pivô') ? 'selected' : ''; ?>>Pivô</option>
                </select>
                
                <label for="numero">Número:</label>
                <input type="number" id="numero" name="numero" value="<?php echo htmlspecialchars($jogador['numero']); ?>" required>
                
                <label for="gols">Gols:</label>
                <input type="number" id="gols" name="gols" value="<?php echo htmlspecialchars($jogador['gols']); ?>">
                
                <label for="assistencias">Assistências:</label>
                <input type="number" id="assistencias" name="assistencias" value="<?php echo htmlspecialchars($jogador['assistencias']); ?>">
                
                <label for="cartoes_amarelos">Cartões Amarelos:</label>
                <input type="number" id="cartoes_amarelos" name="cartoes_amarelos" value="<?php echo htmlspecialchars($jogador['cartoes_amarelos']); ?>">
                
                <label for="cartoes_vermelhos">Cartões Vermelhos:</label>
                <input type="number" id="cartoes_vermelhos" name="cartoes_vermelhos" value="<?php echo htmlspecialchars($jogador['cartoes_vermelhos']); ?>">
                
                <label for="imagem">Imagem do Jogador:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage()">
                <img id="imagem-preview" src="#" alt="Imagem do Jogador">
                
                <input type="submit" value="Salvar">
            </form>
        </div>
    </div>

    <script>
        function previewImage() {
            const fileInput = document.getElementById('imagem');
            const imagePreview = document.getElementById('imagem-preview');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Exibe a imagem atual do jogador, se houver
        <?php if ($jogador['imagem']) { ?>
            const imagePreview = document.getElementById('imagem-preview');
            imagePreview.src = 'data:image/jpeg;base64,<?php echo base64_encode($jogador['imagem']); ?>';
            imagePreview.style.display = 'block';
        <?php } ?>
    </script>
</body>
</html>
