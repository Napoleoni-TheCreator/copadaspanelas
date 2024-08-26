<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estatísticas dos Jogadores</title>
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
    <style>
    .main{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-family: Arial, sans-serif;
    }
    #titulo_eli{
    font-size: 30px;
    margin-bottom: 10px;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    }
    .container {
        display: flex;
        justify-content: center;
        padding: 20px;
        border: 1px solid red;
        border-radius: 10px;
        box-shadow: 0 4px 8px red;
        max-width: 1200px; 
        overflow: hidden;
        background-image: url('../../public/img/ESCUDO\ COPA\ DAS\ PANELAS.png');
        background-size: 15% auto;
        background-position: top center;
        background-repeat: no-repeat;
        margin-bottom: 10%;
    }

    .bracket {
        display: flex;
        justify-content: space-between;
        width: 100%;
        flex-wrap: nowrap; /* Garantir que as colunas não quebrem para a próxima linha */
        align-items: center;
    }

    .column {
        flex: 1;
        margin-left: 10px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 200px; /* Ajustar conforme necessário */
    }

    .match {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #ffffff;
        border-radius: 5px;
        box-shadow: 0 4px 8px red;
        text-align: center;
        box-sizing: border-box; /* Inclui padding e border no tamanho total */
    }

    .team {
        display: flex;
        align-items: center;
        text-align: left; /* Alinha o texto à esquerda */
        padding: 10px;
        box-sizing: border-box; /* Inclui padding e border no tamanho total */
    }

    /* Estilos das Imagens das Bandeiras */
    .flag {
        width: 40px;
        height: 40px;
        margin-right: 10px;
        object-fit: contain;
    }

    /* Estilos dos Nomes dos Times */
    .team-name {
        font-weight: bold;
        margin-right: 10px;
        max-width: 150px; /* Limitar a largura máxima do nome do time */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /* Estilos para o Placar dos Jogos */
    .score {
        border: 1px solid #ccc;
        padding: 5px 10px;
        border-radius: 5px;
        margin-right: auto; /* Move o placar para o lado esquerdo */
        font-size: 1.2em;
    }

    /* Estilos Específicos para a Final */
    .final-match {
        display: flex;
        justify-content: space-between;
        width: 100%; /* Garantir que ocupa a largura total da coluna */
    }

    /* Estilos dos Separadores */
    .vs {
        font-size: 1.2em;
        font-weight: bold;
        margin: 0 10px;
    }

    body.dark-mode .container {
        background-color: #1e1e1e;
        border: 1px solid #444;
        box-shadow: 0 4px 8px #444;
    }

    body.dark-mode .match {
        background-color: #333;
        border: 1px solid #555;
        box-shadow: 0 4px 8px #555;
    }

    body.dark-mode .round-label {
        color: #e0e0e0;
    }

    body.dark-mode #titulo_eli {
        color: #e0e0e0;
        text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.7);
    }
    h1{
        margin-top: 5%;
    }

    /* Media Queries para iPhone 12 e dispositivos similares */
    @media (max-width: 768px) { /* iPhone 12 portrait mode */
        .container {
            padding: 10px;
            max-width: 100%; /* Ajustar a largura para ocupar 100% da tela */
        }

        .column {
            margin: 5px;
            max-width: 100%; /* Ajustar a largura máxima para 100% */
        }

        .flag {
            width: 30px; /* Reduzir o tamanho das bandeiras */
            height: 30px;
        }

        .team-name {
            max-width: 120px; /* Reduzir a largura máxima do nome do time */
        }
    }


    @media (max-width: 480px) { /* iPhone 12 mini e outros dispositivos menores */
        .container {
            /* flex-direction: column; Empilhar os elementos verticalmente */
            width: 80%;
            overflow-x:auto;
        }
        .match {
            font-size: 10px;
            width: auto;
            height: auto;
            padding: 0;
        }
        h1{
            margin-top: 20%;
        }
        /* Estilos do Layout dos Confrontos */
        .bracket {
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap; /* Garantir que as colunas não quebrem para a próxima linha */
            align-items: center;
        }

        .flag {
            width: 25px; /* Reduzir ainda mais o tamanho das bandeiras */
            height: 25px;
        }
        .column{
            margin: 0;
        }
    }
    </style>
</head>
<body>
<?php require_once '../pages/header_classificacao.php'; ?>
<div class="main">
<h1 id="titulo_eli">ELIMINATORIA</h1>
<div class="container">
    <div class="bracket">
        <?php
        include '../config/conexao.php';

        function exibirImagemLogo($conn, $time_id) {
            $sql = "SELECT logo FROM times WHERE id = $time_id";
            $result = $conn->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                $logo_bin = $row['logo'];
                if ($logo_bin) {
                    $logo_base64 = base64_encode($logo_bin);
                    echo "<img class='flag' src='data:image/png;base64,{$logo_base64}' alt='Logo do time'>";
                } else {
                    echo "<img class='flag' src='path/to/logos/default.png' alt='Logo padrão'>";
                }
            } else {
                echo "<img class='flag' src='path/to/logos/default.png' alt='Logo padrão'>";
            }
        }

