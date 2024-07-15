<?php
include '../../../app/config/conexao.php';

// Função para obter os confrontos das quartas de final
function obterConfrontosQuartas() {
    global $conn;

    $confrontos = [];

    // Consulta para obter os confrontos das quartas de final
    $sql = "SELECT qc.id, qc.timeA_id, tA.nome AS timeA_nome, tA.logo AS timeA_logo, qc.timeB_id, tB.nome AS timeB_nome, tB.logo AS timeB_logo
            FROM quartas_de_final_confrontos qc
            JOIN times tA ON qc.timeA_id = tA.id
            JOIN times tB ON qc.timeB_id = tB.id
            WHERE qc.fase = 'quartas'";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $confrontos[] = $row;
        }
    }

    return $confrontos;
}

// Função para exibir os confrontos das quartas de final
function exibirConfrontosQuartas() {
    $confrontos = obterConfrontosQuartas();

    if (!empty($confrontos)) {
        echo '<div class="confronto">';
        echo '<div class="confronto-header">Confrontos</div>';
        foreach ($confrontos as $confronto) {
            echo '<div class="matchup">';
            echo '<div class="team-info">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($confronto['timeA_logo']) . '" alt="' . htmlspecialchars($confronto['timeA_nome']) . '">';
            echo '<span class="team-name">' . htmlspecialchars($confronto['timeA_nome']) . '</span>';
            echo '</div>';
            echo '<span>X</span>';
            echo '<div class="team-info">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($confronto['timeB_logo']) . '" alt="' . htmlspecialchars($confronto['timeB_nome']) . '">';
            echo '<span class="team-name">' . htmlspecialchars($confronto['timeB_nome']) . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="confronto">';
        echo '<div class="confronto-header">Confrontos</div>';
        echo '<div class="matchup">Nenhum confronto disponível.</div>';
        echo '</div>';
    }
}

exibirConfrontosQuartas();
?>
