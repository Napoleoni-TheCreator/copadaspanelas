<!DOCTYPE html>
<html>
<head>
    <title>Rodadas das Fases de Grupo</title>
    <style>
        * {
            /* margin: 0; */
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
            transition: background-color 0.3s, color 0.3s;
            font-family: "Times New Roman", serif;
        }

        #rodadas-wrapper {
            display: flex;
            align-items: center;
            margin-top: 1%;
            margin-bottom: 5%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid black;
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            width: 60%;
            transition: background-color 0.3s, box-shadow 0.3s;
            height: auto;
        }
        .dark-mode #rodadas-wrapper {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            color: white; 
        }
        .time_teste {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid black;
            margin-top: 5px;
            border-radius: 5px;
            padding: 20px;
        }
        .time_teste img {
            width: 80px;
            height: auto;
        }
        .tr_teste {
            display: flex;
            justify-content: center;
        }
        h1 {
            font-size: 30px;
            margin-top: 5%;
            margin-bottom: 10px;
            text-align: center;
        }
        .table-container {
            display: flex;
            justify-content: space-between;
            overflow-x: auto;
            width: 100%;
        }
        .rodada-container {
            width: 100%;
            background-color: #ffffff;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            margin-top: 2%;
        }
        .dark-mode .rodada-container {
            background-color: #2c2c2c;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
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
            margin-right: 8px;
        }
        .no-break {
            white-space: nowrap;
            font-size: 20px;
            text-align: center; /* Alinha o texto horizontalmente */
            flex: 1; /* Permite que o elemento ocupe a largura disponível */
        }
        .btn-save, .btn-toggle-mode {
            display: none; /* Remove os botões */
        }
        .arrow {
            cursor: pointer;
            padding: 10px;
            font-size: 24px;
            user-select: none;
        }
        .arrow.left {
            margin-right: 10px;
        }
        .arrow.right {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<?php include 'header_classificacao.php'; ?>
<h1>RODADAS DAS FASES DE GRUPO</h1>
<div id="rodadas-wrapper">
    <div class="arrow left" onclick="previousRodada()">&#9664;</div>
    <div class="table-container">
        <?php exibirRodadas(); ?>
    </div>
    <div class="arrow right" onclick="nextRodada()">&#9654;</div>
</div>
<?php
function exibirRodadas() {
    include '../config/conexao.php';

    $sqlRodadas = "SELECT DISTINCT rodada FROM jogos_fase_grupos ORDER BY rodada";
    $resultRodadas = $conn->query($sqlRodadas);

    if ($resultRodadas->num_rows > 0) {
        while ($rowRodada = $resultRodadas->fetch_assoc()) {
            $rodada = $rowRodada['rodada'];

            echo '<div class="rodada-container">';
            echo '<h2 class="rodada-header">' . $rodada . 'ª RODADA</h2>';

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
                    while ($rowConfronto = $resultConfrontos->fetch_assoc()) {
                        $timeA_nome = $rowConfronto['nome_timeA'];
                        $timeB_nome = $rowConfronto['nome_timeB'];
                        $logoA = !empty($rowConfronto['logo_timeA']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeA']) : '';
                        $logoB = !empty($rowConfronto['logo_timeB']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeB']) : '';
                        $golsA = $rowConfronto['gols_marcados_timeA'];
                        $golsB = $rowConfronto['gols_marcados_timeB'];

                        // Determina o resultado do jogo
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
                        echo '<div class="time_teste">';
                        echo '<div class="time-row">';
                        if ($logoA) {
                            echo '<img src="' . $logoA . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $timeA_nome . '</span>';
                        echo '</div>';
                        echo '<div class="no-break">' . $golsA . ' X ' . $golsB . '</div>';
                        echo '<div class="time-row">';
                        echo '<span class="time-name">' . $timeB_nome . '</span>';
                        if ($logoB) {
                            echo '<img src="' . $logoB . '" class="logo-time">';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Nenhum confronto encontrado para o grupo ' . $grupoNome . ' na ' . $rodada . 'ª rodada.</p>';
                }
            }

            echo '</div>';
        }
    } else {
        echo '<p>Nenhuma rodada encontrada.</p>';
    }

    $conn->close();
}
?>
<script>
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

    // Toggle dark mode
    var isDarkMode = false;
    function toggleMode() {
        isDarkMode = !isDarkMode;
        document.body.classList.toggle('dark-mode', isDarkMode);
    }
</script>
</body>
</html>
