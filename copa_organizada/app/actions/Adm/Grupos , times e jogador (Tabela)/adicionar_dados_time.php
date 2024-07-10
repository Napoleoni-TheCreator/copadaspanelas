<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Dados ao Time</title>
</head>
<body>
    <h1>Adicionar Dados ao Time</h1>

    <?php
    // Conexão com o banco de dados
    include '../../../config/conexao.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $timeId = $_POST['time_id'];
        $action = $_POST['action'];
        $golsMarcados = isset($_POST['gols_marcados']) ? (int)$_POST['gols_marcados'] : 0;
        $golsSofridos = isset($_POST['gols_sofridos']) ? (int)$_POST['gols_sofridos'] : 0;

        // Obtém os dados atuais do time
        $sql = "SELECT * FROM times WHERE id = $timeId";
        $result = $conn->query($sql);
        $time = $result->fetch_assoc();

        // Variável para armazenar o resultado do jogo
        $resultadoJogo = '';

        // Atualiza os dados conforme a ação
        switch ($action) {
            case 'add_vitoria':
                if ($golsMarcados > $golsSofridos) {
                    $time['vitorias'] += 1;
                    $time['pts'] += 3;
                    $time['gm'] += $golsMarcados;
                    $time['gc'] += $golsSofridos;
                    $resultadoJogo = 'V';
                } else {
                    echo "Erro: Gols marcados devem ser maiores que gols sofridos para uma vitória.";
                    exit();
                }
                break;
            case 'add_derrota':
                if ($golsSofridos > $golsMarcados) {
                    $time['derrotas'] += 1;
                    $time['gm'] += $golsMarcados;
                    $time['gc'] += $golsSofridos;
                    $resultadoJogo = 'D';
                } else {
                    echo "Erro: Gols sofridos devem ser maiores que gols marcados para uma derrota.";
                    exit();
                }
                break;
            case 'add_empate':
                if ($golsMarcados == $golsSofridos) {
                    $time['empates'] += 1;
                    $time['pts'] += 1;
                    $time['gm'] += $golsMarcados;
                    $time['gc'] += $golsSofridos;
                    $resultadoJogo = 'E';
                } else {
                    echo "Erro: Para adicionar um empate, os gols marcados e sofridos devem ser iguais.";
                    exit();
                }
                break;
        }

        // Recalcula o saldo de gols
        $time['sg'] = $time['gm'] - $time['gc'];

        // Atualiza os dados no banco de dados
        $sql = "UPDATE times SET
                pts = {$time['pts']},
                vitorias = {$time['vitorias']},
                empates = {$time['empates']},
                derrotas = {$time['derrotas']},
                gm = {$time['gm']},
                gc = {$time['gc']},
                sg = {$time['sg']}
                WHERE id = $timeId";

        if ($conn->query($sql) === TRUE) {
            // Insere o resultado do jogo na tabela jogos
            $sqlJogo = "INSERT INTO jogos (time_id, resultado, data_jogo) VALUES ($timeId, '$resultadoJogo', NOW())";
            if ($conn->query($sqlJogo) === TRUE) {
                echo "Dados do time e resultado do jogo atualizados com sucesso!";
            } else {
                echo "Erro ao registrar o resultado do jogo: " . $conn->error;
            }
        } else {
            echo "Erro ao atualizar os dados do time: " . $conn->error;
        }
    }
    ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="time_id">Selecione o Time:</label>
        <select id="time_id" name="time_id" required>
            <?php
            // Carrega os times disponíveis
            $sql = "SELECT id, nome FROM times ORDER BY nome";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['nome'] . '</option>';
                }
            } else {
                echo '<option value="">Nenhum time encontrado</option>';
            }
            ?>
        </select><br><br>

        <label for="gols_marcados">Gols Marcados:</label>
        <input type="number" id="gols_marcados" name="gols_marcados" min="0"><br><br>

        <label for="gols_sofridos">Gols Sofridos:</label>
        <input type="number" id="gols_sofridos" name="gols_sofridos" min="0"><br><br>

        <button type="submit" name="action" value="add_vitoria">Adicionar Vitória</button>
        <button type="submit" name="action" value="add_derrota">Adicionar Derrota</button>
        <button type="submit" name="action" value="add_empate">Adicionar Empate</button>
    </form>
    <script>
        // Verificação de campos para evitar envio incorreto
        document.querySelector('button[value="add_vitoria"]').addEventListener('click', function(event) {
            const golsMarcados = document.getElementById('gols_marcados').value;

            if (!golsMarcados) {
                alert('Para adicionar uma vitória, os gols marcados são obrigatórios.');
                event.preventDefault();
            }
        });

        document.querySelector('button[value="add_derrota"]').addEventListener('click', function(event) {
            const golsSofridos = document.getElementById('gols_sofridos').value;

            if (!golsSofridos) {
                alert('Para adicionar uma derrota, os gols sofridos são obrigatórios.');
                event.preventDefault();
            }
        });

        document.querySelector('button[value="add_empate"]').addEventListener('click', function(event) {
            const golsMarcados = document.getElementById('gols_marcados').value || 0;
            const golsSofridos = document.getElementById('gols_sofridos').value || 0;

            if (golsMarcados != golsSofridos) {
                alert('Para adicionar um empate, os gols marcados e sofridos devem ser iguais.');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
