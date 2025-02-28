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

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Excluir Campeonato
    if (isset($_POST['excluir_campeonato'])) {
        $campeonato_id = $_POST['campeonato_id'];
        
        $tables = [
            'posicoes_jogadores', 'jogadores', 'jogos_fase_grupos', 'semifinais_confrontos',
            'quartas_de_final_confrontos', 'oitavas_de_final_confrontos', 'final_confrontos',
            'jogos_finais', 'fase_execucao', 'semifinais', 'quartas_de_final', 'oitavas_de_final',
            'configuracoes', 'times', 'jogos', 'grupos', 'noticias', 'linkinstagram', 'linklive'
        ];

        $conn->begin_transaction();
        try {
            foreach ($tables as $table) {
                $sql = "DELETE FROM $table WHERE campeonato_id = ?";
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

    // Excluir Grupo
    if (isset($_POST['excluir_grupo'])) {
        $grupo_id = $_POST['grupo_id'];
        
        $conn->begin_transaction();
        try {
            $sql = "DELETE jogadores, posicoes_jogadores FROM jogadores 
                    LEFT JOIN posicoes_jogadores ON jogadores.id = posicoes_jogadores.jogador_id 
                    WHERE jogadores.time_id IN (SELECT id FROM times WHERE grupo_id = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grupo_id);
            $stmt->execute();
            
            $sql = "DELETE FROM jogos_fase_grupos WHERE grupo_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grupo_id);
            $stmt->execute();
            
            $sql = "DELETE FROM times WHERE grupo_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grupo_id);
            $stmt->execute();
            
            $sql = "DELETE FROM grupos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $grupo_id);
            $stmt->execute();
            
            $conn->commit();
            echo "<p style='color:green;'>Grupo excluído com sucesso!</p>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
        }
    }

    // Excluir Registro
    if (isset($_POST['excluir_registro'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        
        try {
            $sql = "DELETE FROM $table WHERE id = ?";
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
        $table = $_POST['table'];
        $id = $_POST['id'];
        $fields = $_POST['fields'];
        
        try {
            $set = [];
            $types = '';
            $values = [];
            
            foreach ($fields as $field => $value) {
                $set[] = "$field = ?";
                $types .= 's';
                $values[] = $value;
            }
            
            $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE id = ?";
            $types .= 'i';
            $values[] = $id;
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
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
}

// Obter campeonatos
$campeonatos = [];
$sql = "SELECT * FROM campeonatos ORDER BY id DESC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    $campeonatos[] = $row;
}

// Obter dados do campeonato selecionado
$selectedCampeonato = null;
$dadosCampeonato = [];

if (isset($_GET['campeonato_id']) && is_numeric($_GET['campeonato_id'])) {
    $campeonato_id = $_GET['campeonato_id'];
    
    $sql = "SELECT * FROM campeonatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $campeonato_id);
    $stmt->execute();
    $selectedCampeonato = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $related_tables = [
        'grupos' => 'Grupos',
        'times' => 'Times',
        'jogos_fase_grupos' => 'Jogos da Fase de Grupos',
        'jogos_finais' => 'Jogos Finais',
        'configuracoes' => 'Configurações',
        'noticias' => 'Notícias',
        'linkinstagram' => 'Link Instagram',
        'linklive' => 'Link Live'
    ];
    
    foreach ($related_tables as $table => $name) {
        $sql = "SELECT * FROM $table WHERE campeonato_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $campeonato_id);
        $stmt->execute();
        $dadosCampeonato[$table] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
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
    </style>
    <script>
        function toggleEditForm(formId) {
            document.getElementById(formId).classList.toggle('active');
        }
    </script>
</head>
<body>
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

        <?php if ($selectedCampeonato): ?>
        <!-- Editar Campeonato -->
        <div class="section">
            <button onclick="toggleEditForm('editCampeonato')" class="btn edit">Editar Campeonato</button>
            <form method="post" id="editCampeonato" class="edit-form">
                <input type="hidden" name="table" value="campeonatos">
                <input type="hidden" name="id" value="<?= $selectedCampeonato['id'] ?>">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="fields[nome]" value="<?= htmlspecialchars($selectedCampeonato['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Data Início:</label>
                    <input type="date" name="fields[data_inicio]" value="<?= $selectedCampeonato['data_inicio'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Data Final:</label>
                    <input type="date" name="fields[data_final]" value="<?= $selectedCampeonato['data_final'] ?>" required>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="fields[ativo]" value="1" <?= $selectedCampeonato['ativo'] ? 'checked' : '' ?>> Ativo</label>
                </div>
                <button type="submit" name="editar_registro" class="btn edit">Salvar</button>
                <button type="button" onclick="toggleEditForm('editCampeonato')" class="btn delete">Cancelar</button>
            </form>

            <!-- Excluir Campeonato -->
            <form method="post" onsubmit="return confirm('Confirma exclusão total?')">
                <input type="hidden" name="campeonato_id" value="<?= $selectedCampeonato['id'] ?>">
                <button type="submit" name="excluir_campeonato" class="btn delete">Excluir Tudo</button>
            </form>
        </div>

        <!-- Listar e Editar Dados -->
        <?php foreach ($related_tables as $table => $name): ?>
            <div class="section">
                <h2><?= $name ?></h2>
                <?php if (!empty($dadosCampeonato[$table])): ?>
                    <?php foreach ($dadosCampeonato[$table] as $registro): ?>
                        <div class="data-box">
                            <button onclick="toggleEditForm('edit_<?= $table ?>_<?= $registro['id'] ?>')" 
                                    class="btn edit">Editar</button>
                            
                            <form method="post" id="edit_<?= $table ?>_<?= $registro['id'] ?>" class="edit-form">
                                <input type="hidden" name="table" value="<?= $table ?>">
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
                                            <?php else: ?>
                                                <input type="text" name="fields[<?= $campo ?>]" 
                                                    value="<?= htmlspecialchars($valor) ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <button type="submit" name="editar_registro" class="btn edit">Salvar</button>
                                <button type="button" 
                                        onclick="toggleEditForm('edit_<?= $table ?>_<?= $registro['id'] ?>')" 
                                        class="btn delete">Cancelar</button>
                            </form>

                            <pre><?= json_encode($registro, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                            
                            <form method="post">
                                <input type="hidden" name="table" value="<?= $table ?>">
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
</body>
</html>