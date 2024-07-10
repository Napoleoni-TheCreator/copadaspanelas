<?php
// Inclui a configuração de conexão
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

// Função para obter times classificados por grupo
function getClassificadosPorGrupo($conn) {
    $sql = "SELECT t.id, t.nome, t.grupo_id, g.nome AS grupo_nome, t.pts AS pontos
            FROM times t
            JOIN grupos g ON t.grupo_id = g.id
            ORDER BY t.grupo_id, t.pts DESC";
    $result = $conn->query($sql);
    if (!$result) {
        die("Erro ao obter times classificados: " . $conn->error);
    }

    $classificados = [];
    while ($row = $result->fetch_assoc()) {
        $classificados[$row['grupo_id']][] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'grupo_id' => $row['grupo_id'],
            'grupo' => $row['grupo_nome'],
            'pontos' => $row['pontos']
        ];
    }

    return $classificados;
}

// Função para verificar se dois times são do mesmo grupo
function mesmoGrupo($timeA, $timeB) {
    return $timeA['grupo_id'] == $timeB['grupo_id'];
}

// Função para gerar confrontos das quartas de final
function gerarConfrontos($classificados) {
    $confrontos = [];
    $numTimes = count($classificados);

    // Assegura que temos 8 times para as quartas de final
    if ($numTimes >= 8) {
        for ($i = 0; $i < $numTimes / 2; $i++) {
            $timeA = $classificados[$i];
            $timeB = $classificados[$numTimes - $i - 1];

            // Ajusta se os times forem do mesmo grupo
            if (mesmoGrupo($timeA, $timeB)) {
                for ($j = $i + 1; $j < $numTimes / 2; $j++) {
                    $timeB = $classificados[$numTimes - $j - 1];
                    if (!mesmoGrupo($timeA, $timeB)) {
                        $confrontos[] = [$timeA['nome'], $timeB['nome']];
                        $classificados[$numTimes - $j - 1] = $classificados[$numTimes - $i - 1];
                        $classificados[$numTimes - $i - 1] = $timeB;
                        break;
                    }
                }
            } else {
                $confrontos[] = [$timeA['nome'], $timeB['nome']];
            }
        }
    }

    return $confrontos;
}

// Função para atualizar a tabela de confrontos
function atualizarConfrontos($conn, $confrontos) {
    // Limpa a tabela de confrontos das quartas de final
    $sqlTruncate = "TRUNCATE TABLE quartas_de_final_confrontos";
    if (!$conn->query($sqlTruncate)) {
        die("Erro ao limpar tabela quartas_de_final_confrontos: " . $conn->error);
    }

    // Prepara a consulta para inserir novos confrontos
    $stmt = $conn->prepare("INSERT INTO quartas_de_final_confrontos (timeA_nome, timeB_nome) VALUES (?, ?)");
    if (!$stmt) {
        die("Erro na preparação da consulta de confrontos: " . $conn->error);
    }

    // Insere novos confrontos
    foreach ($confrontos as $confronto) {
        $stmt->bind_param('ss', $confronto[0], $confronto[1]);
        if (!$stmt->execute()) {
            die("Erro ao inserir confronto {$confronto[0]} vs {$confronto[1]}: " . $stmt->error);
        }
    }

    $stmt->close();
}

// Função principal para gerenciar as fases finais
function gerenciarFasesFinais($conn) {
    $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
    $resultConfig = $conn->query($sqlConfig);
    if (!$resultConfig || $resultConfig->num_rows == 0) {
        die("Erro na consulta de configuração: " . $conn->error);
    }
    $rowConfig = $resultConfig->fetch_assoc();
    $faseFinal = $rowConfig['fase_final'];

    if ($faseFinal == 'quartas') {
        $classificados = getClassificadosPorGrupo($conn);
        $timesClassificados = [];
        foreach ($classificados as $grupo => $times) {
            $timesClassificados = array_merge($timesClassificados, $times);
        }

        if (count($timesClassificados) >= 8) {
            $confrontos = gerarConfrontos($timesClassificados);
            atualizarConfrontos($conn, $confrontos);
        } else {
            echo "Não há times suficientes para as quartas de final.";
        }
    }
}

// Executa a gestão das fases finais
gerenciarFasesFinais($conn);

// Função para exibir confrontos
function exibirConfrontos($conn, $fase) {
    $tabelaConfrontos = '';
    switch ($fase) {
        case 'oitavas':
            $tabelaConfrontos = 'oitavas_de_final_confrontos';
            break;
        case 'quartas':
            $tabelaConfrontos = 'quartas_de_final_confrontos';
            break;
        case 'semifinais':
            $tabelaConfrontos = 'semifinais_confrontos';
            break;
        case 'final':
            $tabelaConfrontos = 'final_confrontos';
            break;
        default:
            die("Fase desconhecida: " . $fase);
    }

    $sql = "SELECT timeA_nome, timeB_nome, gols_marcados_timeA, gols_marcados_timeB, gols_contra_timeA, gols_contra_timeB
            FROM $tabelaConfrontos
            ORDER BY id";
    $result = $conn->query($sql);
    if (!$result) {
        die("Erro na consulta de confrontos: " . $conn->error);
    }

    echo "<h2>Confrontos das $fase</h2>";
    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Time A</th>
                    <th>Time B</th>
                    <th>Gols Marcados Time A</th>
                    <th>Gols Marcados Time B</th>
                    <th>Gols Contra Time A</th>
                    <th>Gols Contra Time B</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['timeA_nome']}</td>
                <td>{$row['timeB_nome']}</td>
                <td>{$row['gols_marcados_timeA']}</td>
                <td>{$row['gols_marcados_timeB']}</td>
                <td>{$row['gols_contra_timeA']}</td>
                <td>{$row['gols_contra_timeB']}</td>
              </tr>";
    }

    echo "</tbody>
        </table>";
}

// Função para exibir finais
function exibirFinais($conn) {
    // Exibir confrontos das oitavas de final
    exibirConfrontos($conn, 'oitavas');

    // Exibir confrontos das quartas de final
    exibirConfrontos($conn, 'quartas');

    // Exibir confrontos das semifinais
    exibirConfrontos($conn, 'semifinais');

    // Exibir confrontos da final
    exibirConfrontos($conn, 'final');
}

exibirFinais($conn);
?>
