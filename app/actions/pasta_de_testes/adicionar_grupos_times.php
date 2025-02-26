<?php
require 'conexao.php';

// Busca o campeonato ativo
$sql = "SELECT id FROM campeonatos WHERE ativo = TRUE LIMIT 1";
$stmt = $pdo->query($sql);
$campeonatoAtivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$campeonatoAtivo) {
    die("Nenhum campeonato ativo encontrado.");
}

$campeonatoId = $campeonatoAtivo['id'];

// Adicionar grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_grupo'])) {
    $nomeGrupo = $_POST['nome_grupo'];

    $sql = "INSERT INTO grupos (nome, campeonato_id) VALUES (:nome, :campeonato_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nomeGrupo, 'campeonato_id' => $campeonatoId]);
}

// Adicionar time
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_time'])) {
    $nomeTime = $_POST['nome_time'];
    $grupoId = $_POST['grupo_id'];

    $sql = "INSERT INTO times (nome, grupo_id, campeonato_id) VALUES (:nome, :grupo_id, :campeonato_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => $nomeTime, 'grupo_id' => $grupoId, 'campeonato_id' => $campeonatoId]);
}

// Busca todos os grupos do campeonato ativo
$sql = "SELECT id, nome FROM grupos WHERE campeonato_id = :campeonato_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['campeonato_id' => $campeonatoId]);
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Grupos e Times</title>
</head>
<body>
    <h1>Adicionar Grupos e Times</h1>

    <h2>Adicionar Grupo</h2>
    <form method="POST">
        <label for="nome_grupo">Nome do Grupo:</label>
        <input type="text" id="nome_grupo" name="nome_grupo" required>
        <button type="submit" name="adicionar_grupo">Adicionar Grupo</button>
    </form>

    <h2>Adicionar Time</h2>
    <form method="POST">
        <label for="nome_time">Nome do Time:</label>
        <input type="text" id="nome_time" name="nome_time" required>

        <label for="grupo_id">Grupo:</label>
        <select id="grupo_id" name="grupo_id" required>
            <?php foreach ($grupos as $grupo): ?>
                <option value="<?= $grupo['id'] ?>"><?= $grupo['nome'] ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="adicionar_time">Adicionar Time</button>
    </form>
</body>
</html>