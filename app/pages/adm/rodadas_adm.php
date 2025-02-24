<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
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
    <style>
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .confirm-modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 25px;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
        }

        .date-info {
            color: #666;
            margin: 15px 0;
            font-size: 0.9em;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .confirm-modal-buttons {
            margin-top: 20px;
        }

        .confirm-modal-btn {
            padding: 10px 25px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .confirm-modal-yes {
            background-color: #28a745;
            color: white;
        }

        .confirm-modal-no {
            background-color: #dc3545;
            color: white;
        }

        .datetime-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .date-input, .time-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .date-input {
            width: 60%;
        }

        .time-input {
            width: 40%;
        }

        .error {
            border-color: #dc3545 !important;
            background-color: #fff3f3;
        }

        /* Estilo adicional para o novo modal */
        #classificarConfirmModal .confirm-modal-content {
            max-width: 400px;
        }

        #classificarMessage {
            margin: 15px 0;
            font-size: 1.1em;
            color: #333;
        }
    </style>
</head>
<body>
<?php require_once 'header_classificacao.php' ?>
<h1 id="dynamic-text">FASES DE GRUPO</h1>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const textElement = document.getElementById('dynamic-text');
        const text = textElement.textContent;
        textElement.textContent = '';

        let index = 0;
        const typingSpeed = 100;

        function typeLetter() {
            if (index < text.length) {
                textElement.textContent += text.charAt(index);
                index++;
                setTimeout(typeLetter, typingSpeed);
            }
        }

        typeLetter();

        const elements = document.querySelectorAll('.table-container *');
        elements.forEach(element => element.classList.add('hidden'));

        function revealElement(element, delay) {
            setTimeout(() => {
                element.classList.remove('hidden');
                element.classList.add('reveal');
            }, delay);
        }

        elements.forEach((element, index) => revealElement(element, index * 20));
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
    <a href="../../actions/funcoes/confrontos_rodadas.php" class="btn-redirect" id="classificar-link">Classificar Confrontos Rodadas</a>
</div>

<!-- Modal de Confirmação para Salvar -->
<div id="saveConfirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <p id="confirmMessage">Tem certeza que deseja salvar os resultados?</p>
        <div class="date-info" id="dateInfo"></div>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn confirm-modal-yes" id="confirmSave">Sim</button>
            <button class="confirm-modal-btn confirm-modal-no" id="cancelSave">Cancelar</button>
        </div>
    </div>
</div>

<!-- Novo Modal de Confirmação para Classificar -->
<div id="classificarConfirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
        <p id="classificarMessage">Tem certeza que deseja classificar os confrontos desta rodada?</p>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn confirm-modal-yes" id="confirmClassificar">Sim</button>
            <button class="confirm-modal-btn confirm-modal-no" id="cancelClassificar">Cancelar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let formToSubmit = null;
        const saveModal = document.getElementById('saveConfirmModal');
        const confirmMessage = document.getElementById('confirmMessage');
        const dateInfo = document.getElementById('dateInfo');

        // Configuração do modal de classificação
        const classificarModal = document.getElementById('classificarConfirmModal');
        const classificarLink = document.getElementById('classificar-link');
        let redirectUrl = '';

        // Handler para o link de classificação
        classificarLink.addEventListener('click', function(e) {
            e.preventDefault();
            redirectUrl = this.href;
            classificarModal.style.display = 'block';
        });

        // Confirmar classificação
        document.getElementById('confirmClassificar').addEventListener('click', () => {
            window.location.href = redirectUrl;
        });

        // Cancelar classificação
        document.getElementById('cancelClassificar').addEventListener('click', () => {
            classificarModal.style.display = 'none';
            redirectUrl = '';
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', (e) => {
            if(e.target === classificarModal) {
                classificarModal.style.display = 'none';
                redirectUrl = '';
            }
        });

        // Código original do modal de salvar
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                formToSubmit = this;
                
                const dateInputs = this.querySelectorAll('input[type="date"]');
                const timeInputs = this.querySelectorAll('input[type="time"]');
                let allValid = true;
                let datesList = [];

                dateInputs.forEach((dateInput, index) => {
                    const timeInput = timeInputs[index];
                    if (!dateInput.value || !timeInput.value) {
                        allValid = false;
                        dateInput.classList.add('error');
                        timeInput.classList.add('error');
                    } else {
                        dateInput.classList.remove('error');
                        timeInput.classList.remove('error');
                        const dateTime = new Date(`${dateInput.value}T${timeInput.value}`);
                        datesList.push(dateTime.toLocaleString('pt-BR', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }));
                    }
                });

                if (!allValid) {
                    alert('Por favor, preencha todas as datas e horários dos jogos');
                    return;
                }

                confirmMessage.textContent = 'Tem certeza que deseja salvar os resultados e datas?';
                dateInfo.innerHTML = datesList.length > 0 
                    ? `<strong>Datas e horários dos jogos:</strong><br>${datesList.join('<br>')}`
                    : 'Nenhum dado temporal registrado';
                
                saveModal.style.display = 'block';
            });
        });

        document.getElementById('confirmSave').addEventListener('click', () => {
            if(formToSubmit) formToSubmit.submit();
            saveModal.style.display = 'none';
        });

        document.getElementById('cancelSave').addEventListener('click', () => {
            saveModal.style.display = 'none';
            formToSubmit = null;
        });

        window.addEventListener('click', (e) => {
            if(e.target === saveModal) {
                saveModal.style.display = 'none';
                formToSubmit = null;
            }
        });

        let currentRodadaIndex = 0;
        const rodadaContainers = document.getElementsByClassName('rodada-container');

        window.showRodada = (index) => {
            Array.from(rodadaContainers).forEach((container, i) => {
                container.style.display = i === index ? 'block' : 'none';
            });
        };

        window.previousRodada = () => {
            if(currentRodadaIndex > 0) {
                currentRodadaIndex--;
                showRodada(currentRodadaIndex);
            }
        };

        window.nextRodada = () => {
            if(currentRodadaIndex < rodadaContainers.length - 1) {
                currentRodadaIndex++;
                showRodada(currentRodadaIndex);
            }
        };

        showRodada(currentRodadaIndex);
    });
