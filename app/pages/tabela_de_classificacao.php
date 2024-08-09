<!DOCTYPE html>
<html>
<head>
    <title>Tabela de Classificação</title>
</head>
<body>
<?php include 'header_classificacao.php'; ?>
<h1>FASE DE GRUPOS</h1>
<div id="tabela-wrapper">
    <h4>Tabela de Classificação</h4>
    <?php mostrarGrupos(); ?>

    <div id="legenda-simbolos">
        <div>
            <div class="simbolo" style="background-color: green;"></div>
            <span class="descricao">Vitória</span>
        </div>
        <div>
            <div class="simbolo" style="background-color: red;"></div>
            <span class="descricao">Derrota</span>
        </div>
        <div>
            <div class="simbolo" style="background-color: gray;"></div>
            <span class="descricao">Empate</span>
        </div>
        <div>
            <div class="simbolo" style="background-color: lightgray;"></div>
            <span class="descricao">Não houve jogo</span>
        </div>
    </div>
</div>

<?php
function mostrarGrupos() {
    include '../config/conexao.php';

    $sqlGrupos = "SELECT id, nome FROM grupos ORDER BY nome";
    $resultGrupos = $conn->query($sqlGrupos);

    if ($resultGrupos->num_rows > 0) {
        while ($rowGrupos = $resultGrupos->fetch_assoc()) {
            $grupoId = $rowGrupos['id'];
            $grupoNome = $rowGrupos['nome'];

            echo '<div class="grupo-container">';
            echo '<div class="grupo-header">' . $grupoNome . '</div>';
            echo '<div class="tabela-flex">';
            echo '<div class="tabela-flex-header">';
            echo '<div class="clube">Clube</div>'; // Coluna de Nome do Clube
            echo '<div class="small-col">P</div>'; // Coluna de Pontos
            echo '<div class="small-col">J</div>'; // Coluna de Partidas
            echo '<div class="small-col">V</div>'; // Coluna de Vitórias
            echo '<div class="small-col">E</div>'; // Coluna de Empates
            echo '<div class="small-col">D</div>'; // Coluna de Derrotas
            echo '<div class="small-col">GP</div>'; // Coluna de Gols Pró
            echo '<div class="small-col">GC</div>'; // Coluna de Gols Contra
            echo '<div class="small-col">SG</div>'; // Coluna de Saldo de Gols
            echo '<div class="small-col">%</div>'; // Coluna de Porcentagem de Aproveitamento
            echo '<div class="larger-col">ÚLT. JOGOS</div>'; // Coluna de Últimos Jogos
            echo '</div>';

            $sqlTimes = "SELECT t.id, t.nome, t.logo, 
                                COALESCE(SUM(j.gols_marcados_timeA), 0) + COALESCE(SUM(j.gols_marcados_timeB), 0) AS gm,
                                COALESCE(SUM(j.gols_marcados_timeB), 0) + COALESCE(SUM(j.gols_marcados_timeA), 0) AS gc,
                                COALESCE(SUM(j.gols_marcados_timeA > j.gols_marcados_timeB), 0) AS vitorias,
                                COALESCE(SUM(j.gols_marcados_timeA = j.gols_marcados_timeB), 0) AS empates,
                                COALESCE(SUM(j.gols_marcados_timeA < j.gols_marcados_timeB), 0) AS derrotas,
                                COALESCE(SUM(j.gols_marcados_timeA - j.gols_marcados_timeB), 0) AS sg,
                                COUNT(j.id) AS partidas,
                                COALESCE(SUM(j.gols_marcados_timeA > j.gols_marcados_timeB), 0) * 3 + COALESCE(SUM(j.gols_marcados_timeA = j.gols_marcados_timeB), 0) AS pts
                         FROM times t
                         LEFT JOIN jogos_fase_grupos j ON t.id = j.timeA_id OR t.id = j.timeB_id
                         WHERE t.grupo_id = $grupoId
                         GROUP BY t.id
                         ORDER BY pts DESC, gm DESC, gc ASC, sg DESC";
            $resultTimes = $conn->query($sqlTimes);

            if ($resultTimes->num_rows > 0) {
                $posicao = 1;
                while ($rowTimes = $resultTimes->fetch_assoc()) {
                    echo '<div class="tabela-flex-row">';
                    echo '<div class="time-info">';
                    echo '<span class="posicao_num">' . $posicao . '</span>';
                    if (!empty($rowTimes['logo'])) {
                        $imageData = base64_encode($rowTimes['logo']);
                        $imageSrc = 'data:image/jpeg;base64,'.$imageData;
                        echo '<img src="' . $imageSrc . '" class="logo-time">';
                    }
                    echo '<span class="time-name">' . $rowTimes['nome'] . '</span>';
                    echo '</div>';
                    echo '<div class="small-col">' . $rowTimes['pts'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['partidas'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['vitorias'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['empates'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['derrotas'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['gm'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['gc'] . '</div>';
                    echo '<div class="small-col">' . $rowTimes['sg'] . '</div>';
                    echo '<div class="small-col">' . formatarPorcentagemAproveitamento($rowTimes['vitorias'], $rowTimes['partidas']) . '</div>';
                    echo '<div class="larger-col">';
                    echo gerarUltimosJogos($rowTimes['id']);
                    echo '</div>';
                    echo '</div>';
                    $posicao++;
                }
            } else {
                echo '<div class="tabela-flex-row"><div colspan="11">Nenhum time encontrado para este grupo.</div></div>';
            }

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "Nenhum grupo encontrado.";
    }

    $conn->close();
}

function formatarPorcentagemAproveitamento($vitorias, $partidas) {
    if ($partidas > 0) {
        $porcentagem = number_format(($vitorias / $partidas) * 100, 1);
        if (substr($porcentagem, -2) == '.0') {
            return substr($porcentagem, 0, -2);
        } else {
            return $porcentagem;
        }
    } else {
        return '0';
    }
}

function gerarUltimosJogos($timeId) {
    include '../config/conexao.php';

    $sqlJogos = "SELECT CASE 
                    WHEN timeA_id = $timeId THEN resultado_timeA
                    WHEN timeB_id = $timeId THEN resultado_timeB
                    ELSE 'G'
                  END AS resultado
                  FROM jogos_fase_grupos 
                  WHERE timeA_id = $timeId OR timeB_id = $timeId 
                  ORDER BY data_jogo DESC 
                  LIMIT 5";
    $resultJogos = $conn->query($sqlJogos);

    $ultimosJogos = [];

    if ($resultJogos->num_rows > 0) {
        while ($rowJogos = $resultJogos->fetch_assoc()) {
            $ultimosJogos[] = $rowJogos['resultado'];
        }
    }

    while (count($ultimosJogos) < 5) {
        $ultimosJogos[] = 'G'; // Cinza para jogos não existentes
    }

    $output = '';
    foreach ($ultimosJogos as $resultado) {
        if ($resultado == 'V') {
            $output .= '<div class="inf"></div>';
        } elseif ($resultado == 'D') {
            $output .= '<div class="inf2" ></div>';
        } elseif ($resultado == 'E') {
            $output .= '<div class="inf3" ></div>';
        } else {
            $output .= '<div class="inf4" ></div>';
        }   
    }

    return $output;
}
?>
<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }
</script>
</body>
</html>
