<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Times Classificados para as Finais</title>
    <style>
        body {
            display: flex;
            align-items: center;
            flex-direction: column;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
            /* font-family: Arial, sans-serif; */
        }
        #tabela-wrapper {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 40px rgba(255, 0, 0, 1.8);
            width: 80%;
            margin-top: 1%;
            margin-bottom: 10%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            font-size: 40px;
            margin-top: 5%;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5);
        }
        h2 {
            margin-bottom: 10px;
            font-size: 1.5em;
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
            transition: background-color 0.3s, color 0.3s;
        }
        td {
            padding: 20px 0;
        }
        td:hover {
            background-color: #f0f0f0;
            color: #333;
        }
        th {
            background-color: #f2f2f2;
        }
        .grupo-container {
            width: 100%;
            margin-bottom: 20px;
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
            font-size: 20px;
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
            width: 70px;
            text-align: center;
        }
        .larger-col {
            width: 70px;
            text-align: center;
        }
        .descricao {
            display: inline-block;
            margin-left: 5px;
        }
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        .dark-mode #tabela-wrapper {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .dark-mode th {
            background-color: #333;
        }
        .dark-mode td {
            background-color: #333;
            border-radius: 5px;
            padding: 10px;
        }
        .dark-mode h1, .dark-mode h2 {
            color: #ffffff;
        }
        .dark-mode .logo-time {
            border: 1px solid #ffffff;
        }
        .dark-mode #legenda-simbolos {
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

/* Media Queries para Ajustar o Layout em Telas Menores */
@media (max-width: 768px) {
    h1 {
        font-size: 1.5em;
    }

    h2 {
        font-size: 1.2em;
    }

    .time-cell {
        font-size: 14px;
    }

    .logo-time {
        max-width: 30px;
        max-height: 30px;
    }

    .small-col, .larger-col {
        width: 50px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.2em;
    }

    h2 {
        font-size: 1em;
    }

    .time-cell {
        font-size: 12px;
    }

    .logo-time {
        max-width: 25px;
        max-height: 25px;
    }

    .small-col, .larger-col {
        width: 40px;
    }

    #tabela-wrapper {
        padding: 10px;
        width: 95%; /* Ajusta a largura para dispositivos ainda menores */
    }
}
    </style>
</head>
<body>
    <button class="dark-mode-button" onclick="toggleDarkMode()">Modo Escuro/Claro</button>
    
    <?php include 'header_classificacao.php'; ?>
    <h1>CLASSIFICADOS</h1>
    <div id="tabela-wrapper">

        <?php
        function exibirTimes($titulo, $tabela) {
            include '../config/conexao.php';

            $sql = "SELECT t.id, t.logo, t.nome AS time_nome, tf.grupo_nome 
                    FROM $tabela AS tf
                    JOIN times AS t ON tf.time_id = t.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>$titulo</h2>";
                echo "<table>";
                echo "<tr><th>Posição</th><th>Logo</th><th>Nome do Time</th><th>Grupo</th></tr>";

                $posicao = 1;
                while ($row = $result->fetch_assoc()) {
                    $logoData = !empty($row['logo']) ? 'data:image/jpeg;base64,' . base64_encode($row['logo']) : '';

                    echo "<tr>";
                    echo "<td class='small-col'>" . $posicao . "</td>";
                    echo "<td><img src=\"$logoData\" class=\"logo-time\" alt=\"Logo\"></td>";
                    echo "<td class='time-cell'>" . htmlspecialchars($row['time_nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['grupo_nome']) . "</td>";
                    echo "</tr>";

                    $posicao++;
                }

                echo "</table>";
            }
        }

        exibirTimes('Times Classificados para as Oitavas de Final', 'oitavas_de_final');
        exibirTimes('Times Classificados para as Quartas de Final', 'quartas_de_final');
        exibirTimes('Times Classificados para as Semifinais', 'semifinais');
        exibirTimes('Times Classificados para a Final', 'final');
        ?>
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
</body>
</html>
