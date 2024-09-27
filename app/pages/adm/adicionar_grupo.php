<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

include '../../config/conexao.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Grupo</title>
    <link rel="stylesheet" href="../../../public/css/adm/adicionar_grupo.css">
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
</head>
<body>
<?php require_once 'header_classificacao.php' ?>
<div class="main">
    <div class="form-container">
        <h1>Adicionar Grupo</h1>
        <form id="formConfiguracao" method="post" action="">
            <fieldset>
                <legend>Configuração dos Grupos:</legend>
                <label for="equipesPorGrupo">Número de equipes por grupo (máx 16):</label>
                <input type="number" id="equipesPorGrupo" name="equipesPorGrupo" min="1" max="16" required><br>
                <label for="numeroGrupos">Número de grupos (máx 18):</label>
                <input type="number" id="numeroGrupos" name="numeroGrupos" min="1" max="18" required><br>
                <label for="faseFinal">Fase Final:</label>
                <select id="faseFinal" name="faseFinal" required>
                    <option value="oitavas">Oitavas de Final</option>
                    <option value="quartas">Quartas de Final</option>
                </select><br>
                <button type="submit">Calcular e Criar Grupos</button>
            </fieldset>
        </form>
        <div id="mensagem">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipesPorGrupo']) && isset($_POST['numeroGrupos']) && isset($_POST['faseFinal'])) {
                $equipesPorGrupo = intval($_POST['equipesPorGrupo']);
                $numeroGrupos = intval($_POST['numeroGrupos']);
                $faseFinal = $_POST['faseFinal'];

                $MAX_EQUIPES_POR_GRUPO = 16;
                $MAX_GRUPOS = 18;
                $MIN_TIMES_OITAVAS = 16;
                $MIN_TIMES_QUARTAS = 8;

                if ($equipesPorGrupo > $MAX_EQUIPES_POR_GRUPO || $numeroGrupos > $MAX_GRUPOS) {
                    echo "<p class='error-message'>O número máximo de equipes por grupo é 16 e o número máximo de grupos é 18.</p>";
                } else {
                    $totalEquipes = $equipesPorGrupo * $numeroGrupos;

                    if (($faseFinal === 'oitavas' && $totalEquipes < $MIN_TIMES_OITAVAS) || 
                        ($faseFinal === 'quartas' && $totalEquipes < $MIN_TIMES_QUARTAS)) {
                        $minimo = ($faseFinal === 'oitavas') ? $MIN_TIMES_OITAVAS : $MIN_TIMES_QUARTAS;
                        echo "<p class='error-message'>Não é possível criar grupos. O número total de equipes deve ser pelo menos $minimo para a fase final selecionada.</p>";
                    } else {
                        // Condições para verificar se a divisão é exata para a fase final
                        if ($faseFinal === 'oitavas') {
                            if (($totalEquipes % 16) !== 0) {
                                echo "<p class='error-message'>Para a fase de oitavas, o número total de equipes deve ser múltiplo de 16.</p>";
                            } elseif (($numeroGrupos * $equipesPorGrupo) % 8 !== 0) {
                                echo "<p class='error-message'>Número de grupos ou equipes por grupo não permite uma divisão exata para a fase de oitavas.</p>";
                            } else {
                                try {
                                    // Desativar restrições de chave estrangeira (necessário para usar TRUNCATE em algumas configurações)
                                    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
                                    // Limpar tabelas
                                    $tables = [
                                        'jogadores',
                                        'times',
                                        'final',
                                        'quartas_de_final',
                                        'oitavas_de_final',
                                        'semifinais',
                                        'jogos',
                                        'jogos_fase_grupos',
                                        'grupos'
                                    ];

                                    foreach ($tables as $table) {
                                        $sql = "TRUNCATE TABLE $table";
                                        $conn->query($sql);
                                    }

                                    // Reativar restrições de chave estrangeira
                                    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

                                    // Criar grupos
                                    $sql = "REPLACE INTO configuracoes (id, equipes_por_grupo, numero_grupos, fase_final) VALUES (1, ?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("iis", $equipesPorGrupo, $numeroGrupos, $faseFinal);
                                    $stmt->execute();

                                    $conn->query("DELETE FROM grupos");

                                    for ($i = 1; $i <= $numeroGrupos; $i++) {
                                        $nomeGrupo = "Grupo " . chr(64 + $i);
                                        $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";
                                        $conn->query($sql);
                                    }
                                    echo "<p class='success-message'>Grupos criados com sucesso!</p>";
                                } catch (Exception $e) {
                                    echo "<p class='error-message'>Erro ao deletar dados: " . $e->getMessage() . "</p>";
                                }
                            }
                        } elseif ($faseFinal === 'quartas') {
                            if (($totalEquipes % 8) !== 0) {
                                echo "<p class='error-message'>Para a fase de quartas, o número total de equipes deve ser múltiplo de 8.</p>";
                            } elseif (($numeroGrupos * $equipesPorGrupo) % 2 !== 0) {
                                echo "<p class='error-message'>Número de grupos ou equipes por grupo não permite uma divisão exata para a fase de quartas.</p>";
                            } else {
                                try {
                                    // Desativar restrições de chave estrangeira (necessário para usar TRUNCATE em algumas configurações)
                                    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

                                    // Limpar tabelas
                                    $tables = [
                                        'jogadores',
                                        'posicoes_jogadores',
                                        'times',
                                        'final',
                                        'quartas_de_final',
                                        'oitavas_de_final',
                                        'semifinais',
                                        'jogos',
                                        'jogos_finais',
                                        'jogos_fase_grupos',
                                        'grupos',
                                        'final_confrontos',
                                        'oitavas_de_final_confrontos',
                                        'quartas_de_final_confrontos',
                                        'semifinais_confrontos',
                                        'final_confrontos'
                                    ];

                                    foreach ($tables as $table) {
                                        $sql = "TRUNCATE TABLE $table";
                                        $conn->query($sql);
                                    }

                                    // Reativar restrições de chave estrangeira
                                    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

                                    // Criar grupos
                                    $sql = "REPLACE INTO configuracoes (id, equipes_por_grupo, numero_grupos, fase_final) VALUES (1, ?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("iis", $equipesPorGrupo, $numeroGrupos, $faseFinal);
                                    $stmt->execute();

                                    $conn->query("DELETE FROM grupos");

                                    for ($i = 1; $i <= $numeroGrupos; $i++) {
                                        $nomeGrupo = "Grupo " . chr(64 + $i);
                                        $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";
                                        $conn->query($sql);
                                    }
                                    echo "<p class='success-message'>Grupos criados com sucesso!</p>";
                                } catch (Exception $e) {
                                    echo "<p class='error-message'>Erro ao deletar dados: " . $e->getMessage() . "</p>";
                                }
                            }
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<?php require_once '../footer.php' ?>
</body>
</html>
