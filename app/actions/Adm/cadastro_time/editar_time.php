<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../config/conexao.php";

// Exibe erros para depuração
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verifica se o token do time foi passado via GET
if (!isset($_GET['token']) || empty($_GET['token'])) {
    // Redireciona para a página anterior ou para uma página de erro
    header("Location: listar_times.php");
    exit();
}

$token = $_GET['token'];

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
    $sql = "SELECT logo FROM times WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $time = $result->fetch_assoc();
    $logo_atual = $time['logo'];
    $stmt->close();

    // Verifica se foi enviada uma nova imagem
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $imagem = $_FILES['logo']['tmp_name'];
        $imagem_tipo = mime_content_type($imagem);

        // Verifica o tipo de imagem
        if (in_array($imagem_tipo, ['image/jpeg', 'image/png'])) {
            $logo = file_get_contents($imagem);
        } else {
            echo "<script>alert('Tipo de arquivo inválido. Somente JPEG e PNG são permitidos.');</script>";
            $logo = $logo_atual;
        }
    } else {
        $logo = $logo_atual;
    }

    // Atualiza os dados do time
    $sql = "UPDATE times SET nome = ?, logo = ?, grupo_id = ?, pts = ?, vitorias = ?, empates = ?, derrotas = ?, gm = ?, gc = ?, sg = ? WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiiiiiiiss', $nome, $logo, $grupo_id, $pts, $vitorias, $empates, $derrotas, $gm, $gc, $sg, $token);

    if ($stmt->execute()) {
        header("Location: listar_times.php");
        exit();
    } else {
        echo "<script>alert('Erro ao atualizar time: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Consulta para obter os dados do time pelo token
$sql = "SELECT * FROM times WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Se não encontrar o time com o token fornecido, redireciona para a página anterior ou para uma página de erro
    header("Location: listar_times.php");
    exit();
}

$time = $result->fetch_assoc();
$stmt->close();

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
            font-family: 'Arial', sans-serif;
            /* background-color: red; */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .container {
            padding: 20px;
            width: 100%;
            max-width: 60%;
        }
        .form-container {
            /* background-color: rgb(218, 215, 215);
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px); */
            border: 3px solid rgba(31, 38, 135, 0.37);
            padding: 40px;
            margin-top: 10%;
            margin-bottom: 10%;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 2px;
            font-size: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-size: 18px;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], select {
            padding: 10px;
            border: 3px solid rgba(31, 38, 135, 0.37);
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.2);
        }
        input[type="file"] {
            padding: 10px;
            border: 3px solid rgba(31, 38, 135, 0.37);
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.2);
        }
        input[type="submit"] {
            padding: 15px;
            border: 3px solid rgba(31, 38, 135, 0.37);
            background-color: #28a745;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .btn-cancel {
            padding: 15px;
            background-color: #dc3545;
            color: white;
            border: 3px solid rgba(31, 38, 135, 0.37);
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            font-size: 12px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-cancel:hover {
            background-color: #c82333;
        }
        #imagem-preview {
            max-width: 200px;
            height: auto;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
        }
        input[type=number] {
            -webkit-appearance: none;
            -moz-appearance: textfield !important;
            appearance: none;
        }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none; /* Chrome, Safari, Edge */
            margin: 0; /* Remove margin */
        }
         /* Modo escuro */
         .dark-mode {
            background-color: #121212; /* Cor de fundo escura */
            color: #e0e0e0; /* Cor do texto escura */
        }

        .dark-mode .form-container {
            background-color: #1e1e1e; /* Cor de fundo escura para o formulário */
            border: 3px solid #444; /* Borda do formulário escura */
        }

        .dark-mode input[type="text"], 
        .dark-mode input[type="number"], 
        .dark-mode select, 
        .dark-mode input[type="file"] {
            background: #333; /* Fundo escuro para campos */
            color: #e0e0e0; /* Cor do texto escuro para campos */
            border: 1px solid #555; /* Borda escura para campos */
        }

        .dark-mode input[type="submit"] {
            background-color: #007bff; /* Cor do botão escura */
            border: 1px solid #0056b3; /* Borda escura para o botão */
        }

        .dark-mode input[type="submit"]:hover {
            background-color: #0056b3; /* Cor do botão escura ao passar o mouse */
        }

        .dark-mode .btn-cancel {
            background-color: #ff3d3d; /* Cor do botão cancelar escura */
            border: 1px solid #cc0000; /* Borda escura para o botão cancelar */
        }

        .dark-mode .btn-cancel:hover {
            background-color: #cc0000; /* Cor do botão cancelar ao passar o mouse */
        }

        .dark-mode #imagem-preview {
            border: 1px solid #444; /* Borda escura para a imagem */
        }

    
    </style>
    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagem-preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagem-preview').style.display = 'none';
            }
        }
    </script>
</head>
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
                    <a href="../../Adm/adicionar_dados/tabela_de_classificacao.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
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
<body>
    <div class="container">
        <div class="form-container">
            <h2>Editar Time</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($time['token']); ?>">

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
                <input type="number" id="pts" name="pts" value="<?php echo htmlspecialchars($time['pts']); ?>" min="0" max="100" required>

                <label for="vitorias">Vitórias:</label>
                <input type="number" id="vitorias" name="vitorias" value="<?php echo htmlspecialchars($time['vitorias']); ?>" min="0" max="100" required>

                <label for="empates">Empates:</label>
                <input type="number" id="empates" name="empates" value="<?php echo htmlspecialchars($time['empates']); ?>" min="0" max="100" required>

                <label for="derrotas">Derrotas:</label>
                <input type="number" id="derrotas" name="derrotas" value="<?php echo htmlspecialchars($time['derrotas']); ?>" min="0" max="100" required>

                <label for="gm">Gols Marcados:</label>
                <input type="number" id="gm" name="gm" value="<?php echo htmlspecialchars($time['gm']); ?>" min="0" max="1000" required>

                <label for="gc">Gols Contra:</label>
                <input type="number" id="gc" name="gc" value="<?php echo htmlspecialchars($time['gc']); ?>" min="0" max="1000" required>

                <label for="sg">Saldo de Gols:</label>
                <input type="number" id="sg" name="sg" value="<?php echo htmlspecialchars($time['sg']); ?>" min="-1000" max="1000" required>

                <label for="logo">Logo:</label>
                <input type="file" id="logo" name="logo" accept="image/jpeg, image/png" onchange="previewImage(this)">

                <?php if (!empty($time['logo'])): ?>
                    <img id="imagem-preview" src="data:image/jpeg;base64,<?php echo base64_encode($time['logo']); ?>" alt="Logo Atual">
                <?php else: ?>
                    <img id="imagem-preview" style="display:none;" alt="Logo Atual">
                <?php endif; ?>

                <input type="submit" value="Salvar">
                <a href="listar_times.php" class="btn-cancel">Cancelar</a>
            </form>
           
        </div>
    </div>
</body>
</html>
