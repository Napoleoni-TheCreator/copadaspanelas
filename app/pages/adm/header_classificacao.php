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
    <link rel="stylesheet" href="../../../public/css/header_princ.css">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
            <div id="ativo" class="has-submenu">
                <a href="../HomePage.php" id="ativo">Home</a>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="../rodadas.php">Rodadas</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="rodadas_adm.php">Administrar Rodadas</a>
                    <a href="adicionar_grupo.php">Criar novo campeonato</a>
                    <!-- <a href="salvar_historico.php">Salvar historico</a> -->
                    <a href="adicionar_times.php">Adicionar times</a>
                    <a href="editar_time.php">Editar times</a>
                    <a href="adicionar_times_de_forma_aleatoria.php">Adicionar times forma aleatória</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="../tabela_de_classificacao.php">Classificação</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="../classificar.php">Classificados</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                    <a href="../exibir_finais.php">Finais</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="adicionar_dados_finais.php">Administrar finais</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="has-submenu">
                <div id="ativo" >
                     <a href="../estatistica.php">Estatísticas</a>
                </div>
                <?php if ($usuarioLogado): ?>
                <div class="submenu">
                    <a href="crud_jogador.php">Administrar jogadores</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="theme-toggle">
            <img id="theme-icon" src="../../../public/img/header/modoescuro.svg" alt="Toggle Theme">
        </div>
        <?php if ($usuarioLogado): ?>
        <div class="has-submenu" id="deslogar">
            <a href="../../actions/cadastro_adm/logout.php">
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
                icon.src = '../../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../../public/img/header/modoescuro.svg';
            }
        }

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

        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
</body>
</html>
