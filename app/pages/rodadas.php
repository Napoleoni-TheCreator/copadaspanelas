<!DOCTYPE html>
<html>
<head>
    <title>Rodadas das Fases de Grupo</title>
    <link rel="stylesheet" href="../../public/css/adm/rodadas_adm.css">
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
</head>
<body>
<?php include 'header_classificacao.php'; ?>
<h1 id="dynamic-text">FASES DE GRUPO</h1>
<div class="rodada_container1">
<div id="rodadas-wrapper">
    <div class="nav-arrow left" onclick="previousRodada()"><img src="../../public/img/esquerda.svg" alt=""></div>
    <div class="table-container">
        <?php exibirRodadas(); ?>
    </div>
    <div class="nav-arrow right" onclick="nextRodada()"><img src="../../public/img/direita.svg" alt=""></div>
</div>
</div>
<?php
function exibirRodadas() {
    include '../config/conexao.php';

    $sqlRodadas = "SELECT DISTINCT rodada FROM jogos_fase_grupos ORDER BY rodada";
    $resultRodadas = $conn->query($sqlRodadas);

    if ($resultRodadas->num_rows > 0) {
        while ($rowRodada = $resultRodadas->fetch_assoc()) {
            $rodada = $rowRodada['rodada'];
            echo '<div class="rodada-container">';
            echo '<h2 class="rodada-header">' . $rodada . 'ª RODADA</h2>';

            $sqlGrupos = "SELECT DISTINCT grupo_id, nome AS grupo_nome FROM jogos_fase_grupos 
                          JOIN grupos ON jogos_fase_grupos.grupo_id = grupos.id ORDER BY grupo_id";
            $resultGrupos = $conn->query($sqlGrupos);

            while ($rowGrupo = $resultGrupos->fetch_assoc()) {
                $grupoId = $rowGrupo['grupo_id'];
                $grupoNome = substr($rowGrupo['grupo_nome'], -1);

                $sqlConfrontos = "SELECT jfg.id, tA.nome AS nome_timeA, tB.nome AS nome_timeB, 
                                         tA.logo AS logo_timeA, tB.logo AS logo_timeB, 
                                         jfg.gols_marcados_timeA, jfg.gols_marcados_timeB
                                  FROM jogos_fase_grupos jfg
                                  JOIN times tA ON jfg.timeA_id = tA.id
                                  JOIN times tB ON jfg.timeB_id = tB.id
                                  WHERE jfg.grupo_id = $grupoId AND jfg.rodada = $rodada";

                $resultConfrontos = $conn->query($sqlConfrontos);

                if ($resultConfrontos->num_rows > 0) {
                    while ($rowConfronto = $resultConfrontos->fetch_assoc()) {
                        $timeA_nome = $rowConfronto['nome_timeA'];
                        $timeB_nome = $rowConfronto['nome_timeB'];
                        $logoA = !empty($rowConfronto['logo_timeA']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeA']) : '';
                        $logoB = !empty($rowConfronto['logo_timeB']) ? 'data:image/jpeg;base64,' . base64_encode($rowConfronto['logo_timeB']) : '';
                        $golsA = $rowConfronto['gols_marcados_timeA'];
                        $golsB = $rowConfronto['gols_marcados_timeB'];

                        echo '<div class="time_teste">';
                        echo '<div class="time-row">';
                        if ($logoA) {
                            echo '<img src="' . $logoA . '" class="logo-time">';
                        }
                        echo '<span class="time-name">' . $timeA_nome . '</span>';
                        echo '</div>';
                        echo '<div class="no-break">' . $golsA . ' X ' . $golsB . '</div>';
                        echo '<div class="time-row">';
                        echo '<span class="time-name_b">' . $timeB_nome . '</span>';
                        if ($logoB) {
                            echo '<img src="' . $logoB . '" class="logo-time">';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Nenhum confronto encontrado para o grupo ' . $grupoNome . ' na ' . $rodada . 'ª rodada.</p>';
                }
            }

            echo '</div>';
        }
    } else {
        echo '<p>Nenhuma rodada encontrada.</p>';
    }

    $conn->close();
}
?>
<script>
    var currentRodadaIndex = 0;
    var rodadaContainers = document.getElementsByClassName('rodada-container');

    function showRodada(index) {
        for (var i = 0; i < rodadaContainers.length; i++) {
            rodadaContainers[i].style.display = i === index ? 'block' : 'none';
        }
    }

    function previousRodada() {
        if (currentRodadaIndex > 0) {
            currentRodadaIndex--;
            showRodada(currentRodadaIndex);
        }
    }

    function nextRodada() {
        if (currentRodadaIndex < rodadaContainers.length - 1) {
            currentRodadaIndex++;
            showRodada(currentRodadaIndex);
        }
    }

    showRodada(currentRodadaIndex);
</script>

<?php include 'footer.php'?>   
</body>
</html>
