<?php
// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    include '../../../config/conexao.php';

    // Dados do formulário
    $nomeTime = $_POST['nome_time'];
    $logoTime = file_get_contents($_FILES['logo_time']['tmp_name']); // Obtém o conteúdo binário da imagem
    $logoTime = addslashes($logoTime); // Escapa caracteres especiais para evitar problemas de SQL Injection

    // Consulta para obter a quantidade de equipes por grupo
    $configSql = "SELECT equipes_por_grupo FROM configuracoes LIMIT 1";
    $configResult = $conn->query($configSql);

    if ($configResult->num_rows > 0) {
        $configRow = $configResult->fetch_assoc();
        $maxTimesPerGroup = $configRow['equipes_por_grupo'];

        // Consulta para obter a lista de grupos e a quantidade atual de times em cada grupo
        $gruposSql = "
            SELECT g.id, g.nome, COALESCE(t.count, 0) as count
            FROM grupos g
            LEFT JOIN (SELECT grupo_id, COUNT(*) as count FROM times GROUP BY grupo_id) t
            ON g.id = t.grupo_id
        ";
        $gruposResult = $conn->query($gruposSql);

        if ($gruposResult->num_rows > 0) {
            // Criar um array de grupos com capacidade disponível
            $gruposDisponiveis = [];
            while ($row = $gruposResult->fetch_assoc()) {
                if ($row['count'] < $maxTimesPerGroup) {
                    $gruposDisponiveis[] = ['id' => $row['id'], 'nome' => $row['nome']];
                }
            }

            if (count($gruposDisponiveis) > 0) {
                // Embaralhar a lista de grupos disponíveis
                shuffle($gruposDisponiveis);

                // Seleciona o primeiro grupo disponível
                $grupoId = $gruposDisponiveis[0]['id'];

                // Inserção dos dados na tabela de times
                $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg) 
                        VALUES ('$nomeTime', '$logoTime', '$grupoId', 0, 0, 0, 0, 0, 0, 0)";

                if ($conn->query($sql) === TRUE) {
                    echo "Time adicionado com sucesso ao grupo " . $gruposDisponiveis[0]['nome'] . "!";
                } else {
                    echo "Erro ao adicionar time: " . $conn->error;
                }
            } else {
                echo "Não há grupos disponíveis com capacidade para mais times.";
            }
        } else {
            echo "Nenhum grupo encontrado.";
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
    <link rel="stylesheet" href="../../../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Time</title>
    <style>
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
        }
        /* Estilos para a barra de título */
        /* .titulo-barra {
            background-color: #fe0000;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            text-shadow: 3px 3px 3px black;
            font-size: 20px;
        } */
        /* Estilos para o formulário */
        .formulario {
            display: flex;
            height: calc(100vh - 60px); /* Ajusta a altura para deixar espaço para a barra de título */
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        form {
            max-width: 500px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.8); /* Fundo branco com transparência */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-bottom: 15px;
            font-size: 18px;
        }
        input[type="text"],
        input[type="file"] {
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
    </style>
</head>
<body>
    <!-- <div class="titulo-barra">
        <h1>Adicionar Time</h1>
    </div> -->
    <?php include "../../../pages/header.php";  ?>
    <div class="formulario">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <label for="nome_time">Nome do Time:</label>
            <input type="text" id="nome_time" name="nome_time" required>

            <label for="logo_time">Logo do Time:</label>
            <input type="file" id="logo_time" name="logo_time" accept="image/*" required>

            <input type="submit" value="Adicionar Time">
        </form>
    </div>
</body>
</html>
