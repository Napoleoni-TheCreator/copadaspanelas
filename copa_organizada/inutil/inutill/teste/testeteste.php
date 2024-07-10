<?php
// Incluir o arquivo de conexão
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

// Função para obter todos os times classificados
function obterTimesClassificados() {
    global $conn;

    // Consulta SQL para recuperar todos os times classificados com seus dados
    $query = "SELECT t.id, t.nome, g.nome AS grupo_nome, t.pts, t.vitorias, t.empates, t.derrotas, t.gm, t.gc, t.sg
              FROM times t
              JOIN grupos g ON t.grupo_id = g.id
              ORDER BY t.pts DESC, t.vitorias DESC, t.sg DESC, t.gm DESC, t.gc ASC";

    $result = $conn->query($query);

    if ($result === FALSE) {
        die("Erro ao buscar times classificados: " . $conn->error);
    }

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'grupo_nome' => $row['grupo_nome'],
            'pts' => $row['pts'],
            'vitorias' => $row['vitorias'],
            'empates' => $row['empates'],
            'derrotas' => $row['derrotas'],
            'gm' => $row['gm'],
            'gc' => $row['gc'],
            'sg' => $row['sg']
        ];
    }

    return $times;
}

// Função para adicionar times classificados a uma fase final
function adicionarTimesFaseFinal($fase, $times) {
    global $conn;
    
    // Limpar a tabela da fase final antes de adicionar novos registros
    $truncateQuery = "TRUNCATE TABLE " . $fase;
    if ($conn->query($truncateQuery) === FALSE) {
        die("Erro ao limpar tabela: " . $conn->error);
    }

    // Inserir os times na tabela da fase final
    $insertQuery = "INSERT INTO $fase (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    foreach ($times as $time) {
        $stmt->bind_param("iss", $time['id'], $time['grupo_nome'], $time['nome']);
        if (!$stmt->execute()) {
            die("Erro ao inserir time: " . $stmt->error);
        }
    }

    $stmt->close();
}

// Função para registrar os confrontos das fases finais
function registrarConfronto($fase, $timeA, $timeB, $golsMarcadosA, $golsMarcadosB) {
    global $conn;
    
    // Define a tabela de confrontos baseada na fase
    $tabelaConfrontos = $fase . "_confrontos";
    
    $insertQuery = "INSERT INTO $tabelaConfrontos (timeA_id, timeB_id, fase, gols_marcados_timeA, gols_marcados_timeB, gols_contra_timeA, gols_contra_timeB) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    $golsContraA = $golsMarcadosB;
    $golsContraB = $golsMarcadosA;
    
    $stmt->bind_param("iisiiii", $timeA['id'], $timeB['id'], $fase, $golsMarcadosA, $golsMarcadosB, $golsContraA, $golsContraB);
    if (!$stmt->execute()) {
        die("Erro ao registrar confronto: " . $stmt->error);
    }

    $stmt->close();
}

// Função para exibir resultados das fases finais
function exibirResultados() {
    global $conn;
    
    $fases = ['oitavas_de_final', 'quartas_de_final', 'semifinais', 'final'];
    
    foreach ($fases as $fase) {
        $query = "SELECT * FROM " . $fase;
        $result = $conn->query($query);
        
        if ($result === FALSE) {
            die("Erro ao buscar resultados para $fase: " . $conn->error);
        }

        echo "<h2>Resultados da Fase: " . ucfirst(str_replace('_', ' ', $fase)) . "</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID Time A</th>
                    <th>Nome Time A</th>
                    <th>ID Time B</th>
                    <th>Nome Time B</th>
                    <th>Fase</th>
                    <th>Gols Marcados Time A</th>
                    <th>Gols Marcados Time B</th>
                    <th>Gols Contra Time A</th>
                    <th>Gols Contra Time B</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['timeA_id']) . "</td>
                    <td>" . htmlspecialchars(getNomeTime($row['timeA_id'])) . "</td>
                    <td>" . htmlspecialchars($row['timeB_id']) . "</td>
                    <td>" . htmlspecialchars(getNomeTime($row['timeB_id'])) . "</td>
                    <td>" . htmlspecialchars($row['fase']) . "</td>
                    <td>" . htmlspecialchars($row['gols_marcados_timeA']) . "</td>
                    <td>" . htmlspecialchars($row['gols_marcados_timeB']) . "</td>
                    <td>" . htmlspecialchars($row['gols_contra_timeA']) . "</td>
                    <td>" . htmlspecialchars($row['gols_contra_timeB']) . "</td>
                  </tr>";
        }

        echo "</table><br/>";
    }
}

