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

// Buscar dados dos jogadores
$sql = "SELECT j.nome, j.gols, j.assistencias, j.cartoes_amarelos, j.cartoes_vermelhos, j.imagem, t.nome AS nome_time
        FROM jogadores j
        JOIN times t ON j.time_id = t.id";

$result = $conn->query($sql);

$jogadores = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jogadores[] = $row;
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
        background-image: url(ifbaiano.jpg);
        background-repeat: no-repeat;
        background-size: cover;
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        padding: 30px;
        align-items: center;
        overflow-x: auto;
        color: #ffffff;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        margin-bottom: 20px;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        text-align: right;
        padding: 10px;
        color: #ffffff;
    }
    th {
        border-radius: 3%;
        font-size: 20px;
        color: #00ffff;
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
        border-radius: 5px;
        margin-right: 20px;
    }
    .clube {
        display: flex;
        align-items: center; /* Centraliza verticalmente */
        margin-top: 10px;
        margin-bottom: 5px;
    }
    .nomeTime {
        color: #ffffff;
        margin-left: 10px; /* Adapte conforme necessário para ajustar a distância entre a imagem e o texto */
        font-weight: bold;
        font-size: 20px;
    }
    .dados {
        color: #00ffff;
        margin-right: 15px;
        font-size: 24px;
    }
    .grupos {
        font-size: 32px;
        font-family: Arial, sans-serif;
        color: #ffffff;
        margin-bottom: 50px;
        text-shadow: 2px 2px 4px #000000;
    }
    .clube {
        font-size: 30px;
        font-family: Arial, sans-serif;
        margin-left: 10px;
        color: #adff2f;
    }
    .center {
        width: 90%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        margin-top: 1%;
        border: 3px solid #adff2f;
        border-radius: 15px;
        background: rgba(0, 0, 0, 0.5);
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
        margin-bottom: 2%;
        margin-top: 2%;
        text-shadow: 2px 2px 4px #000000;
    }
</style>

</head>
<body>
        <?php include '../pages/header_classificacao.php'?>
        <h1 id ="tituloh1"> Estatísticas dos Jogadores</h1>
    <div class="container center">
        
        <table class="tabela_center">
            <h1 class="grupos">Gols</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Gols</th>
            </tr>
            <?php if (!empty($jogadores)) {
                $i = 1;
                foreach ($jogadores as $jogador) {
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

        <table class="tabela_center">
            <h1 class="grupos">Assistências</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Assistências</th>
            </tr>
            <?php if (!empty($jogadores)) {
                $i = 1;
                foreach ($jogadores as $jogador) {
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

        <table class="tabela_center">
            <h1 class="grupos">Cartões Amarelos</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Cartões Amarelos</th>
            </tr>
            <?php if (!empty($jogadores)) {
                $i = 1;
                foreach ($jogadores as $jogador) {
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

        <table class="tabela_center">
            <h1 class="grupos">Cartões Vermelhos</h1>
            <tr>
                <th class="titulo_th">Jogador</th>
                <th>Cartões Vermelhos</th>
            </tr>
            <?php if (!empty($jogadores)) {
                $i = 1;
                foreach ($jogadores as $jogador) {
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
