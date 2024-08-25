<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Responsivo | GN</title>
    <style>
        :root {
            --cor-branca: #fff;
            --cor-vermelho: rgb(180, 0, 0);
            --cor-escura4: #1c1c1c;
            --cor-texto: #000;
            --cor-fundo: #fff;
        }

        .dark-mode {
            --cor-branca: #fff;
            --cor-vermelho: #cc0000;
            --cor-escura4: #1c1c1c;
            --cor-texto: #e0e0e0;
            --cor-fundo: #121212;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
        }

        .img_logo_header {
            width: 60px;
        }

        .header {
            background-color: var(--cor-vermelho);
            height: 4em;
            box-shadow: 1px 1px 4px var(--cor-escura4);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            padding: 0 5%;
            justify-content: space-between;
        }

        .logo_header {
            flex: 1;
        }

        .navegacao_header {
            display: flex;
            gap: 3em;
            align-items: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            transition: transform 0.3s ease;
        }

        .navegacao_header a {
            text-decoration: none;
            color: var(--cor-texto);
            transition: color 0.3s ease;
            font-weight: bold;
        }

        .navegacao_header a:hover {
            color: var(--cor-branca);
        }

        .ativo {
            padding: 10px;
            border-radius: 10px;
        }

        .btn_icon_header {
            background: transparent;
            border: none;
            color: var(--cor-branca);
            cursor: pointer;
            display: none;
        }
           /* Estilos para o modo escuro */
           .dark-mode {
            background: #121212;
            color: #ffffff;
        }

        .dark-mode .header {
            background-color: #333;
        }

        .dark-mode .nav-item span {
            color: #ffffff;
        }

        .dark-mode table {
            background: rgba(255, 255, 255, 0.1);
        }

        .dark-mode th {
            color: #00ff00; /* Verde */
            border-bottom: 3px solid #ff0000; /* Vermelho */
        }

        .dark-mode .dados {
            color: #ff0000; /* Vermelho */
        }

        .dark-mode .clube {
            color: #00ff00; /* Verde */
        }

        .theme-toggle {
            cursor: pointer;
        }

        .theme-toggle img {
            height: 40px; /* Ajuste o tamanho conforme necessário */
        }

        @media screen and (max-width: 768px) {
            .header {
                padding: 0 1em;
            }

            .logo_header {
                display: flex;
                justify-content: flex-end;
            }

            .navegacao_header {
                position: fixed;
                flex-direction: column;
                top: 0;
                background: var(--cor-vermelho);
                height: 100%;
                width: 35vw;
                padding: 1em;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 22;
            }

            .btn_icon_header {
                display: block;
                z-index: 23;
            }

            .overlay.active + .navegacao_header {
                transform: translateX(0);
            }
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 15;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        .submenu {
            display: none;
            position: absolute;
            left: 0;
            background: var(--cor-vermelho);
            min-width: 150px;
            border-radius: 5px;
            z-index: 22;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        .submenu a {
            color: var(--cor-branca);
            padding: 10px;
            display: block;
            text-decoration: none;
        }

        .submenu a:hover {
            background: var(--cor-escura4);
        }

        .has-submenu {
            position: relative;
        }

        .has-submenu:hover .submenu {
            display: block;
        }

        .theme-toggle {
            margin-left: auto;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    <div class="header" id="header">
        <button onclick="toggleSidebar()" class="btn_icon_header" id="btn_open">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>
        <div class="logo_header">
            <a href="../HomePage.php">            
                <img src="../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Escudo da CP" class="img_logo_header">
            </a>
        </div>
        <div class="navegacao_header" id="navegacao_header">
            <button onclick="toggleSidebar()" class="btn_icon_header" id="btn_close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
            <a href="../HomePage.php" class="ativo">Home</a>
            <div class="has-submenu">
                <a href="../rodadas.php">Rodadas</a>
                <div class="submenu">
                    <a href="rodadas_adm.php">Administrar Rodadas</a>
                    <a href="adicionar_grupo.php">Criar novo campeonato</a>
                    <a href="adicionar_times.php">Adicionar times</a>
                    <a href="editar_time.php">Editar times</a>
                    <a href="adicionar_times_de_forma_aleatoria.php">Adicionar times forma aleatória</a>
                </div>
            </div>
            <div class="has-submenu">
                <a href="../tabela_de_classificacao.php">Classificação</a>
                <div class="submenu">
                    <a href="../classificar.php">Classificados</a>
                </div>
            </div>
            <div class="has-submenu">
                <a href="../exibir_finais.php">Finais</a>
                <div class="submenu">
                    <a href="adicionar_dados_finais.php">Administrar finais</a>
                </div>
            </div>
            <div class="has-submenu">
                <a href="../estatistica.php">Estatísticas</a>
                <div class="submenu">
                    <a href="crud_jogador.php">Administrar jogadores</a>
                </div>
            </div>
            <div class="theme-toggle">
                <img id="theme-icon" src="../../public/img/header/modoescuro.svg" alt="Toggle Theme">
            </div>
        </div>
    </div>
    <script>
        const header = document.getElementById('header');
        const navegacaoHeader = document.getElementById('navegacao_header');
        const overlay = document.getElementById('overlay');
        let mostrarSidebar = false;

        function toggleSidebar() {
            mostrarSidebar = !mostrarSidebar;
            navegacaoHeader.style.transform = mostrarSidebar ? 'translateX(0)' : 'translateX(-100%)';
            overlay.classList.toggle('active', mostrarSidebar);
        }

        function closeSidebar(event) {
            if (mostrarSidebar && !navegacaoHeader.contains(event.target) && !header.contains(event.target) && !overlay.contains(event.target)) {
                toggleSidebar();
            }
        }

        document.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && mostrarSidebar) {
                toggleSidebar();
            }
        });

        // Função para alternar o modo escuro
        function toggleDarkMode() {
            var element = document.body;
            var icon = document.getElementById('theme-icon');
            element.classList.toggle("dark-mode");

            // Atualizar o ícone conforme o tema
            if (element.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                icon.src = '../../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../../public/img/header/modoescuro.svg';
            }
        }

        // Aplicar o tema salvo ao carregar a página
        document.addEventListener("DOMContentLoaded", function() {
            var theme = localStorage.getItem("theme");
            var icon = document.getElementById('theme-icon');
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                icon.src = '../../../public/img/header/modoclaro.svg';
            } else {
                icon.src = '../../../public/img/header/modoescuro.svg';
            }
        });

        // Adiciona o evento de clique para alternar o tema
        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
</body>
</html>