function exibirConfrontos($conn, $fase, $count, $start = 0) {
    $tabelaConfrontos = '';
    switch ($fase) {
        case 'oitavas':
            $tabelaConfrontos = 'oitavas_de_final_confrontos';
            break;
        case 'quartas':
            $tabelaConfrontos = 'quartas_de_final_confrontos';
            break;
        case 'semifinais':
            $tabelaConfrontos = 'semifinais_confrontos';
            break;
        case 'final':
            $tabelaConfrontos = 'final_confrontos';
            break;
        default:
            die("Fase desconhecida: " . $fase);
    }

    $sql = "SELECT * FROM $tabelaConfrontos LIMIT $start, $count";
    $result = $conn->query($sql);
    if (!$result) {
        die("Erro na consulta de confrontos: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $timeA_id = $row['timeA_id'];
            $timeB_id = $row['timeB_id'];

            // Consultar os nomes dos times
            $timeA_nome = $conn->query("SELECT nome FROM times WHERE id = $timeA_id")->fetch_assoc()['nome'];
            $timeB_nome = $conn->query("SELECT nome FROM times WHERE id = $timeB_id")->fetch_assoc()['nome'];

            $timeA_nome_abreviado = substr($timeA_nome, 0, 3); // Pegar as três primeiras letras do nome
            $timeB_nome_abreviado = substr($timeB_nome, 0, 3); // Pegar as três primeiras letras do nome

            if ($fase == 'final') {
                // Exibir o confronto da final em uma única div
                echo "<div class='match final-match'>";
                echo "<div class='team'>";
                exibirImagemLogo($conn, $timeA_id);
                // echo "<span class='team-name' title='{$timeA_nome}'>{$timeA_nome_abreviado}</span>";
                // echo "<span class='score'>{$row['gols_marcados_timeA']}</span>";
                echo "</div>";
                echo "<span class='vs'>X</span>"; // Adiciona um separador 'VS'
                echo "<div class='team'>";
                exibirImagemLogo($conn, $timeB_id);
                // echo "<span class='team-name' title='{$timeB_nome}'>{$timeB_nome_abreviado}</span>";
                // echo "<span class='score'>{$row['gols_marcados_timeB']}</span>";
                echo "</div>";
                echo "</div>";
            } else {
                // Exibir confrontos para outras fases no formato atual
                echo "<div class='match'>";
                echo "<div class='team'>";
                exibirImagemLogo($conn, $timeA_id);
                echo "<span class='team-name' title='{$timeA_nome}'>{$timeA_nome_abreviado}</span>";
                echo "<span class='score'>{$row['gols_marcados_timeA']}</span>";
                echo "</div>";
                echo "</div>";
                echo "<div class='match'>";
                echo "<div class='team'>";
                exibirImagemLogo($conn, $timeB_id);
                echo "<span class='team-name' title='{$timeB_nome}'>{$timeB_nome_abreviado}</span>";
                echo "<span class='score'>{$row['gols_marcados_timeB']}</span>";
                echo "</div>";
                echo "</div>";
            }
        }
    }
}

        // Exibir os confrontos das fases na ordem desejada
        echo "<div class='column'>";
        // echo "<div class='round-label'>Oitavas</div>";
        exibirConfrontos($conn, 'oitavas', 4, 0);
        echo "</div>";

        echo "<div class='column'>";
        echo "<div class='round-label'>Quartas</div>";
        exibirConfrontos($conn, 'quartas', 2, 0);
        echo "</div>";

        echo "<div class='column'>";
        echo "<div class='round-label'>Semifinais</div>";
        exibirConfrontos($conn, 'semifinais', 1, 0);
        echo "</div>";

        echo "<div class='column'>";
        echo "<div class='round-label'>Final</div>";
        exibirConfrontos($conn, 'final', 1);
        echo "</div>";

        echo "<div class='column'>";
        echo "<div class='round-label'>Semifinais</div>";
        exibirConfrontos($conn, 'semifinais', 1, 1);
        echo "</div>";

        echo "<div class='column'>";
        echo "<div class='round-label'>Quartas</div>";
        exibirConfrontos($conn, 'quartas', 2, 2);
        echo "</div>";

        echo "<div class='column'>";
        // echo "<div class='round-label'>Oitavas</div>";
        exibirConfrontos($conn, 'oitavas', 4, 4);
        echo "</div>";
        ?>
    </div>
</div>
</div>
<?php include 'footer.php'?>  
</body>
</html>
