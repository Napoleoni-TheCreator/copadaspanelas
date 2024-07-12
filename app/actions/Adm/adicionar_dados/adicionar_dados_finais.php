<?php
include '../../../config/conexao.php';

// Obtém a fase final configurada
$sql = "SELECT fase_final FROM configuracoes ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fase_final = $row['fase_final'];
} else {
    die("Erro ao obter a fase final configurada.");
}

// Define a tabela a ser usada com base na fase final
$tabela_confrontos = "";
switch ($fase_final) {
    case 'oitavas':
        $tabela_confrontos = "oitavas_de_final_confrontos";
        break;
    case 'quartas':
        $tabela_confrontos = "quartas_de_final_confrontos";
        break;
    case 'semifinais':
        $tabela_confrontos = "semifinais_confrontos";
        break;
    case 'final':
        $tabela_confrontos = "final_confrontos";
        break;
    default:
        die("Fase final desconhecida.");
}

// Manipula a atualização individual dos gols
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_individual'])) {
    $id = $_POST['id'];
    $gols_marcados_timeA = $_POST['gols_marcados_timeA'];
    $gols_marcados_timeB = $_POST['gols_marcados_timeB'];

    // Verifica se é um empate
    if ($gols_marcados_timeA == $gols_marcados_timeB) {
        echo "<script>alert('Não é possível adicionar um empate.');</script>";
    } else {
        $gols_contra_timeA = $gols_marcados_timeB; // Gols contra do Time A é igual aos gols marcados do Time B
        $gols_contra_timeB = $gols_marcados_timeA; // Gols contra do Time B é igual aos gols marcados do Time A

        $sql_update = "UPDATE $tabela_confrontos SET 
                       gols_marcados_timeA = ?, gols_contra_timeA = ?, 
                       gols_marcados_timeB = ?, gols_contra_timeB = ? 
                       WHERE id = ?";

        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param('iiiii', $gols_marcados_timeA, $gols_contra_timeA, $gols_marcados_timeB, $gols_contra_timeB, $id);

        if ($stmt->execute()) {
            echo "Dados atualizados com sucesso!";
        } else {
            echo "Erro ao atualizar os dados: " . $conn->error;
        }
    }
}

// Obtém os confrontos da fase final configurada
$sql_confrontos = "SELECT * FROM $tabela_confrontos";
$result_confrontos = $conn->query($sql_confrontos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Dados Finais</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .form-inline {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-inline input[type=number] {
            width: 50px;
            margin: 0 5px;
        }
        .form-inline button {
            margin-left: 10px;
        }
        #input{
            width: 29px;
        }

        input[type=number] {
            -webkit-appearance: none; /* Remove os botões em navegadores baseados em WebKit (Chrome, Safari) */
            -moz-appearance: textfield !important; /* Remove os botões em Firefox */
            appearance: none; /* Remova os botões em navegadores que suportam a propriedade padrão */
        }

    </style>
</head>
<body>
    <h1>Adicionar Dados Finais - Fase: <?php echo ucfirst($fase_final); ?></h1>
    <form method="post" action="adicionar_dados_finais.php">
        <table>
            <tr>
                <th>Time A</th>
                <th>Gols Time A</th>
                <th></th>
                <th>Gols Time B</th>
                <th>Time B</th>
                <th>Ação</th>
            </tr>
            <?php
            if ($result_confrontos->num_rows > 0) {
                while ($row = $result_confrontos->fetch_assoc()) {
                    echo "<tr>";
                    echo "<form method='post' action='adicionar_dados_finais.php' class='form-inline'>";
                    echo "<td>" . htmlspecialchars($row['timeA_nome']) . "</td>";
                    echo "<td><input type='number' id = 'input' name='gols_marcados_timeA' value='" . htmlspecialchars($row['gols_marcados_timeA']) . "' required></td>";
                    echo "<td>vs</td>";
                    echo "<td><input type='number' id = 'input' name='gols_marcados_timeB' value='" . htmlspecialchars($row['gols_marcados_timeB']) . "' required></td>";
                    echo "<td>" . htmlspecialchars($row['timeB_nome']) . "</td>";
                    echo "<td>
                            <input type='hidden' id = 'input' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' name='atualizar_individual'>Atualizar</button>
                          </td>";
                    echo "</form>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum confronto encontrado.</td></tr>";
            }
            ?>
        </table>
    </form>
</body>
</html>

<?php
$conn->close();
?>
