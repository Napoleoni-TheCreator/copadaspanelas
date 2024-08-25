<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Times Classificados para as Finais</title>
    <style>
        * {
            padding: 0;
            box-sizing: border-box;
        }

        .main {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            font-family: "Times New Roman", serif;
        }

        #tabela-wrapper {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
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
            font-size: 30px;
            margin-top: 5%;
            margin-bottom: 10px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .grupo-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .grupo-header {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .grupo-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s, color 0.3s;
        }

        .grupo-item:hover {
            background-color: #f0f0f0;
            color: #333;
        }

        .grupo-item .position {
            font-weight: bold;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .grupo-item img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 10%;
            margin-right: 10px;
        }

        .grupo-item .time-info {
            flex: 1;
        }

        .grupo-item .time-info .time-name {
            font-size: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .grupo-item .time-info .grupo-name {
            font-size: 16px;
            color: #666;
        }
        .dark-mode .grupo-item, .position :hover{
            color: #ffffff;
        }
        
        /* Media Queries para Ajustar o Layout em Telas Menores */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5em;
            }

            h2 {
                font-size: 1.2em;
            }

            .grupo-item .time-info .time-name {
                font-size: 14px;
            }

            .grupo-item img {
                max-width: 30px;
                max-height: 30px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.2em;
            }

            h2 {
                font-size: 1em;
            }

            .grupo-item .time-info .time-name {
                font-size: 12px;
            }

            .grupo-item img {
                max-width: 25px;
                max-height: 25px;
            }

            #tabela-wrapper {
                padding: 10px;
                width: 95%; /* Ajusta a largura para dispositivos ainda menores */
            }
        }
    </style>
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
</head>
<body>
    <?php include 'header_classificacao.php'; ?>
    <h1>CLASSIFICADOS</h1>
    <div class="main">
    <div id="tabela-wrapper">
        <?php
        function exibirTimes($titulo, $tabela) {
            include '../config/conexao.php';

            $sql = "SELECT t.id, t.logo, t.nome AS time_nome, tf.grupo_nome 
                    FROM $tabela AS tf
                    JOIN times AS t ON tf.time_id = t.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<div class='grupo-container'>";
                echo "<h2 class='grupo-header'>$titulo</h2>";

                $posicao = 1;
                while ($row = $result->fetch_assoc()) {
                    $logoData = !empty($row['logo']) ? 'data:image/jpeg;base64,' . base64_encode($row['logo']) : '';

                    echo "<div class='grupo-item'>";
                    echo "<div class='position'>" . $posicao . "</div>";
                    echo "<img src=\"$logoData\" alt=\"Logo\">";
                    echo "<div class='time-info'>";
                    echo "<div class='time-name'>" . htmlspecialchars($row['time_nome']) . "</div>";
                    echo "<div class='grupo-name'>" . htmlspecialchars($row['grupo_nome']) . "</div>";
                    echo "</div>";
                    echo "</div>";

                    $posicao++;
                }

                echo "</div>";
            }
        }

        exibirTimes('Oitavas de Final', 'oitavas_de_final');
        exibirTimes('Quartas de Final', 'quartas_de_final');
        exibirTimes('Semifinais', 'semifinais');
        exibirTimes('Final', 'final');
        ?>
    </div>
    </div>
<?php include 'footer.php'?>   
</body>
</html>
