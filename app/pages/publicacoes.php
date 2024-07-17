<?php
include '../config/conexao.php';
$noticias = $conn->query("SELECT * FROM noticias ORDER BY data_adicao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/publi.css">
    <link rel="stylesheet" href="../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="../../public/imgs/ESCUDO COPA DAS PANELAS.png" type="image/x-icon">    
    <title>Todas as Notícias</title>
</head>
<body>
    <?php include '../pages/header.php'?>
    <h1>Todas as Notícias</h1>
    <div class="news-container">
        <?php while($row = $noticias->fetch_assoc()): ?>
            <div class="news-block">
                <a href="<?php echo $row['link']; ?>" target="_blank">
                    <div class="img-container">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['imagem']); ?>" alt="<?php echo $row['titulo']; ?>">
                    </div>
                    <div class="news-text">
                        <h3><?php echo $row['titulo']; ?></h3>
                        <p><?php echo $row['descricao']; ?></p>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php include 'footer.php'?>
</body>
</html>
