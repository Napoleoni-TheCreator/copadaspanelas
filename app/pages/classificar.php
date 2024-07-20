<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Times Classificados para as Finais</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        #tabela-wrapper {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 3%;
            margin-bottom: 10%;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: background-color 0.3s, color 0.3s;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #333;
            transition: color 0.3s;
        }
        h2 {
            margin-bottom: 10px;
            font-size: 1.5em;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
            transition: background-color 0.3s, color 0.3s;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        .logo-time {
            max-width: 50px;
            max-height: 50px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .posicao-col {
            width: 50px;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode #tabela-wrapper {
            background-color: #1e1e1e;
        }
        .dark-mode th {
            background-color: #333;
            color: #ffffff;
        }
        .dark-mode td {
            background-color: #2e2e2e;
        }
        .dark-mode h1, .dark-mode h2 {
            color: #ffffff;
        }
        .dark-mode .logo-time {
            border: 1px solid #ffffff;
        }
        .toggle-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        .toggle-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="toggle-button" onclick="toggleDarkMode()">Modo Escuro/Claro</button>
    
    <?php include 'header_classificacao.php'; ?>
    
    <div id="tabela-wrapper">
        <h1>Times Classificados para as Finais</h1>

        <?php
        function exibirTimes($titulo, $tabela) {
            include '../config/conexao.php';

            // SQL para obter os times classificados e os grupos
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
                    // Codifica a imagem em base64 se necessário
                    $logoData = !empty($row['logo']) ? 'data:image/jpeg;base64,' . base64_encode($row['logo']) : '';

                    echo "<tr>";
                    echo "<td class='posicao-col'>" . $posicao . "</td>";
                    echo "<td><img src=\"$logoData\" class=\"logo-time\" alt=\"Logo\"></td>";
                    echo "<td>" . htmlspecialchars($row['time_nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['grupo_nome']) . "</td>";
                    echo "</tr>";

                    $posicao++;
                }

                echo "</table>";
            }
        }

        // Exibe times classificados para as oitavas de final, se houver
        exibirTimes('Times Classificados para as Oitavas de Final', 'oitavas_de_final');

        // Exibe times classificados para as quartas de final, se houver
        exibirTimes('Times Classificados para as Quartas de Final', 'quartas_de_final');

        // Exibe times classificados para as semifinais, se houver
        exibirTimes('Times Classificados para as Semifinais', 'semifinais');

        // Exibe times classificados para a final, se houver
        exibirTimes('Times Classificados para a Final', 'final');

        ?>
    </div>
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
</body>
</html>
