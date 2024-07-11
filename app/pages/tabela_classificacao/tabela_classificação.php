<!DOCTYPE html>
<html>
<head>
    <title>Tabela de classificação</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0; /* Remova as margens superior e inferior aqui */
            margin-bottom: 10%;
            background-color: #f0f8ff; /* Azul claro como fundo */
        }
        #tabela-wrapper {
            background-color: #f0f8ff; /* Azul claro como fundo */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%; /* Largura da div ajustável conforme necessário */
            margin-top: 20px; /* Ajusta a margem do topo para o título */
            display: flex;
            flex-direction: column; /* Organiza conteúdo em coluna */
            align-items: center; /* Centraliza itens horizontalmente */
        }

        h1 {
            margin-bottom: 20px; /* Espaço abaixo do título */
        }
        .grupo-container {
            width: 100%; /* Largura total do conteúdo do grupo */
            margin-bottom: 20px; /* Espaçamento entre grupos */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: none; /* Remove todas as bordas da tabela */
        }
        th, td {
            border: none; /* Remove todas as bordas das células */
            padding: 3px 0; /* Padding vertical de 8px e removendo padding horizontal */
            text-align: left; /* Alinha todo o conteúdo à esquerda */
            vertical-align: middle; /* Centraliza verticalmente o conteúdo das células */
        }
        th {
            background-color: #f2f2f2;
        }
        .grupo-header {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .time-cell {
            white-space: nowrap; /* Impede que o texto quebre em várias linhas */
            overflow: hidden; /* Esconde qualquer conteúdo que exceda a largura máxima */
            text-overflow: ellipsis; /* Adiciona reticências no final do texto se ele for muito longo */
            text-align: left; /* Alinha o texto à esquerda dentro da célula */
            vertical-align: middle; /* Centraliza verticalmente o conteúdo das células */
            max-width: 250px; /* Largura máxima para o nome do time */
        }
        .logo-time {
            max-width: 50px; /* Tamanho máximo da logo (ajuste conforme necessário) */
            max-height: 50px; /* Altura máxima da logo (ajuste conforme necessário) */
            vertical-align: middle; /* Centraliza verticalmente a logo */
            margin-right: 5px; /* Espaço mínimo entre a logo e o texto do time */
            border-radius: 100%;/* Utiliza 50% para criar um círculo*/

        }
        .small-col {
            width: 70px; /* Largura das colunas pequenas (P, J, V, E, D, GP, GC, SG) */
            max-width: 70px;
        }
        .larger-col {
            width: 50px; /* Largura das colunas maiores (% e ÚLT. JOGO) */
        }
        .resultado-vitoria {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: green;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-derrota {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-empate {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: gray;
            border-radius: 50%;
            margin-right: 2px;
        }
        .resultado-indefinido {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: lightgray;
            border-radius: 50%;
            margin-right: 2px;
        }
        #legenda-simbolos {
            margin-top: 20px; /* Espaço acima da legenda */
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
            text-align: left;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .simbolo {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
        }
        .descricao {
            display: inline-block;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div id="tabela-wrapper">
        <h1>TABELA DE CLASSIFICAÇÃO</h1>
        <h4>FASE DE GRUPOS</h4>
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
        include '../../config/conexao.php';

        $sqlGrupos = "SELECT id, nome FROM grupos ORDER BY nome";
        $resultGrupos = $conn->query($sqlGrupos);

        if ($resultGrupos->num_rows > 0) {
            while ($rowGrupos = $resultGrupos->fetch_assoc()) {
                $grupoId = $rowGrupos['id'];
                $grupoNome = $rowGrupos['nome'];

                echo '<div class="grupo-container">';
                echo '<div class="grupo-header">' . $grupoNome . '</div>';
                echo '<table>';
                echo '<tr>';
                echo '<th>Clube</th>'; // Coluna de Nome do Clube
                echo '<th class="small-col">P</th>'; // Coluna de Pontos
                echo '<th class="small-col">J</th>'; // Coluna de Partidas
                echo '<th class="small-col">V</th>'; // Coluna de Vitórias
                echo '<th class="small-col">E</th>'; // Coluna de Empates
                echo '<th class="small-col">D</th>'; // Coluna de Derrotas
                echo '<th class="small-col">GP</th>'; // Coluna de Gols Pró
                echo '<th class="small-col">GC</th>'; // Coluna de Gols Contra
                echo '<th class="small-col">SG</th>'; // Coluna de Saldo de Gols
                echo '<th class="larger-col">%</th>'; // Coluna de Porcentagem de Aproveitamento
                echo '<th class="larger-col">ÚLT. JOGOS</th>'; // Coluna de Últimos Jogos
                echo '</tr>';

                // Consulta para buscar os times deste grupo, ordenados por pts descendentemente, gm descendentemente, gc ascendentemente, sg descendentemente
                $sqlTimes = "SELECT id, nome, logo, pts, vitorias, empates, derrotas, gm, gc, sg
                             FROM times 
                             WHERE grupo_id = $grupoId
                             ORDER BY pts DESC, gm DESC, gc ASC, sg DESC";
                $resultTimes = $conn->query($sqlTimes);

                if ($resultTimes->num_rows > 0) {
                    $posicao = 1;
                    while ($rowTimes = $resultTimes->fetch_assoc()) {
                        echo '<tr>';
                        // Exibir a posição do time ao lado da logo e do nome do time
                        echo '<td>';
                        echo '<span style="font-weight: bold; margin-right: 5px;">' . $posicao . '</span>';
                        // Exibir a logo do time se disponível
                        if (!empty($rowTimes['logo'])) {
                            $imageData = base64_encode($rowTimes['logo']);
                            $imageSrc = 'data:image/jpeg;base64,'.$imageData;
                            echo '<img src="' . $imageSrc . '" class="logo-time">';
                        }
                        // Exibir o nome do time
                        echo '<span class="time-cell">' . $rowTimes['nome'] . '</span>';
                        echo '</td>';
                        echo '<td class="small-col">' . $rowTimes['pts'] . '</td>';
                        $partidas = $rowTimes['vitorias'] + $rowTimes['empates'] + $rowTimes['derrotas'];
                        echo '<td class="small-col">' . $partidas . '</td>';
                        echo '<td class="small-col">' . $rowTimes['vitorias'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['empates'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['derrotas'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['gm'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['gc'] . '</td>';
                        echo '<td class="small-col">' . $rowTimes['sg'] . '</td>';
                        echo '<td class="larger-col">' . formatarPorcentagemAproveitamento($rowTimes['vitorias'], $partidas) . '</td>';
                        echo '<td class="larger-col">';
                        echo gerarUltimosJogos($rowTimes['id']);
                        echo '</td>';
                        echo '</tr>';
                        $posicao++;
                    }
                } else {
                    echo '<tr><td colspan="12">Nenhum time encontrado para este grupo.</td></tr>';
                }

                echo '</table>';
                echo '</div>'; // Fecha div .grupo-container
            }
        } else {
            echo "Nenhum grupo encontrado.";
        }

        $conn->close();
    }

    function formatarPorcentagemAproveitamento($vitorias, $partidas) {
        if ($partidas > 0) {
            $porcentagem = number_format(($vitorias / $partidas) * 100, 1);
            // Verifica se a porcentagem termina com .0 e remove se for o caso
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
        include '../../config/conexao.php';

        // Consulta para buscar os últimos resultados dos jogos ordenados por data decrescente e limitados aos últimos 5 jogos
        $sqlJogos = "SELECT resultado FROM jogos WHERE time_id = $timeId ORDER BY id DESC LIMIT 5";
        $resultJogos = $conn->query($sqlJogos);

        $ultimosJogos = [];

        if ($resultJogos->num_rows > 0) {
            while ($rowJogos = $resultJogos->fetch_assoc()) {
                $ultimosJogos[] = $rowJogos['resultado'];
            }
        }

        // Preenche com resultados cinza se houver menos de 5 jogos
        while (count($ultimosJogos) < 5) {
            $ultimosJogos[] = 'G'; // Cinza para jogos não existentes
        }

        // Monta a string HTML para exibir os últimos jogos
        $output = '';
        foreach ($ultimosJogos as $resultado) {
            if ($resultado == 'V') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: green; border-radius: 50%; margin-right: 2px;"></div>';
            } elseif ($resultado == 'D') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: red; border-radius: 50%; margin-right: 2px;"></div>';
            } elseif ($resultado == 'E') {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: gray; border-radius: 50%; margin-right: 2px;"></div>';
            } else {
                $output .= '<div style="display: inline-block; width: 10px; height: 10px; background-color: lightgray; border-radius: 50%; margin-right: 2px;"></div>';
            }
        }

        // Retornar a string HTML dos últimos jogos
        return $output;
    }
    ?>
</body>
</html>
