<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/HomePage.css') }}">

    <title>COPA DAS PANELAS</title>
</head>
<body>
    
    <script>
        function playVideo() {
        const video = document.getElementById("video");
        video.play();
        }
    </script>
    
    <header>
        <div class="pika">
            <h1 id="Titulo">COPA DAS PANELAS</h1>

            <img src="{{ asset('images/ESCUDO COPA DAS PANELAS.png') }}" height="130.8px">
        </div>
        <nav>
            <ul>
                <li><a href="#">Página Principal</a></li>
                <li><a href="{{route('transmissão')}}">Transmissão</a></li>
                <li><a href="#">Campeonato</a></li>
                <li><a href="#">Jogadores</a></li>
                <li><a href="#">Sobre Nós</a></li>
            </ul>
        </nav>
    </header>

    <main>
        
        <div class="container-video">
            <video id="video" width="100%" height="200px" controls>
                <source src="{{ asset('videos/UEFA_INTRO.mp4') }}" type="video/mp4">
              </video>
        </div>
        <div class="container-lore">
            

            <div class="img-lore">
                <h2>Origem da Copa</h2>
                <img src="{{ asset('images/placeholder-bola.jpg') }}" alt="Foto dos Cria da CopadasPanela" width="350px" height="300px">
            </div>

            <div class="text-lore">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Rerum atque qui eveniet inventore ex quis hic reiciendis quae reprehenderit, vel architecto rem deleniti dolorem voluptates praesentium tenetur eos nobis eligendi. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Omnis, eum labore fugit totam repellendus dignissimos debitis incidunt veniam non doloremque qui eveniet, beatae a. Rem voluptate perferendis tempora iure officia.</p>

                <a href="#">Saiba Mais</a>
            </div>
        </div>

        <div class="container-lore">
            
            
            <div class="img-lore">
                <h2>Galeria de Fotos</h2>
                <img src="{{ asset('images/placeholder-bola.jpg') }}" alt="Galeria de Fotos da Copa das Panelas" width="350px" height="300px">
            </div>

            <div class="text-lore">
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias incidunt optio possimus corporis, sint, similique dignissimos laudantium delectus facere velit sunt fugiat harum doloremque. Eos eum nostrum velit et officiis! Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel quo, neque culpa qui voluptatem omnis, rem adipisci itaque impedit inventore asperiores. Optio assumenda suscipit beatae, vel vitae veritatis? Sapiente, iure.</p>
            

            <a href="#">Saiba Mais</a>
            </div>
        </div>

    </main>

    <footer>
        <p id="copyright-text">©️ Todos os direitos reservados a Campus BJL</p>
        <p id="local-text">BR-349, Km 14 - Zona Rural,<br> Bom Jesus da Lapa - BA,<br>CEP: 47600-000</p>

        <div class="redes-sociais">
            <p>Redes Sociais</p>
            <hr>    
            <a href="https://www.instagram.com/pan_cup/"><img src="{{ asset('images/instagram-removebg-preview.png') }}" alt="" width="40px" height="40px"></a>
        </div>
    </footer>
</body>
</html>