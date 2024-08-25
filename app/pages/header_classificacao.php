<?php
session_start(); // Inicia a sessão
// Verificar se o usuário está logado
$usuarioLogado = isset($_SESSION['admin_id']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Responsivo | GN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        .header {
            top: 0;
            left: 0;
            width: 100%;
        }
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

        #deslogar {
            font-size: 1.1em;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            color: var(--cor-branca);
            position: absolute;
            right: 2%; /* 5px da borda direita */
            top: 50%;
            transform: translateY(-50%); /* Centraliza verticalmente */
        }
        .theme-toggle {
            display: flex;
            align-items: center;
            position: absolute;
            right:19%;
            top: 50%;
            transform: translateY(-50%); /* Centraliza verticalmente */
        }
        #deslogar a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
        }

        #deslogar a i {
            padding-right: 8px;
            font-size: 1.2em;
        }

        #deslogar:hover {
            background-color: var(--cor-branca);
            transition: 0.8s;
        }

        #deslogar a:hover {
            color: rgb(150, 0, 0);
            transition: 0.3s;
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
        }

        .img_logo_header {
            width: 90px;
        }

        .header {
            background-color: var(--cor-vermelho);
            height: 6em; /* Aumenta a altura do header */
            box-shadow: 1px 1px 4px var(--cor-escura4);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative; /* Necessário para o posicionamento absoluto do #deslogar */
        }
        .navegacao_header {
            display: flex;
            gap: 3em;
            align-items: center;
            position: absolute;
            left: 35%;
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

        #ativo :hover {
            padding: 10px;
            background-color: white;
            padding: auto;
            color: var(--cor-texto);
            border-radius:4px;
        }

        .btn_icon_header {
            background: transparent;
            border: none;
            color: var(--cor-branca);
            cursor: pointer;
            display: none;
        }

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
                justify-content: center;
            }
            .img_logo_header{
                width: 40px;
                margin-left: 0;
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
            .theme-toggle img{
                width: 35px;
            }
            .overlay.active + .navegacao_header {
                transform: translateX(0);
            }
            #deslogar {
                right: 3%; 
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
            background: rgb(109, 0, 0);
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
        .submenu a:hover{
            background-color: #e0e0e0;
            color: var(--cor-texto);
            border-radius: 4px;
        }

        .has-submenu {
            position: relative;
        }

        .has-submenu:hover .submenu {
            display: block;
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
            <a href="HomePage.php">            
                <img src="../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Escudo da CP" class="img_logo_header">
            </a>
        </div>
        <div class="navegacao_header" id="navegacao_header">
            <button onclick="toggleSidebar()" class="btn_icon_header" id="btn_close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
            <div id="ativo" class="has-submenu">
                <a href="HomePage.php" id="ativo">Home</a>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="rodadas.php">Rodadas</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="../pages/adm/rodadas_adm.php">Administrar Rodadas</a>
                    <a href="../pages/adm/adicionar_grupo.php">Criar novo campeonato</a>
                    <a href="../pages/adm/adicionar_times.php">Adicionar times</a>
                    <a href="../pages/adm/editar_time.php">Editar times</a>
                    <a href="../pages/adm/adicionar_times_de_forma_aleatoria.php">Adicionar times forma aleatória</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="tabela_de_classificacao.php">Classificação</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="classificar.php">Classificados</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="exibir_finais.php">Finais</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="../pages/adm/adicionar_dados_finais.php">Administrar finais</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                     <a href="estatistica.php">Estatísticas</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="../pages/adm/crud_jogador.php">Administrar jogadores</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="theme-toggle">
            <img id="theme-icon" src="../../public/img/header/modoescuro.svg" alt="Toggle Theme">
        </div>
        <?php if ($usuarioLogado): ?>
        <div class="has-submenu" id="deslogar">
            <a href="../actions/cadastro_adm/logout.php">
                <i class="fas fa-user"></i> Deslogar
            </a>
        </div>
        <?php endif; ?>
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

        function toggleDarkMode() {
            var element = document.body;
            var icon = document.getElementById('theme-icon');
            element.classList.toggle("dark-mode");

            if (element.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                icon.src = '../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../public/img/header/modoescuro.svg';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var theme = localStorage.getItem("theme");
            var icon = document.getElementById('theme-icon');
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                icon.src = '../../public/img/header/modoclaro.svg';
            } else {
                icon.src = '../../public/img/header/modoescuro.svg';
            }
        });

        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
</body>
</html>
