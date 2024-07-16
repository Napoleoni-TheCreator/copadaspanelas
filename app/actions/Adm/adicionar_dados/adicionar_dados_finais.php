<?php
include '../../../config/conexao.php';

// Função para limpar o status das fases subsequentes
function limparFases($fase_atual) {
    global $conn;
    
    // Ordem das fases
    $ordem_fases = ['oitavas', 'quartas', 'semifinais', 'final'];
    
    // Encontra o índice da fase atual
    $indice_atual = array_search($fase_atual, $ordem_fases);
    
    if ($indice_atual !== false) {
        // Atualiza o status das fases subsequentes
        foreach ($ordem_fases as $indice => $fase) {
            if ($indice > $indice_atual) {
                $status = FALSE;
                $stmt = $conn->prepare("UPDATE fase_execucao SET executado = ? WHERE fase = ?");
                $stmt->bind_param("is", $status, $fase);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

// Função para atualizar o status das fases com base na fase selecionada
function atualizarFases($fase_selecionada) {
    global $conn;
    
    // Ordem das fases
    $ordem_fases = ['oitavas', 'quartas', 'semifinais', 'final'];
    
    // Encontra o índice da fase selecionada
    $indice_selecionado = array_search($fase_selecionada, $ordem_fases);
    
    if ($indice_selecionado !== false) {
        // Atualiza o status das fases
        foreach ($ordem_fases as $indice => $fase) {
            $status = ($indice < $indice_selecionado) ? TRUE : FALSE;
            $stmt = $conn->prepare("UPDATE fase_execucao SET executado = ? WHERE fase = ?");
            $stmt->bind_param("is", $status, $fase);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Função para verificar se uma fase já foi executada
function faseJaExecutada($fase) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM fase_execucao WHERE fase = ? AND executado = 1");
    $stmt->bind_param("s", $fase);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $executado = $row['count'] > 0;
    $stmt->close();
    return $executado;
}

// Função para inicializar as fases fixas na tabela
function inicializarFaseExecucao() {
    global $conn;
    $fases = ['oitavas', 'quartas', 'semifinais', 'final'];
    foreach ($fases as $fase) {
        // Verifica se a fase já existe na tabela
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM fase_execucao WHERE fase = ?");
        $stmt->bind_param("s", $fase);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            // Insere a fase somente se não existir
            $stmt = $conn->prepare("INSERT INTO fase_execucao (fase) VALUES (?)");
            $stmt->bind_param("s", $fase);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Inicializa as fases na tabela (evita duplicatas)
inicializarFaseExecucao();

// Processa a mudança de fase final
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fase_final'])) {
    $nova_fase_final = $_POST['fase_final'];

    // Atualiza a fase final na tabela de configurações
    $stmt = $conn->prepare("UPDATE configuracoes SET fase_final = ? WHERE id = (SELECT MAX(id) FROM configuracoes)");
    $stmt->bind_param("s", $nova_fase_final);
    $stmt->execute();
    $stmt->close();
    
    // Atualiza o status das fases
    atualizarFases($nova_fase_final);
}

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

// Define o fuso horário para o horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Manipula a atualização dos dados dos confrontos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_individual'])) {
    $id = $_POST['id'];
    $gols_marcados_timeA = $_POST['gols_marcados_timeA'];
    $gols_marcados_timeB = $_POST['gols_marcados_timeB'];

    // Determina o resultado com base nos gols marcados
    if ($gols_marcados_timeA > $gols_marcados_timeB) {
        $resultado_timeA = 'V'; // Vitória para o Time A
        $resultado_timeB = 'D'; // Derrota para o Time B
    } elseif ($gols_marcados_timeA < $gols_marcados_timeB) {
        $resultado_timeA = 'D'; // Derrota para o Time A
        $resultado_timeB = 'V'; // Vitória para o Time B
    } else {
        // Informa ao usuário que empates não são permitidos
        echo "<script>alert('Empates não são permitidos. Por favor, insira novos dados.');</script>";
        // Redireciona o usuário de volta para o formulário de entrada ou outra página relevante
        echo "<script>window.location.href = 'adicionar_dados_finais.php';</script>";
        exit; // Encerra a execução do script para evitar a inserção do empate
    }

    // Atualiza o confronto com os gols marcados e contra
    $sql_update = "UPDATE $tabela_confrontos SET 
                   gols_marcados_timeA = ?, gols_contra_timeB = ?, 
                   gols_marcados_timeB = ?, gols_contra_timeA = ? 
                   WHERE id = ?";

    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('iiiii', $gols_marcados_timeA, $gols_marcados_timeA, $gols_marcados_timeB, $gols_marcados_timeB, $id);

    if ($stmt->execute()) {
        // Obtém os dados do confronto
        $data_jogo = date('Y-m-d H:i:s'); // Data e hora atual no fuso horário de Brasília
        $stmt_select = $conn->prepare("SELECT timeA_id, timeB_id, timeA_nome, timeB_nome FROM $tabela_confrontos WHERE id = ?");
        $stmt_select->bind_param('i', $id);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();
        $row_confronto = $result_select->fetch_assoc();
        $stmt_select->close();

        // Debug: Verifique os valores de $resultado_timeA e $resultado_timeB
        echo "Debug - resultado_timeA: $resultado_timeA, resultado_timeB: $resultado_timeB\n";

        // Insere o resultado na tabela jogos_finais
        $stmt_insert = $conn->prepare("INSERT INTO jogos_finais (timeA_id, timeB_id, nome_timeA, nome_timeB, gols_marcados_timeA, gols_marcados_timeB, resultado_timeA, resultado_timeB, data_jogo, fase) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param('iissssssss', 
            $row_confronto['timeA_id'], $row_confronto['timeB_id'], 
            $row_confronto['timeA_nome'], $row_confronto['timeB_nome'], 
            $gols_marcados_timeA, $gols_marcados_timeB, 
            $resultado_timeA, $resultado_timeB, 
            $data_jogo, $fase_final
        );

        if ($stmt_insert->execute()) {
            echo "<script>alert('Dados atualizados com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao inserir dados: " . $stmt_insert->error . "');</script>";
        }
        $stmt_insert->close();
    } else {
        echo "<script>alert('Erro ao atualizar dados: " . $stmt->error . "');</script>";
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
        #input {
            width: 29px;
        }
        input[type=number] {
            -webkit-appearance: none; /* Remove os botões em navegadores baseados em WebKit (Chrome, Safari) */
            -moz-appearance: textfield !important; /* Remove os botões em Firefox */
            appearance: none; /* Remove os botões em navegadores que suportam a propriedade padrão */
        }
    </style>
    <script>
        function classificar() {
            var xhr = new XMLHttpRequest();
            // Ajuste o caminho para o arquivo PHP conforme necessário
            xhr.open('POST', '/copadaspanelas/app/actions/funcoes/classificar_teste.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Atualiza a página com a resposta do servidor
                    document.getElementById('resultado_classificacao').innerHTML = xhr.responseText;
                } else {
                    // Exibe uma mensagem de erro se a requisição falhar
                    alert('Erro ao classificar: ' + xhr.statusText);
                }
            };

            xhr.onerror = function() {
                alert('Erro ao classificar: Ocorreu um erro na requisição.');
            };

            // Envia a requisição
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('classificarButton').addEventListener('click', classificar);
            document.getElementById('atualizar').addEventListener('click',classificar)
        });
    </script>
</head>
<body>
    <h1>Adicionar Dados Finais - Fase: <?php echo ucfirst($fase_final); ?></h1>
    
    <!-- Formulário para selecionar e atualizar a fase final -->
    <form method="post" action="">
        <label for="fase_final">Selecionar Fase Final:</label>
        <select id="fase_final" name="fase_final" onchange="this.form.submit()">
            <option value="oitavas" <?php if ($fase_final == 'oitavas') echo 'selected'; ?>>Oitavas de Final</option>
            <option value="quartas" <?php if ($fase_final == 'quartas') echo 'selected'; ?>>Quartas de Final</option>
            <option value="semifinais" <?php if ($fase_final == 'semifinais') echo 'selected'; ?>>Semifinais</option>
            <option value="final" <?php if ($fase_final == 'final') echo 'selected'; ?>>Final</option>
        </select>
    </form>
    
    <!-- Tabela com confrontos -->
    <form method="post" action="">
        <table>
            <thead>
                <tr>
                    <th>Time A</th>
                    <th>Gols Time A</th>
                    <th>vs</th>
                    <th>Gols Time B</th>
                    <th>Time B</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_confrontos = $result_confrontos->fetch_assoc()) { ?>
                <tr>
                    <form method="post" action="">
                        <td><?php echo htmlspecialchars($row_confrontos['timeA_nome']); ?></td>
                        <td>
                            <input type="number" name="gols_marcados_timeA" value="<?php echo htmlspecialchars($row_confrontos['gols_marcados_timeA']); ?>" required>
                        </td>
                        <td>vs</td>
                        <td>
                            <input type="number" name="gols_marcados_timeB" value="<?php echo htmlspecialchars($row_confrontos['gols_marcados_timeB']); ?>" required>
                        </td>
                        <td><?php echo htmlspecialchars($row_confrontos['timeB_nome']); ?></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row_confrontos['id']); ?>">
                            <button type="submit" name="atualizar_individual" id= "atualizar">Atualizar</button>
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
    <!-- Botão para executar classificar.php sem abrir uma nova página -->
    <button id="classificarButton">Classificar</button>


</body>
</html>
