<?php
include "../../config/conexao.php";
session_start(); // Iniciar a sessão

// Função para gerar token CSRF
function gerarTokenCSRF() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Processa a atualização dos dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica o token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido.");
    }

    $token = trim($_POST['token']);
    
    // Valida o token e obtém o ID do jogador
    $sql = "SELECT id FROM jogadores WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
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
            // Redirecionar após sucesso
            header('Location: crud_jogador.php');
            exit;
        } else {
            // Redirecionar após erro
            header('Location: erro.php?error=' . urlencode($stmt->error));
            exit;
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }
}

// Recupera o jogador para exibir o formulário de edição
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    $sql = "SELECT * FROM jogadores WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $jogador = $result->fetch_assoc();
    } else {
        die("Jogador não encontrado.");
    }
    $stmt->close();
} else {
    die("Token não especificado.");
}

$conn->close();
$csrf_token = gerarTokenCSRF();
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Jogador</title>
    <link rel="stylesheet" href="../../../public/css/adm/cadastros_times_jogadores_adm/editar_jogador.css">
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
</head>
<body>
<?php 
require_once 'header_classificacao.php'
?>
    <div class="main">
    <div class="container">
        <div class="form-container">
            <h2 id="editable">Editar Jogador</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($jogador['token']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($jogador['nome']); ?>" required>
                
                <label for="posicao">Posição</label>
                <select id="posicao" name="posicao" required>
                    <option value="">Selecione a posição</option>
                    <option value="Fixo" <?php echo ($jogador['posicao'] == 'Fixo') ? 'selected' : ''; ?>>Fixo</option>
                    <option value="Ala Direita" <?php echo ($jogador['posicao'] == 'Ala Direita') ? 'selected' : ''; ?>>Ala Direita</option>
                    <option value="Ala Esquerda" <?php echo ($jogador['posicao'] == 'Ala Esquerda') ? 'selected' : ''; ?>>Ala Esquerda</option>
                    <option value="Pivô" <?php echo ($jogador['posicao'] == 'Pivô') ? 'selected' : ''; ?>>Pivô</option>
                </select>
                
                <label for="numero">Número</label>
                <input type="number" id="numero" name="numero" value="<?php echo htmlspecialchars($jogador['numero']); ?>" min="0" required>
                
                <label for="gols">Gols:</label>
                <input type="number" id="gols" name="gols" value="<?php echo htmlspecialchars($jogador['gols']); ?>" min="0">
                
                <label for="assistencias">Assistências</label>
                <input type="number" id="assistencias" name="assistencias" value="<?php echo htmlspecialchars($jogador['assistencias']); ?>" min="0">
                
                <label for="cartoes_amarelos">Cartões Amarelos</label>
                <input type="number" id="cartoes_amarelos" name="cartoes_amarelos" value="<?php echo htmlspecialchars($jogador['cartoes_amarelos']); ?>"min="0">
                
                <label for="cartoes_vermelhos">Cartões Vermelhos</label>
                <input type="number" id="cartoes_vermelhos" name="cartoes_vermelhos" value="<?php echo htmlspecialchars($jogador['cartoes_vermelhos']); ?>" min="0">
                
                <label for="imagem">Imagem do Jogador</label>
                <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImagem(event)">
                
                <img id="imagem-preview" src="<?php if ($jogador['imagem']) { echo 'data:image/jpeg;base64,' . base64_encode($jogador['imagem']); } ?>" alt="Imagem do Jogador">
                
                <input type="submit" value="Atualizar Jogador">
                <a href="javascript:history.back()" class="btn-cancel">Não Atualizar</a>
            </form>
        </div>
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('.form-container *');
        let delay = 0;

        // Adiciona a classe .hidden a todos os elementos dentro da .form-container
        elements.forEach(element => {
            element.classList.add('hidden');
        });

        // Função para remover a classe .hidden e revelar o elemento
        function revealElement(element, delay) {
            setTimeout(() => {
                element.classList.remove('hidden');
            }, delay);
        }

        // Revela cada elemento com um atraso reduzido
        elements.forEach((element, index) => {
            revealElement(element, index * 150); // Diminua o valor para acelerar o efeito
        });

        // Efeito de digitação para o título
        const textElement = document.getElementById('editable');
        const text = textElement.textContent;
        textElement.textContent = '';

        let index = 0;
        const typingSpeed = 50; // Aumente a velocidade do efeito de digitação

        function typeLetter() {
            if (index < text.length) {
                textElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeLetter, typingSpeed);
            }
        }

        typeLetter();
    });
    </script>
<?php require_once '../footer.php'; ?>
</body>
</html>
