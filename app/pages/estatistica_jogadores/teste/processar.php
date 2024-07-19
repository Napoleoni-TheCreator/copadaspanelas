<?php
include "../../../config/conexao.php";

// Verifica se o formulário foi submetido
if(isset($_POST['submit'])) {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $gols = $_POST['gols'];
    $assistencias = $_POST['assistencias'];
    $cartoes_amarelos = $_POST['cartoes_amarelos'];
    $cartoes_vermelhos = $_POST['cartoes_vermelhos'];
    $imagem = $_FILES['imagem']['tmp_name'];

    if($imagem) {
        // Lê o conteúdo da imagem e converte para binário
        $conteudo_imagem = addslashes(file_get_contents($imagem));

        // Insere os dados no banco de dados
        $sql = "INSERT INTO jogadores (nome, gols, assistencias, cartoes_amarelos, cartoes_vermelhos, imagem) VALUES ('$nome', '$gols', '$assistencias', '$cartoes_amarelos', '$cartoes_vermelhos', '$conteudo_imagem')";
        if ($conn->query($sql) === TRUE) {
            // Obtém o ID do jogador inserido
            $jogador_id = $conn->insert_id;
            
            // Calcula as posições para gols
            $sql_posicao_gols = "UPDATE jogadores AS j1
                                 INNER JOIN (
                                     SELECT id, @posicao_gols := IF(@last_gols = gols, @posicao_gols, @posicao_gols + 1) AS posicao_gols, @last_gols := gols
                                     FROM (SELECT id, gols FROM jogadores ORDER BY gols DESC) AS j2, (SELECT @posicao_gols := 0, @last_gols := NULL) AS t
                                 ) AS t1 ON j1.id = t1.id
                                 SET j1.posicao_gols = t1.posicao_gols";
            
            // Calcula as posições para assistências
            $sql_posicao_assistencias = "UPDATE jogadores AS j1
                                          INNER JOIN (
                                              SELECT id, @posicao_assistencias := IF(@last_assistencias = assistencias, @posicao_assistencias, @posicao_assistencias + 1) AS posicao_assistencias, @last_assistencias := assistencias
                                              FROM (SELECT id, assistencias FROM jogadores ORDER BY assistencias DESC) AS j2, (SELECT @posicao_assistencias := 0, @last_assistencias := NULL) AS t
                                          ) AS t1 ON j1.id = t1.id
                                          SET j1.posicao_assistencias = t1.posicao_assistencias";
            
            // Calcula as posições para cartões amarelos
            $sql_posicao_cartoes_amarelos = "UPDATE jogadores AS j1
                                             INNER JOIN (
                                                 SELECT id, @posicao_cartoes_amarelos := IF(@last_cartoes_amarelos = cartoes_amarelos, @posicao_cartoes_amarelos, @posicao_cartoes_amarelos + 1) AS posicao_cartoes_amarelos, @last_cartoes_amarelos := cartoes_amarelos
                                                 FROM (SELECT id, cartoes_amarelos FROM jogadores ORDER BY cartoes_amarelos DESC) AS j2, (SELECT @posicao_cartoes_amarelos := 0, @last_cartoes_amarelos := NULL) AS t
                                             ) AS t1 ON j1.id = t1.id
                                             SET j1.posicao_cartoes_amarelos = t1.posicao_cartoes_amarelos";
            
            // Calcula as posições para cartões vermelhos
            $sql_posicao_cartoes_vermelhos = "UPDATE jogadores AS j1
                                              INNER JOIN (
                                                  SELECT id, @posicao_cartoes_vermelhos := IF(@last_cartoes_vermelhos = cartoes_vermelhos, @posicao_cartoes_vermelhos, @posicao_cartoes_vermelhos + 1) AS posicao_cartoes_vermelhos, @last_cartoes_vermelhos := cartoes_vermelhos
                                                  FROM (SELECT id, cartoes_vermelhos FROM jogadores ORDER BY cartoes_vermelhos DESC) AS j2, (SELECT @posicao_cartoes_vermelhos := 0, @last_cartoes_vermelhos := NULL) AS t
                                              ) AS t1 ON j1.id = t1.id
                                              SET j1.posicao_cartoes_vermelhos = t1.posicao_cartoes_vermelhos";
            
            if ($conn->query($sql_posicao_gols) === TRUE && 
                $conn->query($sql_posicao_assistencias) === TRUE && 
                $conn->query($sql_posicao_cartoes_amarelos) === TRUE && 
                $conn->query($sql_posicao_cartoes_vermelhos) === TRUE) {
                echo "Dados do jogador e imagem carregados com sucesso!";
            } else {
                echo "Erro ao calcular as posições dos jogadores: " . $conn->error;
            }
        } else {
            echo "Erro ao carregar dados do jogador e imagem: " . $conn->error;
        }
    } else {
        echo "Por favor, selecione uma imagem para fazer upload.";
    }
}

// Fecha a conexão
$conn->close();
?>