// Função auxiliar para obter o nome do time
function getNomeTime($timeId) {
    global $conn;
    $stmt = $conn->prepare("SELECT nome FROM times WHERE id = ?");
    $stmt->bind_param("i", $timeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $time = $result->fetch_assoc();
    $stmt->close();
    return $time['nome'];
}

// Função para dividir os times nas fases finais
function dividirTimesFasesFinais($todosOsTimes) {
    global $conn;

    // Configuração do torneio
    $queryConfig = "SELECT fase_final FROM configuracoes LIMIT 1";
    $configResult = $conn->query($queryConfig);

    if ($configResult === FALSE) {
        die("Erro ao obter configuração: " . $conn->error);
    }

    $config = $configResult->fetch_assoc();
    $faseFinal = $config['fase_final'];

    // Calcular o número de times por fase final
    $totalTimes = count($todosOsTimes);
    $totalClassificados = 0;

    switch ($faseFinal) {
        case 'oitavas':
            $totalClassificados = min(16, $totalTimes); // Máximo de 16 times
            break;
        case 'quartas':
            $totalClassificados = min(8, $totalTimes); // Máximo de 8 times
            break;
        case 'semifinais':
            $totalClassificados = min(4, $totalTimes); // Máximo de 4 times
            break;
        case 'final':
            $totalClassificados = min(2, $totalTimes); // Máximo de 2 times
            break;
        default:
            die("Fase final desconhecida.");
    }

    // Dividir os times para cada fase final
    return array_slice($todosOsTimes, 0, $totalClassificados);
}
// Função para registrar confrontos em uma fase final
function registrarConfrontosFase($fase, $times) {
    global $conn;

    // Define a tabela de confrontos baseada na fase
    $tabelaConfrontos = $fase . "_confrontos";
    
    // Limpar a tabela de confrontos existente para a fase atual
    $truncateQuery = "TRUNCATE TABLE " . $tabelaConfrontos;
    if ($conn->query($truncateQuery) === FALSE) {
        die("Erro ao limpar tabela de confrontos: " . $conn->error);
    }

    // Determinar o número de confrontos a serem registrados
    $numTimes = count($times);

    // Criar confrontos baseados na fase
    if ($fase == 'oitavas_de_final') {
        for ($i = 0; $i < $numTimes; $i += 2) {
            if (isset($times[$i]) && isset($times[$i + 1])) {
                $timeA = $times[$i];
                $timeB = $times[$i + 1];
                $golsMarcadosA = 0; // Inicializar com 0 até que sejam definidos
                $golsMarcadosB = 0; // Inicializar com 0 até que sejam definidos
                registrarConfronto($fase, $timeA, $timeB, $golsMarcadosA, $golsMarcadosB);
            }
        }
    } elseif ($fase == 'quartas_de_final') {
        for ($i = 0; $i < $numTimes; $i += 2) {
            if (isset($times[$i]) && isset($times[$i + 1])) {
                $timeA = $times[$i];
                $timeB = $times[$i + 1];
                $golsMarcadosA = 0; // Inicializar com 0 até que sejam definidos
                $golsMarcadosB = 0; // Inicializar com 0 até que sejam definidos
                registrarConfronto($fase, $timeA, $timeB, $golsMarcadosA, $golsMarcadosB);
            }
        }
    } elseif ($fase == 'semifinais') {
        for ($i = 0; $i < $numTimes; $i += 2) {
            if (isset($times[$i]) && isset($times[$i + 1])) {
                $timeA = $times[$i];
                $timeB = $times[$i + 1];
                $golsMarcadosA = 0; // Inicializar com 0 até que sejam definidos
                $golsMarcadosB = 0; // Inicializar com 0 até que sejam definidos
                registrarConfronto($fase, $timeA, $timeB, $golsMarcadosA, $golsMarcadosB);
            }
        }
    } elseif ($fase == 'final') {
        if ($numTimes == 2) {
            $timeA = $times[0];
            $timeB = $times[1];
            $golsMarcadosA = 0; // Inicializar com 0 até que sejam definidos
            $golsMarcadosB = 0; // Inicializar com 0 até que sejam definidos
            registrarConfronto($fase, $timeA, $timeB, $golsMarcadosA, $golsMarcadosB);
        }
    } else {
        die("Fase final desconhecida.");
    }
}

// Função principal para adicionar e registrar fases finais
function adicionarFasesFinais() {
    global $conn;

    // Classificar todos os times de todos os grupos
    $todosOsTimes = obterTimesClassificados();

    // Dividir os times classificados para as fases finais
    $timesFaseFinal = dividirTimesFasesFinais($todosOsTimes);

    // Configuração da fase final
    $configQuery = "SELECT fase_final FROM configuracoes LIMIT 1";
    $result = $conn->query($configQuery);
    $config = $result->fetch_assoc();
    $faseFinal = $config['fase_final'];

    // Adicionar times às fases finais e registrar confrontos
    switch ($faseFinal) {
        case 'oitavas':
            adicionarTimesFaseFinal('oitavas_de_final', $timesFaseFinal);
            registrarConfrontosFase('oitavas_de_final', $timesFaseFinal);
            break;
        case 'quartas':
            adicionarTimesFaseFinal('quartas_de_final', $timesFaseFinal);
            registrarConfrontosFase('quartas_de_final', $timesFaseFinal);
            break;
        case 'semifinais':
            adicionarTimesFaseFinal('semifinais', $timesFaseFinal);
            registrarConfrontosFase('semifinais', $timesFaseFinal);
            break;
        case 'final':
            adicionarTimesFaseFinal('final', $timesFaseFinal);
            registrarConfrontosFase('final', $timesFaseFinal);
            break;
        default:
            die("Fase final desconhecida.");
    }
}
// Incluir o arquivo de conexão
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';

// Função para obter os times classificados para uma fase
function obterTimesFase($fase) {
    global $conn;

    // Consulta SQL para recuperar todos os times classificados para a fase
    $query = "SELECT t.id, t.nome, f.time_nome AS time_nome
              FROM $fase f
              JOIN times t ON f.time_id = t.id";
              
    $result = $conn->query($query);

    if ($result === FALSE) {
        die("Erro ao buscar times classificados para $fase: " . $conn->error);
    }

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'time_nome' => $row['time_nome']
        ];
    }

    return $times;
}

