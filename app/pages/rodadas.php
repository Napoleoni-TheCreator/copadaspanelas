<!DOCTYPE html>
<html>
<head>
    <title>Rodadas das Fases de Grupo</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
        }
        #rodadas-wrapper {
            margin-top: 10%;
            margin-bottom: 5%;
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            overflow-x: auto;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
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
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
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
            margin-left: 5px;
            margin-right: 5px;
        }
        #input {
            width: 40px;
        }
        input[type=number] {
            -webkit-appearance: none;
            -moz-appearance: textfield !important;
            appearance: none;
        }
        .btn-save {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-save:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body> 
<?php include 'header_classificacao.php'; ?>
    <div id="rodadas-wrapper">
        <h1>Rodadas das Fases de Grupo</h1>
        <div class="table-container">
            <?php exibirRodadas(); ?>
        </div>
    </div>
    <?php
    include "../actions/funcoes/confrontos_rodadas.php";
    function exibirRodadas() {
        include '../config/conexao.php';
    
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
                    $grupoNome = substr($rowGrupo['grupo_nome'], -1); // Extrai apenas a última letra do nome do grupo
    
                    $sqlConfrontos = "SELECT jfg.id, tA.nome AS nome_timeA, tB.nome AS nome_timeB, 
                                             tA.logo AS logo_timeA, tB.logo AS logo_timeB, 
                                             jfg.gols_marcados_timeA, jfg.gols_marcados_timeB
                                      FROM jogos_fase_grupos jfg
                                      JOIN times tA ON jfg.timeA_id = tA.id
                                      JOIN times tB ON jfg.timeB_id = tB.id
                                      WHERE jfg.grupo_id = $grupoId AND jfg.rodada = $rodada";
    
                    $resultConfrontos = $conn->query($sqlConfrontos);
    
                    if ($resultConfrontos->num_rows > 0) {
                        echo '<form method="POST" action="../actions/funcoes/atualizar_gols.php">';
                        while ($rowConfronto = $resultConfrontos->fetch_assoc()) {
                            $jogoId = $rowConfronto['id'];
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
    
                            echo '<tr>';
                            echo '<td>' . $grupoNome . '</td>';
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
                        echo '<tr><td colspan="7" style="text-align: center;"><input type="submit" class="btn-save" value="Salvar Todos"></td></tr>';
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
</body>
</html>
