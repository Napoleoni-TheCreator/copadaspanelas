<?php
// Verificar se o usuário está autenticado
session_start();

// Verifica se o usuário está autenticado e se é um administrador
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ./../../cadastro_adm/login.php");
    exit();
}

include("../../cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rodadas das Fases de Grupo</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        #rodadas-wrapper {
            margin-top: 1%;
            margin-bottom: 1%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid black;
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            width: 70%;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .dark-mode #rodadas-wrapper {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        
        tr {
            display: flex;
            align-items: center;
            text-align: center;
        }
        
        .time_teste {
            display: flex;
            justify-content: space-between;
            border: 1px solid black;
            margin-top: 5px;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .time_teste img {
            width: 30px;
            height: 30px;
        }
        .tr_teste {
            display: flex;
            justify-content: center;
        }
        h1 {
            font-size: 40px;
            margin-top: 5%;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .table-container {
            display: flex;
            justify-content: space-between;
            overflow-x: auto;
        }
        .rodada-container {
            width: 60%;
            background-color: #ffffff;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid black; 
            margin-right: 10px;
            margin-left: 5%;
            margin-top: 2%;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .dark-mode .rodada-container {
            background-color: #2c2c2c;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
        }
        .rodada-container:hover {
            background-color: #007bff;
            box-shadow: 0 0 10px hsl(0, 100%, 50%);
            margin-left: 5%;
        }
        .dark-mode .rodada-container:hover {
            background-color: #0056b3;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: center;
            transition: background-color 0.3s, color 0.3s;
        }
        th {
            background-color: #f2f2f2;
        }
        .dark-mode th {
            background-color: #333;
        }
        .rodada-header {
            font-size: 1.2em;
            margin-bottom: 10px;
            text-align: center;
        }
        .logo-time {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
        .time-row {
            display: flex;
            align-items: center;
        }
        .time-name {
            font-size: 20px;
            margin-left: 8px;
        }
        #input {
            width: 20px;
            background-color: #66bb6a;
        }
        input[type=number] {
            -webkit-appearance: none;
            -moz-appearance: textfield !important;
            appearance: none;
        }
        /* Remove as setas em navegadores baseados em WebKit (Chrome, Safari) */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .btn-save {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-save:hover {
            background-color: #45a049;
        }
        .dark-mode .btn-toggle-mode:hover {
            background-color: #333;
        }
        .no-break {
            white-space: nowrap;
            font-size: 20px;
        }
        .admin-only {
            display: <?php echo $isAdmin ? 'block' : 'none'; ?>;
        }
        /* Estilo para o botão */
        .btn-redirect {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
        }

        /* Estilo do modal */
        .modal {
            display: none; 
            position: fixed; /* Fixa o modal na tela */
            z-index: 1; /* Fica acima de outros elementos */
            left: 0;
            top: 0;
            width: 100%; /* Ocupa toda a largura da tela */
            height: 100%; /* Ocupa toda a altura da tela */
            overflow: auto; /* Permite rolagem se necessário */
            background-color: rgba(0, 0, 0, 0.4); /* Fundo semi-transparente */
        }

        /* Estilo do conteúdo do modal */
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* Margem para centralizar verticalmente */
            padding: 20px;
            border: 1px solid #888;
            width: 50%; /* Largura do modal */
            max-width: 600px; /* Largura máxima para evitar que o modal fique muito largo em telas grandes */
            border-radius: 5px; /* Bordas arredondadas */
        }

        /* Estilo do botão de fechar */
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        /* Estilo do botão de fechar quando o mouse está sobre ele */
        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Estilo do botão de confirmação */
        button#confirm-btn {
            background-color: #28a745;
            color: white;
        }
        #btn-toggle-mode{
            width: 50px;
            height: 50px;
            background-size: cover;
            border: none;
            cursor: pointer;
            outline: none;
            background-image: url(../../../../public/img/modoescuro.svg);
        }
        .dark-mode #btn-toggle-mode{
            background-image: url(../../../../public/img/modoclaro.svg);
        }
        /* Estilo do botão de cancelamento */
        button#cancel-btn {
            background-color: #dc3545;
            color: white;
        }
        /* Ajustes responsivos */
@media (max-width: 768px) { /* Para iPad */
    .modal-content {
        width: 90%; /* Largura do modal maior em telas menores */
        max-width: 400px; /* Ajusta a largura máxima do modal */
    }
    
    .btn-redirect {
        padding: 8px 16px;
        font-size: 0.9rem; /* Tamanho do texto menor em telas menores */
    }

    .rodada-container {
        width: 80%;
        /* margin: 1% auto; */
        padding: 10px;
    }
}

