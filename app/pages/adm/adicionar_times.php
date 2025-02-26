<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado como admin
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

// Função para gerar o formulário com base na quantidade de times
function generateFormFields($numTimes) {
    $fieldsHtml = '';
    for ($i = 0; $i < $numTimes; $i++) {
        $fieldsHtml .= '
        <label for="nome_time_' . $i . '">Nome do Time ' . ($i + 1) . ':</label>
        <input type="text" id="nome_time_' . $i . '" name="nome_time[]" required>
        
        <label for="logo_time_' . $i . '">Logo do Time ' . ($i + 1) . ':</label>
        <input type="file" id="logo_time_' . $i . '" name="logo_time[]" accept="image/*" required>
        ';
    }
    return $fieldsHtml;
}

// Função para gerar um token único
function generateUniqueToken() {
    return bin2hex(random_bytes(16)); // Gera um token de 32 caracteres
}

// Conexão com o banco de dados
include '../../config/conexao.php';

// Busca o campeonato ativo
$sql = "SELECT id FROM campeonatos WHERE ativo = TRUE LIMIT 1";
$stmt = $conn->query($sql);
$campeonatoAtivo = $stmt->fetch_assoc();

if (!$campeonatoAtivo) {
    die("Nenhum campeonato ativo encontrado.");
}

$campeonatoId = $campeonatoAtivo['id'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $grupoId = $_POST['grupo_id']; // ID do grupo selecionado
    $numTimes = count($_POST['nome_time']); // Número de times a serem adicionados

    // Consulta para obter a quantidade de equipes por grupo
    $configSql = "SELECT equipes_por_grupo FROM configuracoes WHERE campeonato_id = $campeonatoId LIMIT 1";
    $configResult = $conn->query($configSql);

    if ($configResult->num_rows > 0) {
        $configRow = $configResult->fetch_assoc();
        $maxTimesPerGroup = $configRow['equipes_por_grupo'];

        // Conta quantos times já existem no grupo selecionado
        $countSql = "SELECT COUNT(*) as count FROM times WHERE grupo_id = $grupoId AND campeonato_id = $campeonatoId";
        $countResult = $conn->query($countSql);
        $countRow = $countResult->fetch_assoc();
        $currentCount = $countRow['count'];

        if (($currentCount + $numTimes) <= $maxTimesPerGroup) {
            $success = true; // Flag para indicar sucesso
            $duplicateNames = []; // Array para armazenar nomes duplicados

            // Loop para processar cada time
            for ($i = 0; $i < $numTimes; $i++) {
                // Dados do time atual
                $nomeTime = $_POST['nome_time'][$i];

                // Verifica se o nome do time já existe no campeonato ativo
                $checkSql = "SELECT COUNT(*) as count FROM times WHERE nome = '$nomeTime' AND campeonato_id = $campeonatoId";
                $checkResult = $conn->query($checkSql);
                $checkRow = $checkResult->fetch_assoc();
                
                if ($checkRow['count'] > 0) {
                    $duplicateNames[] = $nomeTime; // Adiciona o nome à lista de duplicados
                    $success = false; // Marca como falha
                } else {
                    // Tratamento do upload da imagem
                    $logoTime = file_get_contents($_FILES['logo_time']['tmp_name'][$i]); // Obtém o conteúdo binário da imagem
                    $logoTime = addslashes($logoTime); // Escapa caracteres especiais para evitar problemas de SQL Injection

                    // Gera um token único para o time
                    $token = generateUniqueToken();

                    // Inserção dos dados na tabela de times
                    $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg, token, campeonato_id) 
                            VALUES ('$nomeTime', '$logoTime', '$grupoId', 0, 0, 0, 0, 0, 0, 0, '$token', '$campeonatoId')";

                    if ($conn->query($sql) !== TRUE) {
                        $_SESSION['message'] = "Erro ao adicionar time: " . $conn->error;
                        $_SESSION['message_type'] = 'error';
                        $success = false;
                        break; // Encerra o loop em caso de erro
                    }
                }
            }

            // Configura a mensagem de sucesso ou erro com base nos resultados
            if ($success) {
                if (count($duplicateNames) > 0) {
                    $_SESSION['message'] = "Times adicionados com sucesso, mas os seguintes nomes já existem: " . implode(", ", $duplicateNames);
                    $_SESSION['message_type'] = 'warning';
                } else {
                    $_SESSION['message'] = "Times adicionados com sucesso!";
                    $_SESSION['message_type'] = 'success';
                }
            } else {
                $_SESSION['message'] = "Não foi possível adicionar todos os times.";
                if (count($duplicateNames) > 0) {
                    $_SESSION['message'] .= " Os seguintes nomes já existem: " . implode(", ", $duplicateNames);
                }
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = "Não é possível adicionar mais times. O grupo já contém o número máximo de times permitido.";
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = "Erro ao obter a configuração de equipes por grupo.";
        $_SESSION['message_type'] = 'error';
    }

    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Redireciona para evitar o reenvio do formulário
    exit();
}

$numTimesToAdd = isset($_POST['num_times']) ? (int)$_POST['num_times'] : 1;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Times</title>
    <link rel="stylesheet" href="../../../public/css/adm/cadastros_times_jogadores_adm/adicionar_times.css">
</head>
<body>
<?php require_once 'header_classificacao.php' ?>
<div class="main">
    <div class="titulo-barra">
        <h1>Adicionar Times</h1>
    </div>

    <div class="formulario" id="main-content">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <label for="num_times">Número de Times para Adicionar:</label>
            <select id="num_times" name="num_times" onchange="updateFormFields(this.value)">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= ($numTimesToAdd == $i) ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <div id="times-fields">
                <?php echo generateFormFields($numTimesToAdd); ?>
            </div>

            <label for="grupo_id">Grupo:</label>
            <select id="grupo_id" name="grupo_id" required>
                <?php
                // Conexão com o banco de dados para carregar os grupos disponíveis
                include '../../config/conexao.php';

                $sql = "SELECT id, nome FROM grupos WHERE campeonato_id = $campeonatoId ORDER BY nome";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['nome'] . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhum grupo encontrado</option>';
                }

                $conn->close();
                ?>
            </select>

            <input type="submit" value="Adicionar Times">
            <?php
                if (isset($_SESSION['message'])) {
                    $messageType = $_SESSION['message_type'];
                    $messageClass = $messageType == 'success' ? 'success' : ($messageType == 'warning' ? 'warning' : 'error');
                    echo "<div class='message $messageClass'>{$_SESSION['message']}</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
            ?>
        </form>
    </div>
</div>
<script>
function updateFormFields(num) {
    const container = document.getElementById('times-fields');
    let fieldsHtml = '';
    for (let i = 0; i < num; i++) {
        fieldsHtml += `
        <label for="nome_time_${i}">Nome do Time ${i + 1}:</label>
        <input type="text" id="nome_time_${i}" name="nome_time[]" required>
        
        <label for="logo_time_${i}">Logo do Time ${i + 1}:</label>
        <input type="file" id="logo_time_${i}" name="logo_time[]" accept="image/*" required>
        `;
    }
    container.innerHTML = fieldsHtml;
}

// Inicializa os campos do formulário de acordo com o número de times selecionados
document.addEventListener('DOMContentLoaded', function() {
    updateFormFields(<?php echo $numTimesToAdd; ?>);
});
</script>

<?php include "../footer.php"; ?>
</body>
</html>