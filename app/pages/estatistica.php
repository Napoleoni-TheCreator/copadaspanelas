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
        margin: 0;
        padding: 0%;
        box-sizing: border-box;
    }
    body {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #f0f8ff;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        /* margin-bottom: 20px; */
        /* background: rgba(0, 0, 0, 0.7); */
        /* border-radius: 10px; */
        /* overflow: hidden; */
    }
    th, td {
        text-align: right;
        /* padding: 10px; */
        /* color: #ffffff; */
    }
    th {
        /* border-radius: 3%; */
        font-size: 20px;
        /* color: #00ffff; */
        border-bottom: 3px solid #adff2f; /* Borda inferior para células de cabeçalho */
    }
    .titulo_th {
        text-align: left;
    }
    td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Borda inferior para células de dados */
    }
    .logTime {
        width: 50px; /* Defina o tamanho desejado */
        height: auto; /* Defina o tamanho desejado */
         /* object-fit: contain; Ajusta a imagem sem cortar */
        /* border-radius: 5px;
        margin-right: 20px; */
    }
    .clube {
        display: flex;
        align-items: center; /* Centraliza verticalmente */
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .nomeTime {
        /* color: #ffffff; */
        margin-left: 10px; /* Adapte conforme necessário para ajustar a distância entre a imagem e o texto */
        /* font-weight: bold; */
        font-size: 18px;
    }
    .dados {
        /* color: #00ffff; */
        margin-right: 15px;
        font-size: 30px;
    }
    .grupos {
        font-size: 30px;
        font-family: Arial, sans-serif;
        /* color: #ffffff; */
        margin-top: 4%;
        margin-bottom: 0px;
        /* text-shadow: 2px 2px 4px #000000; */
    }
    .clube {
        font-size: 30px;
        font-family: Arial, sans-serif;
        margin-left: 10px;
        /* color: #adff2f; */
    }
    .center {
        width: 80%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        /* margin-top: 1%; */
        border: 1px solid black;
        border-radius: 15px;
        /* background: rgba(0, 0, 0, 0.5); */
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
        margin-bottom: 10%;
    }
    img {
        margin-left: 10px;
    }
    /* h1:hover {
        font-size: 32px;
        font-family: Arial, Helvetica, sans-serif;
        color: #00ffff;
        background: rgba(206, 72, 72, 0.226);
        padding: 1px;
        border-bottom: 1px solid #adff2f;
    } */
    .tabela_center:hover {
        cursor: pointer;
    }
    tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    #tituloh1{
        font-size: 40px;
        font-family: Arial, sans-serif;
        color: red;
        margin-bottom: 3%;
        margin-top: 6%;
        text-shadow: 2px 2px 4px #000000;
    }
</style>
</head>
<body>
    <?php include '../pages/header_classificacao.php'?>
    <h1 id="tituloh1"> Estatísticas dos Jogadores</h1>
    <div class="container center">
        
        <!-- Tabela de Gols -->
        <table class="tabela_center">
            <h1 class="grupos">Gols</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Gols</th>
            </tr>
            <?php if (!empty($jogadores_gols)) {
                $i = 1;
                foreach ($jogadores_gols as $jogador) {
                    echo "<tr>";
                    echo "<td>
                            <div class='clube'>
                                <span>$i</span> 
                                <img src='" . exibirImagem($jogador['imagem']) . "' alt='' class='logTime'>
                                <span class='nomeTime'>" . $jogador['nome'] . "</span>
                            </div>
                          </td>";
                    echo "<td><span class='dados'>" . $jogador['gols'] . "</span></td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='2'>Nenhum jogador encontrado</td></tr>";
            } ?>
        </table>

        <!-- Tabela de Assistências -->
        <table class="tabela_center">
            <h1 class="grupos">Assistências</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Assistências</th>
            </tr>
            <?php if (!empty($jogadores_assistencias)) {
                $i = 1;
                foreach ($jogadores_assistencias as $jogador) {
                    echo "<tr>";
                    echo "<td>
                            <div class='clube'>
                                <span>$i</span> 
                                <img src='" . exibirImagem($jogador['imagem']) . "' alt='' class='logTime'>
                                <span class='nomeTime'>" . $jogador['nome'] . "</span>
                            </div>
                          </td>";
                    echo "<td><span class='dados'>" . $jogador['assistencias'] . "</span></td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='2'>Nenhum jogador encontrado</td></tr>";
            } ?>
        </table>

        <!-- Tabela de Cartões Amarelos -->
        <table class="tabela_center">
            <h1 class="grupos">Cartões Amarelos</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Cartões Amarelos</th>
            </tr>
            <?php if (!empty($jogadores_cartoes_amarelos)) {
                $i = 1;
                foreach ($jogadores_cartoes_amarelos as $jogador) {
                    echo "<tr>";
                    echo "<td>
                            <div class='clube'>
                                <span>$i</span> 
                                <img src='" . exibirImagem($jogador['imagem']) . "' alt='' class='logTime'>
                                <span class='nomeTime'>" . $jogador['nome'] . "</span>
                            </div>
                          </td>";
                    echo "<td><span class='dados'>" . $jogador['cartoes_amarelos'] . "</span></td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='2'>Nenhum jogador encontrado</td></tr>";
            } ?>
        </table>

        <!-- Tabela de Cartões Vermelhos -->
        <table class="tabela_center">
            <h1 class="grupos">Cartões Vermelhos</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Cartões Vermelhos</th>
            </tr>
            <?php if (!empty($jogadores_cartoes_vermelhos)) {
                $i = 1;
                foreach ($jogadores_cartoes_vermelhos as $jogador) {
                    echo "<tr>";
                    echo "<td>
                            <div class='clube'>
                                <span>$i</span> 
                                <img src='" . exibirImagem($jogador['imagem']) . "' alt='' class='logTime'>
                                <span class='nomeTime'>" . $jogador['nome'] . "</span>
                            </div>
                          </td>";
                    echo "<td><span class='dados'>" . $jogador['cartoes_vermelhos'] . "</span></td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='2'>Nenhum jogador encontrado</td></tr>";
            } ?>
        </table>
    </div>
</body>
</html>
