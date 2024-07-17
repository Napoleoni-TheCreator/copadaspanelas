<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Gols Marcados</title>
</head>
<body>
    <h2>Adicionar Gols Marcados</h2>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label for="confronto">Selecione o Confronto:</label>
        <select id="confronto" name="confronto">
            <?php
            // Inclui o arquivo de configuração da conexão com o banco de dados
            include "../config/conexao.php";

            // Consulta SQL para obter os confrontos
            $sql = "SELECT id, nome_timeA, nome_timeB FROM jogos_fase_grupos";
            $result = $conn->query($sql);

            // Verifica se encontrou resultados
            if ($result->num_rows > 0) {
                // Exibe cada confronto como uma opção no menu suspenso
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['nome_timeA']) . ' vs ' . htmlspecialchars($row['nome_timeB']) . '</option>';
                }
            } else {
                echo '<option value="">Nenhum confronto encontrado</option>';
            }

            // Fecha a conexão com o banco de dados
            $conn->close();
            ?>
        </select><br><br>

        <label for="gols_timeA">Gols Marcados pelo Time A:</label>
        <input type="number" id="gols_timeA" name="gols_timeA" min="0"><br><br>
        
        <label for="gols_timeB">Gols Marcados pelo Time B:</label>
        <input type="number" id="gols_timeB" name="gols_timeB" min="0"><br><br>
        
        <input type="submit" name="submit" value="Adicionar Gols">
    </form>

    <?php
    // Processamento do formulário quando submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se o confronto foi selecionado e os campos foram preenchidos
        if (!empty($_POST['confronto']) && isset($_POST['gols_timeA']) && isset($_POST['gols_timeB'])) {
            // Coleta e sanitiza os dados do formulário
            $confronto_id = $_POST['confronto'];
            $gols_timeA = intval($_POST['gols_timeA']);
            $gols_timeB = intval($_POST['gols_timeB']);

            // Conexão com o banco de dados (novamente, se necessário)
            include "../config/conexao.php";

            // Prepara e executa a consulta SQL para atualizar os gols marcados
            $sql_update = "UPDATE jogos_fase_grupos SET gols_marcados_timeA = ?, gols_marcados_timeB = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("iii", $gols_timeA, $gols_timeB, $confronto_id);

            if ($stmt->execute()) {
                echo "<p>Gols marcados atualizados com sucesso!</p>";
            } else {
                echo "<p>Erro ao atualizar gols marcados: " . $stmt->error . "</p>";
            }

            // Fecha o statement
            $stmt->close();
            // Fecha a conexão com o banco de dados
            $conn->close();
        } else {
            echo "<p>Por favor, selecione um confronto e preencha os gols marcados.</p>";
        }
    }
    ?>
</body>
</html>
