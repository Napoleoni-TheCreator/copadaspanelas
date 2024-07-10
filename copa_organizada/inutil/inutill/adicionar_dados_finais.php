<?php
include '../../app/config/conexao.php';

// Verifica a conexão com o banco de dados
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Define a fase final atual
$faseFinal = $_POST['fase_final'] ?? 'oitavas';
$tabelaConfrontos = '';

switch ($faseFinal) {
    case 'oitavas':
        $tabelaConfrontos = 'oitavas_de_final_confrontos';
        break;
    case 'quartas':
        $tabelaConfrontos = 'quartas_de_final_confrontos';
        break;
    case 'semifinais':
        $tabelaConfrontos = 'semifinais_confrontos';
        break;
    case 'final':
        $tabelaConfrontos = 'final_confrontos';
        break;
    default:
        die("Fase final desconhecida.");
}

// Função para atualizar dados do confronto
function atualizarConfronto($conn, $tabelaConfrontos, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB) {
    $golsContraTimeA = $golsMarcadosTimeB;
    $golsContraTimeB = $golsMarcadosTimeA;

    $sqlUpdate = "UPDATE $tabelaConfrontos
                  SET gols_marcados_timeA = ?, gols_marcados_timeB = ?, gols_contra_timeA = ?, gols_contra_timeB = ?
                  WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    if (!$stmtUpdate) {
        die("Erro na preparação da consulta de atualização: " . $conn->error);
    }
    $stmtUpdate->bind_param('iiiii', $golsMarcadosTimeA, $golsMarcadosTimeB, $golsContraTimeA, $golsContraTimeB, $confrontoId);
    if (!$stmtUpdate->execute()) {
        die("Erro ao atualizar confronto ID $confrontoId: " . $stmtUpdate->error);
    }
    $stmtUpdate->close();
}

// Função para inserir os dados dos jogos na tabela 'jogos'
function inserirJogo($conn, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB) {
    // Definir os resultados dos times
    $resultadoTimeA = ($golsMarcadosTimeA > $golsMarcadosTimeB) ? 'V' : (($golsMarcadosTimeA < $golsMarcadosTimeB) ? 'D' : 'E');
    $resultadoTimeB = ($golsMarcadosTimeB > $golsMarcadosTimeA) ? 'V' : (($golsMarcadosTimeB < $golsMarcadosTimeA) ? 'D' : 'E');
    
    // Obter ids dos times A e B (substituir pelos ids reais dos times no banco de dados)
    $timeA_id = $confrontoId * 2 - 1; // Exemplo de obtenção de id
    $timeB_id = $confrontoId * 2;     // Exemplo de obtenção de id
    
    // Inserir jogo para time A
    $sqlInsertJogoA = "INSERT INTO jogos_finais (time_id, resultado, data_jogo) VALUES (?, ?, NOW())";
    $stmtInsertJogoA = $conn->prepare($sqlInsertJogoA);
    if (!$stmtInsertJogoA) {
        die("Erro na preparação da consulta de inserção: " . $conn->error);
    }
    $stmtInsertJogoA->bind_param('is', $timeA_id, $resultadoTimeA);
    if (!$stmtInsertJogoA->execute()) {
        die("Erro ao inserir jogo para time A: " . $stmtInsertJogoA->error);
    }
    $stmtInsertJogoA->close();
    
    // Inserir jogo para time B
    $sqlInsertJogoB = "INSERT INTO jogos_finais (time_id, resultado, data_jogo) VALUES (?, ?, NOW())";
    $stmtInsertJogoB = $conn->prepare($sqlInsertJogoB);
    if (!$stmtInsertJogoB) {
        die("Erro na preparação da consulta de inserção: " . $conn->error);
    }
    $stmtInsertJogoB->bind_param('is', $timeB_id, $resultadoTimeB);
    if (!$stmtInsertJogoB->execute()) {
        die("Erro ao inserir jogo para time B: " . $stmtInsertJogoB->error);
    }
    
    $stmtInsertJogoB->close();
}
// Processa o formulário de adição de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confrontos'])) {
    $confrontos = $_POST['confrontos'];

    // Limpa os confrontos das fases posteriores e atualiza a fase final
    if ($faseFinal === 'oitavas') {
        $sqlDeleteQuartas = "DELETE FROM quartas_de_final_confrontos";
        $sqlDeleteSemifinais = "DELETE FROM semifinais_confrontos";
        $sqlDeleteFinal = "DELETE FROM final_confrontos";
        $fase_execucao = "DELETE FROM fase_execucao";
        // limpa as fase
        $sqlDeleteQuartasa = "DELETE FROM quartas_de_final";
        $sqlDeleteSemifinaisa = "DELETE FROM semifinais";
        $sqlDeleteFinala = "DELETE FROM final";

        $conn->query($sqlDeleteQuartas);
        $conn->query($sqlDeleteSemifinais);
        $conn->query($sqlDeleteFinal);
        $conn->query($sqlDeleteQuartasa);
        $conn->query($sqlDeleteSemifinaisa);
        $conn->query($sqlDeleteFinala);
        $conn->query($fase_execucao);
        // Atualiza a fase final para 'oitavas' na tabela de configurações
        $sqlUpdateFase = "UPDATE configuracoes SET fase_final = 'oitavas' WHERE id = 1";
        if (!$conn->query($sqlUpdateFase)) {
            die("Erro ao atualizar fase final: " . $conn->error);
        }
    } elseif ($faseFinal === 'quartas') {
        $sqlDeleteSemifinais = "DELETE FROM semifinais_confrontos";
        $sqlDeleteFinal = "DELETE FROM final_confrontos";
        // $sqlDeleteQuartasa = "DELETE FROM quartas_de_final_confrontos";  //corrigir para deletar todos os times 

        $sqlDeleteSemifinaisa = "DELETE FROM semifinais";
        $sqlDeleteFinala = "DELETE FROM final";


        $conn->query($sqlDeleteSemifinais);
        $conn->query($sqlDeleteFinal);
        // $conn->query($sqlDeleteQuartasa);
        $conn->query($sqlDeleteSemifinaisa);
        $conn->query($sqlDeleteFinala);

        // Atualiza a fase final para 'quartas' na tabela de configurações
        $sqlUpdateFase = "UPDATE configuracoes SET fase_final = 'quartas' WHERE id = 1";
        if (!$conn->query($sqlUpdateFase)) {
            die("Erro ao atualizar fase final: " . $conn->error);
        }
    } elseif ($faseFinal === 'semifinais') {
        $sqlDeleteFinal = "DELETE FROM final_confrontos";

        $conn->query($sqlDeleteFinal);

        // Atualiza a fase final para 'semifinais' na tabela de configurações
        $sqlUpdateFase = "UPDATE configuracoes SET fase_final = 'semifinais' WHERE id = 1";
        if (!$conn->query($sqlUpdateFase)) {
            die("Erro ao atualizar fase final: " . $conn->error);
        }
    }

    // Agora proceda com a atualização dos confrontos da fase atual (oitavas, quartas, etc.)
    foreach ($confrontos as $confrontoId => $dados) {
        $golsMarcadosTimeA = intval($dados['gols_marcados_timeA']);
        $golsMarcadosTimeB = intval($dados['gols_marcados_timeB']);

        // Atualiza o confronto
        atualizarConfronto($conn, $tabelaConfrontos, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB);

        // Insere os dados na tabela 'jogos'
        inserirJogo($conn, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB);
    }

    echo "<p>Dados atualizados com sucesso!</p>";
}

