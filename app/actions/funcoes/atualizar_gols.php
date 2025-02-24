<?php
session_start();
include '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confrontos']) && !empty($_POST['confrontos'])) {
        foreach ($_POST['confrontos'] as $confrontoId) {
            if (isset($_POST['golsA_' . $confrontoId]) && isset($_POST['golsB_' . $confrontoId])) {
                // Capturar dados do formulário
                $golsA = (int)$_POST['golsA_' . $confrontoId];
                $golsB = (int)$_POST['golsB_' . $confrontoId];
                $data = $_POST['data_partida_' . $confrontoId];
                $hora = $_POST['hora_partida_' . $confrontoId];
                
                // Processar data e hora
                $dataJogo = null;
                if (!empty($data) && !empty($hora)) {
                    try {
                        $dataHora = DateTime::createFromFormat('Y-m-d H:i', $data . ' ' . $hora);
                        if ($dataHora === false) {
                            throw new Exception("Formato de data/hora inválido");
                        }
                        $dataJogo = $dataHora->format('Y-m-d H:i:s');
                    } catch (Exception $e) {
                        $_SESSION['error'] = "Erro no formato de data/hora: " . $e->getMessage();
                        continue;
                    }
                }

                // Determinar resultados
                $resultadoA = 'E';
                $resultadoB = 'E';
                if ($golsA > $golsB) {
                    $resultadoA = 'V';
                    $resultadoB = 'D';
                } elseif ($golsA < $golsB) {
                    $resultadoA = 'D';
                    $resultadoB = 'V';
                }

                // Executar atualização
                try {
                    $sql = "UPDATE jogos_fase_grupos SET 
                            gols_marcados_timeA = ?,
                            gols_marcados_timeB = ?,
                            resultado_timeA = ?,
                            resultado_timeB = ?,
                            data_jogo = ?
                            WHERE id = ?";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iisssi', $golsA, $golsB, $resultadoA, $resultadoB, $dataJogo, $confrontoId);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Erro ao atualizar jogo ID $confrontoId: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
        }
    }
    $conn->close();
    header('Location: /copadaspanelas/app/pages/adm/rodadas_adm.php');
    exit();
} else {
    $_SESSION['error'] = "Método de requisição inválido";
    header('Location: /copadaspanelas/app/pages/adm/rodadas_adm.php');
    exit();
}
?>