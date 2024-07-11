<?php
include '../config/conexao.php';
$noticias = $conn->query("SELECT * FROM noticias ORDER BY data_adicao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas as Notícias</title>
</head>
<body>
    <h1>Todas as Notícias</h1>
    <ul>
        <?php while($row = $noticias->fetch_assoc()): ?>
            <li>
                <strong><?php echo $row['titulo']; ?></strong><br>
                <?php echo $row['descricao']; ?><br>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo $row['titulo']; ?>" style="max-width: 100px;"><br>
                <a href="<?php echo $row['link']; ?>">Leia mais</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
