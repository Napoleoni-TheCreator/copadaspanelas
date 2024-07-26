<?php
include "../config/conexao.php";

// Função para converter dados binários da imagem em base64
function exibirImagem($imagem) {
    if ($imagem != null) {
        return 'data:image/jpeg;base64,' . base64_encode($imagem);
    } else {
        return 'default.jpg'; // caminho para uma imagem padrão
    }
}

// Buscar dados dos jogadores ordenados por gols
$sql_gols = "SELECT j.nome, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS nome_time
             FROM jogadores j
             JOIN times t ON j.time_id = t.id
             ORDER BY j.gols DESC";
$result_gols = $conn->query($sql_gols);
$jogadores_gols = [];
if ($result_gols->num_rows > 0) {
    while ($row = $result_gols->fetch_assoc()) {
        $jogadores_gols[] = $row;
    }
}

// Buscar dados dos jogadores ordenados por assistências
$sql_assistencias = "SELECT j.nome, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS nome_time
                     FROM jogadores j
                     JOIN times t ON j.time_id = t.id
                     ORDER BY j.assistencias DESC";
$result_assistencias = $conn->query($sql_assistencias);
$jogadores_assistencias = [];
if ($result_assistencias->num_rows > 0) {
    while ($row = $result_assistencias->fetch_assoc()) {
        $jogadores_assistencias[] = $row;
    }
}

// Buscar dados dos jogadores ordenados por cartões amarelos
$sql_cartoes_amarelos = "SELECT j.nome, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS nome_time
                         FROM jogadores j
                         JOIN times t ON j.time_id = t.id
                         ORDER BY j.cartoes_amarelos DESC";
$result_cartoes_amarelos = $conn->query($sql_cartoes_amarelos);
$jogadores_cartoes_amarelos = [];
if ($result_cartoes_amarelos->num_rows > 0) {
    while ($row = $result_cartoes_amarelos->fetch_assoc()) {
        $jogadores_cartoes_amarelos[] = $row;
    }
}

// Buscar dados dos jogadores ordenados por cartões vermelhos
$sql_cartoes_vermelhos = "SELECT j.nome, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS nome_time
                          FROM jogadores j
                          JOIN times t ON j.time_id = t.id
                          ORDER BY j.cartoes_vermelhos DESC";