// Função para obter e exibir os confrontos de uma fase
function exibirConfrontos($fase) {
    global $conn;

    $query = "SELECT * FROM {$fase}_confrontos";
    $result = $conn->query($query);

    if ($result === FALSE) {
        die("Erro ao buscar confrontos para $fase: " . $conn->error);
    }

    echo "<h2>Confrontos da Fase: " . ucfirst(str_replace('_', ' ', $fase)) . "</h2>";
    echo "<table border='1'>
            <tr>
                <th>Time A</th>
                <th>Time B</th>
                <th>Fase</th>
                <th>Gols Marcados Time A</th>
                <th>Gols Marcados Time B</th>
                <th>Gols Contra Time A</th>
                <th>Gols Contra Time B</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['timeA_nome']) . "</td>
                <td>" . htmlspecialchars($row['timeB_nome']) . "</td>
                <td>" . htmlspecialchars($row['fase']) . "</td>
                <td>" . htmlspecialchars($row['gols_marcados_timeA']) . "</td>
                <td>" . htmlspecialchars($row['gols_marcados_timeB']) . "</td>
                <td>" . htmlspecialchars($row['gols_contra_timeA']) . "</td>
                <td>" . htmlspecialchars($row['gols_contra_timeB']) . "</td>
              </tr>";
    }

    echo "</table><br/>";
}

// Função para exibir os times e confrontos nas fases finais
function exibirFasesFinais() {
    $fases = ['oitavas_de_final', 'quartas_de_final', 'semifinais', 'final'];

    foreach ($fases as $fase) {
        // Exibir times classificados para a fase
        echo "<h2>Times Classificados para a Fase: " . ucfirst(str_replace('_', ' ', $fase)) . "</h2>";
        $times = obterTimesFase($fase);
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Nome do Time</th>
                </tr>";
        
        foreach ($times as $time) {
            echo "<tr>
                    <td>" . htmlspecialchars($time['id']) . "</td>
                    <td>" . htmlspecialchars($time['nome']) . "</td>
                    <td>" . htmlspecialchars($time['time_nome']) . "</td>
                  </tr>";
        }

        echo "</table><br/>";

        // Exibir confrontos da fase
        exibirConfrontos($fase);
    }
}

// Executar a função para exibir as fases finais
exibirFasesFinais();
// Executar o processo de adicionar fases finais
adicionarFasesFinais();

// Exibir resultados
exibirResultados();

?>
