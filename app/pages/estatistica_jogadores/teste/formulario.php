<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleção de Jogador e Edição</title>
</head>
<body>
    <h2>Seleção de Jogador e Edição</h2>
    <form action="processar_edicao.php" method="post" enctype="multipart/form-data">
        <label for="jogador_id">Selecione o jogador:</label>
        <select id="jogador_id" name="jogador_id">
            <?php
            include "../../../config/conexao.php";

            $sql = "SELECT id, nome FROM jogadores";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["nome"] . "</option>";
                }
            } else {
                echo "<option value=''>Nenhum jogador encontrado</option>";
            }
            $conn->close();
            ?>
        </select><br><br>
        
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome"><br><br>
        
        <label for="gols">Gols:</label>
        <input type="number" id="gols" name="gols" min="0"><br><br>
        
        <label for="assistencias">Assistências:</label>
        <input type="number" id="assistencias" name="assistencias" min="0"><br><br>
        
        <label for="cartoes_amarelos">Cartões Amarelos:</label>
        <input type="number" id="cartoes_amarelos" name="cartoes_amarelos" min="0"><br><br>
        
        <label for="cartoes_vermelhos">Cartões Vermelhos:</label>
        <input type="number" id="cartoes_vermelhos" name="cartoes_vermelhos" min="0"><br><br>
        
        <label for="imagem">Selecione uma nova imagem:</label>
        <input type="file" id="imagem" name="imagem"><br><br>
        
        <input type="submit" value="Editar Jogador" name="submit">
    </form>
</body>
</html>


<!-- <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Imagem e Dados do Jogador</title>
</head>
<body>
    <h2>Upload de Imagem e Dados do Jogador</h2>
    <form action="processar.php" method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome"><br><br>
        
        <label for="gols">Gols:</label>
        <input type="number" id="gols" name="gols" min="0"><br><br>
        
        <label for="assistencias">Assistências:</label>
        <input type="number" id="assistencias" name="assistencias" min="0"><br><br>
        
        <label for="cartoes_amarelos">Cartões Amarelos:</label>
        <input type="number" id="cartoes_amarelos" name="cartoes_amarelos" min="0"><br><br>
        
        <label for="cartoes_vermelhos">Cartões Vermelhos:</label>
        <input type="number" id="cartoes_vermelhos" name="cartoes_vermelhos" min="0"><br><br>
        
        Selecione uma imagem para fazer upload:
        <input type="file" name="imagem"><br><br>
        
        <input type="submit" value="Upload Imagem e Dados" name="submit">
    </form>
</body>
</html> -->
