<!DOCTYPE html>
<html>
<head>
    <style>
body {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 0;
    /* background-color: #f0f8ff; */
    font-family: Arial, sans-serif;
}

.container {
    display: flex;
    justify-content: center;
    padding: 20px;
    border: 1px solid red;
    border-radius: 10px;
    box-shadow: 0 4px 8px red;
    width: 100%;
    max-width: 1200px; /* Limitar a largura máxima */
    overflow: hidden;
    background-image: url('../../public/img/ESCUDO\ COPA\ DAS\ PANELAS.png');
    background-size: 15% auto;
    background-position: top center;
    background-repeat: no-repeat;
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
    justify-content: center;
    align-items: center;
    width: 100%;
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 4px 8px red;
    text-align: center;
}

.team {
    display: flex;
    align-items: center;
    text-align: center;
    padding: 10px;
}

.flag {
    width: 46px;
    height: 46px;
    margin-right: 10px; /* Espaçamento entre a imagem e o nome */
    border-radius: 100%;
    object-fit: contain;
}

.team-name {
    font-weight: bold;
    margin-right: 10px; /* Espaçamento entre o nome e o número de gols */
}

.round-label {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.final-match {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    margin-bottom: 20px; /* Adicionar espaço abaixo do bloco da final */
    flex-direction: center; /* Alterar para coluna para nome ficar abaixo da logo */
}

.final-match .team {
    /* margin: 0 0 1px 0; Espaço entre as equipes */
    flex-direction: column; /* Alterar para coluna para nome ficar abaixo da logo */
    /* align-items: center; */
}

.final-match .flag {
    margin-bottom: 5px; /* Espaçamento entre a imagem e o nome */
}

.final-match .vs {
    font-size: 24px;
    font-weight: bold;
    margin: 0 15px;
}

#titulo_eli {
    margin-top: 5%;
    font-size: 40px; /* Define o tamanho da fonte */
    margin-bottom: 20px; /* Define a margem inferior */
    text-align: center; /* Alinha o texto ao centro */
    text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5); /* Adiciona uma sombra ao texto */

}

    </style>
</head>
<body>
<h1 id="titulo_eli">ELIMINATORIA</h1>
<?php include 'header_classificacao.php'; ?>
<div class="container">
    <div class="bracket">
        <?php
        // Incluir a configuração de conexão
        include '../config/conexao.php';

        // Função para exibir a imagem do logo
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

        // Função para exibir confrontos
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

                    if ($fase == 'final') {
                        // Exibir o confronto da final em uma única div
                        echo "<div class='match final-match'>";
                        echo "<div class='team'>";
                        exibirImagemLogo($conn, $timeA_id);
                        echo "<span class='team-name'>{$timeA_nome}</span>";
                        echo "<span>{$row['gols_marcados_timeA']}</span>";
                        echo "</div>";
                        echo "<span class='vs'>X</span>"; // Adiciona um separador 'VS'
                        echo "<div class='team'>";
                        exibirImagemLogo($conn, $timeB_id);
                        echo "<span class='team-name'>{$timeB_nome}</span>";
                        echo "<span>{$row['gols_marcados_timeB']}</span>";
                        echo "</div>";
                        echo "</div>";
                    } else {
                        // Exibir confrontos para outras fases no formato atual
                        echo "<div class='match'>";
                        echo "<div class='team'>";
                        exibirImagemLogo($conn, $timeA_id);
                        echo "<span class='team-name'>{$timeA_nome}</span>";
                        echo "<span>{$row['gols_marcados_timeA']}</span>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='match'>";
                        echo "<div class='team'>";
                        exibirImagemLogo($conn, $timeB_id);
                        echo "<span class='team-name'>{$timeB_nome}</span>";
                        echo "<span>{$row['gols_marcados_timeB']}</span>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            }
        }

        // Exibir os confrontos das fases na ordem desejada
        echo "<div class='column'>";
        echo "<div class='round-label'>Oitavas</div>";
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
        exibirConfrontos($conn, 'final', 1, 0);
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
        echo "<div class='round-label'>Oitavas</div>";
        exibirConfrontos($conn, 'oitavas', 4, 4);
        echo "</div>";

        // Fechar a conexão com o banco de dados
        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
