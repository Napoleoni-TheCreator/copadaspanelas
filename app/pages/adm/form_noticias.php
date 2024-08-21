<?php
include '../../config/conexao.php';

function escapeString($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}

// Operações CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add']) && isset($_FILES['imagem'])) {
        $titulo = escapeString($conn, $_POST['titulo']);
        $descricao = escapeString($conn, $_POST['descricao']);
        $link = escapeString($conn, $_POST['link']);
        $imagem = addslashes(file_get_contents($_FILES['imagem']['tmp_name']));

        // Verifique o número de notícias
        $result = $conn->query("SELECT COUNT(*) as total FROM noticias");
        $row = $result->fetch_assoc();
        $totalNoticias = $row['total'];

        if ($totalNoticias >= 4) {
            // Substitua a notícia mais antiga
            $conn->query("DELETE FROM noticias ORDER BY data_adicao ASC LIMIT 1");
        }

        $sql = "INSERT INTO noticias (titulo, descricao, imagem, link) VALUES ('$titulo', '$descricao', '$imagem', '$link')";
        $conn->query($sql);
    } elseif (isset($_POST['update']) && isset($_FILES['imagem'])) {
        $id = escapeString($conn, $_POST['id']);
        $titulo = escapeString($conn, $_POST['titulo']);
        $descricao = escapeString($conn, $_POST['descricao']);
        $link = escapeString($conn, $_POST['link']);
        $imagem = addslashes(file_get_contents($_FILES['imagem']['tmp_name']));

        $sql = "UPDATE noticias SET titulo='$titulo', descricao='$descricao', imagem='$imagem', link='$link' WHERE id=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id = escapeString($conn, $_POST['id']);
        $sql = "DELETE FROM noticias WHERE id=$id";
        $conn->query($sql);
    }
}

$noticias = $conn->query("SELECT * FROM noticias ORDER BY data_adicao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/form_noticias.css">
    <title>Administração de Notícias</title>
</head>
<body>
    <header><img src="../../../public/img/ESCUDO COPA DAS PANELAS.png" alt=""></header>
    <h1>Administração de Notícias</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id">
        <label for="titulo">Título:</label><br>
        <input type="text" name="titulo" id="titulo" required><br>
        <label for="descricao">Descrição:</label><br>
        <textarea name="descricao" id="descricao" required></textarea><br>
        <label for="imagem">Imagem:</label><br>
        <input type="file" name="imagem" id="imagem" accept="image/*" required><br>
        <label for="link">Link:</label><br>
        <input type="text" name="link" id="link" required><br><br>
        <button type="submit" name="add">Adicionar</button>
        <button type="submit" name="update">Atualizar</button>
        <button type="submit" name="delete">Deletar</button>
    </form>
    <h2>Notícias Atuais</h2>
    <ul>
        <?php while($row = $noticias->fetch_assoc()): ?>
            <li>
                <strong><?php echo $row['titulo']; ?></strong><br>
                <?php echo $row['descricao']; ?><br>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo $row['titulo']; ?>" style="max-width: 100px;"><br>
                <a href="<?php echo $row['link']; ?>">Leia mais</a><br>
                <button onclick="editNoticia(<?php echo $row['id']; ?>, '<?php echo $row['titulo']; ?>', '<?php echo $row['descricao']; ?>', '<?php echo base64_encode($row['imagem']); ?>', '<?php echo $row['link']; ?>')">Editar</button>
            </li>
        <?php endwhile; ?>
    </ul>
    <script>
        function editNoticia(id, titulo, descricao, imagem, link) {
            document.getElementById('id').value = id;
            document.getElementById('titulo').value = titulo;
            document.getElementById('descricao').value = descricao;
            document.getElementById('imagem').value = ''; // Clear file input
            document.getElementById('link').value = link;
        }
    </script>
</body>
</html>