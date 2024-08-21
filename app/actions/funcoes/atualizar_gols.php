<?php
include '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confrontos']) && !empty($_POST['confrontos'])) {
        foreach ($_POST['confrontos'] as $confrontoId) {
            // Verificar se os dados estão presentes e definidos
            if (isset($_POST['golsA_' . $confrontoId]) && isset($_POST['golsB_' . $confrontoId])) {
                $golsA = $_POST['golsA_' . $confrontoId];
                $golsB = $_POST['golsB_' . $confrontoId];

                // Determina o resultado do jogo
                if ($golsA > $golsB) {
                    $resultadoA = 'V';
                    $resultadoB = 'D';
                } elseif ($golsA < $golsB) {
                    $resultadoA = 'D';
                    $resultadoB = 'V';
                } else {
                    $resultadoA = 'E';
                    $resultadoB = 'E';
                }

                // Preparar e executar a query SQL para atualizar os gols e resultados
                $sqlUpdate = "UPDATE jogos_fase_grupos SET 
                              gols_marcados_timeA = ?, 
                              gols_marcados_timeB = ?, 
                              resultado_timeA = ?, 
                              resultado_timeB = ? 
                              WHERE id = ?";
                $stmt = $conn->prepare($sqlUpdate);
                $stmt->bind_param('iisss', $golsA, $golsB, $resultadoA, $resultadoB, $confrontoId);

                if ($stmt->execute()) {
                    echo "Gols e resultados atualizados com sucesso para o confronto ID $confrontoId.";
                } else {
                    echo "Erro ao atualizar gols e resultados para o confronto ID $confrontoId: " . $conn->error;
                }
            } else {
                echo "Dados insuficientes para o confronto ID $confrontoId.";
            }
        }
    } else {
        echo "Nenhum confronto selecionado para atualização.";
    }
} else {
    echo "Método de requisição inválido. Apenas POST é permitido.";
}

$conn->close();
header('Location: /copadaspanelas/app/pages/adm/rodadas_adm.php');
?>
