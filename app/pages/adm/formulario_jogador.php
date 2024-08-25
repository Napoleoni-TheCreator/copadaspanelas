<?php
include '../../config/conexao.php';
// Função para gerar um token único
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Inicia a sessão para usar flash messages
session_start();
// Verifica se o usuário está autenticado e se é um administrador
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

$response = [
    'success' => true,
    'message' => ''
];

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepara os dados do formulário para inserção no banco de dados
    $nome = trim($_POST['nome']);
    $posicao = trim($_POST['posicao']);
    $numero = trim($_POST['numero']);
    $time_id = trim($_POST['time']);

    // Valida o nome (deve ser uma string sem números)
    if (!preg_match("/^[a-zA-Z\s]+$/", $nome)) {
        $response['success'] = false;
        $response['message'] = "Nome do jogador deve ser uma string sem números.";
        echo json_encode($response);
        exit;
    }

    // Valida se todos os campos estão preenchidos
    if (empty($nome) || empty($posicao) || empty($numero) || empty($time_id)) {
        $response['success'] = false;
        $response['message'] = "Todos os campos são obrigatórios.";
        echo json_encode($response);
        exit;
    }

    // Valida o número (deve ser um número entre 0 e 99, com no máximo 2 dígitos)
    if (!is_numeric($numero) || $numero < 0 || $numero > 99 || strlen($numero) > 2) {
        $response['success'] = false;
        $response['message'] = "Número deve ser um valor entre 0 e 99, com no máximo 2 dígitos.";
        echo json_encode($response);
        exit;
    }

    // Verifica se o número do jogador já está em uso para o time especificado
    $sql = "SELECT COUNT(*) FROM jogadores WHERE numero = ? AND time_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $numero, $time_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['success'] = false;
        $response['message'] = "Número já está em uso para este time.";
        echo json_encode($response);
        exit;
    }

    // Processa a imagem do jogador
    $imagem = $_FILES['imagem'];
    if ($imagem['error'] == UPLOAD_ERR_NO_FILE) {
        // Se nenhum arquivo foi enviado, usar uma imagem padrão
        $imgData = file_get_contents('../../../public/img/perfil_padrao_jogador.png'); // Atualize o caminho para a imagem padrão
    } else {
        $imagemTmpName = $imagem['tmp_name'];
        $imgData = file_get_contents($imagemTmpName);
    }

    // Gera um token único para o jogador
    $token = generateToken();

    // Insere os dados no banco de dados
    $sql = "INSERT INTO jogadores (nome, posicao, numero, time_id, imagem, token) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $nome, $posicao, $numero, $time_id, $imgData, $token);

    if ($stmt->execute()) {
        $response['message'] = "Jogador adicionado com sucesso!";
        echo json_encode($response);
    } else {
        $response['success'] = false;
        $response['message'] = "Erro ao adicionar jogador: " . $conn->error;
        echo json_encode($response);
    }

    // Fecha a conexão com o banco de dados
    $stmt->close();
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Jogadores</title>
    <link rel="stylesheet" href="../../../public/css/adm/cadastros_times_jogadores_adm/formulario_jogador.css">
    <style>
        .error-message {
            display: none;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
        }
        .error-message.visible {
            display: block;
        }
        .success-message {
            display: none;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
        }
        .success-message.visible {
            display: block;
        }
    </style>
</head>
<body>
<?php 
require_once 'header_classificacao.php'
?>
<div class="fundo-tela">
    <div class="formulario" id="main-content">
        <form id="form-jogador" action="" method="post" enctype="multipart/form-data">
            <h2 id="editable">Editar Jogador</h2>

            <label for="nome">Nome do Jogador</label>
            <input type="text" id="nome" name="nome" required maxlength="90">
            <label for="posicao">Posição</label>
            <select id="posicao" name="posicao" required>
                <option value="">Selecione a posição</option>
                <option value="Fixo">Fixo</option>
                <option value="Ala Direita">Ala Direita</option>
                <option value="Ala Esquerda">Ala Esquerda</option>
                <option value="Pivô">Pivô</option>
            </select>
            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" required maxlength="3">
            <label for="time">Time</label>
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
            <label for="imagem">Imagem do Jogador</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage()">
            <img id="imagem-preview" src="#" alt="Imagem do Jogador">
            <div id="message-container">
                <div id="error-message" class="error-message"></div>
                <div id="success-message" class="success-message"></div>
            </div>
            <input type="submit" value="Cadastrar">
        </form>
    </div>
    <?php include '../footer.php' ?>
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

    document.getElementById('form-jogador').addEventListener('submit', function (event) {
        event.preventDefault(); // Impede o envio do formulário padrão

        const formData = new FormData(this);
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('success-message');

            if (!data.success) {
                errorMessage.textContent = data.message;
                errorMessage.classList.add('visible');
                successMessage.classList.remove('visible');
            } else {
                successMessage.textContent = data.message;
                successMessage.classList.add('visible');
                errorMessage.classList.remove('visible');
                document.getElementById('form-jogador').reset(); // Limpa o formulário
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    });
    const element = document.getElementById('editable');
    let index = 0;
    const speed = 100; // Velocidade de digitação em milissegundos

    function typeWriter() {
        if (index < text.length) {
            element.innerHTML += text.charAt(index);
            index++;
            setTimeout(typeWriter, speed);
        }
    }

    // Inicia a digitação ao carregar a página
    window.onload = typeWriter;

</script>
</body>
</html>
