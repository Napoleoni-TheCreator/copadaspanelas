<?php
include '../../../config/conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepara os dados do formulário para inserção no banco de dados
    $nome = $_POST['nome'];
    $posicao = $_POST['posicao'];
    $numero = $_POST['numero'];
    $time_id = $_POST['time'];

    // Processa a imagem do jogador
    $imagem = $_FILES['imagem'];
    $imagemTmpName = $imagem['tmp_name'];

    // Lê o conteúdo da imagem
    $imgData = file_get_contents($imagemTmpName);

    // Insere os dados no banco de dados
    $sql = "INSERT INTO jogadores (nome, posicao, numero, time_id, imagem) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $nome, $posicao, $numero, $time_id, $imgData);

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
        .titulo-barra {
            background-color: #fe0000;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            text-shadow: 3px 3px 3px black;
            font-size: 20px;
        }
        .formulario {
            display: flex;
            height: 85vh;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
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
            max-width: 100%;
            height: auto;
            display: none;
        }
        label {
            font-size: 30px;
        }
                /* Estilo para o select */
                select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 20px;
            font-family: Arial, sans-serif;
            appearance: none; /* Remove o estilo padrão do sistema */
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 20 20" fill="currentColor"><path d="M7 9l3 3 3-3h-6z"/></svg>'); /* Ícone de seta dropdown (SVG inline) */
            background-repeat: no-repeat;
            background-position: right 10px center; /* Posiciona o ícone à direita */
            background-size: 20px;
            background-color: #fff; /* Cor de fundo do select */
            cursor: pointer;
        }
        select:focus {
            outline: none; /* Remove o contorno ao focar */
            border-color: #666; /* Cor da borda ao focar */
        }
        select option {
            font-size: 16px; /* Tamanho da fonte das opções */
        }
    </style>
</head>
<body>
    <div class="fundo-tela">
        <div class="titulo-barra">
            <h1>Cadastro de Jogadores</h1>
        </div>
        <div class="formulario">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="nome">Nome do Jogador:</label>
                <input type="text" id="nome" name="nome" required>
                <label for="posicao">Posição:</label>
                <input type="text" id="posicao" name="posicao" required>
                <label for="numero">Número:</label>
                <input type="text" id="numero" name="numero" required>
                <label for="time">Time:</label>
                <select id="time" name="time" required>
                    <option value="">Selecione o time</option>
                    <?php
                    // Conexão com o banco de dados
                    include '../../../config/conexao.php';

                    // Consulta SQL para obter todos os times
                    $sql = "SELECT id, nome FROM times";
                    $result = $conn->query($sql);

                    // Loop para gerar as opções do select
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nome'] . "</option>";
                    }

                    // Fechar a conexão com o banco de dados
                    $conn->close();
                    ?>
                </select>
                <label for="imagem">Imagem do Jogador:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage()" required>
                <img id="imagem-preview" src="#" alt="Imagem do Jogador">
                <input type="submit" value="Cadastrar">
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
    </script>
</body>
</html>
