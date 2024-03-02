<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmissão e Jogos</title>
    <script src="https://www.youtube.com/iframe_api"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        body {
            width: 100%;
            height: auto;
        }

        header {
            width: 100%;
            height: 70px;
            background-image: linear-gradient(to left top, #df1331, #9d000d);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            padding-left: 20px;
        }

        header img {
            width: 120px;
            height: 100px;
        }

        header a {
            text-decoration: none;
            color: white;
            margin-right: 20px;
        }

        .transmit {
            width: 100%;
            height: auto;
            background-color: rgba(0, 0, 0, 0.233);
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        main {
            width: 100%;
            min-height: 100vh;
        }

        .nxtGames {
            width: 100%;
            height: auto;
            background-color: rgb(255, 255, 255);
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .nxtGames .jogos {
            margin: 10px;
            display: flex;
            width: 400px;
            height: 200px;
            background-color: #00000028;
            justify-content: space-between;
            position: relative;
            border-radius: 10px;
        }

        .nxtGames .jogos .jogos-team1 {
            display: flex;
            flex-direction: column;
            width: 40%;
            align-items: center;
            margin-top: 20px;
        }

        .nxtGames .jogos .jogos-team1 .spc-logo {
            width: 50px;
            height: 50px;
        }

        .nxtGames .jogos .jogos-team1 .spc-logo img {
            height: 50px;
            width: 50px;
            max-width: inherit;
            object-fit: contain;
        }

        .nxtGames .jogos #versus {
            font-size: x-large;
            margin-top: 45px;
        }

        .nxtGames .jogos .jogos-team1 p {
            margin-top: 5px;
        }

        .situation {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px;
        }

        footer {
            font-size: larger;
            width: 100%;
            height: 130px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-around;
            background-image: linear-gradient(to left top, rgb(138, 6, 6), rgb(114, 17, 17));
            color: white;
        }

        .jogos {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .jogos:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    
    <header>
        <h1>Transmissão e jogos</h1>
        <img src="{{ asset('images/ESCUDO COPA DAS PANELAS.png') }}" alt="Logo Copa das panelas">
        <a href="HomePage.html">Voltar à Página Inicial</a>
    </header>
    <main>
        <section class="transmit">
            <div id="player"></div>
        </section>
        <h1 style="padding-left: 20px;">Jogos Próximos</h1>
        <section class="nxtGames">
            <div id="seg" class="jogos">
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/506.png') }}" alt="foto Time 2"></div>
                    <p>Juventus</p>
                </div>
                <div class="situation">
                    <h1>
                        Jogo Começa amanhã às 13h
                    </h1>
                </div>
            </div>
            <!-- Adicione os demais jogos aqui -->
            <div id="ter" class="jogos">
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/506.png') }}" alt="foto Time 2"></div>
                    <p>Juventus</p>
                </div>
                <div class="situation">
                    <h1>
                        Jogo Começa amanhã às 13h
                    </h1>
                </div>
            </div>
            <div id="qua" class="jogos">
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/506.png') }}" alt="foto Time 2"></div>
                    <p>Juventus</p>
                </div>
                <div class="situation">
                    <h1>
                        Jogo Começa amanhã às 13h
                    </h1>
                </div>
            </div>
            <div id="qui" class="jogos">
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1">
                    <div class="spc-logo"><img src="{{ asset('images/506.png') }}" alt="foto Time 2"></div>
                    <p>Juventus</p>
                </div>
                <div class="situation">
                    <h1>
                        Jogo Começa amanhã às 13h
                    </h1>
                </div>
            </div>
            
        </section>
    </main>
    <footer>
        <p id="copyright-text">©️ Todos os direitos reservados a Campus BJL</p>
        <p id="local-text">BR-349, Km 14 - Zona Rural, Bom Jesus da Lapa - BA, CEP: 47600-000</p>
    </footer>
</body>
<script src="https://www.youtube.com/iframe_api"></script>
<script src="{{ asset('js/script.js') }}"></script>
</html>
