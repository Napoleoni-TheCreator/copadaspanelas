<?php
require 'conexao.php';

// Busca todos os campeonatos
$sql = "SELECT * FROM campeonatos";
$stmt = $pdo->query($sql);
$campeonatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ativar/Desativar campeonato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $acao = $_POST['acao'];

    if ($acao === 'ativar') {
        // Desativa todos os campeonatos
        $pdo->query("UPDATE campeonatos SET ativo = FALSE");

        // Ativa o campeonato selecionado
        $sql = "UPDATE campeonatos SET ativo = TRUE WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    } elseif ($acao === 'desativar') {
        // Desativa o campeonato selecionado
        $sql = "UPDATE campeonatos SET ativo = FALSE WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    header("Location: ativar_campeonato.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ativar/Desativar Campeonato</title>
</head>
<body>
    <h1>Ativar/Desativar Campeonato</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($campeonatos as $campeonato): ?>
        <tr>
            <td><?= $campeonato['id'] ?></td>
            <td><?= $campeonato['nome'] ?></td>
            <td><?= $campeonato['ativo'] ? 'Ativo' : 'Inativo' ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $campeonato['id'] ?>">
                    <button type="submit" name="acao" value="ativar">Ativar</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $campeonato['id'] ?>">
                    <button type="submit" name="acao" value="desativar">Desativar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>