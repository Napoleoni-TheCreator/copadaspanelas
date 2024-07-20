<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Example</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            padding-top: 80px; /* Espaçamento para o cabeçalho fixo */
        }

        .header {
            background-color: red;
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
            height: 80px;
            margin-right: 10px;
        }

        .nav-icons {
            display: flex;
            flex: 1;
            justify-content: center;
            gap: 30px; /* Espaçamento entre os itens */
        }

        .nav-item {
            text-align: center;
        }

        .nav-item img {
            height: 40px;
            width: 40px;
        }

        .nav-item span {
            display: block;
            margin-top: 5px;
            color: white;
            font-size: 14px;
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
            </nav>
        </div>
    </header>
</body>
</html>
