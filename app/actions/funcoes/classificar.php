<?php
// include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
include '../../app/config/conexao.php';
function atualizarFaseExecutada($fase) {
    global $conn;
    $stmt = $conn->prepare("UPDATE fase_execucao SET executado = TRUE WHERE fase = ?");
    $stmt->bind_param("s", $fase);
    $stmt->execute();
    $stmt->close();
}

// function faseJaExecutada($fase) {
//     global $conn;
//     $stmt = $conn->prepare("SELECT executado FROM fase_execucao WHERE fase = ?");
//     $stmt->bind_param("s", $fase);
//     $stmt->execute();
//     $stmt->bind_result($executado);
//     $stmt->fetch();
//     $stmt->close();
//     return $executado;
// }
function faseJaExecutada($fase) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM fase_execucao WHERE fase = ? AND executado = 1");
    $stmt->bind_param("s", $fase);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $executado = $row['count'] > 0;
    $stmt->close();
    return $executado;
}


function inicializarFaseExecucao() {
    global $conn;
    $fases = ['oitavas', 'quartas', 'semifinais', 'final'];
    foreach ($fases as $fase) {
        $stmt = $conn->prepare("INSERT IGNORE INTO fase_execucao (fase) VALUES (?)");
        $stmt->bind_param("s", $fase);
        $stmt->execute();
        $stmt->close();
    }
}


