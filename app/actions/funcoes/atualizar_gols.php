<?php
include '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confrontos']) && !empty($_POST['confrontos'])) {
        foreach ($_POST['confrontos'] as $confrontoId) {
            // Verificar se os dados estão presentes e definidos
            if (isset($_POST['golsA_' . $confrontoId]) && isset($_POST['golsB_' . $confrontoId])) {
                $golsA = $_POST['golsA_' . $confrontoId];
                $golsB = $_POST['golsB_' . $confrontoId];

                // Preparar e executar a query SQL
                $sqlUpdate = "UPDATE jogos_fase_grupos SET gols_marcados_timeA = ?, gols_marcados_timeB = ? WHERE id = ?";
                $stmt = $conn->prepare($sqlUpdate);
                $stmt->bind_param('iii', $golsA, $golsB, $confrontoId);

                if ($stmt->execute()) {
                    echo "Gols atualizados com sucesso para o confronto ID $confrontoId.";
                } else {
                    echo "Erro ao atualizar gols para o confronto ID $confrontoId: " . $conn->error;
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
header('Location: /copadaspanelas/app/pages/rodadas.php');
$conn->close();
?>
