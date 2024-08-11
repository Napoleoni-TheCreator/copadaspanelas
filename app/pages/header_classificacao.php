<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Example</title>
    <style>
        body {
            margin: 0;
            padding-top: 80px; /* Espaçamento para o cabeçalho fixo */
            transition: background-color 0.3s, color 0.3s; /* Transição suave */
        }

        .header {
            background-color: rgb(180, 0, 0);
            padding: 10px 0;
            width: 100%;
            position: fixed; /* Fixado no topo da página */
            top: 0;
            left: 0;
            z-index: 1000; /* Garantir que o header esteja acima de outros elementos */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Adiciona uma sombra ao cabeçalho */
        }

        .containerr {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .logo img {
            height: 60px; /* Ajustado para dispositivos menores */
            margin-right: 10px;
        }

        .nav-icons {
            display: flex;
            flex: 1;
            justify-content: center;
            gap: 20px; /* Espaçamento reduzido */
        }

        .nav-item {
            text-align: center;
        }

        .nav-item img {
            height: 30px; /* Ajustado para dispositivos menores */
            width: 30px;
        }

        .nav-item span {
            display: block;
            margin-top: 5px;
            color: white;
            font-size: 12px; /* Ajustado para dispositivos menores */
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

        /* Media queries para dispositivos menores */
        @media (max-width: 767px) {
            .logo img {
                height: 50px;
            }

            .nav-icons {
                gap: 15px;
            }

            .nav-item img {
                height: 25px;
                width: 25px;
            }

            .nav-item span {
                font-size: 10px;
            }

            .theme-toggle img {
                height: 35px;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 40px;
            }

            .nav-icons {
                gap: 10px;
            }

            .nav-item img {
                height: 20px;
                width: 20px;
            }

            .nav-item span {
                font-size: 8px;
            }

            .theme-toggle img {
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../pages/HomePage.php"><img src="../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
            </div>
            <nav class="nav-icons">
                <div class="nav-item">
                    <a href="../pages/rodadas.php"><img src="../../public/img/header/rodadas.png" alt="Soccer Icon"></a>
                    <span>Rodadas</span>
                </div>
                <div class="nav-item">
                    <a href="../pages/tabela_de_classificacao.php"><img src="../../public/img/header/campo.png" alt="Field Icon"></a>
                    <span>Classificação</span>
                </div>
                <div class="nav-item">
                    <a href="../pages/classificar.php"><img src="../../public/img/header/classificados.png" alt="Chess Icon"></a>
                    <span>Classificados</span>
                </div>
                <div class="nav-item">
                    <a href="../pages/exibir_finais.php"><img src="../../public/img/header/oitavas.png" alt="Trophy Icon"></a>
                    <span>Finais</span>
                </div>
                <div class="nav-item">
                    <a href="../pages/estatistica.php"><img src="../../public/img/header/prancheta.svg" alt="Trophy Icon"></a>
                    <span>Estatistica</span>
                </div>
            </nav>
            <div class="theme-toggle">
                <img id="theme-icon" src="../../public/img/header/modoescuro.svg" alt="Toggle Theme">
            </div>
        </div>
    </header>

    <script>
        // Função para alternar o modo escuro
        function toggleDarkMode() {
            var element = document.body;
            var icon = document.getElementById('theme-icon');
            element.classList.toggle("dark-mode");

            // Atualizar o ícone conforme o tema
            if (element.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                icon.src = '../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../public/img/header/modoescuro.svg';
            }
        }

        // Aplicar o tema salvo ao carregar a página
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

        // Adiciona o evento de clique para alternar o tema
        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
</body>
</html>
