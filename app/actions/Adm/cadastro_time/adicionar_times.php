<?php
session_start(); // Inicia a sessão

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
// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    include '../../../config/conexao.php';

    // Dados do formulário
    $grupoId = $_POST['grupo_id']; // Assume que você obtém o ID do grupo selecionado
    $numTimes = count($_POST['nome_time']); // Obtém o número de times a serem adicionados

    // Consulta para obter a quantidade de equipes por grupo
    $configSql = "SELECT equipes_por_grupo FROM configuracoes LIMIT 1";
    $configResult = $conn->query($configSql);

    if ($configResult->num_rows > 0) {
        $configRow = $configResult->fetch_assoc();
        $maxTimesPerGroup = $configRow['equipes_por_grupo'];

        // Conta quantos times já existem no grupo selecionado
        $countSql = "SELECT COUNT(*) as count FROM times WHERE grupo_id = $grupoId";
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

                // Verifica se o nome do time já existe em toda a tabela
                $checkSql = "SELECT COUNT(*) as count FROM times WHERE nome = '$nomeTime'";
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
                    $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg, token) 
                            VALUES ('$nomeTime', '$logoTime', '$grupoId', 0, 0, 0, 0, 0, 0, 0, '$token')";

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
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Times</title>
    <link rel="stylesheet" href="../../../../public/css/adm/cadastros_times_jogadores_adm/adicionar_times.css">
    <link rel="stylesheet" href="../../../../public/css/adm/header_cl.css">
</head>
<body>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
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

            <div class="theme-toggle">
                <img id="theme-icon" src="../../../../public/img/header/modoescuro.svg" alt="Toggle Theme">
            </div>
        </div>
    </header>
    <script>
        // Função para alternar o modo escuro
        function toggleDarkMode() {
            var element = document.body;
            var icon = document.getElementById('theme-icon');
            element.classList.toggle("dark-mode");

            // Atualizar o ícone conforme o tema
            if (element.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                icon.src = '../../../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../../../public/img/header/modoescuro.svg';
            }
        }

        // Aplicar o tema salvo ao carregar a página
        document.addEventListener("DOMContentLoaded", function() {
            var theme = localStorage.getItem("theme");
            var icon = document.getElementById('theme-icon');
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                icon.src = '../../../../public/img/header/modoclaro.svg';
            } else {
                icon.src = '../../../../public/img/header/modoescuro.svg';
            }
        });

        // Adiciona o evento de clique para alternar o tema
        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
<div class="titulo-barra">
    <h1>Adicionar Times</h1>
</div>

<div class="formulario" id="main-content">

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <label for="num_times">Número de Times para Adicionar:</label>
        <select id="num_times" name="num_times" onchange="updateFormFields(this.value)">
            <option value="1" <?php if ($numTimesToAdd == 1) echo 'selected'; ?>>1</option>
            <option value="2" <?php if ($numTimesToAdd == 2) echo 'selected'; ?>>2</option>
            <option value="3" <?php if ($numTimesToAdd == 3) echo 'selected'; ?>>3</option>
            <option value="4" <?php if ($numTimesToAdd == 4) echo 'selected'; ?>>4</option>
            <option value="5" <?php if ($numTimesToAdd == 5) echo 'selected'; ?>>5</option>
            <option value="6" <?php if ($numTimesToAdd == 6) echo 'selected'; ?>>6</option>
            <option value="7" <?php if ($numTimesToAdd == 7) echo 'selected'; ?>>7</option>
            <option value="8" <?php if ($numTimesToAdd == 8) echo 'selected'; ?>>8</option>
            <option value="9" <?php if ($numTimesToAdd == 9) echo 'selected'; ?>>9</option>
            <option value="10" <?php if ($numTimesToAdd == 10) echo 'selected'; ?>>10</option>
        </select>

        <div id="times-fields">
            <?php echo generateFormFields($numTimesToAdd); ?>
        </div>

        <label for="grupo_id">Grupo:</label>
        <select id="grupo_id" name="grupo_id" required>
            <?php
            // Conexão com o banco de dados para carregar os grupos disponíveis
            include '../../../config/conexao.php';

            $sql = "SELECT id, nome FROM grupos ORDER BY nome";
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

<?php include "../../../pages/footer.php"; ?>
</body>
</html>
