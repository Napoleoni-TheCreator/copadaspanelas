<?php
include '../../../config/conexao.php';
// session_start(); 

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


// Processa o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_individual'])) {
    $id = $_POST['id'];
    $gols_marcados_timeA = $_POST['gols_marcados_timeA'];
    $gols_marcados_timeB = $_POST['gols_marcados_timeB'];

    if (empty($id) || !is_numeric($id)) {
        $_SESSION['error_message'] = "ID inválido.";
    } else {
        // Determina os gols contra
        $gols_contra_timeA = $gols_marcados_timeB;
        $gols_contra_timeB = $gols_marcados_timeA;

        // Determina o resultado com base nos gols marcados
        if ($gols_marcados_timeA > $gols_marcados_timeB) {
            $resultado_timeA = 'V'; // Vitória para o Time A
            $resultado_timeB = 'D'; // Derrota para o Time B
        } elseif ($gols_marcados_timeA < $gols_marcados_timeB) {
            $resultado_timeA = 'D'; // Derrota para o Time A
            $resultado_timeB = 'V'; // Vitória para o Time B
        } else {
            // Informa ao usuário que empates não são permitidos
            $_SESSION['error_message'] = "Empates não são permitidos. Atualização não realizada.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE $tabela_confrontos SET 
            gols_marcados_timeA = ?, gols_contra_timeB = ?, 
            gols_marcados_timeB = ?, gols_contra_timeA = ? 
            WHERE id = ?");
        if ($stmt === false) {
            $_SESSION['error_message'] = "Erro na preparação da consulta: " . $conn->error;
        } else {
            $stmt->bind_param("iiiii", 
                $gols_marcados_timeA, $gols_contra_timeB, 
                $gols_marcados_timeB, $gols_contra_timeA, 
                $id
            );

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Confronto atualizado com sucesso!";
                // Redireciona para a mesma página para evitar ressubmissão de formulário
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $_SESSION['error_message'] = "Erro ao atualizar o confronto: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// Obtém os confrontos para exibir na tabela
$sql_confrontos = "SELECT * FROM $tabela_confrontos";
$result_confrontos = $conn->query($sql_confrontos);

// Função para obter o nome do time pelo ID
function obterNomeTime($id_time) {
    global $conn;
    $stmt = $conn->prepare("SELECT nome FROM times WHERE id = ?");
    $stmt->bind_param("i", $id_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nome = $row['nome'];
    $stmt->close();
    return $nome;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Confrontos</title>
    <link rel="stylesheet" href="../../../../public/css/adm/adicionar_dados_finais.css">
    <link rel="stylesheet" href="../../../../public/css/adm/header_cl.css">
</head>
<body>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
            </div>
            <nav class="nav-icons">
            <div class="nav-item">
                <a href="../../Adm/adicionar_dados/rodadas_adm.php"><img src="../../../../public/img/header/rodadas.png" alt="Soccer Icon"></a>
                <span>Rodadas</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/adicionar_dados/tabela_de_classificacao.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
                <span>Classificação</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/cadastro_time/listar_times.php"><img src="../../../../public/img/header/classificados.png" alt="Chess Icon"></a>
                <span>editar times</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/adicionar_dados/adicionar_dados_finais.php"><img src="../../../../public/img/header/oitavas.png" alt="Trophy Icon"></a>
                <span>editar finais</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/header/prancheta.svg" alt="Trophy Icon"></a>
                <span>Editar jogadores</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/header/grupo.svg" alt="Trophy Icon"></a>
                <span>Criar grupos</span>
            </div>
            <div class="nav-item">
                <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/header/adtime.svg" alt="Trophy Icon"></a>
                <span>Adicionar times</span>
            </div>
            <div class="nav-item">
                <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/header/adadm.svg" alt="cadastro novos adm"></a>
                <span>Adicionar outro adm</span>
            </div>
        </nav>

            <div class="theme-toggle">
                <img id="theme-icon" src="../../../../public/img/header/modoescuro.svg" alt="Toggle Theme">
            </div>
        </div>
    </header>
    <script>
        // Função para alternar o modo escuro
        function toggleDarkMode() {
            var element = document.body;
            var icon = document.getElementById('theme-icon');
            element.classList.toggle("dark-mode");

            // Atualizar o ícone conforme o tema
            if (element.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                icon.src = '../../../../public/img/header/modoclaro.svg';
            } else {
                localStorage.setItem("theme", "light");
                icon.src = '../../../../public/img/header/modoescuro.svg';
            }
        }

        // Aplicar o tema salvo ao carregar a página
        document.addEventListener("DOMContentLoaded", function() {
            var theme = localStorage.getItem("theme");
            var icon = document.getElementById('theme-icon');
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                icon.src = '../../../../public/img/header/modoclaro.svg';
            } else {
                icon.src = '../../../../public/img/header/modoescuro.svg';
            }
        });

        // Adiciona o evento de clique para alternar o tema
        document.getElementById('theme-icon').addEventListener('click', toggleDarkMode);
    </script>
    <h1>Atualizar Confrontos para a Fase de <?php echo ucfirst($fase_final); ?></h1>

    <div class="form-container">
        <!-- Exibe a mensagem de erro ou sucesso -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <p class="error-message"><?php echo $_SESSION['error_message']; ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php elseif (isset($_SESSION['success_message'])): ?>
            <p class="success-message"><?php echo $_SESSION['success_message']; ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Formulário para selecionar a fase final -->
        <form method="post" action="">
            <label for="fase_final">Selecionar Fase Final:</label>
            <select id="fase_final" name="fase_final" onchange="this.form.submit()">
                <option value="oitavas" <?php if ($fase_final == 'oitavas') echo 'selected'; ?>>Oitavas de Final</option>
                <option value="quartas" <?php if ($fase_final == 'quartas') echo 'selected'; ?>>Quartas de Final</option>
                <option value="semifinais" <?php if ($fase_final == 'semifinais') echo 'selected'; ?>>Semifinais</option>
                <option value="final" <?php if ($fase_final == 'final') echo 'selected'; ?>>Final</option>
            </select>
        </form>

        <!-- Formulário para atualizar os confrontos -->
        <form method="post" action="">
            <?php if ($result_confrontos->num_rows > 0): ?>
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
                    <?php while ($row_confrontos = $result_confrontos->fetch_assoc()) { 
                        $nome_timeA = obterNomeTime($row_confrontos['timeA_id']);
                        $nome_timeB = obterNomeTime($row_confrontos['timeB_id']);
                    ?>
                    <tr>
                        <form method="post" action="">
                            <td><?php echo htmlspecialchars($nome_timeA); ?></td>
                            <td>
                                <input type="number" name="gols_marcados_timeA" value="<?php echo htmlspecialchars($row_confrontos['gols_marcados_timeA']); ?>" required>
                            </td>
                            <td>vs</td>
                            <td>
                                <input type="number" name="gols_marcados_timeB" value="<?php echo htmlspecialchars($row_confrontos['gols_marcados_timeB']); ?>" required>
                            </td>
                            <td><?php echo htmlspecialchars($nome_timeB); ?></td>
                            <td>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row_confrontos['id']); ?>">
                                <button id="butao" type="submit" name="atualizar_individual">Atualizar</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>Não existem times classificados para a fase.</p>
            <?php endif; ?>
        </form>
    </div>

<!-- Formulário para classificar os confrontos -->
<div class="form-container">
    <form id="classificacao-form" method="post" action="../../funcoes/classificar.php" target="result_frame">
        <h3>Deseja classificar os times para essa fase final?</h3>
        <p>Selecione uma opção:</p>
        <label>
            <input type="radio" name="opcao" value="sim" required>
            Sim, aperte o botão Classificar;
        </label>
        <label>
            <input type="radio" name="opcao" value="nao" required>
            Não, aperte o botão Classificar;
        </label>
        <button id="butao" type="submit" name="classificar">Classificar</button>
    </form>
    <!-- Frame para redirecionamento após classificação -->
    <iframe name="result_frame" style="display:none;"></iframe>
</div>

<script>
    document.getElementById('classificacao-form').addEventListener('submit', function(event) {
        // Obtém o valor selecionado
        var selecionado = document.querySelector('input[name="opcao"]:checked');
        
        // Verifica se a opção "Não" foi selecionada
        if (selecionado && selecionado.value === 'nao') {
            // Previne o envio do formulário
            event.preventDefault();
            // alert('Você selecionou "Não". O botão Classificar não será executado.');
        }
    });
</script>

</body>
</html>
