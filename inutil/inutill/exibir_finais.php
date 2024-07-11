<?php
// include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
include '../../config/conexao.php';
function exibirFinais() {
    global $conn;

    // Obter a configuração atual
    $result = $conn->query("SELECT * FROM configuracoes WHERE id = 1");
    if ($result->num_rows === 0) {
        die("Configuração não encontrada.");
    }
    $config = $result->fetch_assoc();
    $fase_final = $config['fase_final'];

    // Limpar o conteúdo
    echo "<html><head><title>Fases Finais</title><style>
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px; text-align: center; border: 1px solid #ddd; }
            th { background-color: #f4f4f4; }
            .fase { font-weight: bold; }
        </style></head><body>";

    // Função para exibir confrontos
    function exibirConfrontos($fase, $tabela) {
        global $conn;
        echo "<h2 class='fase'>$fase</h2>";
        $sql = "SELECT * FROM $tabela";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table><tr><th>Time A</th><th>Time B</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['timeA_nome']}</td><td>{$row['timeB_nome']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Sem confrontos para esta fase.</p>";
        }
    }

    // Sempre exibir todas as fases
    exibirConfrontos('Oitavas de Final', 'oitavas_de_final_confrontos');
    exibirConfrontos('Quartas de Final', 'quartas_de_final_confrontos');
    exibirConfrontos('Semifinais', 'semifinais_confrontos');
    exibirConfrontos('Final', 'final_confrontos');

    echo "</body></html>";
}

// Executar a função para exibir as fases finais
exibirFinais();
?>
