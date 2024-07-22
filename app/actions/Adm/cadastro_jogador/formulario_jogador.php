<?php
include '../../../config/conexao.php';

// Função para gerar um token único
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepara os dados do formulário para inserção no banco de dados
    $nome = trim($_POST['nome']);
    $posicao = trim($_POST['posicao']);
    $numero = trim($_POST['numero']);
    $time_id = trim($_POST['time']);
    
    // Valida o nome (deve ser uma string sem números)
    if (!preg_match("/^[a-zA-Z\s]+$/", $nome)) {
        echo "Nome do jogador deve ser uma string sem números.";
        exit;
    }

    // Valida se todos os campos estão preenchidos
    if (empty($nome) || empty($posicao) || empty($numero) || empty($time_id)) {
        echo "Todos os campos são obrigatórios.";
        exit;
    }

    // Valida o número (deve ser um número entre 0 e 99, com no máximo 2 dígitos)
    if (!is_numeric($numero) || $numero < 0 || $numero > 99 || strlen($numero) > 2) {
        echo "Número deve ser um valor entre 0 e 99, com no máximo 2 dígitos.";
        exit;
    }

    // Processa a imagem do jogador
    $imagem = $_FILES['imagem'];
    $imagemTmpName = $imagem['tmp_name'];
    $imgData = file_get_contents($imagemTmpName);

    // Gera um token único para o jogador
    $token = generateToken();

    // Insere os dados no banco de dados
    $sql = "INSERT INTO jogadores (nome, posicao, numero, time_id, imagem, token) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $nome, $posicao, $numero, $time_id, $imgData, $token);

    if ($stmt->execute()) {
        echo "Jogador adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar jogador: " . $conn->error;
    }

    // Fecha a conexão com o banco de dados
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Jogadores</title>
    <style>
        body {
            height: 100vh;
            background-size: cover;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
        }
        .formulario {
            margin-bottom: 5%;
            display: flex;
            height: 100%;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        #main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        form {
            max-width: 400px;
            margin-top: 5%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 80px;
            border-radius: 30px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="file"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-size: 20px;
        }
        input[type="submit"] {
            background-color: #c60909;
            color: #fff;
            border: none;
            padding: 15px 25px;
            cursor: pointer;
            border-radius: 5px;
        }
        #imagem-preview {
            max-width: 20%;
            height: auto;
            display: none;
        }
        label {
            font-size: 30px;
        }
        select {
            width: 105%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 20px;
            font-family: Arial, sans-serif;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 20 20" fill="currentColor"><path d="M7 9l3 3 3-3h-6z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            background-color: #fff;
            cursor: pointer;
        }
        select:focus {
            outline: none;
            border-color: #666;
        }
        select option {
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="fundo-tela">
    <?php include '../../../pages/header.php' ?>
    <div class="formulario" id="main-content">
        <form id="form-jogador" action="" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
            <label for="nome">Nome do Jogador:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="posicao">Posição:</label>
            <select id="posicao" name="posicao" required>
                <option value="">Selecione a posição</option>
                <option value="Fixo">Fixo</option>
                <option value="Ala Direita">Ala Direita</option>
                <option value="Ala Esquerda">Ala Esquerda</option>
                <option value="Pivô">Pivô</option>
            </select>
            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" required>
            <label for="time">Time:</label>
            <select id="time" name="time" required>
                <option value="">Selecione o time</option>
                <?php
                include '../../../config/conexao.php';
                $sql = "SELECT id, nome FROM times";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                }
                $conn->close();
                ?>
            </select>
            <label for="imagem">Imagem do Jogador:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage()" required>
            <img id="imagem-preview" src="#" alt="Imagem do Jogador">
            <input type="submit" value="Cadastrar">
        </form>
    </div>
    <?php include '../../../pages/footer.php' ?>
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

    function validarFormulario() {
        const nome = document.getElementById('nome').value;
        const numero = document.getElementById('numero').value;
        const nomeRegex = /^[a-zA-Z\s]+$/;
        const numeroRegex = /^[0-9]{1,2}$/;

        if (!nomeRegex.test(nome)) {
            alert('Nome do jogador deve ser uma string sem números.');
            return false;
        }

        if (!numeroRegex.test(numero) || parseInt(numero) > 99) {
            alert('Número deve ser entre 0 e 99, com no máximo 2 dígitos.\n Não pode ser letras.\n Por favor tente novamente.');
            return false;
        }
        return true;
    }
</script>
</body>
</html>