// Processa o formulário de adição de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confrontos'])) {
    $confrontos = $_POST['confrontos'];
    foreach ($confrontos as $confrontoId => $dados) {
        $golsMarcadosTimeA = intval($dados['gols_marcados_timeA']);
        $golsMarcadosTimeB = intval($dados['gols_marcados_timeB']);

        // Atualiza o confronto
        atualizarConfronto($conn, $tabelaConfrontos, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB);
        
        // Insere os dados na tabela 'jogos'
        inserirJogo($conn, $confrontoId, $golsMarcadosTimeA, $golsMarcadosTimeB);
    }

    echo "<p>Dados atualizados com sucesso!</p>";

    // Atualiza a fase final para a próxima fase se necessário
    if ($faseFinal === 'semifinais') {
        // Atualiza a fase final para 'final' se for a fase semifinal
        $sqlUpdateFase = "UPDATE configuracoes SET fase_final = 'final' WHERE id = 1";
        if (!$conn->query($sqlUpdateFase)) {
            die("Erro ao atualizar fase final: " . $conn->error);
        }
        echo "<p>Fase final atualizada para final!</p>";
    }
}

// Obtém os confrontos para exibir no formulário
$sqlConfrontos = "SELECT id, 
                          timeA_nome, 
                          timeB_nome,
                          gols_marcados_timeA,
                          gols_marcados_timeB
                   FROM $tabelaConfrontos";
$resultConfrontos = $conn->query($sqlConfrontos);
if (!$resultConfrontos) {
    die("Erro na consulta de confrontos: " . $conn->error);
}
$confrontos = [];
while ($row = $resultConfrontos->fetch_assoc()) {
    $confrontos[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Adicionar Dados dos Confrontos das Finais</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            max-width: 800px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <div class="form-group">
            <label for="fase_final">Selecione a Fase Final:</label>
            <select id="fase_final" name="fase_final" onchange="this.form.submit()">
                <option value="oitavas" <?= $faseFinal === 'oitavas' ? 'selected' : '' ?>>Oitavas de Final</option>
                <option value="quartas" <?= $faseFinal === 'quartas' ? 'selected' : '' ?>>Quartas de Final</option>
                <option value="semifinais" <?= $faseFinal === 'semifinais' ? 'selected' : '' ?>>Semifinais</option>
                <option value="final" <?= $faseFinal === 'final' ? 'selected' : '' ?>>Final</option>
            </select>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID do Confronto</th>
                    <th>Time A</th>
                    <th>Time B</th>
                    <th>Gols Marcados Time A</th>
                    <th>Gols Marcados Time B</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($confrontos as $confronto): ?>
                <tr>
                    <input type="hidden" name="confrontos[<?php echo htmlspecialchars($confronto['id']); ?>][id]" value="<?php echo htmlspecialchars($confronto['id']); ?>">
                    <td><?php echo htmlspecialchars($confronto['id']); ?></td>
                    <td><?php echo htmlspecialchars($confronto['timeA_nome']); ?></td>
                    <td><?php echo htmlspecialchars($confronto['timeB_nome']); ?></td>
                    <td><input type="number" name="confrontos[<?php echo htmlspecialchars($confronto['id']); ?>][gols_marcados_timeA]" value="<?php echo htmlspecialchars($confronto['gols_marcados_timeA']); ?>"></td>
                    <td><input type="number" name="confrontos[<?php echo htmlspecialchars($confronto['id']); ?>][gols_marcados_timeB]" value="<?php echo htmlspecialchars($confronto['gols_marcados_timeB']); ?>"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Atualizar Confrontos</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
