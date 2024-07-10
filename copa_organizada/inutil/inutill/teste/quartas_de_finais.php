<!DOCTYPE html>
<html>
<head>
    <title>Quartas de Finais</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        #confrontos-wrapper {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
            overflow-x: auto;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .confronto {
            margin-bottom: 20px;
        }
        .confronto-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .matchup {
            font-size: 18px;
            text-align: center;
            padding: 10px;
        }
        .matchup img {
            width: 30px;
            height: 30px;
            vertical-align: middle;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div id="confrontos-wrapper">
        <h1>CONFRONTOS - QUARTAS DE FINAIS</h1>
        <?php exibirConfrontos(); ?>
    </div>

    <?php
    function exibirConfrontos() {
        include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

        // Obtém a configuração atual da fase final
        $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
        $resultConfig = $conn->query($sqlConfig);
        $faseFinal = 'oitavas'; // Default
        if ($resultConfig->num_rows > 0) {
            $rowConfig = $resultConfig->fetch_assoc();
            $faseFinal = $rowConfig['fase_final'];
        }

        // Define a quantidade de times a serem exibidos com base na fase final
        $totalTimes = ($faseFinal == 'oitavas') ? 16 : 8;
        $confrontos = [];

        if ($faseFinal == 'oitavas') {
            // Obtém os times classificados para as oitavas de final
            $sqlTimes = "SELECT t.id, t.nome, t.logo, g.id AS grupo_id
                         FROM times t
                         JOIN grupos g ON t.grupo_id = g.id
                         ORDER BY t.pts DESC, t.gm DESC, t.gc ASC, t.sg DESC
                         LIMIT $totalTimes";
            $resultTimes = $conn->query($sqlTimes);

            if ($resultTimes->num_rows > 0) {
                $classificados = [];
                while ($rowTimes = $resultTimes->fetch_assoc()) {
                    $classificados[$rowTimes['grupo_id']][] = $rowTimes;
                }

                // Organiza os times por grupo
                foreach ($classificados as $grupoId => $times) {
                    $numTimes = count($times);
                    for ($i = 0; $i < $numTimes / 2; $i++) {
                        $timeA = $times[$i];
                        $timeB = $times[$numTimes - $i - 1];
                        $confrontos[] = [$timeA, $timeB];
                    }
                }
            }
        } else {
            // Obtém os times classificados para as quartas de final
            $sqlTimes = "SELECT t.id, t.nome, t.logo
                         FROM times t
                         ORDER BY t.pts DESC, t.gm DESC, t.gc ASC, t.sg DESC
                         LIMIT $totalTimes";
            $resultTimes = $conn->query($sqlTimes);

            if ($resultTimes->num_rows > 0) {
                $classificados = [];
                while ($rowTimes = $resultTimes->fetch_assoc()) {
                    $classificados[] = $rowTimes;
                }

                // Organiza os confrontos
                $numTimes = count($classificados);
                for ($i = 0; $i < $numTimes / 2; $i++) {
                    $timeA = $classificados[$i];
                    $timeB = $classificados[$numTimes - $i - 1];
                    $confrontos[] = [$timeA, $timeB];
                }
            }
        }

        if (!empty($confrontos)) {
            echo '<div class="confronto">';
            echo '<div class="confronto-header">Confrontos</div>';
            foreach ($confrontos as $confronto) {
                $timeA = $confronto[0];
                $timeB = $confronto[1];
                
                echo '<div class="matchup">';
                if (!empty($timeA['logo'])) {
                    $logoA = 'data:image/jpeg;base64,' . base64_encode($timeA['logo']);
                    echo '<img src="' . $logoA . '" alt="' . htmlspecialchars($timeA['nome']) . '">';
                }
                echo htmlspecialchars($timeA['nome']);
                echo ' X ';
                if (!empty($timeB['logo'])) {
                    $logoB = 'data:image/jpeg;base64,' . base64_encode($timeB['logo']);
                    echo '<img src="' . $logoB . '" alt="' . htmlspecialchars($timeB['nome']) . '">';
                }
                echo htmlspecialchars($timeB['nome']);
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="confronto">';
            echo '<div class="confronto-header">Confrontos</div>';
            echo '<div class="matchup">Nenhum confronto disponível.</div>';
            echo '</div>';
        }

        $conn->close();
    }
    ?>
</body>
</html>