@media (max-width: 576px) { /* Para iPhone 12 e telas menores */
    body {
        font-size: 8px; /* Ajusta o tamanho da fonte */
    }
    .no-break{
        font-size: 12px;
    }
    .modal-content {
        width: 95%;
        margin-top: 100%;
        max-width: 200px; /* Ajusta a largura máxima do modal */
    }
    .time-name{
        font-size: 10px;
    }
    .time_teste{
        padding: 0px;
    }
    td{
        padding: 4px;
    }

    .btn-redirect {
        padding: 6px 12px;
        font-size: 0.8rem; /* Tamanho do texto menor */
    }

    .rodada-container {
        width: 90%;
        /* margin: 2% auto; */
        margin-left: 10px;
        padding: 8px;
    }
    h1{
        font-size: 12px;
        margin-top: 20%;
    }
}
    </style>
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
                    <a href="tabela_de_classificacao.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
                    <span>Classificação</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/listar_times.php"><img src="../../../../public/img/header/classificados.png" alt="classificados"></a>
                    <span>editar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_dados_finais.php"><img src="../../../../public/img/header/oitavas.png" alt="finais"></a>
                    <span>editar finais</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/prancheta.svg" alt="estatisticas"></a>
                    <span>Editar jogadores</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/grupo.svg" alt="grupos"></a>
                    <span>Criar grupos</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/adtime.svg" alt="cadastro times"></a>
                    <span>Adicionar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/adadm.svg" alt="cadastro novos adm"></a>
                    <span>Adicionar outro adm</span>
                </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()"></button>
        </div>
    </header>
<h1>RODADAS DAS FASES DE GRUPO</h1>
<div id="rodadas-wrapper">
    <div class="nav-arrow left" onclick="scrollLeft()">&lt;</div>
    <div class="table-container">
        <?php exibirRodadas(); ?>
    </div>
    <div class="nav-arrow right" onclick="scrollRight()">&gt;</div>
</div>

<!-- Exibe o botão "Classificar Rodadas" apenas para administradores -->
<div class="admin-only">
    <button class="btn-save" onclick="classificarRodadas()">Classificar Rodadas</button>
</div>

