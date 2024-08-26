<?php
// Função para gerar um token aleatório
function gerarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    include '../../config/conexao.php';

    // Dados do formulário
    $nomeTime = $_POST['nome_time'];
    $logoTime = file_get_contents($_FILES['logo_time']['tmp_name']); // Obtém o conteúdo binário da imagem
    $logoTime = addslashes($logoTime); // Escapa caracteres especiais para evitar problemas de SQL Injection

    // Gera um token para o novo time
    $token = gerarToken();

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
                $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg, token) 
                        VALUES ('$nomeTime', '$logoTime', '$grupoId', 0, 0, 0, 0, 0, 0, 0, '$token')";

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
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <link rel="stylesheet" href="../../../public/css/adm/cadastros_times_jogadores_adm/adicionar_times.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Time</title>
    
</head>
<body>
<?php 
require_once 'header_classificacao.php'
?>
<div class="main">
    <div class="titulo-barra">
        <h1>Adicionar times
        </h1>
    </div>
    <div class="formulario">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <label for="nome_time">Nome do Time:</label>
            <input type="text" id="nome_time" name="nome_time" required>

            <label for="logo_time">Logo do Time:</label>
            <input type="file" id="logo_time" name="logo_time" accept="image/*" required>

            <input type="submit" value="Adicionar Time">
        </form>
    </div>
</div>
<?php 
require_once '../footer.php'
?>
</body>
</html>