</script>

<?php
function exibirRodadas() {
    include '../../config/conexao.php';

    $sqlRodadas = "SELECT DISTINCT rodada FROM jogos_fase_grupos ORDER BY rodada";
    $resultRodadas = $conn->query($sqlRodadas);

    $rodadas = [];
    if ($resultRodadas->num_rows > 0) {
        while ($rowRodada = $resultRodadas->fetch_assoc()) {
            $rodadas[] = $rowRodada['rodada'];
        }
    }

    foreach ($rodadas as $rodada) {
        echo '<div class="rodada-container">';
        echo '<div class="rodada-header"><h2 class="rodada-header_h1">' . $rodada . 'ª RODADA</h2></div>';
        echo '<table>';

        $sqlGrupos = "SELECT DISTINCT grupo_id, nome AS grupo_nome FROM jogos_fase_grupos 
                    JOIN grupos ON jogos_fase_grupos.grupo_id = grupos.id ORDER BY grupo_id";
        $resultGrupos = $conn->query($sqlGrupos);

        while ($rowGrupo = $resultGrupos->fetch_assoc()) {
            $grupoId = $rowGrupo['grupo_id'];
            $grupoNome = substr($rowGrupo['grupo_nome'], -1);

            $sqlConfrontos = "SELECT jfg.id, tA.nome AS nome_timeA, tB.nome AS nome_timeB, 
                                    jfg.data_jogo, 
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
                    
                    $dataValue = '';
                    $horaValue = '';
                    if ($rowConfronto['data_jogo'] && $rowConfronto['data_jogo'] != '0000-00-00 00:00:00') {
                        $dataHora = new DateTime($rowConfronto['data_jogo']);
                        $dataValue = $dataHora->format('Y-m-d');
                        $horaValue = $dataHora->format('H:i');
                    }

                    echo '<tr class="time_teste">';
                    echo '<td class="time-row">';
                    if ($logoA) echo '<img src="' . $logoA . '" class="logo-time">';
                    echo '<span class="time-name">' . $timeA_nome . '</span>';
                    echo '</td>';
                    echo '<td><input type="number" min="0" step="1" name="golsA_' . $jogoId . '" value="' . $golsA . '"></td>';
                    echo '<td> X </td>';
                    echo '<td><input type="number" min="0" step="1" name="golsB_' . $jogoId . '" value="' . $golsB . '"></td>';
                    echo '<td class="time-row">';
                    echo '<span class="time-name_b">' . $timeB_nome . '</span>';
                    if ($logoB) echo '<img src="' . $logoB . '" class="logo-time">';
                    echo '</td>';
                    echo '<input type="hidden" name="confrontos[]" value="' . $jogoId . '">';
                    echo '</tr>';
                    
                    echo '<tr class="date-row">';
                    echo '<td colspan="5">';
                    echo '<div class="datetime-container">';
                    echo '<input type="date" name="data_partida_'.$jogoId.'" 
                           value="'.$dataValue.'" 
                           class="date-input"
                           required>';
                    echo '<input type="time" name="hora_partida_'.$jogoId.'" 
                           value="'.$horaValue.'" 
                           class="time-input"
                           required>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '<tr class="tr_teste"><td colspan="7">';
                echo '<input type="submit" class="btn-save" value="Salvar resultados"></td></tr>';
                echo '</form>';
            } else {
                echo '<tr><td colspan="7">Nenhum confronto encontrado para o grupo ' . $grupoNome . ' na ' . $rodada . 'ª rodada.</td></tr>';
            }
        }
        echo '</table></div>';
    }
    $conn->close();
}
?>

<?php include '../footer.php' ?>
</body>
</html>