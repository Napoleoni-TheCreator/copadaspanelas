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
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
    <link rel="stylesheet" href="../../public/css/estatistica.css">
</head>
<body>
    <?php include '../pages/header_classificacao.php'; ?>
    <div class="main">
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
</div>
<?php include 'footer.php'?>  
</body>
</html>
