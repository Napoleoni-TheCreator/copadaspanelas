<!DOCTYPE html>
<html>
<head>
    <title>Tabela de Classificação</title>
    <style>
        /* Estilo atualizado */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
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
            padding: 8px; /* Padding vertical de 8px */
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
        .logo-time {
    max-width: 50px; /* Ajuste conforme necessário */
    max-height: 50px; /* Ajuste conforme necessário */
    vertical-align: middle; /* Alinha a logo verticalmente com o texto */
}

.time-cell {
    display: inline-block;
    vertical-align: middle; /* Alinha o nome do time verticalmente com a logo */
    margin-left: 5px; /* Espaço entre a logo e o nome */
}

.small-col {
    text-align: center; /* Alinha o texto ao centro */
}

.larger-col {
    text-align: center; /* Alinha o texto ao centro */
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
            flex-direction: column;
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
        <h3>Classificados</h3>
        <?php
// Inclua o arquivo de conexão e a função getClassificados
include '/opt/lampp/htdocs/CLASSIFICACAO/app/config/conexao.php';
include '/opt/lampp/htdocs/CLASSIFICACAO/app/pages/tabela_classificacao/getClassificados.php';

// Função para formatar a porcentagem de aproveitamento
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

// Função para gerar os últimos jogos
function gerarUltimosJogos($timeId) {
    global $conn; // Usa a conexão global

    // Consulta para buscar os últimos resultados dos jogos ordenados por data decrescente e limitados aos últimos 5 jogos
    $sqlJogos = "SELECT resultado FROM jogos WHERE time_id = ? ORDER BY id DESC LIMIT 5";
    $stmt = $conn->prepare($sqlJogos);
    $stmt->bind_param("i", $timeId);
    $stmt->execute();
    $resultJogos = $stmt->get_result();

    $ultimosJogos = [];

    // Armazena os resultados dos jogos na array
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

    // Fecha a conexão (se necessário, opcional dependendo do seu fluxo de trabalho)
    // $stmt->close();
    // $conn->close();

    // Retornar a string HTML dos últimos jogos
    return $output;
}



function mostrarClassificados() {
    global $conn;
    $faseFinal = 'oitavas'; // Valor default
    $sqlConfig = "SELECT fase_final FROM configuracoes WHERE id = 1";
    $resultConfig = $conn->query($sqlConfig);
    if ($resultConfig && $resultConfig->num_rows > 0) {
        $rowConfig = $resultConfig->fetch_assoc();
        $faseFinal = $rowConfig['fase_final'];
    }

    $classificadosPorGrupo = getClassificados($conn);
    $totalClassificados = ($faseFinal == 'oitavas') ? 16 : 8;

    // Filtra os times classificados de acordo com a fase final
    $timesClassificados = [];
    foreach ($classificadosPorGrupo as $grupo => $times) {
        foreach ($times as $time) {
            $timesClassificados[] = $time;
            if (count($timesClassificados) >= $totalClassificados) {
                break 2;
            }
        }
    }
    if (!empty($classificadosPorGrupo)) {
        foreach ($classificadosPorGrupo as $grupoNome => $grupoClassificado) {
            echo '<div class="grupo-container">';
            echo '<div class="grupo-header">' . htmlspecialchars($grupoNome) . '</div>';
            echo '<table>';
            echo '<tr>';
            echo '<th>Clube</th>'; // Coluna para Nome do Clube
            echo '<th class="small-col">P</th>'; // Coluna para Pontos
            echo '<th class="small-col">J</th>'; // Coluna para Partidas
            echo '<th class="small-col">V</th>'; // Coluna para Vitórias
            echo '<th class="small-col">E</th>'; // Coluna para Empates
            echo '<th class="small-col">D</th>'; // Coluna para Derrotas
            echo '<th class="small-col">GP</th>'; // Coluna para Gols Pró
            echo '<th class="small-col">GC</th>'; // Coluna para Gols Contra
            echo '<th class="small-col">SG</th>'; // Coluna para Saldo de Gols
            echo '<th class="larger-col">%</th>'; // Coluna para Porcentagem de Aproveitamento
            echo '<th class="larger-col">ÚLT. JOGOS</th>'; // Coluna para Últimos Jogos
            echo '</tr>';

            $posicao = 1; // Inicializa a posição

            foreach ($grupoClassificado as $rowTimes) {
                echo '<tr>';
                // Exibir a posição do time ao lado da logo e do nome do time
                echo '<td>';
                echo '<span style="font-weight: bold; margin-right: 5px;">' . $posicao . '</span>';
                // Exibir a logo do time se disponível
                if (!empty($rowTimes['logo'])) {
                    $imageData = base64_encode($rowTimes['logo']);
                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                    echo '<img src="' . $imageSrc . '" class="logo-time">';
                }
                // Exibir o nome do time
                echo '<span class="time-cell">' . htmlspecialchars($rowTimes['nome']) . '</span>';
                echo '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['pts']) . '</td>';
                $partidas = $rowTimes['vitorias'] + $rowTimes['empates'] + $rowTimes['derrotas'];
                echo '<td class="small-col">' . htmlspecialchars($partidas) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['vitorias']) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['empates']) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['derrotas']) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['gm']) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['gc']) . '</td>';
                echo '<td class="small-col">' . htmlspecialchars($rowTimes['sg']) . '</td>';
                // Calcular o aproveitamento e exibir
                echo '<td class="larger-col">' . formatarPorcentagemAproveitamento($rowTimes['vitorias'], $partidas) . '</td>';
                echo '<td class="larger-col">';
                echo gerarUltimosJogos($rowTimes['id']);
                echo '</td>';
                echo '</tr>';
                $posicao++;
            }
            echo '</table>';
            echo '</div>';
        }
    } else {
        echo 'Nenhum time classificado encontrado.';
    }
}

mostrarClassificados();
?>

        <div id="legenda-simbolos">
            <div><span class="simbolo" style="background-color: green;"></span><span class="descricao">Vitória</span></div>
            <div><span class="simbolo" style="background-color: red;"></span><span class="descricao">Derrota</span></div>
            <div><span class="simbolo" style="background-color: gray;"></span><span class="descricao">Empate</span></div>
            <div><span class="simbolo" style="background-color: lightgray;"></span><span class="descricao">Indefinido</span></div>
        </div>
    </div>
</body>
</html>
