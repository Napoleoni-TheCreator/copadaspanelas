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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Times</title>
    <style>
        body {
            height: 100vh;
            background-size: cover;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
        }
        /* Estilos para a barra de título */
        .titulo-barra {
            background-color: #fe0000;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            text-shadow: 3px 3px 3px black;
            font-size: 20px;
        }
        /* Estilos para o formulário */
        .formulario {
            display: flex;
            height: 85vh;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        form {
            max-width: 700px;
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
    </style>
</head>
<body>
    <div class="titulo-barra">
        <h1>Adicionar Times</h1>
    </div>
    <div class="formulario">
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
</body>
</html>
