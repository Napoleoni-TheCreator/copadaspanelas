<?php
require 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

// Processar o formulário de criação de campeonato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeCampeonato = $_POST['nome_campeonato'];

    // Verifica se o nome do campeonato foi fornecido
    if (empty($nomeCampeonato)) {
        $erro = "O nome do campeonato é obrigatório.";
    } else {
        // Insere o novo campeonato no banco de dados (inicialmente inativo)
        $sql = "INSERT INTO campeonatos (nome, ativo) VALUES (:nome, FALSE)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome' => $nomeCampeonato]);

        $sucesso = "Campeonato criado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Campeonato</title>
    <style>
        .mensagem {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
        }
        .erro {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Criar Campeonato</h1>

    <?php if (isset($erro)): ?>
        <div class="mensagem erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if (isset($sucesso)): ?>
        <div class="mensagem sucesso"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="nome_campeonato">Nome do Campeonato:</label>
        <input type="text" id="nome_campeonato" name="nome_campeonato" required>
        <button type="submit">Criar Campeonato</button>
    </form>

    <br>
    <a href="ativar_campeonato.php">Gerenciar Campeonatos</a> | 
    <a href="adicionar_grupos_times.php">Adicionar Grupos e Times</a>
</body>
</html>