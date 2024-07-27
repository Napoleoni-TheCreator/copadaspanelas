<?php
// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    include '../../../config/conexao.php';

    // Dados do formulário
    $grupoId = $_POST['grupo_id']; // Assume que você obtém o ID do grupo selecionado

    // Consulta para obter a quantidade de equipes por grupo
    $configSql = "SELECT equipes_por_grupo FROM configuracoes LIMIT 1";
    $configResult = $conn->query($configSql);

    if ($configResult->num_rows > 0) {
        $configRow = $configResult->fetch_assoc();
        $maxTimesPerGroup = $configRow['equipes_por_grupo'];

        // Conta quantos times já existem no grupo selecionado
        $countSql = "SELECT COUNT(*) as count FROM times WHERE grupo_id = $grupoId";
        $countResult = $conn->query($countSql);
        $countRow = $countResult->fetch_assoc();
        $currentCount = $countRow['count'];

        // Verifica quantos times foram submetidos
        $numTimes = count($_POST['nome_time']);

        if (($currentCount + $numTimes) <= $maxTimesPerGroup) {
            // Loop para processar cada time
            for ($i = 0; $i < $numTimes; $i++) {
                // Dados do time atual
                $nomeTime = $_POST['nome_time'][$i];

                // Tratamento do upload da imagem
                $logoTime = file_get_contents($_FILES['logo_time']['tmp_name'][$i]); // Obtém o conteúdo binário da imagem
                $logoTime = addslashes($logoTime); // Escapa caracteres especiais para evitar problemas de SQL Injection

                // Inserção dos dados na tabela de times
                $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg) 
                        VALUES ('$nomeTime', '$logoTime', '$grupoId', 0, 0, 0, 0, 0, 0, 0)";

                if ($conn->query($sql) !== TRUE) {
                    echo "Erro ao adicionar time: " . $conn->error;
                    break; // Encerra o loop em caso de erro
                }
            }

            if ($i == $numTimes) {
                echo "Times adicionados com sucesso!";
            }
        } else {
            echo "Não é possível adicionar mais times. O grupo já contém o número máximo de times permitido.";
        }
    } else {
        echo "Erro ao obter a configuração de equipes por grupo.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Times</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            background-size: cover;
        }
        #main-content {
            flex: 1; /* Faz com que o conteúdo principal ocupe o espaço restante */
        }
        
        footer {
            background-color: rgb(27, 25, 25);
            width: 100%;
            position: relative;
            bottom: 0;
        }
        .titulo-barra{
            margin-top: 5%;
        }
        label {
            display: block;
            margin-bottom: 15px;
            font-size: 18px;
        }
        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #c60909;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #a60000;
        }
        /* Estilos padrão para o formulário */
        .formulario {
            display: flex;
            margin-bottom: 5%;
            height: auto;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 700px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .formulario{
                margin-top:10%;
                height:100%;
            }
            form {
                width: 300px;
                /* padding: 20px; */
                /* box-shadow: none; */
            }

            label {
                font-size: 16px;
            }

            input[type="text"],
            input[type="file"],
            select {
                width: 90%;
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 14px;
                padding: 10px 15px;
            }
        }

        @media (max-width: 480px) {
            form {
                padding: 15px;
            }

            label {
                font-size: 14px;
            }

            input[type="text"],
            input[type="file"],
            select {
                font-size: 12px;
            }

            input[type="submit"] {
                font-size: 12px;
                padding: 8px 10px;
            }
        }

    </style>
</head>
<body>
<?php include '../../../pages/header_classificacao.php'; ?>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../../../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="logo"></a>
            </div>
            <nav class="nav-icons">
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/rodadas_adm.php"><img src="../../../../public/img/header/rodadas.png" alt="rodadas"></a>
                    <span>Rodadas</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/tabela_de_classificacao.php"><img src="../../../../public/img/header/campo.png" alt="classificação"></a>
                    <span>Classificação</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/listar_times.php"><img src="../../../../public/img/header/classificados.png" alt="classificados"></a>
                    <span>editar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_dados_finais.php"><img src="../../../../public/img/header/oitavas.png" alt="finais"></a>
                    <span>editar finais</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/prancheta.svg" alt="prancheta"></a>
                    <span>Editar jogadores</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/grupo.svg" alt="grupos"></a>
                    <span>Criar grupos</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/adtime.svg" alt="adicinar timess"></a>
                    <span>Adicionar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/adadm.svg" alt="cadastro novos adm"></a>
                    <span>Adicionar outro adm</span>
                </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()">Modo Escuro</button>
        </div>
    </header>
    <div class="titulo-barra">
        <h1>Adicionar Times</h1>
    </div>
    <div class="formulario" id="main-content">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <!-- Repetição para dois times -->
            <?php for ($i = 0; $i < 2; $i++): ?>
                <label for="nome_time_<?php echo $i; ?>">Nome do Time <?php echo $i+1; ?>:</label>
                <input type="text" id="nome_time_<?php echo $i; ?>" name="nome_time[]" required>

                <label for="logo_time_<?php echo $i; ?>">Logo do Time <?php echo $i+1; ?>:</label>
                <input type="file" id="logo_time_<?php echo $i; ?>" name="logo_time[]" accept="image/*" required>
            <?php endfor; ?>

            <label for="grupo_id">Grupo:</label>
            <select id="grupo_id" name="grupo_id" required>
                <?php
                // Conexão com o banco de dados para carregar os grupos disponíveis
                include '../../../config/conexao.php';

                $sql = "SELECT id, nome FROM grupos ORDER BY nome";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . $row['nome'] . '</option>';
                    }
                } else {
                    echo '<option value="">Nenhum grupo encontrado</option>';
                }

                $conn->close();
                ?>
            </select>

            <input type="submit" value="Adicionar Times">
        </form>
    </div>
    <?php include "../../../pages/footer.php";  ?>
</body>
</html>