function classificarOitavasDeFinal() {
    global $conn;
    if (faseJaExecutada('oitavas')) {
        return;
    }

        // Obter a quantidade de grupos

        // Calcular a quantidade de times por grupo para as quartas
        // Obter os times classificados de cada grupo
        // Inserir os times classificados na tabela de quartas de final
          // Organizar os confrontos das quartas de final
            // Inserir os confrontos das quartas de final

    // Obter a configuração da fase final
    // $result = $conn->query("SELECT fase_final, numero_grupos FROM configuracoes WHERE id = 1");
    // $config = $result->fetch_assoc();
    // $faseFinal = $config['fase_final'];
    // $numeroGrupos = (int) $config['numero_grupos'];

    // // Verificar se a fase final é 'quartas'
    // if ($faseFinal == 'quartas') {
    //     // Chamar a função classificarQuartasDeFinal se a fase final for 'quartas'
    //     classificarQuartasDeFinal();
    //     return;
    // }
    // Obter a configuração da fase final
    $result = $conn->query("SELECT fase_final, numero_grupos FROM configuracoes WHERE id = 1");
    $config = $result->fetch_assoc();
    $faseFinal = $config['fase_final'];
    $numeroGrupos = (int) $config['numero_grupos'];

    // Verificar a fase final e chamar a função apropriada
    switch ($faseFinal) {
        case 'oitavas':
            // Continuar com a classificação das oitavas de final
            break;

        case 'quartas':
            // Chamar a função para classificar quartas de final
            classificarQuartasDeFinal();
            return;

        case 'semifinais':
            // Chamar a função para classificar semifinais
            classificarSemifinais();
            return;

        case 'final':
            // Chamar a função para classificar final
            classificarFinal();
            return;

        default:
            die("Fase final desconhecida.");
    }

    $num_oitavas = 16; // Número esperado de times para oitavas de final

    // Verificar se o número de grupos é válido
    if ($numeroGrupos <= 0) {
        die("Número de grupos inválido.");
    }

    // Calcular a quantidade de times por grupo que deve ser classificada
    $times_por_grupo = intdiv($num_oitavas, $numeroGrupos);

    if ($times_por_grupo < 1) {
        die("Número de times classificados insuficiente para iniciar as oitavas de final.");
    }

    // Limpar a tabela de oitavas de final e confrontos
    $conn->query("TRUNCATE TABLE oitavas_de_final");
    $conn->query("TRUNCATE TABLE oitavas_de_final_confrontos");

    $times_classificados = [];

    // Obter times classificados de cada grupo
    for ($i = 1; $i <= $numeroGrupos; $i++) {
        $result = $conn->query("SELECT * FROM times WHERE grupo_id = $i ORDER BY pts DESC, sg DESC, gm DESC LIMIT $times_por_grupo");
        while ($row = $result->fetch_assoc()) {
            $times_classificados[$i][] = $row;
        }
    }

    // Verificar se temos exatamente 16 times classificados
    $total_times_classificados = array_reduce($times_classificados, function($carry, $group) {
        return $carry + count($group);
    }, 0);

    if ($total_times_classificados != $num_oitavas) {
        die("Erro na classificação dos times para as oitavas de final.");
    }

    // Organizar os confrontos das oitavas de final
    $confrontos = [];
    $num_confrontos = $num_oitavas / 2;

    for ($i = 1; $i <= $numeroGrupos / 2; $i++) {
        $grupoA = $i;
        $grupoB = $i + $numeroGrupos / 2;

        // Ordenar times de cada grupo
        $timesGrupoA = $times_classificados[$grupoA];
        $timesGrupoB = $times_classificados[$grupoB];

        // Criar confrontos entre os times dos grupos
        for ($j = 0; $j < count($timesGrupoA); $j++) {
            if ($j >= count($timesGrupoB)) break; // Evitar acesso fora dos limites
            $timeA = $timesGrupoA[$j];
            $timeB = $timesGrupoB[count($timesGrupoB) - 1 - $j]; // Últimos times de B

            $confrontos[] = [
                'timeA' => $timeA,
                'timeB' => $timeB
            ];
        }
    }

    // Inserir os confrontos das oitavas de final
    foreach ($confrontos as $confronto) {
        $timeA = $confronto['timeA'];
        $timeB = $confronto['timeB'];

        // Inserir os times classificados para as oitavas de final
        $stmt = $conn->prepare("INSERT INTO oitavas_de_final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $timeA['id'], $timeA['grupo_id'], $timeA['nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['id'], $timeB['grupo_id'], $timeB['nome']);
        $stmt->execute();
        $stmt->close();

        // Inserir os confrontos das oitavas de final
        $stmt = $conn->prepare("INSERT INTO oitavas_de_final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'oitavas')");
        $stmt->bind_param("ss", $timeA['nome'], $timeB['nome']);
        $stmt->execute();
        $stmt->close();
    }
    atualizarFaseExecutada('oitavas');
}

function classificarQuartasDeFinal() {
    
    global $conn;
    if (faseJaExecutada('quartas')) {
        return;
    }
    // Limpar a tabela de quartas de final e confrontos
    $conn->query("TRUNCATE TABLE quartas_de_final");
    $conn->query("TRUNCATE TABLE quartas_de_final_confrontos");

    // Obter a fase final
    $stmt = $conn->prepare("SELECT fase_final FROM configuracoes LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($fase_final);
    $stmt->fetch();
    $stmt->close();

    // Verificar a fase final
    if ($fase_final == 'oitavas') {
        // Verificar se existem confrontos das oitavas de final
        $result = $conn->query("SELECT COUNT(*) FROM oitavas_de_final_confrontos");
        $row = $result->fetch_row();
        $temConfrontosOitavas = $row[0] > 0;

        if ($temConfrontosOitavas) {
            // Obter os confrontos das oitavas de final com os resultados dos jogos
            $result = $conn->query("SELECT * FROM oitavas_de_final_confrontos");
            $timesGols = [];

            while ($row = $result->fetch_assoc()) {
                $timeA_nome = $row['timeA_nome'];
                $timeB_nome = $row['timeB_nome'];

                // Obter gols marcados por cada time
                $stmt = $conn->prepare("SELECT SUM(gols_marcados) FROM jogos WHERE time_id = (SELECT id FROM times WHERE nome = ?)");
                $stmt->bind_param("s", $timeA_nome);
                $stmt->execute();
                $stmt->bind_result($timeA_gols);
                $stmt->fetch();
                $stmt->close();

                $stmt = $conn->prepare("SELECT SUM(gols_marcados) FROM jogos WHERE time_id = (SELECT id FROM times WHERE nome = ?)");
                $stmt->bind_param("s", $timeB_nome);
                $stmt->execute();
                $stmt->bind_result($timeB_gols);
                $stmt->fetch();
                $stmt->close();

                // Adicionar resultados para classificação
                $timesGols[$timeA_nome] = $timeA_gols;
                $timesGols[$timeB_nome] = $timeB_gols;
            }

            // Ordenar os times baseando-se na quantidade de gols marcados
            arsort($timesGols); // Ordenar em ordem decrescente

            // Obter os 8 melhores times
            $timesClassificados = array_slice(array_keys($timesGols), 0, 8);

            // Inserir os times classificados na tabela de quartas de final
            foreach ($timesClassificados as $time_nome) {
                $stmt = $conn->prepare("INSERT INTO quartas_de_final (time_nome) VALUES (?)");
                $stmt->bind_param("s", $time_nome);
                $stmt->execute();
                $stmt->close();
            }

            // Organizar os confrontos das quartas de final
            $numConfrontos = count($timesClassificados) / 2;
            $confrontos = [];

            for ($i = 0; $i < $numConfrontos; $i++) {
                $timeA = $timesClassificados[$i];
                $timeB = $timesClassificados[$numConfrontos + $i];

                $confrontos[] = [
                    'timeA' => $timeA,
                    'timeB' => $timeB
                ];
            }

            // Inserir os confrontos das quartas de final
            foreach ($confrontos as $confronto) {
                $stmt = $conn->prepare("INSERT INTO quartas_de_final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'quartas')");
                $stmt->bind_param("ss", $confronto['timeA'], $confronto['timeB']);
                $stmt->execute();
                $stmt->close();
            }
        }
    } elseif ($fase_final == 'quartas') {
        // Obter a configuração da fase final
        $result = $conn->query("SELECT fase_final, numero_grupos FROM configuracoes WHERE id = 1");
        $config = $result->fetch_assoc();
        $faseFinal = $config['fase_final'];
        $numeroGrupos = (int) $config['numero_grupos'];
    
        if ($faseFinal == 'quartas') {
            // Calcular a quantidade de times por grupo para as quartas
            $timesPorGrupo = 8 / $numeroGrupos;
    
            if ($timesPorGrupo <= 0 || $timesPorGrupo != intval($timesPorGrupo)) {
                die("Número de times por grupo inválido para as quartas de final.");
            }
    
            // Limpar a tabela de quartas de final e confrontos
            $conn->query("TRUNCATE TABLE quartas_de_final");
            $conn->query("TRUNCATE TABLE quartas_de_final_confrontos");
    
            // Obter os times classificados de cada grupo
            $timesClassificados = [];
            $gruposClassificados = [];
            for ($i = 1; $i <= $numeroGrupos; $i++) {
                $result = $conn->query("SELECT id, nome, grupo_id FROM times WHERE grupo_id = $i ORDER BY pts DESC LIMIT $timesPorGrupo");
                while ($row = $result->fetch_assoc()) {
                    $timesClassificados[] = $row;
                    $gruposClassificados[$i][] = $row;
                }
            }
    
            // Verificar se temos exatamente 8 times classificados
            if (count($timesClassificados) != 8) {
                die("Erro na classificação dos times para as quartas de final.");
            }
    
            // Inserir os times classificados na tabela de quartas de final
            foreach ($timesClassificados as $time) {
                $stmt = $conn->prepare("INSERT INTO quartas_de_final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $time['id'], $grupo_nome, $time['nome']);
                $grupo_nome = $conn->query("SELECT nome FROM grupos WHERE id = " . $time['grupo_id'])->fetch_assoc()['nome'];
                $stmt->execute();
                $stmt->close();
            }
    
            // Organizar os confrontos das quartas de final
            $confrontos = [];
            $numGrupos = $numeroGrupos;
    
            for ($i = 1; $i <= $numGrupos / 2; $i++) {
                $grupoA = $i;
                $grupoB = $i + $numGrupos / 2;
    
                $timesGrupoA = $gruposClassificados[$grupoA];
                $timesGrupoB = $gruposClassificados[$grupoB];
    
                // Criação dos confrontos
                for ($j = 0; $j < count($timesGrupoA); $j++) {
                    $timeA = $timesGrupoA[$j];
                    $timeB = $timesGrupoB[count($timesGrupoB) - $j - 1];
    
                    $confrontos[] = [
                        'timeA' => $timeA,
                        'timeB' => $timeB
                    ];
                }
            }
            // Inserir os confrontos das quartas de final
            foreach ($confrontos as $confronto) {
                $stmt = $conn->prepare("INSERT INTO quartas_de_final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'quartas')");
                $stmt->bind_param("ss", $confronto['timeA']['nome'], $confronto['timeB']['nome']);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    atualizarFaseExecutada('quartas');
}
function classificarSemifinais() {
    global $conn;

    // Verificar se a fase de semifinais já foi executada
    if (faseJaExecutada('semifinais')) {
        return;
    }

    // Limpar a tabela de semifinais e confrontos
    $conn->query("TRUNCATE TABLE semifinais");
    $conn->query("TRUNCATE TABLE semifinais_confrontos");

    // Obter os vencedores dos confrontos das quartas de finais
    $result = $conn->query("
        SELECT 
            qfc.timeA_nome AS timeA_nome,
            qfc.timeB_nome AS timeB_nome,
            qfc.gols_marcados_timeA AS gols_marcados_timeA,
            qfc.gols_marcados_timeB AS gols_marcados_timeB,
            CASE
                WHEN qfc.gols_marcados_timeA > qfc.gols_marcados_timeB THEN qfc.timeA_nome
                WHEN qfc.gols_marcados_timeB > qfc.gols_marcados_timeA THEN qfc.timeB_nome
                ELSE NULL
            END AS time_classificado
        FROM quartas_de_final_confrontos qfc
    ");

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $time_classificado = $row['time_classificado'];
        if ($time_classificado) {
            // Obter informações do time classificado
            $time_result = $conn->query("SELECT * FROM quartas_de_final WHERE time_nome = '$time_classificado'")->fetch_assoc();
            $times[] = $time_result;
        }
    }

    $num_times = count($times);
    $num_semifinais = 4; // Número esperado de times para semifinais
    $min_times = min($num_times, $num_semifinais);

    if ($min_times < 2) {
        die("Número de times classificados insuficiente para iniciar as semifinais.");
    }

    // Organizar os confrontos das semifinais
    $confrontos = [
        [$times[0], $times[3]], // Primeiro contra o quarto
        [$times[1], $times[2]]  // Segundo contra o terceiro
    ];

    // Inserir os times classificados para as semifinais e os confrontos
    foreach ($confrontos as $confronto) {
        $timeA = $confronto[0];
        $timeB = $confronto[1];

        // Inserir os times classificados para as semifinais
        $stmt = $conn->prepare("INSERT INTO semifinais (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();

        // Inserir os confrontos das semifinais
        $stmt = $conn->prepare("INSERT INTO semifinais_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'semifinais')");
        $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
        $stmt->execute();
        $stmt->close();
    }
    
    // Atualizar o status da fase como executada
    atualizarFaseExecutada('semifinais');
}

function classificarFinal() {
    global $conn;

    // Verificar a fase final configurada
    $config_result = $conn->query("SELECT fase_final FROM configuracoes LIMIT 1");
    if (!$config_result) {
        echo "Erro na consulta de configuração: " . $conn->error;
        return;
    }
    $config = $config_result->fetch_assoc();
    $fase_final = $config['fase_final'];

    echo "Fase final configurada: " . $fase_final . "<br>";

    // Executar somente se a fase final configurada for 'final'
    if ($fase_final !== 'final') {
        echo "A fase final configurada não é 'final'.";
        return;
    }

    // Verificar se a fase de final já foi executada
    if (faseJaExecutada('final')) {
        echo "A fase final já foi executada.";
        return;
    }

    // Limpar a tabela de final e confrontos
    if (!$conn->query("TRUNCATE TABLE final")) {
        echo "Erro ao truncar tabela final: " . $conn->error;
        return;
    }
    if (!$conn->query("TRUNCATE TABLE final_confrontos")) {
        echo "Erro ao truncar tabela final_confrontos: " . $conn->error;
        return;
    }

    // Obter os vencedores dos confrontos das semifinais
    $result = $conn->query("
        SELECT 
            sfc.timeA_nome AS timeA_nome,
            sfc.timeB_nome AS timeB_nome,
            sfc.gols_marcados_timeA AS gols_marcados_timeA,
            sfc.gols_marcados_timeB AS gols_marcados_timeB,
            CASE
                WHEN sfc.gols_marcados_timeA > sfc.gols_marcados_timeB THEN sfc.timeA_nome
                WHEN sfc.gols_marcados_timeB > sfc.gols_marcados_timeA THEN sfc.timeB_nome
                ELSE NULL
            END AS time_classificado
        FROM semifinais_confrontos sfc
    ");

    if (!$result) {
        echo "Erro na consulta de semifinais: " . $conn->error;
        return;
    }

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $time_classificado = $row['time_classificado'];
        if ($time_classificado) {
            $time_result = $conn->query("SELECT * FROM semifinais WHERE time_nome = '$time_classificado'")->fetch_assoc();
            if ($time_result) {
                $times[] = $time_result;
            } else {
                echo "Erro ao encontrar informações do time classificado: $time_classificado";
            }
        }
    }

    $num_times = count($times);
    $num_final = 2; // Número esperado de times para final
    $min_times = min($num_times, $num_final);

    if ($min_times < 2) {
        echo "Número de times classificados insuficiente para iniciar a final.";
        return;
    }

    // Organizar o confronto final
    $timeA = $times[0];
    $timeB = $times[1];

    // Inserir os times classificados para a final
    $stmt = $conn->prepare("INSERT INTO final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
    if ($stmt->execute()) {
        echo "Time A inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir time A: " . $stmt->error . "<br>";
    }

    $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
    if ($stmt->execute()) {
        echo "Time B inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir time B: " . $stmt->error . "<br>";
    }
    $stmt->close();

    // Inserir o confronto final
    $stmt = $conn->prepare("INSERT INTO final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'final')");
    $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
    if ($stmt->execute()) {
        echo "Confronto final inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir confronto final: " . $stmt->error . "<br>";
    }
    $stmt->close();

    // Atualizar o status da fase como executada
    atualizarFaseExecutada('final');
    // Atualizar a fase final para 'final'
    $stmt = $conn->prepare("UPDATE configuracoes SET fase_final = 'final' WHERE id = 1");
    $stmt->execute();
    $stmt->close();
}


// function classificarFinal() {
//     global $conn;
//     if (faseJaExecutada('final')) {
//         return;
//     }
//     // Limpar a tabela de final e confrontos
//     $conn->query("TRUNCATE TABLE final");
//     $conn->query("TRUNCATE TABLE final_confrontos");

//     // Obter os times classificados para a final
//     $result = $conn->query("SELECT * FROM semifinais ORDER BY id LIMIT 2");
//     $times = [];
//     while ($row = $result->fetch_assoc()) {
//         $times[] = $row;
//     }

//     $num_times = count($times);
//     $num_final = 2; // Número esperado de times para final
//     $min_times = min($num_times, $num_final);

//     if ($min_times < 2) {
//         die("Número de times classificados insuficiente para iniciar a final.");
//     }

//     // Organizar o confronto final
//     $timeA = $times[0];
//     $timeB = $times[1];

//     // Inserir os times classificados para a final
//     $stmt = $conn->prepare("INSERT INTO final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
//     $stmt->bind_param("iss", $timeA['time_id'], $timeA['grupo_nome'], $timeA['time_nome']);
//     $stmt->execute();
//     $stmt->bind_param("iss", $timeB['time_id'], $timeB['grupo_nome'], $timeB['time_nome']);
//     $stmt->execute();
//     $stmt->close();

//     // Inserir o confronto final
//     $stmt = $conn->prepare("INSERT INTO final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'final')");
//     $stmt->bind_param("ss", $timeA['time_nome'], $timeB['time_nome']);
//     $stmt->execute();
//     $stmt->close();
//     atualizarFaseExecutada('final');
// }

function classificarFases() {
    if (!faseJaExecutada('oitavas')) {
        classificarOitavasDeFinal();
    }

    if (!faseJaExecutada('quartas')) {
        classificarQuartasDeFinal();
    }

    if (!faseJaExecutada('semifinais')) {
        classificarSemifinais();
    }

    if (!faseJaExecutada('final')) {
        classificarFinal();
    }
}

// Inicializar a tabela de controle (chamar uma vez, se necessário)
inicializarFaseExecucao();

// Executar a classificação das fases
classificarFases();

echo "Classificação concluída com sucesso!";
?>
