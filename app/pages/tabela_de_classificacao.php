<!DOCTYPE html>
<html>
<head>
    <title>Tabela de Classificação</title>
    <style>
        * {
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100%;
            /* background-color: #f0f8ff; */
            background: linear-gradient(to top,#f879118c,rgba(255, 0, 0, 0.603));
        }

        #tabela-wrapper {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            width: 80%;
            margin-top: 1%;
            margin-bottom: 10%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin-top: 5%;
        }

        .grupo-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .grupo-header {
            font-size: 1.2em;
            margin-bottom: 10px;
            text-align: center;
        }

        .tabela-flex {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .tabela-flex-header, .tabela-flex-row {
            display: flex;
            width: 100%;
            /* background-color: #f2f2f2; */
            /* border: 1px solid #ddd; */
            border-bottom: 1px solid #ddd ;
        }
        .tabela-flex-header{
             background-color: #f2f2f2;
        }

        .tabela-flex-header > div, .tabela-flex-row > div {
            flex: 1;
            padding: 10px;
            text-align: center;
            /* border-right: 1px solid #ddd; */
        }

        .tabela-flex-header > div:last-child, .tabela-flex-row > div:last-child {
            border-right: none;
        }

        .logo-time {
            max-width: 50px;
            max-height: 50px;
            vertical-align: middle;
            margin-right: 5px;
            border-radius: 10%;
            margin-left: 10px;
        }

        .small-col {
            min-width: 70px;
            text-align: center;
            align-items: center;
            display: flex;
            justify-content:center;
        }

        .larger-col {
            min-width: 70px;
            text-align: center;
            align-items: center;
            display: flex;
            justify-content:center;
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

        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode #tabela-wrapper {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .tabela-flex-header {
            background-color: #333333;
        }

        body.dark-mode .tabela-flex-row {
            background-color: #333333;
            color: #e0e0e0;
        }

        body.dark-mode #legenda-simbolos {
            background-color: #2c2c2c;
            border: 1px solid #444;
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

        .dark-mode-button:hover {
            background-color: #0056b3;
        }

        .time-info {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-width: 270px; /* Largura mínima */
            min-height: 80px;
            max-width: 270px;
            max-height: 80px;
            overflow: hidden;
        }
        .clube{
            min-width: 270px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .posicao_num {
            font-weight: bold;
            margin-right: 10px;
            font-size: 1.2em;
        }

        .time-name {
            display: flex;
            align-items: center;
            flex: 1; /* Faz com que o nome do time ocupe o espaço restante */
            overflow: hidden; /* Oculta texto que excede o limite */
            text-overflow: ellipsis; /* Adiciona '...' ao final do texto que excede o limite */
            white-space: nowrap; /* Evita que o texto quebre em várias linhas */
        }
        .inf{
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            background-color: green; 
            border-radius: 50%; 
            margin-right: 2px;
        }
        /* 2 = red , 3 = gray, 4 = lightgray */
        /* Media Queries */
        .inf2{
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            background-color: red; 
            border-radius: 50%; 
            margin-right: 2px;
        }
        .inf3{
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            background-color: gray; 
            border-radius: 50%; 
            margin-right: 2px;
        }
        .inf4{
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            background-color: lightgray; 
            border-radius: 50%; 
            margin-right: 2px;
        }
@media (max-width: 1200px) {
    .larger-col {
        min-width: 60px;
    }
}

@media (max-width: 768px) {
    h1,h2 {
        font-size: 10px;
    }
    h1{
        margin-top: 10%;
    }
    /* .tabela-flex-header, .tabela-flex-row {
        flex-direction: column;
        align-items: stretch;
    } */

    .small-col{
        min-width: 0%;
        max-width: 20px;
        text-align: center;
    }
    .larger-col{
        min-width: 50px;
        max-width: 50px;
    }
    .inf{
        width: 5px;
        height: 5px;
    }
    .inf2{
        width: 5px;
        height: 5px;
    }
    .inf3{
        width: 5px;
        height: 5px;
    }
    .inf4{
        width: 5px;
        height: 5px;
    }
    .grupo-header{
        font-size: 10px;
    }
    #tabela-wrapper{
        width: 100%;
    }
    .tabela-flex-header > div, .tabela-flex-row > div{
        padding: 0px;
        margin: 0px;
    }
    .time-info{
        display: flex;
            align-items: center;
            justify-content: flex-start;
            min-width: 100px; /* Largura mínima */
            min-height: 8px;
            max-width: 100px;
            max-height: 20px;
            overflow: hidden;
    }
    .tabela-flex-header {
        font-size: 10px;
    }

    .logo-time {
        max-width: 10px;
        max-height: 10px;
        margin-left: 1px;
        margin-right: 1px;
    }

    .posicao_num {
        font-size: 1em;
        margin-left: 0px;
        margin-right: 0px;
    }

    .time-name {
        font-size: 7px;
        min-width: 10px;
        max-width: 30px;
    }
    .clube{
            min-width: 100px; /* Largura mínima */
            max-width: 100px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 10px;
    }

    .tabela-flex-header, .tabela-flex-row {
        flex-direction: center;
        align-items: stretch;
    }

    .small-col, .larger-col {
        min-width:23px;
        text-align: center;
        font-size: 7px;
    }

    .logo-time {
        max-width: 10px;
        max-height: 10px;
    }

    .posicao_num {
        font-size: 10px;
    }

    .time-name {
        font-size: 10px;
    }
}

    </style>
</head>
<body>
<?php include 'header_classificacao.php'; ?>
<h1>FASE DE GRUPOS</h1>
<div id="tabela-wrapper">
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
            echo '<div class="tabela-flex">';
            echo '<div class="tabela-flex-header">';
            echo '<div class="clube">Clube</div>'; // Coluna de Nome do Clube
            echo '<div class="small-col">P</div>'; // Coluna de Pontos
            echo '<div class="small-col">J</div>'; // Coluna de Partidas
            echo '<div class="small-col">V</div>'; // Coluna de Vitórias
            echo '<div class="small-col">E</div>'; // Coluna de Empates
            echo '<div class="small-col">D</div>'; // Coluna de Derrotas
            echo '<div class="small-col">GP</div>'; // Coluna de Gols Pró
            echo '<div class="small-col">GC</div>'; // Coluna de Gols Contra
            echo '<div class="small-col">SG</div>'; // Coluna de Saldo de Gols
            echo '<div class="small-col">%</div>'; // Coluna de Porcentagem de Aproveitamento
            echo '<div class="larger-col">ÚLT. JOGOS</div>'; // Coluna de Últimos Jogos
            echo '</div>';

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
                    echo '<div class="tabela-flex-row">';
                    echo '<div class="time-info">';
                    echo '<span class="posicao_num">' . $posicao . '</span>';
                    if (!empty($rowTimes['logo'])) {
                        $imageData = base64_encode($rowTimes['logo']);
                        $imageSrc = 'data:image/jpeg;base64,'.$imageData;
                        echo '<img src="' . $imageSrc . '" class="logo-time">';
                    }
                    echo '<span class="time-name">' . $rowTimes['nome'] . '</span>';
                    echo '</div>';
                    echo '<div class="small-col">' . $rowTimes['pts'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['partidas'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['vitorias'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['empates'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['derrotas'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['gm'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['gc'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['sg'] . '</div>';
                    echo '<div class="small-col">' . formatarPorcentagemAproveitamento($rowTimes['vitorias'], $rowTimes['partidas']) . '</div>';
                    echo '<div class="larger-col">';
                    echo gerarUltimosJogos($rowTimes['id']);
                    echo '</div>';
                    echo '</div>';
                    $posicao++;
                }
            } else {
                echo '<div class="tabela-flex-row"><div colspan="11">Nenhum time encontrado para este grupo.</div></div>';
            }

            echo '</div>';
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
            $output .= '<div class="inf"></div>';
        } elseif ($resultado == 'D') {
            $output .= '<div class="inf2" ></div>';
        } elseif ($resultado == 'E') {
            $output .= '<div class="inf3" ></div>';
        } else {
            $output .= '<div class="inf4" ></div>';
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
