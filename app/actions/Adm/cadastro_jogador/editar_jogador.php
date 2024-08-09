<?php
include "../../../config/conexao.php";
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
    <link rel="stylesheet" href="../../../../public/css/adm/cadastros_times_jogadores_adm/editar_jogador.css">
</head>
<body>
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
                        <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/header/prancheta.svg" alt="Trophy Icon"></a>
                        <span>Editar jogadores</span>
                    </div>
                    <div class="nav-item">
                        <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/header/grupo.svg" alt="Trophy Icon"></a>
                        <span>Criar grupos</span>
                    </div>
                    <div class="nav-item">
                        <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/header/adtime.svg" alt="Trophy Icon"></a>
                        <span>Adicionar times</span>
                    </div>
                    <div class="nav-item">
                        <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/header/adadm.svg" alt="cadastro novos adm"></a>
                        <span>Adicionar outro adm</span>
                    </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()">Modo Escuro</button>
        </div>
    </header>
    <div class="container">
        <div class="form-container">
            <h2>Editar Jogador</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($jogador['token']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
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
                <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImagem(event)">
                
                <img id="imagem-preview" src="<?php if ($jogador['imagem']) { echo 'data:image/jpeg;base64,' . base64_encode($jogador['imagem']); } ?>" alt="Imagem do Jogador">
                
                <input type="submit" value="Atualizar Jogador">
                <a href="javascript:history.back()" class="btn-cancel">Não Atualizar</a>
            </form>
        </div>
    </div>

    <script>
        function previewImagem(event) {
            var input = event.target;
            var preview = document.getElementById('imagem-preview');
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
