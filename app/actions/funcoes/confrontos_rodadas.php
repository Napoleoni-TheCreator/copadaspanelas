<?php
include '../../config/conexao.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

function gerarRodadas() {
    global $conn;

    $sqlGrupos = "SELECT id, nome FROM grupos ORDER BY nome";
    $resultGrupos = $conn->query($sqlGrupos);

    $grupos = [];
    if ($resultGrupos->num_rows > 0) {
        while ($rowGrupos = $resultGrupos->fetch_assoc()) {
            $grupoId = $rowGrupos['id'];
            $grupoNome = $rowGrupos['nome'];

            // Buscar times no grupo
            $sqlTimes = "SELECT id, nome, logo FROM times WHERE grupo_id = $grupoId";
            $resultTimes = $conn->query($sqlTimes);

            $times = [];
            if ($resultTimes->num_rows > 0) {
                while ($rowTimes = $resultTimes->fetch_assoc()) {
                    $times[] = $rowTimes;
                }
            }

            $grupos[$grupoNome] = ['id' => $grupoId, 'times' => $times];
        }
    }

    $rodadas = [];
    foreach ($grupos as $grupoNome => $grupoData) {
        $times = $grupoData['times'];
        $grupoId = $grupoData['id'];
        $quantidadeTimes = count($times);
        if ($quantidadeTimes % 2 != 0) {
            $times[] = ["id" => null, "nome" => "BYE", "logo" => null]; // Adiciona um BYE se o número de times for ímpar
            $quantidadeTimes++;
        }

        $totalRodadas = $quantidadeTimes - 1;
        $jogosPorRodada = $quantidadeTimes / 2;

        // Gerar todas as rodadas usando o algoritmo round-robin
        $rodadasGrupo = [];
        for ($rodada = 0; $rodada < $totalRodadas; $rodada++) {
            $rodadasGrupo[$rodada] = [];
            for ($jogo = 0; $jogo < $jogosPorRodada; $jogo++) {
                $timeA = $times[$jogo];
                $timeB = $times[$quantidadeTimes - 1 - $jogo];
                if ($timeA["nome"] != "BYE" && $timeB["nome"] != "BYE") {
                    $rodadasGrupo[$rodada][] = [
                        'grupo_id' => $grupoId,
                        'timeA_id' => $timeA['id'],
                        'timeA_nome' => $timeA['nome'],
                        'timeB_id' => $timeB['id'],
                        'timeB_nome' => $timeB['nome']
                    ];
                }
            }
            // Rotaciona os times, exceto o primeiro
            $times = array_merge([$times[0]], array_slice($times, -1), array_slice($times, 1, -1));
        }

        foreach ($rodadasGrupo as $index => $partidas) {
            $rodadas[$index + 1][$grupoNome] = $partidas;
        }
    }

    return $rodadas;
}

function inserirOuAtualizarConfrontos($rodadas) {
    global $conn;

    foreach ($rodadas as $rodada => $grupos) {
        foreach ($grupos as $grupoNome => $partidas) {
            // Primeiro, exclua todos os confrontos antigos para o grupo e rodada
            $stmtDelete = $conn->prepare("DELETE FROM jogos_fase_grupos WHERE grupo_id = ? AND rodada = ?");
            $stmtDelete->bind_param("ii", $partidas[0]['grupo_id'], $rodada);
            $stmtDelete->execute();
            $stmtDelete->close();

            foreach ($partidas as $partida) {
                // Adiciona depuração para verificar dados
                echo "<pre>";
                print_r($partida);
                echo "</pre>";
                // Inserir um novo confronto
                $stmtInsert = $conn->prepare("INSERT INTO jogos_fase_grupos (grupo_id, timeA_id, timeB_id, nome_timeA, nome_timeB, data_jogo, rodada) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                $stmtInsert->bind_param("iiissi", $partida['grupo_id'], $partida['timeA_id'], $partida['timeB_id'], $partida['timeA_nome'], $partida['timeB_nome'], $rodada);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        }
    }
}

$rodadas = gerarRodadas();
inserirOuAtualizarConfrontos($rodadas);
$conn->close();
header('Location: /copadaspanelas/app/pages/adm/rodadas_adm.php');
exit();
?>
