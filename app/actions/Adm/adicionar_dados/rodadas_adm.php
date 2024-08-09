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
    <link rel="stylesheet" href="../../../../public/css/adm/rodadas_adm.css">
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
<h1>RODADAS DAS FASES DE GRUPO</h1>
<div id="rodadas-wrapper">
    <div class="nav-arrow left" onclick="scrollLeft()">&lt;</div>
    <div class="table-container">
        <?php exibirRodadas(); ?>
    </div>
    <div class="nav-arrow right" onclick="scrollRight()">&gt;</div>
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
