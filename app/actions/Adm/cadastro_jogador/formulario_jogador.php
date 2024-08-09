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
        header('Location: crud_jogador.php');
        exit;
        // echo "Jogador adicionado com sucesso!";
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
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Jogadores</title>
    <link rel="stylesheet" href="../../../../public/css/adm/cadastros_times_jogadores_adm/formulario_jogador.css">
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
<div class="fundo-tela">
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




