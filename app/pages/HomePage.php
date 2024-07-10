<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/HomePage.css">
    <link rel="stylesheet" href="../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="../../public/imgs/ESCUDO COPA DAS PANELAS.png" type="image/x-icon">    
    <title>Copa das Panelas</title>
</head>
<body>
    <?php include 'header.php'?>
    <nav>
        <ul>
            <li><a href="../pages/HomePage.php">Home</a></li>

            <!--<li>
                <a href="">Cadastros ▾</a>
                <ul class="dropdown">
                    <li><a href="">Times</a></li>
                    <li><a href="">Jogadores</a></li>
                    <li><a href="">Info C</a></li>
                    <li><a href="">Info D</a></li>
                </ul>
            </li>-->
            <li>
                <a href="">Tabelas de Jogos ▾</a>
                <ul class="dropdown">
                   <li><a href="">Grupos</a></li>
                   <li><a href="">Eliminatórias</a></li>
                </ul>     
            </li>
            <li>
                <a href="">Dados da Copa ▾</a>
                <ul class="dropdown">
                    <li><a href="">História</a></li>
                    <li><a href="">Estatísticas</a></li>
                </ul>
            </li>
            <li><a href="Jogos Proximos.php">Transmissão</a></li>
            <li><a href="sobreNos.html">Sobre nós</a></li>
        </ul>
    </nav>

    <main>
        <section class="Slideshare">
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="../../public/imgs/pngtree-banner-background-of-football-colorful-game-publicity-poster-image_924290.jpg" style="width:100%">
                </div>
                <div class="mySlides fade">
                    <img src="../../public/imgs/TSF-2022-EH-1400x500-banner.png" style="width:100%">
                </div>
                <div class="mySlides fade">
                    <img src="../../public/imgs/Banner04-RBA-1400x500-1.jpg" style="width:100%">
                </div>
            </div>

        </section>
    <h3>NOTICIAS</h3>
    <hr>

        <section class="noticias">

            <div class="news-container">
                <div class="news-block">
                    <a href="https://www.example.com/noticia1" target="_blank">
                        <img src="noticia1.jpg" alt="Notícia 1">
                        <div class="news-text">
                            <h3>Título da Notícia 1</h3>
                            <p>Descrição breve da notícia 1.</p>
                        </div>
                    </a>
                </div>
                <div class="news-block">
                    <a href="https://www.example.com/noticia2" target="_blank">
                        <img src="noticia2.jpg" alt="Notícia 2">
                        <div class="news-text">
                            <h3>Título da Notícia 2</h3>
                            <p>Descrição breve da notícia 2.</p>
                        </div>
                    </a>
                </div>
                <div class="news-block">
                    <a href="https://www.example.com/noticia3" target="_blank">
                        <img src="../../public/imgs/neymar.jpeg" alt="Notícia 3">
                        <div class="news-text">
                            <h3>Título da Notícia 3</h3>
                            <p>Descrição breve da notícia 3.</p>
                        </div>
                    </a>
                </div>
                <div class="news-block">
                    <a href="https://www.example.com/noticia4" target="_blank">
                        <img src="noticia4.jpg" alt="Notícia 4">
                        <div class="news-text">
                            <h3>Título da Notícia 4</h3>
                            <p>Descrição breve da notícia 4.</p>
                        </div>
                    </a>
                </div>
            </div>

        </section>

    <H3>CONTEÚDO DA COPA</H3>
    <hr>

        <section class="conteudo">
            <div class="image-block">
                <img src="../../public/imgs/IMG-20240404-WA0002.jpg" alt="Imagem Descritiva">
            </div>
            <div class="text-block">
                <h2><a href="">Historia Copa das Panelas</a></h2>
                <p>Descrição detalhada sobre o conteúdo do bloco. Este texto fornece mais informações sobre o que é representado pela imagem e o contexto relevante.</p>
            </div>

            <div class="image-block">
                <img src="../../public/imgs/IMG-20240404-WA0002.jpg" alt="Imagem Descritiva">
            </div>
            <div class="text-block">
                <h2><a href="">Jogadores da Copa</a></h2>
                <p>Descrição detalhada sobre o conteúdo do bloco. Este texto fornece mais informações sobre o que é representado pela imagem e o contexto relevante.</p>
            </div>
        </section>

    </main>

    <?php include 'footer.php'?>

    <div id="countdown-balloon">
        <span id="close-btn">&times;</span>
        <div id="description">INICIO DA COPA DAS PANELAS</div>
        <div id="countdown">
            <div id="days">00</div> dias
            <div id="hours">00</div> horas
            <div id="minutes">00</div> minutos
            <div id="seconds">00</div> segundos
        </div>
    </div>

    <script src="../../public/js/homepage.js"></script>
</body>
</html>