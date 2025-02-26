<?php
include '../../config/conexao.php';
session_start();

// Verifica se o usuário está autenticado e se é um administrador
if (!isset($_SESSION['admin_id'])) {
    // Armazenar a URL de referência para redirecionar após o login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

include("../../actions/cadastro_adm/session_check.php");

$isAdmin = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Busca o campeonato ativo
$sql = "SELECT id FROM campeonatos WHERE ativo = TRUE LIMIT 1";
$stmt = $conn->query($sql);
$campeonatoAtivo = $stmt->fetch_assoc();

if (!$campeonatoAtivo) {
    die("Nenhum campeonato ativo encontrado.");
}

$campeonatoId = $campeonatoAtivo['id'];

function gerarRodadas($campeonatoId) {
    global $conn;

    // Busca os grupos do campeonato ativo
    $sqlGrupos = "SELECT id, nome FROM grupos WHERE campeonato_id = ? ORDER BY nome";
    $stmtGrupos = $conn->prepare($sqlGrupos);
    $stmtGrupos->bind_param("i", $campeonatoId);
    $stmtGrupos->execute();
    $resultGrupos = $stmtGrupos->get_result();

    $grupos = [];
    if ($resultGrupos->num_rows > 0) {
        while ($rowGrupos = $resultGrupos->fetch_assoc()) {
            $grupoId = $rowGrupos['id'];
            $grupoNome = $rowGrupos['nome'];

            // Buscar times no grupo
            $sqlTimes = "SELECT id, nome, logo FROM times WHERE grupo_id = ? AND campeonato_id = ?";
            $stmtTimes = $conn->prepare($sqlTimes);
            $stmtTimes->bind_param("ii", $grupoId, $campeonatoId);
            $stmtTimes->execute();
            $resultTimes = $stmtTimes->get_result();

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

function inserirOuAtualizarConfrontos($rodadas, $campeonatoId) {
    global $conn;

    foreach ($rodadas as $rodada => $grupos) {
        foreach ($grupos as $grupoNome => $partidas) {
            // Primeiro, exclua todos os confrontos antigos para o grupo e rodada
            $stmtDelete = $conn->prepare("DELETE FROM jogos_fase_grupos WHERE grupo_id = ? AND rodada = ? AND campeonato_id = ?");
            $stmtDelete->bind_param("iii", $partidas[0]['grupo_id'], $rodada, $campeonatoId);
            $stmtDelete->execute();
            $stmtDelete->close();

            foreach ($partidas as $partida) {
                // Inserir um novo confronto
                $stmtInsert = $conn->prepare("INSERT INTO jogos_fase_grupos (grupo_id, timeA_id, timeB_id, nome_timeA, nome_timeB, data_jogo, rodada, campeonato_id) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
                $stmtInsert->bind_param("iiissii", $partida['grupo_id'], $partida['timeA_id'], $partida['timeB_id'], $partida['timeA_nome'], $partida['timeB_nome'], $rodada, $campeonatoId);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
        }
    }
}

$rodadas = gerarRodadas($campeonatoId);
inserirOuAtualizarConfrontos($rodadas, $campeonatoId);
$conn->close();
header('Location: /copadaspanelas/app/pages/adm/rodadas_adm.php');
exit();
?>