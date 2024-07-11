<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Grupo</title>
</head>
<body>
    <h1>Adicionar Grupo</h1>
    
    <!-- Formulário para configurar grupos -->
    <form id="formConfiguracao" method="post" action="">
        <fieldset>
            <legend>Configuração dos Grupos:</legend>

            <label for="equipesPorGrupo">Número de equipes por grupo (máx 16):</label>
            <input type="number" id="equipesPorGrupo" name="equipesPorGrupo" min="1" max="16" required><br>
            
            <label for="numeroGrupos">Número de grupos (máx 18):</label>
            <input type="number" id="numeroGrupos" name="numeroGrupos" min="1" max="18" required><br>

            <label for="faseFinal">Fase Final:</label>
            <select id="faseFinal" name="faseFinal" required>
                <option value="oitavas">Oitavas de Final</option>
                <option value="quartas">Quartas de Final</option>
            </select><br>
            
            <button type="submit">Calcular e Criar Grupos</button>
        </fieldset>
    </form>

    <div id="mensagem"></div>
    <div id="grupos">
        <?php
        include 'C:\xampp\htdocs\copa_organizada\app\config\conexao.php';

        // Verifica se o formulário foi submetido
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipesPorGrupo']) && isset($_POST['numeroGrupos']) && isset($_POST['faseFinal'])) {
            $equipesPorGrupo = intval($_POST['equipesPorGrupo']);
            $numeroGrupos = intval($_POST['numeroGrupos']);
            $faseFinal = $_POST['faseFinal'];

            // Define os limites máximos e mínimos
            $MAX_EQUIPES_POR_GRUPO = 16;
            $MAX_GRUPOS = 18;
            $MIN_TIMES_OITAVAS = 16;
            $MIN_TIMES_QUARTAS = 8;

            // Verifica se os valores estão dentro dos limites permitidos
            if ($equipesPorGrupo > $MAX_EQUIPES_POR_GRUPO || $numeroGrupos > $MAX_GRUPOS) {
                echo "<p>O número máximo de equipes por grupo é 16 e o número máximo de grupos é 18.</p>";
            } else {
                // Calcula o número total de equipes
                $totalEquipes = $equipesPorGrupo * $numeroGrupos;

                // Verifica se o total de equipes é suficiente para a fase final selecionada
                if (($faseFinal === 'oitavas' && $totalEquipes < $MIN_TIMES_OITAVAS) || 
                    ($faseFinal === 'quartas' && $totalEquipes < $MIN_TIMES_QUARTAS)) {
                    $minimo = ($faseFinal === 'oitavas') ? $MIN_TIMES_OITAVAS : $MIN_TIMES_QUARTAS;
                    echo "<p>Não é possível criar grupos. O número total de equipes deve ser pelo menos $minimo para a fase final selecionada.</p>";
                } else {
                    // Atualiza as configurações no banco de dados
                    $sql = "REPLACE INTO configuracoes (id, equipes_por_grupo, numero_grupos, fase_final) VALUES (1, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iis", $equipesPorGrupo, $numeroGrupos, $faseFinal);
                    $stmt->execute();

                    // Remove grupos existentes antes de criar novos
                    $conn->query("DELETE FROM grupos");

                    // Verifica se o total de equipes pode ser dividido igualmente nos grupos
                    if ($equipesPorGrupo > 0 && $numeroGrupos > 0 && ($totalEquipes % $numeroGrupos) === 0) {
                        // Cria grupos novos
                        for ($i = 1; $i <= $numeroGrupos; $i++) {
                            $nomeGrupo = "Grupo " . chr(64 + $i); // Gera nomes de grupos como A, B, C, etc.
                            $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";
                            $conn->query($sql);
                        }
                        echo "<p>Grupos criados com sucesso!</p>";
                    } else {
                        echo "<p>Não é possível criar os grupos com a quantidade de equipes fornecida. Cada grupo deve ter a mesma quantidade de equipes.</p>";
                    }

                    // Exibe os grupos
                    $sql = "SELECT nome FROM grupos ORDER BY nome";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div>';
                            echo '<h3>' . $row['nome'] . '</h3>';
                            echo '<table>';
                            echo '<tr>';
                            echo '<th>Clube</th>';
                            echo '<th>Pts</th>';
                            echo '<th>VIT</th>';
                            echo '<th>E</th>';
                            echo '<th>DER</th>';
                            echo '<th>GM</th>';
                            echo '<th>GC</th>';
                            echo '<th>SG</th>';
                            echo '<th>Últimas 5</th>';
                            echo '</tr>';
                            // Aqui você pode adicionar as linhas da tabela com os dados dos clubes, se necessário
                            echo '</table>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>Nenhum grupo encontrado.</p>";
                    }
                }
            }
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
