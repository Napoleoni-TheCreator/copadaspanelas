<!DOCTYPE html>
<html>
<head>
    <title>Tabela de Classificação</title>
    <style>
        /* Estilos atualizados para a tabela de classificação */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
        }
        #tabela-wrapper {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 10%;
            margin-bottom: 10%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .grupo-container {
            width: 100%;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        th, td {
            border: none;
            padding: 3px 0;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
        }
        .grupo-header {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .time-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: left;
            vertical-align: middle;
            max-width: 250px;
        }
        .logo-time {
            max-width: 50px;
            max-height: 50px;
            vertical-align: middle;
            margin-right: 5px;
            border-radius: 100%;
        }
        .small-col {
            width: 70px;
        }
        .larger-col {
            width: 50px;
        }
        .resultado-vitoria {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: green;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-derrota {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-empate {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: gray;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-indefinido {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: lightgray;
            border-radius: 50%;
            margin-right: 2px;
        }
        #legenda-simbolos {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
            text-align: left;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .simbolo {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
        }
        .descricao {
            display: inline-block;
            margin-left: 5px;
        }
        body.dark-mode #legenda-simbolos {
            background-color: #2c2c2c;
            border: 1px solid #444;
        }
        body.dark-mode th {
            background-color: #333333;
            padding: 10px;
        }
        body.dark-mode td {
            background-color:#333333;
            border-radius: 5px;
            padding: 10px;
        }
        body.dark-mode #tabela-wrapper {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .dark-mode-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
            border-radius: 5px;
        }
        .dark-mode-button:hover {
            background-color: #0056b3;
        }
        .dark-mode .small-col:hover{
            background-color: white;
            color: red;
        }
    </style>
</head>
<body>
<?php include 'header_classificacao.php'; ?>
    <div id="tabela-wrapper">
        <h1>FASE DE GRUPOS</h1>
        <h4>Tabela de Classificação</h4>
        <?php mostrarGrupos(); ?>

        <div id="legenda-simbolos">
            <div>
                <div class="simbolo" style="background-color: green;"></div>
                <span class="descricao">Vitória</span>
            </div>
            <div>
                <div class="simbolo" style="background-color: red;"></div>
                <span class="descricao">Derrota</span>
            </div>
            <div>
                <div class="simbolo" style="background-color: gray;"></div>
                <span class="descricao">Empate</span>
            </div>
            <div>
                <div class="simbolo" style="background-color: lightgray;"></div>
                <span class="descricao">Não houve jogo</span>
            </div>
        </div>
    </div>

    <?php
    function mostrarGrupos() {
        include '../config/conexao.php';

        $sqlGrupos = "SELECT id, nome FROM grupos ORDER BY nome";
        $resultGrupos = $conn->query($sqlGrupos);

        if ($resultGrupos->num_rows > 0) {
            while ($rowGrupos = $resultGrupos->fetch_assoc()) {
                $grupoId = $rowGrupos['id'];
                $grupoNome = $rowGrupos['nome'];

                echo '<div class="grupo-container">';
                echo '<div class="grupo-header">' . $grupoNome . '</div>';
                echo '<table>';
                echo '<tr>';
                echo '<th>Clube</th>'; // Coluna de Nome do Clube
                echo '<th class="small-col">P</th>'; // Coluna de Pontos
                echo '<th class="small-col">J</th>'; // Coluna de Partidas
                echo '<th class="small-col">V</th>'; // Coluna de Vitórias
                echo '<th class="small-col">E</th>'; // Coluna de Empates
                echo '<th class="small-col">D</th>'; // Coluna de Derrotas
                echo '<th class="small-col">GP</th>'; // Coluna de Gols Pró
                echo '<th class="small-col">GC</th>'; // Coluna de Gols Contra
                echo '<th class="small-col">SG</th>'; // Coluna de Saldo de Gols
                echo '<th class="larger-col">%</th>'; // Coluna de Porcentagem de Aproveitamento
                echo '<th class="larger-col">ÚLT. JOGOS</th>'; // Coluna de Últimos Jogos
                echo '</tr>';

                $sqlTimes = "SELECT t.id, t.nome, t.logo, 
                                    COALESCE(SUM(j.gols_marcados_timeA), 0) + COALESCE(SUM(j.gols_marcados_timeB), 0) AS gm,
                                    COALESCE(SUM(j.gols_marcados_timeB), 0) + COALESCE(SUM(j.gols_marcados_timeA), 0) AS gc,
                                    COALESCE(SUM(j.gols_marcados_timeA > j.gols_marcados_timeB), 0) AS vitorias,
                                    COALESCE(SUM(j.gols_marcados_timeA = j.gols_marcados_timeB), 0) AS empates,
                                    COALESCE(SUM(j.gols_marcados_timeA < j.gols_marcados_timeB), 0) AS derrotas,
                                    COALESCE(SUM(j.gols_marcados_timeA - j.gols_marcados_timeB), 0) AS sg,
                                    COUNT(j.id) AS partidas,
                                    COALESCE(SUM(j.gols_marcados_timeA > j.gols_marcados_timeB), 0) * 3 + COALESCE(SUM(j.gols_marcados_timeA = j.gols_marcados_timeB), 0) AS pts
                             FROM times t
                             LEFT JOIN jogos_fase_grupos j ON t.id = j.timeA_id OR t.id = j.timeB_id
                             WHERE t.grupo_id = $grupoId
                             GROUP BY t.id
                             ORDER BY pts DESC, gm DESC, gc ASC, sg DESC";
                $resultTimes = $conn->query($sqlTimes);

                if ($resultTimes->num_rows > 0) {
                    $posicao = 1;
                    while ($rowTimes = $resultTimes->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>';
                        echo '<span style="font-weight: bold; margin-right: 5px;">' . $posicao . '</span>';
                        if (!empty($rowTimes['logo'])) {
                            $imageData = base64_encode($rowTimes['logo']);
                            $imageSrc = 'data:image/jpeg;base64,'.$imageData;
                            echo '<img src="' . $imageSrc . '" class="logo-time">';
                        }
                        echo '<span class="time-cell">' . $rowTimes['nome'] . '</span>';
                        echo '</td>';
                        echo '<td class="small-col">' . $rowTimes['pts'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['partidas'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['vitorias'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['empates'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['derrotas'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['gm'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['gc'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['sg'] . '</td>';
                        echo '<td class="larger-col">' . formatarPorcentagemAproveitamento($rowTimes['vitorias'], $rowTimes['partidas']) . '</td>';
                        echo '<td class="larger-col">';
                        echo gerarUltimosJogos($rowTimes['id']);
                        echo '</td>';
                        echo '</tr>';
                        $posicao++;
                    }
                } else {
                    echo '<tr><td colspan="12">Nenhum time encontrado para este grupo.</td></tr>';
                }

                echo '</table>';
                echo '</div>';
            }
        } else {
            echo "Nenhum grupo encontrado.";
        }

        $conn->close();
    }

    function formatarPorcentagemAproveitamento($vitorias, $partidas) {
        if ($partidas > 0) {
            $porcentagem = number_format(($vitorias / $partidas) * 100, 1);
            if (substr($porcentagem, -2) == '.0') {
                return substr($porcentagem, 0, -2);
            } else {
                return $porcentagem;
            }
        } else {
            return '0';
        }
    }

    function gerarUltimosJogos($timeId) {
        include '../config/conexao.php';

        $sqlJogos = "SELECT CASE 
                        WHEN timeA_id = $timeId THEN resultado_timeA
                        WHEN timeB_id = $timeId THEN resultado_timeB
                        ELSE 'G'
                      END AS resultado
                      FROM jogos_fase_grupos 
                      WHERE timeA_id = $timeId OR timeB_id = $timeId 
                      ORDER BY data_jogo DESC 
                      LIMIT 5";
        $resultJogos = $conn->query($sqlJogos);

        $ultimosJogos = [];

        if ($resultJogos->num_rows > 0) {
            while ($rowJogos = $resultJogos->fetch_assoc()) {
                $ultimosJogos[] = $rowJogos['resultado'];
            }
        }

        while (count($ultimosJogos) < 5) {
            $ultimosJogos[] = 'G'; // Cinza para jogos não existentes
        }

        $output = '';
        foreach ($ultimosJogos as $resultado) {
            if ($resultado == 'V') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: green; border-radius: 50%; margin-right: 2px;"></div>';
            } elseif ($resultado == 'D') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: red; border-radius: 50%; margin-right: 2px;"></div>';
            } elseif ($resultado == 'E') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: gray; border-radius: 50%; margin-right: 2px;"></div>';
            } else {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: lightgray; border-radius: 50%; margin-right: 2px;"></div>';
            }
        }

        return $output;
    }
    ?>
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>
