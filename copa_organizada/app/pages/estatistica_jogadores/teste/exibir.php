<?php
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'teste2';

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se o formulário foi submetido
if(isset($_POST['submit'])) {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $gols = $_POST['gols'];
    $assistencias = $_POST['assistencias'];
    $cartoes_amarelos = $_POST['cartoes_amarelos'];
    $cartoes_vermelhos = $_POST['cartoes_vermelhos'];
    $imagem = $_FILES['imagem']['tmp_name'];

    if($imagem) {
        // Lê o conteúdo da imagem e converte para binário
        $conteudo_imagem = addslashes(file_get_contents($imagem));

        // Insere os dados no banco de dados
        $sql = "INSERT INTO jogadores (nome, gols, assistencias, cartoes_amarelos, cartoes_vermelhos, imagem) VALUES ('$nome', '$gols', '$assistencias', '$cartoes_amarelos', '$cartoes_vermelhos', '$conteudo_imagem')";
        if ($conn->query($sql) === TRUE) {
            echo "Dados do jogador e imagem inseridos com sucesso!";
        } else {
            echo "Erro ao carregar dados do jogador e imagem: " . $conn->error;
        }
    } else {
        echo "Por favor, selecione uma imagem para fazer upload.";
    }
}

// Recuperar dados dos jogadores para cada categoria, incluindo a imagem e a posição
$sql_gols = "SELECT posicao_gols AS posicao, nome, gols, imagem FROM jogadores ORDER BY gols DESC";
$sql_assistencias = "SELECT posicao_assistencias AS posicao, nome, assistencias, imagem FROM jogadores ORDER BY assistencias DESC";
$sql_cartoes_amarelos = "SELECT posicao_cartoes_amarelos AS posicao, nome, cartoes_amarelos, imagem FROM jogadores ORDER BY cartoes_amarelos DESC";
$sql_cartoes_vermelhos = "SELECT posicao_cartoes_vermelhos AS posicao, nome, cartoes_vermelhos, imagem FROM jogadores ORDER BY cartoes_vermelhos DESC";

$resultado_gols = $conn->query($sql_gols);
$resultado_assistencias = $conn->query($sql_assistencias);
$resultado_cartoes_amarelos = $conn->query($sql_cartoes_amarelos);
$resultado_cartoes_vermelhos = $conn->query($sql_cartoes_vermelhos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exibir Jogadores por Categoria</title>
    <!-- Estilos -->
    <link rel="stylesheet" href="/CLASSIFICACAO/css/app_css/telas_css/estatisticas_css/style.css">

</head>
<body>
    <h1>Tabela de Estatísticas dos Jogadores</h1>

    <!-- Tabela de Gols -->
    <h2>Tabela de Gols</h2>
    <table>
        <tr>
            <th>Posição</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Gols</th>
        </tr>
        <?php
        if ($resultado_gols->num_rows > 0) {
            while ($linha = $resultado_gols->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $linha['posicao']; ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($linha['imagem']); ?>" alt="Imagem"></td>
                    <td><?php echo $linha['nome']; ?></td>
                    <td><?php echo $linha['gols']; ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">Nenhum jogador encontrado.</td>
            </tr>
            <?php
        }
        ?>
    </table>

    <!-- Tabela de Assistências -->
    <h2>Tabela de Assistências</h2>
    <table>
        <tr>
            <th>Posição</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Assistências</th>
        </tr>
        <?php
        if ($resultado_assistencias->num_rows > 0) {
            while ($linha = $resultado_assistencias->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $linha['posicao']; ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($linha['imagem']); ?>" alt="Imagem"></td>
                    <td><?php echo $linha['nome']; ?></td>
                    <td><?php echo $linha['assistencias']; ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">Nenhum jogador encontrado.</td>
            </tr>
            <?php
        }
        ?>
    </table>

    <!-- Tabela de Cartões Amarelos -->
    <h2>Tabela de Cartões Amarelos</h2>
    <table>
        <tr>
            <th>Posição</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Cartões Amarelos</th>
        </tr>
        <?php
        if ($resultado_cartoes_amarelos->num_rows > 0) {
            while ($linha = $resultado_cartoes_amarelos->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $linha['posicao']; ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($linha['imagem']); ?>" alt="Imagem"></td>
                    <td><?php echo $linha['nome']; ?></td>
                    <td><?php echo $linha['cartoes_amarelos']; ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">Nenhum jogador encontrado.</td>
            </tr>
            <?php
        }
        ?>
    </table>

    <!-- Tabela de Cartões Vermelhos -->
    <h2>Tabela de Cartões Vermelhos</h2>
    <table>
        <tr>
            <th>Posição</th>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Cartões Vermelhos</th>
        </tr>
        <?php
        if ($resultado_cartoes_vermelhos->num_rows > 0) {
            while ($linha = $resultado_cartoes_vermelhos->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $linha['posicao']; ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($linha['imagem']); ?>" alt="Imagem"></td>
                    <td><?php echo $linha['nome']; ?></td>
                    <td><?php echo $linha['cartoes_vermelhos']; ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">Nenhum jogador encontrado.</td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
