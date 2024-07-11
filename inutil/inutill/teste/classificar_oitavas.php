<?php
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
function classificarOitavas() {
    global $conn;

    // Limpar as tabelas de oitavas de final e confrontos
    $conn->query("TRUNCATE TABLE oitavas_de_final");
    $conn->query("TRUNCATE TABLE oitavas_de_final_confrontos");

    // Obter a configuração atual
    $result = $conn->query("SELECT * FROM configuracoes WHERE id = 1");
    $config = $result->fetch_assoc();
    $num_grupos = $config['numero_grupos'];
    $times_por_grupo = $config['equipes_por_grupo'];
    $fase_final = $config['fase_final'];

    // Obter todos os times classificados
    $sql = "SELECT * FROM times ORDER BY pts DESC, sg DESC, gm DESC";
    $result = $conn->query($sql);

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = $row;
    }

    // Determinar os confrontos das oitavas de final
    $grupos = [];
    foreach ($times as $time) {
        $grupo_id = $time['grupo_id'];
        if (!isset($grupos[$grupo_id])) {
            $grupos[$grupo_id] = [];
        }
        $grupos[$grupo_id][] = $time;
    }

    $confrontos = [];
    foreach ($grupos as $grupo_id => $times) {
        usort($times, function($a, $b) {
            return $b['pts'] - $a['pts'] ?: $b['sg'] - $a['sg'] ?: $b['gm'] - $a['gm'];
        });

        $times_classificados[$grupo_id] = array_slice($times, 0, 2);
    }

    // Confrontos baseados na combinação de grupos
    $confrontos[] = [$times_classificados[1][0], $times_classificados[2][1]]; // Exemplo: Grupo A1 vs Grupo B2
    $confrontos[] = [$times_classificados[1][1], $times_classificados[2][0]]; // Exemplo: Grupo A2 vs Grupo B1
    // Continue para outros grupos

    foreach ($confrontos as $confronto) {
        $timeA = $confronto[0];
        $timeB = $confronto[1];

        // Inserir os times classificados para as oitavas de final
        $stmt = $conn->prepare("INSERT INTO oitavas_de_final (time_id, grupo_nome, time_nome) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $timeA['id'], $timeA['nome'], $timeA['nome']);
        $stmt->execute();
        $stmt->bind_param("iss", $timeB['id'], $timeB['nome'], $timeB['nome']);
        $stmt->execute();
        $stmt->close();

        // Inserir os confrontos das oitavas de final
        $stmt = $conn->prepare("INSERT INTO oitavas_de_final_confrontos (timeA_nome, timeB_nome, fase) VALUES (?, ?, 'oitavas')");
        $stmt->bind_param("ss", $timeA['nome'], $timeB['nome']);
        $stmt->execute();
        $stmt->close();
    }
}

classificarOitavas();
?>
