<?php
include '../../config/conexao.php';
session_start();

// Verifica se o usuário está autenticado e se é um administrador
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Busca o campeonato ativo
$sql = "SELECT id FROM campeonatos WHERE ativo = TRUE LIMIT 1";
$stmt = $conn->query($sql);
$campeonatoAtivo = $stmt->fetch_assoc();

if (!$campeonatoAtivo) {
    die("Nenhum campeonato ativo encontrado.");
}

$campeonatoId = $campeonatoAtivo['id'];

// Função para gerar token
function gerarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Função para lidar com a exclusão de um time
if (isset($_POST['delete_token'])) {
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token inválido");
    }

    $token = $_POST['delete_token'];
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    $sql = "SELECT id FROM times WHERE token = ? AND campeonato_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $token, $campeonatoId);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if ($id) {
        $deleteSql = "DELETE FROM times WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            header("Location: listar_times.php?status=success");
            exit();
        } else {
            header("Location: listar_times.php?status=error");
            exit();
        }
        $stmt->close();
    } else {
        die("Token inválido");
    }

    $conn->query("SET FOREIGN_KEY_CHECKS=1");
}

// Consulta para obter os times do campeonato ativo
$sql = "SELECT t.id, t.nome, t.logo, t.token, g.nome AS grupo_nome 
        FROM times t 
        JOIN grupos g ON t.grupo_id = g.id 
        WHERE t.campeonato_id = ? 
        ORDER BY g.nome, t.nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $campeonatoId);
$stmt->execute();
$result = $stmt->get_result();

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[$row['grupo_nome']][] = $row;
}

$stmt->close();
$conn->close();

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
    <link rel="stylesheet" href="../../../public/css/adm/cadastros_times_jogadores_adm/listar_times.css">
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
</head>
<body>
<?php include 'header_classificacao.php'; ?>

    <h1 class="text-center">LISTAR DE TIMES</h1>

    <div class="container">
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success text-center">Time excluído com sucesso!</div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-danger text-center">Erro ao excluir time.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row">
        <?php foreach ($times as $grupo_nome => $timesGrupo): ?>
            <div class="col-md-3">
                <h3 id="nome_grupo"><?php echo htmlspecialchars($grupo_nome); ?></h3>
                <?php foreach ($timesGrupo as $time): ?>
                    <div class="time-card">
                        <?php if ($time['logo']): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($time['logo']); ?>" class="time-image" alt="Logo do Time">
                        <?php else: ?>
                            <img src="../../../public/images/default-team.png" class="time-image" alt="Logo do Time">
                        <?php endif; ?>
                        <div class="time-details">
                            <strong>Nome:</strong> <?php echo htmlspecialchars($time['nome']); ?><br>
                            <strong>Grupo:</strong> <?php echo htmlspecialchars($time['grupo_nome']); ?><br>
                        </div>
                        <div class="time-actions">
                            <a href="#" class="delete" data-toggle="modal" data-target="#confirmDeleteModal" data-token="<?php echo htmlspecialchars($time['token']); ?>">Excluir</a>
                            <a href="editar_time.php?token=<?php echo $time['token']; ?>" class="edit">Editar</a>
                        </div>
                    </div>
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
    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var token = button.data('token');
        var modal = $(this);
        modal.find('#delete_token').val(token);
    });
    </script>

    <?php include '../footer.php'; ?>
</body>
</html>