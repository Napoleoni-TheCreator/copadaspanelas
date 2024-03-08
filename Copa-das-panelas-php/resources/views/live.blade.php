<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmissão e Jogos</title>
    <script src="https://www.youtube.com/iframe_api"></script>
    <link rel="stylesheet" href="{{ asset('css/Live.css') }}">
</head>

<body>
    
    <header>
        <h1>Transmissão e jogos</h1>
        <img src="{{ asset('images/ESCUDO COPA DAS PANELAS.png') }}" alt="Logo Copa das panelas">
        <a href="/">Voltar à Página Inicial</a>
    </header>
    <main>
        <section class="transmit">
            <div id="player"></div>
        </section>
        <h1 style="padding-left: 20px;">Jogos Próximos</h1>
        <section class="nxtGames">
            <div id="seg" class="jogos">
                <div class="jogos-team1" id="team-1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1" id="team-2">
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
                <div class="jogos-team1" id="team-1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1" id="team-2">
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
                <div class="jogos-team1" id="team-1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1" id="team-2">
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
                <div class="jogos-team1" id="team-1">
                    <div class="spc-logo"><img src="{{ asset('images/2017_logobahia.png') }}" alt="Foto do time"></div>
                    <p>Bahia</p>
                </div>
                <p id="versus">X</p>
                <div class="jogos-team1" id="team-2">
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
<script src="{{ asset('js/script_live.js') }}"></script>
</html>