$result_cartoes_vermelhos = $conn->query($sql_cartoes_vermelhos);
$jogadores_cartoes_vermelhos = [];
if ($result_cartoes_vermelhos->num_rows > 0) {
    while ($row = $result_cartoes_vermelhos->fetch_assoc()) {
        $jogadores_cartoes_vermelhos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estatísticas dos Jogadores</title>
    <style>
        * {
            /* margin: 0; */
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0f8ff;
            font-family: "Times New Roman", serif;
        }

        .container {
            width: 80%;
            margin-bottom: 10%;
            padding: 30px;
            border: 1px solid black;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
        }

        .section {
            margin-bottom: 20px;
        }

        .section h1 {
            font-size: 30px;
            font-family: Arial, sans-serif;
            color: red;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px #000000;
            text-align: center; /* Centraliza apenas o título h1 */
        }

        .player-card {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s;
        }

        .player-card:hover {
            background-color: #f0f0f0;
        }

        .player-card img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .player-card .info {
            display: flex;
            flex-direction: column;
            flex: 1;
            margin-right: 10px; /* Margin between image and info */
        }

        .player-card .info .name {
            font-size: 18px;
            font-weight: bold;
        }

        .player-card .info .team {
            font-size: 16px;
            color: #555;
        }

        .player-card .info .stat {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-left: auto; /* Move stat to the right */
        }

        .index {
            font-size: 18px;
            font-weight: bold;
            margin-right: 10px;
            text-align: center;
            width: 30px;
        }

        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        .dark-mode .container {
            background-color: #1e1e1e;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .dark-mode .section h1 {
            color: #ffffff;
        }

        .dark-mode .player-card {
            background-color: #333;
            border: 1px solid #444;
        }

        .dark-mode .player-card img {
            border: 1px solid #ffffff;
        }

        .dark-mode .player-card .info .name,
        .dark-mode .player-card .info .stat,
        .dark-mode .player-card .info .team {
            color: #ffffff;
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
        #tituloh1{
            margin-top: 5%;
        }

    </style>
</head>
<body>
    <button class="dark-mode-button" onclick="toggleDarkMode()">Modo Escuro/Claro</button>
    
    <?php include '../pages/header_classificacao.php'; ?>
    <h1 id="tituloh1">Estatísticas dos Jogadores</h1>
    <div class="container">

        <!-- Seção de Gols -->
        <div class="section">
            <h1>Gols</h1>
            <?php if (!empty($jogadores_gols)) {
                $i = 1;
                foreach ($jogadores_gols as $jogador) {
                    echo "<div class='player-card'>";
                    echo "<div class='index'>$i</div>";
                    echo "<img src='" . exibirImagem($jogador['imagem']) . "' alt='Imagem do Jogador'>";
                    echo "<div class='info'>";
                    echo "<span class='name'>" . htmlspecialchars($jogador['nome']) . "</span>";
                    echo "<span class='team'>" . htmlspecialchars($jogador['nome_time']) . "</span>";
                    echo "</div>";
                    echo "<span class='stat'>" . htmlspecialchars($jogador['gols']) . "</span>";
                    echo "</div>";
                    $i++;
                }
            } else {
                echo "<div class='player-card'>Nenhum jogador encontrado</div>";
            } ?>
        </div>

        <!-- Seção de Assistências -->
        <div class="section">
            <h1>Assistências</h1>
            <?php if (!empty($jogadores_assistencias)) {
                $i = 1;
                foreach ($jogadores_assistencias as $jogador) {
                    echo "<div class='player-card'>";
                    echo "<div class='index'>$i</div>";
                    echo "<img src='" . exibirImagem($jogador['imagem']) . "' alt='Imagem do Jogador'>";
                    echo "<div class='info'>";
                    echo "<span class='name'>" . htmlspecialchars($jogador['nome']) . "</span>";
                    echo "<span class='team'>" . htmlspecialchars($jogador['nome_time']) . "</span>";
                    echo "</div>";
                    echo "<span class='stat'>" . htmlspecialchars($jogador['assistencias']) . "</span>";
                    echo "</div>";
                    $i++;
                }
            } else {
                echo "<div class='player-card'>Nenhum jogador encontrado</div>";
            } ?>
        </div>

        <!-- Seção de Cartões Amarelos -->
        <div class="section">
            <h1>Cartões Amarelos</h1>
            <?php if (!empty($jogadores_cartoes_amarelos)) {
                $i = 1;
                foreach ($jogadores_cartoes_amarelos as $jogador) {
                    echo "<div class='player-card'>";
                    echo "<div class='index'>$i</div>";
                    echo "<img src='" . exibirImagem($jogador['imagem']) . "' alt='Imagem do Jogador'>";
                    echo "<div class='info'>";
                    echo "<span class='name'>" . htmlspecialchars($jogador['nome']) . "</span>";
                    echo "<span class='team'>" . htmlspecialchars($jogador['nome_time']) . "</span>";
                    echo "</div>";
                    echo "<span class='stat'>" . htmlspecialchars($jogador['cartoes_amarelos']) . "</span>";
                    echo "</div>";
                    $i++;
                }
            } else {
                echo "<div class='player-card'>Nenhum jogador encontrado</div>";
            } ?>
        </div>

        <!-- Seção de Cartões Vermelhos -->
        <div class="section">
            <h1>Cartões Vermelhos</h1>
            <?php if (!empty($jogadores_cartoes_vermelhos)) {
                $i = 1;
                foreach ($jogadores_cartoes_vermelhos as $jogador) {
                    echo "<div class='player-card'>";
                    echo "<div class='index'>$i</div>";
                    echo "<img src='" . exibirImagem($jogador['imagem']) . "' alt='Imagem do Jogador'>";
                    echo "<div class='info'>";
                    echo "<span class='name'>" . htmlspecialchars($jogador['nome']) . "</span>";
                    echo "<span class='team'>" . htmlspecialchars($jogador['nome_time']) . "</span>";
                    echo "</div>";
                    echo "<span class='stat'>" . htmlspecialchars($jogador['cartoes_vermelhos']) . "</span>";
                    echo "</div>";
                    $i++;
                }
            } else {
                echo "<div class='player-card'>Nenhum jogador encontrado</div>";
            } ?>
        </div>

    </div>

    <script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }
    </script>
</body>
</html>
