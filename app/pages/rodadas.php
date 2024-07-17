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
            flex-direction: column;
            overflow-x: auto;
        }
        .grupo-container {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        .grupo-header {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .logo-time {
            width: 30px;
            height: 30px;
            vertical-align: middle;
        }
        .time-row {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .time-name {
            margin-left: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div id="rodadas-wrapper">
        <h1>Rodadas das Fases de Grupo</h1>
        <div class="table-container">
            <?php exibirRodadas(); ?>
        </div>
    </div>
    <?php
    include "../pages/rodadas_teste.php";
    function exibirRodadas() {
        include '../config/conexao.php';
        $sqlRodadas = "SELECT r.rodada, g.nome AS grupo_nome, 
                              tA.nome AS nome_timeA, tB.nome AS nome_timeB, 
                              tA.logo AS logo_timeA, tB.logo AS logo_timeB
                       FROM jogos_fase_grupos r
                       JOIN grupos g ON r.grupo_id = g.id
                       JOIN times tA ON r.timeA_id = tA.id
                       JOIN times tB ON r.timeB_id = tB.id
                       ORDER BY r.rodada, g.nome";

        $resultRodadas = $conn->query($sqlRodadas);

        if ($resultRodadas->num_rows > 0) {
            $rodadas = [];
            while ($row = $resultRodadas->fetch_assoc()) {
                $rodadas[$row['rodada']][$row['grupo_nome']][] = [
                    'timeA' => ['nome' => $row['nome_timeA'], 'logo' => $row['logo_timeA']],
                    'timeB' => ['nome' => $row['nome_timeB'], 'logo' => $row['logo_timeB']]
                ];
            }

            foreach ($rodadas as $rodada => $grupos) {
                echo '<div class="grupo-container">';
                echo '<h2>Rodada ' . $rodada . '</h2>';
                echo '<table>';
                echo '<tr>';
                echo '<th>Time A</th>';
                echo '<th>VS</th>';
                echo '<th>Time B</th>';
                echo '</tr>';

                foreach ($grupos as $grupoNome => $partidas) {
                    echo '<tr><td colspan="3" class="grupo-header">' . $grupoNome . '</td></tr>';
                    foreach ($partidas as $partida) {
                        $logoA = !empty($partida['timeA']['logo']) ? 'data:image/jpeg;base64,' . base64_encode($partida['timeA']['logo']) : '';
                        $logoB = !empty($partida['timeB']['logo']) ? 'data:image/jpeg;base64,' . base64_encode($partida['timeB']['logo']) : '';

                        echo '<tr>';
                        echo '<td class="time-row">';
                        if ($logoA) {
                            echo '<img src="' . $logoA . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $partida['timeA']['nome'] . '</span>';
                        echo '</td>';
                        echo '<td>VS</td>';
                        echo '<td class="time-row">';
                        if ($logoB) {
                            echo '<img src="' . $logoB . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $partida['timeB']['nome'] . '</span>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }

                echo '</table>';
                echo '</div>';
            }
        } else {
            echo '<p>Nenhum jogo encontrado.</p>';
        }

        $conn->close();
    }
    ?>
</body>
</html>
