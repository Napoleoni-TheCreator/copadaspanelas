<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

function exibirFinais() {
    global $conn;

    // Obter a configuração atual
    $result = $conn->query("SELECT * FROM configuracoes WHERE id = 1");
    if ($result->num_rows === 0) {
        die("Configuração não encontrada.");
    }
    $config = $result->fetch_assoc();
    $fase_final = $config['fase_final'];

    // Limpar o conteúdo
    echo "<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .bracket {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 250px;
            margin: 10px;
        }

        .round {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .match {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .team {
            display: flex;
            align-items: center;
        }

        .flag {
            width: 50px;
            height: 50px;
            margin-right: 5px;
        }

        .team-name {
            font-weight: bold;
        }

        .round-label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .connector {
            width: 100%;
            border-bottom: 1px dashed #ccc;
            margin-bottom: 10px;
        }

        .final {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
        }

        .disputa {
            width: 100%;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<h1 style='text-align: center;'>MATA-MATA</h1>

<div class='container'>";

    // Função para exibir confrontos
    function exibirConfrontos($fase, $tabela) {
        global $conn;
        $sql = "SELECT * FROM $tabela";
        $result = $conn->query($sql);
        $matches = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $matches[] = $row;
            }

            $totalMatches = count($matches);
            $half = ceil($totalMatches / 2);

            // Dividir confrontos em duas partes
            $firstHalf = array_slice($matches, 0, $half);
            $secondHalf = array_slice($matches, $half);

            echo "<div class='bracket'>
                    <div class='round-label'>$fase</div>";

            // Exibir primeira metade
            echo "<div class='round'>";
            foreach ($firstHalf as $match) {
                echo "<div class='match'>
                        <div class='team'>
                            <img class='flag' src='logo.png' alt='Logo Time A'>
                            <span class='team-name'>{$match['timeA_nome']}</span>
                        </div>
                        <div class='team'>
                            <img class='flag' src='logo.png' alt='Logo Time B'>
                            <span class='team-name'>{$match['timeB_nome']}</span>
                        </div>
                      </div>";
            }
            echo "</div>";

            // Exibir segunda metade
            echo "<div class='round'>";
            foreach ($secondHalf as $match) {
                echo "<div class='match'>
                        <div class='team'>
                            <img class='flag' src='logo.png' alt='Logo Time A'>
                            <span class='team-name'>{$match['timeA_nome']}</span>
                        </div>
                        <div class='team'>
                            <img class='flag' src='logo.png' alt='Logo Time B'>
                            <span class='team-name'>{$match['timeB_nome']}</span>
                        </div>
                      </div>";
            }
            echo "</div></div>";
        }
    }

    // Ordem das fases finais conforme solicitado
    $fases = [
        ['Oitavas de Final', 'oitavas_de_final_confrontos'],
        ['Quartas de Final', 'quartas_de_final_confrontos'],
        ['Semifinais', 'semifinais_confrontos'],
        ['Final', 'final_confrontos'],
        ['Semifinais', 'semifinais_confrontos'],
        ['Quartas de Final', 'quartas_de_final_confrontos'],
        ['Oitavas de Final', 'oitavas_de_final_confrontos']
    ];

    foreach ($fases as $fase) {
        $nome_fase = $fase[0];
        $tabela = $fase[1];
        // Exibir fase apenas se houver confrontos
        if ($conn->query("SELECT COUNT(*) FROM $tabela")->fetch_row()[0] > 0) {
            exibirConfrontos($nome_fase, $tabela);
        }
    }

    echo "</div></body></html>";
}

// Executar a função para exibir as fases finais
exibirFinais();
?>
