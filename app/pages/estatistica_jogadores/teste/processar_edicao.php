<?php
include "../../../config/conexao.php";

if(isset($_POST['submit'])) {
    $jogador_id = $_POST['jogador_id'];
    
    // Obtém os novos valores dos campos a serem editados
    $nome = $_POST['nome'];
    $gols = $_POST['gols'];
    $assistencias = $_POST['assistencias'];
    $cartoes_amarelos = $_POST['cartoes_amarelos'];
    $cartoes_vermelhos = $_POST['cartoes_vermelhos'];
    
    // Verifica se a imagem foi enviada
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Lê o conteúdo da imagem e converte para binário
        $conteudo_imagem = addslashes(file_get_contents($_FILES['imagem']['tmp_name']));
        
        // Prepara a consulta SQL para atualizar a imagem do jogador
        $sql_imagem = "UPDATE jogadores SET imagem = '$conteudo_imagem' WHERE id = $jogador_id";
        
        if ($conn->query($sql_imagem) !== TRUE) {
            echo "Erro ao atualizar imagem: " . $conn->error;
        }
    }
    
    // Prepara a consulta SQL para atualizar os outros campos do jogador
    $sql = "UPDATE jogadores SET 
                nome = '$nome', 
                gols = '$gols', 
                assistencias = '$assistencias', 
                cartoes_amarelos = '$cartoes_amarelos', 
                cartoes_vermelhos = '$cartoes_vermelhos' 
            WHERE id = $jogador_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Dados do jogador atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados do jogador: " . $conn->error;
    }
}

$conn->close();
?>
