<?php
include '../../../config/conexao.php';
session_start(); // Iniciar a sessão para validação CSRF e autenticação

// Função para gerar token
function gerarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Função para lidar com a exclusão de um time
if (isset($_POST['delete_token'])) {
    // Verificação do token CSRF
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token inválido");
    }

    $token = $_POST['delete_token'];

    // Desativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    // Validar o token e obter o ID do time
    $sql = "SELECT id FROM times WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
        // Excluir o time com o ID correspondente
        $deleteSql = "DELETE FROM times WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Redireciona para a lista de times com uma mensagem de sucesso
                header("Location: listar_times.php?status=success");
                exit();
            } else {
                echo "<script>alert('Nenhum time encontrado com o ID fornecido.'); window.location.href='listar_times.php';</script>";
            }
        } else {
            echo "<script>alert('Erro ao excluir time: " . $stmt->error . "'); window.location.href='listar_times.php';</script>";
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }

    // Reativar verificações de chave estrangeira
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
}

// Consulta SQL para obter todos os times
$sql = "SELECT t.id, t.nome, t.logo, t.token, g.nome AS grupo_nome FROM times t JOIN grupos g ON t.grupo_id = g.id ORDER BY g.nome, t.nome";
$result = $conn->query($sql);

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[$row['grupo_nome']][] = $row;
}

$conn->close();

// Gerar token CSRF para uso no formulário
$csrf_token = gerarToken();
$_SESSION['csrf_token'] = $csrf_token;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Times</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../../public/css/adm/cadastros_times_jogadores_adm/listar_times.css">
</head>
<?php include '../../../pages/header_classificacao.php'; ?>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../../../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
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
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/prancheta.svg" alt="Trophy Icon"></a>
                    <span>Editar jogadores</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/grupo.svg" alt="Trophy Icon"></a>
                    <span>Criar grupos</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/adtime.svg" alt="Trophy Icon"></a>
                    <span>Adicionar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../cadastro_adm/cadastro_adm.php"><img src="../../../../public/img/adadm.svg" alt="cadastro novos adm"></a>
                    <span>Adicionar outro adm</span>
                </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()">Modo Escuro</button>
        </div>
    </header>
<body>
<h1 class="text-center">LISTAR DE TIMES</h1>
<div class="container">

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success text-center">Time excluído com sucesso!</div>
    <?php endif; ?>

    <div class="row">
    <?php
        $borderColors = ['#FF5733', '#33FF57', '#3357FF', '#F333FF', '#FF33A0', '#33F0FF', '#FFBF00', '#8CFF33'];
        $colorIndex = 0;
    ?>
    <?php foreach ($times as $grupo_nome => $timesGrupo): ?>
        <div class="col-md-3">
            <h3><?php echo htmlspecialchars($grupo_nome); ?></h3>
            <?php foreach ($timesGrupo as $time): ?>
                <div class="time-card" style="border-color: <?php echo $borderColors[$colorIndex]; ?>;">
                    <?php if ($time['logo']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($time['logo']); ?>" class="time-image" alt="Logo do Time">
                    <?php else: ?>
                        <img src="../../../../public/images/default-team.png" class="time-image" alt="Logo do Time">
                    <?php endif; ?>
                    <div class="time-details">
                        <strong>Nome:</strong> <?php echo htmlspecialchars($time['nome']); ?><br>
                        <strong>TECNICO:</strong> <?php echo htmlspecialchars($time['grupo_nome']); ?><br>
                    </div>
                    <div class="time-actions">
                        <a href="#" class="delete" data-toggle="modal" data-target="#confirmDeleteModal" data-token="<?php echo htmlspecialchars($time['token']); ?>">Excluir</a>
                        <a href="editar_time.php?token=<?php echo $time['token']; ?>" class="edit">Editar</a>
                    </div>
                </div>
                <?php
                    $colorIndex = ($colorIndex + 1) % count($borderColors);
                ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este time?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="delete_token" id="delete_token" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Adiciona o token ao campo oculto do formulário de exclusão
    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var token = button.data('token');
        var modal = $(this);
        modal.find('#delete_token').val(token);
    });
</script>
</body>
</html>