<?php
function exibirRodadas() {
    include '../../../config/conexao.php';

    $sqlRodadas = "SELECT DISTINCT rodada FROM jogos_fase_grupos ORDER BY rodada";
    $resultRodadas = $conn->query($sqlRodadas);

    if ($resultRodadas->num_rows > 0) {
        while ($rowRodada = $resultRodadas->fetch_assoc()) {
            $rodada = $rowRodada['rodada'];

            echo '<div class="rodada-container">';
            echo '<h2 class="rodada-header">' . $rodada . 'ª RODADA</h2>';
            echo '<table>';

            $sqlGrupos = "SELECT DISTINCT grupo_id, nome AS grupo_nome FROM jogos_fase_grupos 
                          JOIN grupos ON jogos_fase_grupos.grupo_id = grupos.id ORDER BY grupo_id";
            $resultGrupos = $conn->query($sqlGrupos);

            while ($rowGrupo = $resultGrupos->fetch_assoc()) {
                $grupoId = $rowGrupo['grupo_id'];
                $grupoNome = substr($rowGrupo['grupo_nome'], -1);

                $sqlConfrontos = "SELECT jfg.id, tA.nome AS nome_timeA, tB.nome AS nome_timeB, 
                                         tA.logo AS logo_timeA, tB.logo AS logo_timeB, 
                                         jfg.gols_marcados_timeA, jfg.gols_marcados_timeB
                                  FROM jogos_fase_grupos jfg
                                  JOIN times tA ON jfg.timeA_id = tA.id
                                  JOIN times tB ON jfg.timeB_id = tB.id
                                  WHERE jfg.grupo_id = $grupoId AND jfg.rodada = $rodada";

                $resultConfrontos = $conn->query($sqlConfrontos);

                if ($resultConfrontos->num_rows > 0) {
                    echo '<form method="POST" action="../../funcoes/atualizar_gols.php" class="admin-only">';
                    while ($rowConfronto = $resultConfrontos->fetch_assoc()) {
                        $jogoId = $rowConfronto['id'];
                        $timeA_nome = $rowConfronto['nome_timeA'];
                        $timeB_nome = $rowConfronto['nome_timeB'];
                        $logoA = !empty($rowConfronto['logo_timeA']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeA']) : '';
                        $logoB = !empty($rowConfronto['logo_timeB']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeB']) : '';
                        $golsA = $rowConfronto['gols_marcados_timeA'];
                        $golsB = $rowConfronto['gols_marcados_timeB'];

                        if ($golsA > $golsB) {
                            $resultadoA = 'V';
                            $resultadoB = 'D';
                        } elseif ($golsA < $golsB) {
                            $resultadoA = 'D';
                            $resultadoB = 'V';
                        } else {
                            $resultadoA = 'E';
                            $resultadoB = 'E';
                        }
                        echo '<td class="no-break">Grupo | ' . $grupoNome . '</td>';
                        echo '<tr class="time_teste">';

                        echo '<td class="time-row">';
                        if ($logoA) {
                            echo '<img src="' . $logoA . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $timeA_nome . '</span>';
                        echo '</td>';
                        echo '<td> <input type="number" id="input" name="golsA_' . $jogoId . '" value="' . $golsA . '"> </td>';
                        echo '<td> X </td>';
                        echo '<td> <input type="number" id="input" name="golsB_' . $jogoId . '" value="' . $golsB . '"> </td>';
                        echo '<td class="time-row">';
                        if ($logoB) {
                            echo '<img src="' . $logoB . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $timeB_nome . '</span>';
                        echo '</td>';
                        echo '<input type="hidden" name="confrontos[]" value="' . $jogoId . '">';
                        echo '<input type="hidden" name="resultadoA_' . $jogoId . '" value="' . $resultadoA . '">';
                        echo '<input type="hidden" name="resultadoB_' . $jogoId . '" value="' . $resultadoB . '">';
                        echo '</tr>';
                    }
                    echo '<tr class="tr_teste"><td colspan="7" style="text-align: center;"><input type="submit" class="btn-save" value="Salvar Todos"></td></tr>';
                    echo '</form>';
                } else {
                    echo '<tr>';
                    echo '<td colspan="7">Nenhum confronto encontrado para o grupo ' . $grupoNome . ' na ' . $rodada . 'ª rodada.</td>';
                    echo '</tr>';
                }
            }

            echo '</table>';
            echo '</div>';
        }
    } else {
        echo '<p>Nenhuma rodada encontrada.</p>';
    }

    $conn->close();
}
?>
<!-- Link que aciona o modal -->
<a href="/copadaspanelas/app/actions/funcoes/confrontos_rodadas.php" class="btn-redirect" id="confirm-link">Classificar Confrontos Rodadas</a>

<!-- Modal de Confirmação -->
<div id="confirm-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="close-btn">&times;</span>
        <p>Tem certeza que deseja classificar os confrontos das rodadas?</p>
        <button id="confirm-btn">Sim</button>
        <button id="cancel-btn">Não</button>
    </div>
</div>
<script>
function scrollLeft() {
    const container = document.querySelector('.table-container');
    if (container) {
        if (container.scrollLeft > 0) {
            container.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        }
    }
}

function scrollRight() {
    const container = document.querySelector('.table-container');
    if (container) {
        const maxScrollLeft = container.scrollWidth - container.clientWidth;
        if (container.scrollLeft < maxScrollLeft) {
            container.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        }
    }
}

        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // script.js
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('confirm-modal');
    var confirmLink = document.getElementById('confirm-link');
    var closeBtn = document.getElementById('close-btn');
    var confirmBtn = document.getElementById('confirm-btn');
    var cancelBtn = document.getElementById('cancel-btn');

    // Mostrar o modal quando o link for clicado
    confirmLink.addEventListener('click', function(event) {
        event.preventDefault(); // Previne o comportamento padrão do link
        modal.style.display = 'block';
    });

    // Fechar o modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    confirmBtn.addEventListener('click', function() {
        window.location.href = confirmLink.href; // Redirecionar para o URL do link
    });

    // Fechar o modal se o usuário clicar fora dele
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

    function classificarRodadas() {
        $.post('/copadaspanelas/app/actions/funcoes/confrontos_rodadas.php', function(response) {
            alert(response);
            location.reload();
        }).fail(function() {
            alert('Ocorreu um erro ao classificar as rodadas.');
        });
    }
</script>

</body>
</html>
