<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../config/conexao.php";
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

// Busca o campeonato ativo
$sql = "SELECT id FROM campeonatos WHERE ativo = TRUE LIMIT 1";
$stmt = $conn->query($sql);
$campeonatoAtivo = $stmt->fetch_assoc();

if (!$campeonatoAtivo) {
    die("Nenhum campeonato ativo encontrado.");
}

$campeonatoId = $campeonatoAtivo['id'];

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
    $sql = "SELECT logo FROM times WHERE token = ? AND campeonato_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $token, $campeonatoId);
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
    $sql = "UPDATE times SET nome = ?, logo = ?, grupo_id = ?, pts = ?, vitorias = ?, empates = ?, derrotas = ?, gm = ?, gc = ?, sg = ? WHERE token = ? AND campeonato_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiiiiiiissi', $nome, $logo, $grupo_id, $pts, $vitorias, $empates, $derrotas, $gm, $gc, $sg, $token, $campeonatoId);

    if ($stmt->execute()) {
        header("Location: listar_times.php");
        exit();
    } else {
        echo "<script>alert('Erro ao atualizar time: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Consulta para obter os dados do time pelo token
$sql = "SELECT * FROM times WHERE token = ? AND campeonato_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $token, $campeonatoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Se não encontrar o time com o token fornecido, redireciona para a página anterior ou para uma página de erro
    header("Location: listar_times.php");
    exit();
}

$time = $result->fetch_assoc();
$stmt->close();

// Consulta para obter todos os grupos do campeonato ativo
$sql = "SELECT id, nome FROM grupos WHERE campeonato_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $campeonatoId);
$stmt->execute();
$grupos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Time</title>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <link rel="stylesheet" href="../../../public/css/adm/editar_times.css">
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
<?php require_once 'header_classificacao.php' ?>
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
    <?php 
    require_once '../footer.php';
    ?>
</body>
</html>