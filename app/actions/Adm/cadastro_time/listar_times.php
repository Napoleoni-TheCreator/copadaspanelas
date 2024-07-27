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
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: rgb(218, 215, 215);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s; /* Transição suave */
        }

        h1 {
            font-size: 4rem;
            margin-top: 5%;
            margin-bottom: 1rem;
            text-align: center;
            text-shadow: 4px 2px 4px rgba(0, 0, 0, 0.5);
        }

        h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #333;
        }

        .container {
            margin-top: 3%;
            background-color: rgb(218, 215, 215);
            padding: 1.25rem;
            border: 3px solid rgba(31, 38, 135, 0.37);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 0, 0, 1.8);
            width: 100%;
            overflow-x: auto;
            text-align: center;
            margin-bottom: 10%;
        }

        .time-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 1.25rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 3px solid;
            position: relative;
            text-align: center;
        }

        .time-card:hover {
            animation: borderColorChange 2s infinite;
            box-shadow: 0 0 40px rgba(255, 0, 0, 0.4);
            transform: scale(1.1);
            margin: 1rem;
        }

        .time-image {
            width: 80px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .time-details {
            margin-bottom: 1.25rem;
        }

        .time-actions {
            display: flex;
            gap: 0.625rem;
        }

        .time-actions a {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            border-radius: 5px;
            font-size: 0.875rem;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .time-actions a.delete {
            background-color: #dc3545;
        }

        .time-actions a.delete:hover {
            background-color: #c82333;
            color: #fff;
        }

        .time-actions a.edit {
            background-color: #007bff;
        }

        .time-actions a.edit:hover {
            background-color: #0056b3;
            color: #fff;
        }

        .time-actions a:active {
            transform: scale(0.98);
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem; /* Espaço entre as colunas */
        }

        .col-md-3 {
            flex: 1 1 calc(25% - 1rem); /* Ajusta o tamanho da coluna para dispositivos maiores */
            max-width: calc(25% - 1rem); /* Garante que a coluna não ultrapasse 25% da largura total */
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 767px) {
            h1 {
                font-size: 2.2rem;
            }

            h3 {
                font-size: 1.22rem;
            }

            .container {
                padding: 1rem;
            }

            .time-card {
                padding: 0.5rem;
            }

            .time-image {
                width: 60px;
            }

            .time-actions a {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
            }

            .col-md-3 {
                flex: 1 1 calc(50% - 1rem); /* Ajusta o tamanho da coluna para dispositivos menores */
                max-width: calc(50% - 1rem); /* Garante que a coluna não ultrapasse 50% da largura total */
            }
        }

        /* Consultas de mídia para telas com resolução de 800x1280 */
        @media (max-width: 800px) and (orientation: portrait) {
            h1 {
                font-size: 2rem;
            }

            h3 {
                font-size: 1.125rem;
            }

            .container {
                padding: 0.75rem;
            }

            .time-card {
                padding: 0.75rem;
            }

            .time-image {
                width: 50px;
            }

            .time-actions a {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }

            .col-md-3 {
                flex: 1 1 calc(50% - 0.75rem); /* Ajusta o tamanho da coluna para telas menores */
                max-width: calc(50% - 0.75rem); /* Garante que a coluna não ultrapasse 50% da largura total */
            }
        }

        /* Estilo do modo escuro */
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        .dark-mode h3{
            color: white;
        }

        .dark-mode .container {
            background-color: #1e1e1e;
            border-color: #333;
        }

        .dark-mode .time-card {
            background-color: #2c2c2c;
            color: #e0e0e0;
            border-color: #555;
        }

        .dark-mode .time-card:hover {
            box-shadow: 0 0 40px rgba(255, 0, 0, 0.6);
        }

        .dark-mode .time-actions a.delete {
            background-color: #c82333;
        }

        .dark-mode .time-actions a.edit {
            background-color: #0056b3;
        }
    </style>
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
                    <a href="../../../actions/cadastro_adm/login.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
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
