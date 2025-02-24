<?php
// Verificar se o usuário está autenticado
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rodadas das Fases de Grupo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../../public/css/adm/rodadas_adm.css">
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
</head>
<body>
<?php require_once 'header_classificacao.php' ?>
<h1 id="dynamic-text">FASES DE GRUPO</h1>
<script>
    // JavaScript - script.js
    document.addEventListener('DOMContentLoaded', () => {
        // Efeito de digitação para o título
        const textElement = document.getElementById('dynamic-text');
        const text = textElement.textContent;
        textElement.textContent = '';

        let index = 0;
        const typingSpeed = 100; // Aumente ou diminua a velocidade da digitação

        function typeLetter() {
            if (index < text.length) {
                textElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeLetter, typingSpeed);
            }
        }

        typeLetter();

        // Revelação gradual dos elementos dentro da .form-container
        const elements = document.querySelectorAll('.table-container *');
        let delay = 0;

        // Adiciona a classe .hidden a todos os elementos dentro da .form-container
        elements.forEach(element => {
            element.classList.add('hidden');
        });

        // Função para remover a classe .hidden e revelar o elemento
        function revealElement(element, delay) {
            setTimeout(() => {
                element.classList.remove('hidden');
                element.classList.add('reveal');
            }, delay);
        }

        // Revela cada elemento com um atraso reduzido
        elements.forEach((element, index) => {
            revealElement(element, index * 20); // Diminua o valor para acelerar o efeito
        });
    });
</script>
<div class="rodada_container1">
<div id="rodadas-wrapper">
    <div class="nav-arrow left" onclick="previousRodada()"><img src="../../../public/img/esquerda.svg" alt=""></div>
    <div class="table-container">
        <?php exibirRodadas(); ?>
    </div>
    <div class="nav-arrow right" onclick="nextRodada()"><img src="../../../public/img/direita.svg" alt=""></div>
</div>
<!-- Link que aciona o modal -->
<a href="../../actions/funcoes/confrontos_rodadas.php" class="btn-redirect" id="confirm-link">Classificar Confrontos Rodadas</a>

</div>
<?php
function exibirRodadas() {
    include '../../config/conexao.php';

    $sqlRodadas = "SELECT DISTINCT rodada FROM jogos_fase_grupos ORDER BY rodada";
    $resultRodadas = $conn->query($sqlRodadas);

    $rodadas = [];
    if ($resultRodadas->num_rows > 0) {
        while ($rowRodada = $resultRodadas->fetch_assoc()) {
            $rodada = $rowRodada['rodada'];
            $rodadas[] = $rodada;
        }
    }

    foreach ($rodadas as $rodada) {
        echo '<div class="rodada-container">';
        echo '<div class="rodada-header">';
        echo '<h2 class="rodada-header_h1">' . $rodada . 'ª RODADA</h2>';
        echo '</div>';
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
                echo '<form method="POST" action="../../actions/funcoes/atualizar_gols.php" class="admin-only">';
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
                    echo '<tr class="time_teste">';

                    echo '<td class="time-row">';
                    if ($logoA) {
                        echo '<img src="' . $logoA . '" class="logo-time">';
                    }
                    echo '<span class="time-name">' . $timeA_nome . '</span>';
                    echo '</td>';
                    echo '<td> <input type="number" id="input" min="0" step="1" name="golsA_' . $jogoId . '" value="' . $golsA . '"> </td>';
                    echo '<td> X </td>';
                    echo '<td> <input type="number" id="input" min="0" step="1" name="golsB_' . $jogoId . '" value="' . $golsB . '"> </td>';
                    echo '<td class="time-row">';
                    echo '<span class="time-name_b">' . $timeB_nome . '</span>';
                    if ($logoB) {
                        echo '<img src="' . $logoB . '" class="logo-time">';
                    }
                    echo '</td>';
                    echo '<input type="hidden" name="confrontos[]" value="' . $jogoId . '">';
                    echo '<input type="hidden" name="resultadoA_' . $jogoId . '" value="' . $resultadoA . '">';
                    echo '<input type="hidden" name="resultadoB_' . $jogoId . '" value="' . $resultadoB . '">';
                    echo '</tr>';
                }
                echo '<tr class="tr_teste"><td colspan="7" style="text-align: center;"><input type="submit" class="btn-save" value="Salvar resultados"></td></tr>';
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

    $conn->close();
}
?>
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
    // Controle das Rodadas
    var currentRodadaIndex = 0;
    var rodadaContainers = document.getElementsByClassName('rodada-container');

    function showRodada(index) {
        for (var i = 0; i < rodadaContainers.length; i++) {
            rodadaContainers[i].style.display = i === index ? 'block' : 'none';
        }
    }

    function previousRodada() {
        if (currentRodadaIndex > 0) {
            currentRodadaIndex--;
            showRodada(currentRodadaIndex);
        }
    }

    function nextRodada() {
        if (currentRodadaIndex < rodadaContainers.length - 1) {
            currentRodadaIndex++;
            showRodada(currentRodadaIndex);
        }
    }

    showRodada(currentRodadaIndex);

    // Modal functionality
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
</script>
<?php include '../footer.php' ?>
</body>
</html>
