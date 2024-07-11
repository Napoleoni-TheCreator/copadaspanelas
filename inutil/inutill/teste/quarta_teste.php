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
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        text-align: center;
    }
    .diagram {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .box, .box1 {
        width: 80px;
        height: 40px;
        border: 1px solid black;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        margin: 5px;
    }
    .box1 {
        margin-top: 8px;
    }
    .line {
        width: 100px;
        height: 1px;
        background-color: black;
        margin: 0;
    }
    .vertical-line {
        width: 2px;
        height: 40px;
        background-color: black;
        margin: 0;
    }
    .diagram-container {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }
    .diagram-left, .diagram-right {
        width: 45%;
    }
</style>

</head>
<body>
    <div id="confrontos-wrapper">
        <h1>Quartas de Finais</h1>
        <div class="diagram-container">
            <div class="diagram-left">
                <?php
                include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

                function exibirDiagrama($faseFinal, $confrontos) {
                    // Divida os confrontos para dois grupos
                    $metade = ceil(count($confrontos) / 2);
                    $confrontosA = array_slice($confrontos, 0, $metade);
                    $confrontosB = array_slice($confrontos, $metade);

                    echo '<div class="diagram">';
                    foreach ($confrontosA as $index => $confronto) {
                        $timeA = $confronto[0];
                        $timeB = $confronto[1];
                        
                        echo '<div class="box">' . htmlspecialchars($timeA['nome']) . '</div>';
                        echo '<div class="vertical-line"></div>';
                        echo '<div class="vertical-line"></div>';
                        echo '<div class="vertical-line"></div>';
                        echo '<div class="box">' . htmlspecialchars($timeB['nome']) . '</div>';
                    }
                    echo '</div>';
                }

                function obterConfrontos($conn, $faseFinal) {
                    $totalTimes = ($faseFinal == 'oitavas') ? 16 : 8;
                    $confrontos = [];

                    if ($faseFinal == 'oitavas') {
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

                            $numTimes = count($classificados);
                            for ($i = 0; $i < $numTimes / 2; $i++) {
                                $timeA = $classificados[$i];
                                $timeB = $classificados[$numTimes - $i - 1];
                                $confrontos[] = [$timeA, $timeB];
                            }
                        }
                    }

                    return $confrontos;
                }

                $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
                $resultConfig = $conn->query($sqlConfig);
                $faseFinal = 'oitavas'; // Default
                if ($resultConfig->num_rows > 0) {
                    $rowConfig = $resultConfig->fetch_assoc();
                    $faseFinal = $rowConfig['fase_final'];
                }

                $confrontos = obterConfrontos($conn, $faseFinal);

                if (!empty($confrontos)) {
                    exibirDiagrama($faseFinal, $confrontos);
                } else {
                    echo '<div class="matchup">Nenhum confronto disponível.</div>';
                }

                $conn->close();
                ?>
            </div>
            <div class="diagram-right">
                <?php
                // Re-exibir a função exibirDiagrama para o lado direito, se necessário
                // Pode ser um duplicado ou uma lógica diferente para outra coluna
                if (!empty($confrontos)) {
                    exibirDiagrama($faseFinal, $confrontos);
                } else {
                    echo '<div class="matchup">Nenhum confronto disponível.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>