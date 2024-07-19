<!DOCTYPE html>
<html>
<head>
<style>
body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    /* height: 100vh; Ajusta a altura para a tela inteira */
    margin: 0; /* Remove a margem padrão */
    background-color: #f0f8ff; /* Azul claro como fundo */
    font-family: Arial, sans-serif; /* Fonte para toda a página */
}

.container {
    display: flex;
    height: 700px;
    justify-content: center;
    padding: 90px;
    /* background-color: #ffffff; Cor de fundo padrão */
    border: 1px solid #ddd; /* Borda ao redor do container */
    border-radius: 10px; /* Bordas arredondadas */
    box-shadow: 0 4px 8px blue; /* Sombra leve */
    transform: scale(0.8); /* Reduz o tamanho do conteúdo */
    width: 100%;
    overflow: hidden; /* Impede o conteúdo de exceder o container */
    background-image: url('trofeu.png'); /* Caminho para a imagem de fundo */
    background-size: 15% auto; /* Ajusta o tamanho da imagem */
    background-position: top center; /* Posiciona a imagem no topo e centraliza horizontalmente */
    background-repeat: no-repeat; /* Impede que a imagem se repita */
    overflow-x: auto;
}

.bracket {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}

.match {
    display: flex;
    /* justify-content: space-between; */
    justify-content: center;
    align-items: center;
    width: 150px;
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #ffffff; /* Cor de fundo das partidas */
    border-radius: 5px; /* Bordas arredondadas */
    box-shadow: 0 4px 8px blue; /* Sombra leve */
}

.team {
    display: flex;
    flex-direction: column; /* Muda a direção para coluna */
    align-items: center;    /* Alinha itens horizontalmente ao centro */
    text-align: center;     /* Alinha o texto ao centro */
}

.flag {
    width: 46px;
    height: 46px;
    margin-bottom: 5px; /* Espaço entre a imagem e o nome */
    border-radius: 100%; /* Faz a imagem redonda */
    object-fit: contain; /* Ajusta a imagem para caber dentro do círculo */
    display: block; /* Garante que a imagem seja tratada como bloco, facilitando o alinhamento */
}

.team-name {
    font-weight: bold;
}

.round-label {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333; /* Cor do texto das fases */
}

.round {
    display: flex;
    flex-direction: column; /* Alinha os itens verticalmente */
    align-items: center; /* Centraliza o conteúdo horizontalmente */
    padding: 15px;
    /* background-color: #f9f9f9; Cor de fundo opcional */
}

.round-content {
    text-align: center; /* Centraliza o texto dentro de cada seção */
    margin-bottom: 20px; /* Espaço entre seções */
}

.third-place {
    margin-top: 40px; /* Espaço acima da seção "Terceiro" para separar do conteúdo "Final" */
}

.connector {
    width: 200px;
    border-bottom: 1px dashed #ccc;
    margin: 10px 0;
}

.disputa {
    width: 150px;
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
    background-color: #ffffff; /* Cor de fundo para disputa de terceiro lugar */
    border-radius: 5px; /* Bordas arredondadas */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra leve */
    margin-bottom: 20px; /* Espaço entre a imagem e o texto final */
}

.third-place {
    margin-top: 20px; /* Espaço acima da disputa de terceiro lugar */
}
</style>
</head>
<body>
<?php include 'header_classificacao.php'; ?>
<div class="container">
  <div class="bracket">
    <div class="round">
      <div class="round-label">Oitavas</div>
      <?php
      // Incluir a configuração de conexão
      include '../config/conexao.php';

      // Função para exibir confrontos
      function exibirConfrontos($conn, $fase, $count, $start = 0) {
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
                  ORDER BY id
                  LIMIT $start, $count";
          $result = $conn->query($sql);
          if (!$result) {
              die("Erro na consulta de confrontos: " . $conn->error);
          }

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  if ($fase == 'final') {
                      echo "<div class='match'>";
                      echo "<div class='team'>";
                      echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                      echo "<span class='team-name'>{$row['timeA_nome']}</span>";
                      echo "</div>";
                      echo "<span>VS</span>";
                      echo "<div class='team'>";
                      echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                      echo "<span class='team-name'>{$row['timeB_nome']}</span>";
                      echo "</div>";
                      echo "</div>";
                  } else {
                      echo "<div class='match'>";
                      echo "<div class='team'>";
                      echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                      echo "<span class='team-name'>{$row['timeA_nome']}</span>";
                      echo "</div>";
                      echo "</div>";
                      echo "<div class='match'>";
                      echo "<div class='team'>";
                      echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                      echo "<span class='team-name'>{$row['timeB_nome']}</span>";
                      echo "</div>";
                      echo "</div>";
                  }
              }
          } else {
              for ($i = 0; $i < $count; $i++) {
                  echo "<div class='match'>";
                  echo "<div class='team'>";
                  echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                  echo "<span class='team-name'>Time A</span>";
                  echo "</div>";
                  echo "</div>";
                  echo "<div class='match'>";
                  echo "<div class='team'>";
                  echo "<img class='flag' src='logo.png' alt='Bandeira do time'>";
                  echo "<span class='team-name'>Time B</span>";
                  echo "</div>";
                  echo "</div>";
              }
          }
      }

      // Exibir os confrontos das oitavas
      exibirConfrontos($conn, 'oitavas', 4);
      ?>
    </div>

    <div class="round">
      <div class="round-label">Quartas</div>
      <?php
      // Exibir os confrontos das quartas
      exibirConfrontos($conn, 'quartas', 2);
      ?>
    </div>

    <div class="round">
      <div class="round-label">Semifinais</div>
      <?php
      // Exibir os confrontos das semifinais
      exibirConfrontos($conn, 'semifinais', 1);
      ?>
    </div>

    <div class="round">
      <div class="round-content">
        <div class="round-label">Final</div>
        <?php
        // Exibir a final
        exibirConfrontos($conn, 'final', 1);
        ?>
      </div>
      <div class="round-content third-place">
        <div class="round-label">Terceiro</div>
        <?php
        // Exibir a final mudar depois para terceiro
        exibirConfrontos($conn, 'final', 1);
        ?>
      </div>
    </div>

    <div class="round">
      <div class="round-label">Semifinais</div>
      <?php
      // Exibir os confrontos das semifinais novamente
      exibirConfrontos($conn, 'semifinais', 1, 1);
      ?>
    </div>

    <div class="round">
      <div class="round-label">Quartas</div>
      <?php
      // Exibir os confrontos das quartas novamente
      exibirConfrontos($conn, 'quartas', 2, 2);
      ?>
    </div>

    <div class="round">
      <div class="round-label">Oitavas</div>
      <?php
      // Exibir os confrontos das oitavas novamente
      exibirConfrontos($conn, 'oitavas', 4, 4);
      ?>
    </div>

    <?php
    // Fechar a conexão com o banco de dados
    $conn->close();
    ?>
  </div>

  <div class="right">
    <!-- Placeholder for additional content on the right side, if needed -->
  </div>
</div>

</body>
</html>
