<?php
   include 'C:\xampp\htdocs\copa_organizada\app\config\conexao.php';

// Verifica a conexão com o banco de dados
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Define a tabela de acordo com a fase final
$tabelaConfrontos = '';
$faseFinal = $_POST['fase_final'] ?? 'oitavas';

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

// Processa o formulário de adição de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confrontos'])) {
    $confrontos = $_POST['confrontos'];
    foreach ($confrontos as $confrontoId => $dados) {
        $golsMarcadosTimeA = intval($dados['gols_marcados_timeA']);
        $golsMarcadosTimeB = intval($dados['gols_marcados_timeB']);
        // Calcula os gols contra
        $golsContraTimeA = $golsMarcadosTimeB;
        $golsContraTimeB = $golsMarcadosTimeA;

        // Usa mysqli_real_escape_string para escapar as entradas
        $confrontoId = mysqli_real_escape_string($conn, $confrontoId);
        $golsMarcadosTimeA = mysqli_real_escape_string($conn, $golsMarcadosTimeA);
        $golsMarcadosTimeB = mysqli_real_escape_string($conn, $golsMarcadosTimeB);
        $golsContraTimeA = mysqli_real_escape_string($conn, $golsContraTimeA);
        $golsContraTimeB = mysqli_real_escape_string($conn, $golsContraTimeB);

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

    echo "<p>Dados atualizados com sucesso!</p>";

    // Atualiza a fase final para semifinal se necessário
    if ($faseFinal === 'semifinais') {
        $sqlUpdateFase = "UPDATE configuracoes SET fase_final = 'semifinais' WHERE id = 1";
        if (!$conn->query($sqlUpdateFase)) {
            die("Erro ao atualizar fase final: " . $conn->error);
        }
        echo "<p>Fase final atualizada para semifinais!</p>";
    }

    // Resetar dados da semifinal se necessário
    if ($faseFinal === 'quartas') {
        $sqlResetSemifinal = "UPDATE semifinais_confrontos
                              SET gols_marcados_timeA = 0, gols_marcados_timeB = 0, gols_contra_timeA = 0, gols_contra_timeB = 0";
        if (!$conn->query($sqlResetSemifinal)) {
            die("Erro ao zerar dados da semifinal: " . $conn->error);
        }
        echo "<p>Dados da semifinal zerados com sucesso!</p>";
    }

    // Resetar dados da final se necessário
    if ($faseFinal === 'semifinais') {
        $sqlResetFinal = "UPDATE final_confrontos
                          SET gols_marcados_timeA = 0, gols_marcados_timeB = 0, gols_contra_timeA = 0, gols_contra_timeB = 0";
        if (!$conn->query($sqlResetFinal)) {
            die("Erro ao zerar dados da final: " . $conn->error);
        }
        echo "<p>Dados da final zerados com sucesso!</p>";
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
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        #form-wrapper {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            max-width: 800px;
            margin-top: 20%;
            margin-bottom: 20%;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .confronto {
            margin-bottom: 20px;
        }
        .confronto-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="number"] {
            width: 80px;
        }
        .form-group button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div id="form-wrapper">
        <h1>Adicionar Dados dos Confrontos das Finais</h1>
        <form method="post">
            <div class="form-group">
                <label for="fase_final">Selecione a Fase Final:</label>
                <select id="fase_final" name="fase_final" onchange="this.form.submit()">
                    <option value="oitavas" <?php if ($faseFinal === 'oitavas') echo 'selected'; ?>>Oitavas de Final</option>
                    <option value="quartas" <?php if ($faseFinal === 'quartas') echo 'selected'; ?>>Quartas de Final</option>
                    <option value="semifinais" <?php if ($faseFinal === 'semifinais') echo 'selected'; ?>>Semifinais</option>
                    <option value="final" <?php if ($faseFinal === 'final') echo 'selected'; ?>>Final</option>
                </select>
            </div>
            <?php foreach ($confrontos as $confronto): ?>
                <div class="confronto">
                    <div class="confronto-header">
                        Confronto: <?php echo htmlspecialchars($confronto['timeA_nome']) . ' x ' . htmlspecialchars($confronto['timeB_nome']); ?>
                    </div>
                    <div class="form-group">
                        <label for="gols_marcados_timeA_<?php echo $confronto['id']; ?>">Gols Marcados pelo Time A:</label>
                        <input type="number" id="gols_marcados_timeA_<?php echo $confronto['id']; ?>" name="confrontos[<?php echo $confronto['id']; ?>][gols_marcados_timeA]" min="0" value="<?php echo htmlspecialchars($confronto['gols_marcados_timeA']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="gols_marcados_timeB_<?php echo $confronto['id']; ?>">Gols Marcados pelo Time B:</label>
                        <input type="number" id="gols_marcados_timeB_<?php echo $confronto['id']; ?>" name="confrontos[<?php echo $confronto['id']; ?>][gols_marcados_timeB]" min="0" value="<?php echo htmlspecialchars($confronto['gols_marcados_timeB']); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-group">
                <button type="submit">Atualizar Dados</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
