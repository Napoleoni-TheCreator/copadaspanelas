<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$database = "copa";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Função para gerar token único
function gerarTokenUnico() {
    return bin2hex(random_bytes(16));
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Excluir Campeonato
    if (isset($_POST['excluir_campeonato'])) {
        $campeonato_id = $_POST['campeonato_id'];
        
        $tabelas = [
            'posicoes_jogadores', 'jogadores', 'jogos_fase_grupos', 'semifinais_confrontos',
            'quartas_de_final_confrontos', 'oitavas_de_final_confrontos', 'final_confrontos',
            'jogos_finais', 'fase_execucao', 'semifinais', 'quartas_de_final', 'oitavas_de_final',
            'configuracoes', 'times', 'jogos', 'grupos', 'noticias', 'linkinstagram', 'linklive'
        ];

        $conn->begin_transaction();
        try {
            foreach ($tabelas as $tabela) {
                $sql = "DELETE FROM $tabela WHERE campeonato_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $campeonato_id);
                $stmt->execute();
                $stmt->close();
            }
            
            $sql = "DELETE FROM campeonatos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $campeonato_id);
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            echo "<p style='color:green;'>Campeonato excluído com sucesso!</p>";
            header("Refresh:1");
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }

    // Ativar/Desativar Campeonato
    if (isset($_POST['ativar_campeonato'])) {
        $campeonato_id = $_POST['campeonato_id'];
        
        try {
            $sql = "UPDATE campeonatos SET ativo = 0";
            $conn->query($sql);
            
            $sql = "UPDATE campeonatos SET ativo = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $campeonato_id);
            $stmt->execute();
            $stmt->close();
            
            echo "<p style='color:green;'>Campeonato ativado com sucesso!</p>";
            header("Refresh:1");
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }
// ... (código anterior)

try {
    // 1. Excluir posições de jogadores vinculadas aos times do grupo
    $sql = "DELETE pp FROM posicoes_jogadores pp
            INNER JOIN jogadores j ON pp.jogador_id = j.id
            INNER JOIN times t ON j.time_id = t.id
            WHERE t.grupo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 2. Excluir jogadores dos times do grupo
    $sql = "DELETE j FROM jogadores j
            INNER JOIN times t ON j.time_id = t.id
            WHERE t.grupo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 3. Excluir jogos da fase de grupos
    $sql = "DELETE FROM jogos_fase_grupos WHERE grupo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 4. Excluir jogos de todas as fases (confrontos)
    $tabelas_jogos = [
        'jogos', 
        'jogos_finais', 
        'semifinais_confrontos',
        'quartas_de_final_confrontos', 
        'oitavas_de_final_confrontos',
        'final_confrontos'
    ];

    foreach ($tabelas_jogos as $tabela) {
        if (strpos($tabela, 'confrontos') !== false || $tabela === 'jogos_finais') {
            $sql = "DELETE t FROM $tabela t
                    WHERE EXISTS (SELECT 1 FROM times tm WHERE tm.grupo_id = ? AND tm.id = t.timeA_id)
                    OR EXISTS (SELECT 1 FROM times tm WHERE tm.grupo_id = ? AND tm.id = t.timeB_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $grupo_id, $grupo_id);
        } else {
            $sql = "DELETE t FROM $tabela t
                    WHERE EXISTS (SELECT 1 FROM times tm WHERE tm.grupo_id = ? AND tm.id = t.time_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grupo_id);
        }
        $stmt->execute();
        $stmt->close();
    }

    // 5. Excluir classificações (oitavas, quartas, etc.)
    $tabelas_classificacao = [
        'oitavas_de_final',
        'quartas_de_final',
        'semifinais',
        'final'
    ];

    foreach ($tabelas_classificacao as $tabela) {
        $sql = "DELETE t FROM $tabela t
                INNER JOIN times tm ON t.time_id = tm.id
                WHERE tm.grupo_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $stmt->close();
    }

    // 6. Excluir os times do grupo (ETAPA CRÍTICA)
    $sql = "DELETE FROM times WHERE grupo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 7. Agora sim, excluir o grupo
    $sql = "DELETE FROM grupos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo "<p style='color:green;'>Grupo excluído com sucesso!</p>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
}

    // Excluir Registro
    if (isset($_POST['excluir_registro'])) {
        $tabela = $_POST['table'];
        $id = $_POST['id'];
        
        try {
            $sql = "DELETE FROM $tabela WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo "<p style='color:green;'>Registro excluído!</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }

    // Editar Registro
    if (isset($_POST['editar_registro'])) {
        $tabela = $_POST['table'];
        $id = $_POST['id'];
        $campos = $_POST['fields'];
        
        try {
            $sets = [];
            $tipos = '';
            $valores = [];
            
            foreach ($campos as $campo => $valor) {
                $sets[] = "$campo = ?";
                $tipos .= 's';
                $valores[] = $valor;
            }
            
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
                $tipoArquivo = $_FILES['logo']['type'];
                
                if (!in_array($tipoArquivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de arquivo inválido. Use JPG, PNG ou GIF.");
                }

                $logo = file_get_contents($_FILES['logo']['tmp_name']);
                if ($logo === false) {
                    throw new Exception("Erro ao ler a imagem.");
                }
                
                $sets[] = "logo = ?";
                $tipos .= 's';
                $valores[] = $logo;
            }
            
            $sql = "UPDATE $tabela SET " . implode(', ', $sets) . " WHERE id = ?";
            $tipos .= 'i';
            $valores[] = $id;
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($tipos, ...$valores);
            $stmt->execute();
            
            echo "<p style='color:green;'>Alterações salvas!</p>";
            header("Refresh:1");
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }

    // Criar Campeonato
    if (isset($_POST['criar_campeonato'])) {
        $nome = $_POST['nome'];
        $data_inicio = $_POST['data_inicio'];
        $data_final = $_POST['data_final'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        
        try {
            if ($ativo) {
                $sql = "UPDATE campeonatos SET ativo = 0";
                $conn->query($sql);
            }
            
            $sql = "INSERT INTO campeonatos (nome, data_inicio, data_final, ativo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $data_inicio, $data_final, $ativo);
            $stmt->execute();
            echo "<p style='color:green;'>Campeonato criado!</p>";
            header("Refresh:1");
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }

    // Criar Time
    if (isset($_POST['criar_time'])) {
        $nomeTime = $_POST['nome_time'];
        $grupoId = $_POST['grupo_id'];
        $campeonatoId = $_POST['campeonato_id'];
        
        try {
            if (isset($_FILES['logo_time']) && $_FILES['logo_time']['error'] === UPLOAD_ERR_OK) {
                $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
                $tipoArquivo = $_FILES['logo_time']['type'];
                
                if (!in_array($tipoArquivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de arquivo não permitido.");
                }

                $logoTime = file_get_contents($_FILES['logo_time']['tmp_name']);
                if ($logoTime === false) {
                    throw new Exception("Erro ao ler o arquivo de imagem.");
                }
            } else {
                throw new Exception("Nenhuma imagem foi enviada.");
            }

            $token = gerarTokenUnico();
            $sql = "INSERT INTO times (nome, logo, grupo_id, pts, vitorias, empates, derrotas, gm, gc, sg, token, campeonato_id) 
                    VALUES (?, ?, ?, 0, 0, 0, 0, 0, 0, 0, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $nomeTime, $logoTime, $grupoId, $token, $campeonatoId);
            $stmt->execute();
            
            echo "<p style='color:green;'>Time criado com sucesso!</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }
}

// Obter campeonatos
$campeonatos = [];
$sql = "SELECT * FROM campeonatos ORDER BY id DESC";
$resultado = $conn->query($sql);
while($linha = $resultado->fetch_assoc()) {
    $campeonatos[] = $linha;
}

// Obter dados do campeonato selecionado
$campeonatoSelecionado = null;
$dadosCampeonato = [];

if (isset($_GET['campeonato_id']) && is_numeric($_GET['campeonato_id'])) {
    $campeonato_id = $_GET['campeonato_id'];
    
    $sql = "SELECT * FROM campeonatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $campeonato_id);
    $stmt->execute();
    $campeonatoSelecionado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $tabelasRelacionadas = [
        'grupos' => 'Grupos',
        'times' => 'Times',
        'jogos_fase_grupos' => 'Jogos da Fase de Grupos',
        'jogos_finais' => 'Jogos Finais',
        'configuracoes' => 'Configurações',
        'noticias' => 'Notícias',
        'linkinstagram' => 'Link Instagram',
        'linklive' => 'Link Live'
    ];
    
    foreach ($tabelasRelacionadas as $tabela => $nome) {
        $sql = "SELECT * FROM $tabela WHERE campeonato_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $campeonato_id);
        $stmt->execute();
        $dadosCampeonato[$tabela] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão Completa de Campeonatos</title>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        select { padding: 8px; width: 300px; }
        .btn { 
            padding: 8px 15px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin: 5px;
        }
        .delete { background: #dc3545; color: white; }
        .edit { background: #ffc107; color: black; }
        .new { background: #28a745; color: white; }
        .data-box { padding: 10px; margin: 10px 0; background: #f8f9fa; border: 1px solid #dee2e6; }
        pre { background: white; padding: 10px; border: 1px solid #ccc; }
        .edit-form { display: none; margin: 10px 0; padding: 15px; background: #fff; border: 1px solid #ddd; }
        .edit-form.active { display: block; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .logo-time { max-width: 100px; max-height: 100px; }
    </style>
    <script>
        function toggleEditForm(formId) {
            document.getElementById(formId).classList.toggle('active');
        }
    </script>
</head>
<body>
<?php require_once 'header_classificacao.php'?>
    <div class="container">
        <h1>Gestão de Campeonatos</h1>

        <!-- Criar Novo Campeonato -->
        <div class="section">
            <button onclick="toggleEditForm('novoCampeonato')" class="btn new">Novo Campeonato</button>
            <form method="post" id="novoCampeonato" class="edit-form">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>Data Início:</label>
                    <input type="date" name="data_inicio" required>
                </div>
                <div class="form-group">
                    <label>Data Final:</label>
                    <input type="date" name="data_final" required>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="ativo"> Ativo</label>
                </div>
                <button type="submit" name="criar_campeonato" class="btn new">Salvar</button>
                <button type="button" onclick="toggleEditForm('novoCampeonato')" class="btn delete">Cancelar</button>
            </form>
        </div>

        <!-- Seletor de Campeonato -->
        <div class="section">
            <form method="get">
                <select name="campeonato_id" onchange="this.form.submit()">
                    <option value="">Selecione um Campeonato</option>
                    <?php foreach ($campeonatos as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= isset($_GET['campeonato_id']) && $_GET['campeonato_id'] == $c['id'] ? 'selected' : '' ?>>
                        <?= $c['nome'] ?> (ID: <?= $c['id'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($campeonatoSelecionado): ?>
        <!-- Editar Campeonato -->
        <div class="section">
            <button onclick="toggleEditForm('editCampeonato')" class="btn edit">Editar Campeonato</button>
            <form method="post" id="editCampeonato" class="edit-form">
                <input type="hidden" name="table" value="campeonatos">
                <input type="hidden" name="id" value="<?= $campeonatoSelecionado['id'] ?>">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="fields[nome]" value="<?= htmlspecialchars($campeonatoSelecionado['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Data Início:</label>
                    <input type="date" name="fields[data_inicio]" value="<?= $campeonatoSelecionado['data_inicio'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Data Final:</label>
                    <input type="date" name="fields[data_final]" value="<?= $campeonatoSelecionado['data_final'] ?>" required>
                </div>
                <button type="submit" name="editar_registro" class="btn edit">Salvar</button>
                <button type="button" onclick="toggleEditForm('editCampeonato')" class="btn delete">Cancelar</button>
            </form>

            <!-- Ativar/Desativar Campeonato -->
            <form method="post">
                <input type="hidden" name="campeonato_id" value="<?= $campeonatoSelecionado['id'] ?>">
                <button type="submit" name="ativar_campeonato" class="btn new">
                    <?= $campeonatoSelecionado['ativo'] ? 'Desativar' : 'Ativar' ?>
                </button>
            </form>

            <!-- Excluir Campeonato -->
            <form method="post" onsubmit="return confirm('Confirma exclusão total?')">
                <input type="hidden" name="campeonato_id" value="<?= $campeonatoSelecionado['id'] ?>">
                <button type="submit" name="excluir_campeonato" class="btn delete">Excluir Tudo</button>
            </form>
        </div>

        <!-- Listar e Editar Dados -->
        <?php foreach ($tabelasRelacionadas as $tabela => $nome): ?>
            <div class="section">
                <h2><?= $nome ?></h2>
                <?php if (!empty($dadosCampeonato[$tabela])): ?>
                    <?php foreach ($dadosCampeonato[$tabela] as $registro): ?>
                        <div class="data-box">
                            <button onclick="toggleEditForm('edit_<?= $tabela ?>_<?= $registro['id'] ?>')" 
                                    class="btn edit">Editar</button>
                            
                            <form method="post" id="edit_<?= $tabela ?>_<?= $registro['id'] ?>" class="edit-form" enctype="multipart/form-data">
                                <input type="hidden" name="table" value="<?= $tabela ?>">
                                <input type="hidden" name="id" value="<?= $registro['id'] ?>">
                                <?php foreach ($registro as $campo => $valor): ?>
                                    <?php if (!in_array($campo, ['id', 'campeonato_id'])): ?>
                                        <div class="form-group">
                                            <label><?= ucfirst($campo) ?>:</label>
                                            <?php if (is_bool($valor)): ?>
                                                <input type="checkbox" name="fields[<?= $campo ?>]" 
                                                    value="1" <?= $valor ? 'checked' : '' ?>>
                                            <?php elseif (strpos($campo, 'data') !== false): ?>
                                                <input type="date" name="fields[<?= $campo ?>]" 
                                                    value="<?= htmlspecialchars($valor) ?>">
                                            <?php elseif ($campo === 'logo'): ?>
                                                <img src="data:image/jpeg;base64,<?= base64_encode($valor) ?>" class="logo-time">
                                                <input type="file" name="logo">
                                            <?php else: ?>
                                                <input type="text" name="fields[<?= $campo ?>]" 
                                                    value="<?= htmlspecialchars($valor) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <button type="submit" name="editar_registro" class="btn edit">Salvar</button>
                                <button type="button" 
                                        onclick="toggleEditForm('edit_<?= $tabela ?>_<?= $registro['id'] ?>')" 
                                        class="btn delete">Cancelar</button>
                            </form>

                            <pre><?= json_encode($registro, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                            
                            <form method="post">
                                <input type="hidden" name="table" value="<?= $tabela ?>">
                                <input type="hidden" name="id" value="<?= $registro['id'] ?>">
                                <button type="submit" name="excluir_registro" class="btn delete"
                                        onclick="return confirm('Confirmar exclusão?')">Excluir</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum registro encontrado</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php require_once '../footer.php'?>
</body>
</html>