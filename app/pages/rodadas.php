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
        }
        .table-container {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            overflow-x: auto;
        }
        .grupo-container {
            flex: 1;
            min-width: 300px;
            margin-right: 20px;
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
        <?php exibirRodadas(); ?>
    </div>

    <?php
    function exibirRodadas() {
        include '../config/conexao.php';

        $sqlGrupos = "SELECT id, nome FROM grupos ORDER BY nome";
        $resultGrupos = $conn->query($sqlGrupos);

        $grupos = [];
        if ($resultGrupos->num_rows > 0) {
            while ($rowGrupos = $resultGrupos->fetch_assoc()) {
                $grupoId = $rowGrupos['id'];
                $grupoNome = $rowGrupos['nome'];

                // Buscar times no grupo
                $sqlTimes = "SELECT id, nome, logo FROM times WHERE grupo_id = $grupoId";
                $resultTimes = $conn->query($sqlTimes);

                $times = [];
                if ($resultTimes->num_rows > 0) {
                    while ($rowTimes = $resultTimes->fetch_assoc()) {
                        $times[] = $rowTimes;
                    }
                }

                $grupos[$grupoNome] = $times;
            }
        }

        $conn->close();

        $rodadas = [];
        foreach ($grupos as $grupoNome => $times) {
            $quantidadeTimes = count($times);
            if ($quantidadeTimes % 2 != 0) {
                $times[] = ["nome" => "BYE", "logo" => null]; // Adiciona um BYE se o número de times for ímpar
                $quantidadeTimes++;
            }

            $totalRodadas = $quantidadeTimes - 1;
            $jogosPorRodada = $quantidadeTimes / 2;

            // Gerar todas as rodadas usando o algoritmo round-robin
            $rodadasGrupo = [];
            for ($rodada = 0; $rodada < $totalRodadas; $rodada++) {
                $rodadasGrupo[$rodada] = [];
                for ($jogo = 0; $jogo < $jogosPorRodada; $jogo++) {
                    $timeA = $times[$jogo];
                    $timeB = $times[$quantidadeTimes - 1 - $jogo];
                    if ($timeA["nome"] != "BYE" && $timeB["nome"] != "BYE") {
                        $rodadasGrupo[$rodada][] = ['timeA' => $timeA, 'timeB' => $timeB];
                    }
                }
                // Rotaciona os times, exceto o primeiro
                $times = array_merge([$times[0]], array_slice($times, -1), array_slice($times, 1, -1));
            }

            foreach ($rodadasGrupo as $index => $partidas) {
                $rodadas[$index + 1][$grupoNome] = $partidas;
            }
        }

        echo '<div class="table-container">';

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

        echo '</div>';
    }
    ?>
</body>
</html>
