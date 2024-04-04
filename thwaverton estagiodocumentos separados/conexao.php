<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "seu_usuario";
$password = "sua_senha";
$dbname = "nome_do_banco";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para criar a tabela de times
function criarTabelaTimes($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS times (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        logo_url VARCHAR(255) NOT NULL,
        pontos INT NOT NULL,
        vitorias INT NOT NULL,
        empates INT NOT NULL,
        derrotas INT NOT NULL,
        gol_marcado INT NOT NULL,
        gol_contra INT NOT NULL,
        saldo_gol INT NOT NULL,
        grupo VARCHAR(1) NOT NULL
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Tabela de times criada com sucesso!<br>";
    } else {
        echo "Erro ao criar tabela de times: " . $conn->error . "<br>";
    }
}

// Verifica se a tabela de times já foi criada
criarTabelaTimes($conn);

// Verifica se foi enviado um nome de time via POST
if(isset($_POST['nomeTime'])) {
    $nomeTime = $_POST['nomeTime'];
    $logoURL = $_POST['logoURL'];
    $grupo = $_POST['grupo'];

    // Insere o novo time no banco de dados
    $sql = "INSERT INTO times (nome, logo_url, pontos, vitorias, empates, derrotas, gol_marcado, gol_contra, saldo_gol, grupo) VALUES ('$nomeTime', '$logoURL', 0, 0, 0, 0, 0, 0, 0, '$grupo')";

    if ($conn->query($sql) === TRUE) {
        echo "Novo time inserido com sucesso!<br>";
    } else {
        echo "Erro ao inserir novo time: " . $conn->error . "<br>";
    }
}

// Função para adicionar uma nova tabela
function adicionarTabela($conn, $grupo) {
    $sql = "CREATE TABLE IF NOT EXISTS tabela_$grupo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        pontos INT NOT NULL,
        vitorias INT NOT NULL,
        empates INT NOT NULL,
        derrotas INT NOT NULL,
        gol_marcado INT NOT NULL,
        gol_contra INT NOT NULL,
        saldo_gol INT NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        echo "Nova tabela do grupo $grupo criada com sucesso!<br>";
    } else {
        echo "Erro ao criar nova tabela do grupo $grupo: " . $conn->error . "<br>";
    }
}
// Função para adicionar vitória via JavaScript
function adicionarVitoria() {
  var selecaoTime = document.getElementById('selecaoTime').value;
  if (!selecaoTime) return;

  fetch('nome_do_arquivo.php', {
      method: 'POST',
      body: JSON.stringify({
          acao: 'adicionarVitoria',
          time: selecaoTime
      })
  })
  .then(response => response.text())
  .then(result => {
      console.log(result);
      // Atualize a interface conforme necessário
  })
  .catch(error => {
      console.error('Erro ao adicionar vitória:', error);
  });
}

// Função para adicionar empate via JavaScript
function adicionarEmpate() {
  var selecaoTime = document.getElementById('selecaoTime').value;
  if (!selecaoTime) return;

  fetch('nome_do_arquivo.php', {
      method: 'POST',
      body: JSON.stringify({
          acao: 'adicionarEmpate',
          time: selecaoTime
      })
  })
  .then(response => response.text())
  .then(result => {
      console.log(result);
      // Atualize a interface conforme necessário
  })
  .catch(error => {
      console.error('Erro ao adicionar empate:', error);
  });
}

// Função para adicionar derrota via JavaScript
function adicionarDerrota() {
  var selecaoTime = document.getElementById('selecaoTime').value;
  if (!selecaoTime) return;

  fetch('nome_do_arquivo.php', {
      method: 'POST',
      body: JSON.stringify({
          acao: 'adicionarDerrota',
          time: selecaoTime
      })
  })
  .then(response => response.text())
  .then(result => {
      console.log(result);
      // Atualize a interface conforme necessário
  })
  .catch(error => {
      console.error('Erro ao adicionar derrota:', error);
  });
}

// Função para adicionar gols contra via JavaScript
function adicionarGolsContra() {
  var selecaoTime = document.getElementById('selecaoTime').value;
  if (!selecaoTime) return;

  fetch('nome_do_arquivo.php', {
      method: 'POST',
      body: JSON.stringify({
          acao: 'adicionarGolsContra',
          time: selecaoTime
      })
  })
  .then(response => response.text())
  .then(result => {
      console.log(result);
      // Atualize a interface conforme necessário
  })
  .catch(error => {
      console.error('Erro ao adicionar gols contra:', error);
  });
}
// Função para adicionar gol
function adicionarGol($conn, $grupo, $time) {
    $sql = "UPDATE tabela_$grupo SET gol_marcado = gol_marcado + 1 WHERE nome = '$time'";
    if ($conn->query($sql) === TRUE) {
        echo "Gol adicionado com sucesso para o time $time!<br>";
    } else {
        echo "Erro ao adicionar gol para o time $time: " . $conn->error . "<br>";
    }
}

// Outras funções para adicionar vitória, derrota, empate, etc.

// Fecha a conexão
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classificação</title>
    <style>
        /* Estilos omitidos para brevidade */
    </style>
</head>

<body>
    <h1>Tabelas de classificação</h1>
    <div id="botoes_adicionar">
        <!-- Botões e seletores omitidos para brevidade -->
    </div>
    <div class="container" id="containerDiv">
        <div id="tabelas"></div>
    </div>
    <br>
    <div class="classificacao" id="containerDiv2">
        <table>
            <!-- Tabela de classificação omitida para brevidade -->
        </table>
    </div>

    <script>
        // Função para adicionar gol via JavaScript
        function adicionarGol() {
            var selecaoTime = document.getElementById('selecaoTime').value;
            if (!selecaoTime) return;

            var grupo = prompt('Digite o grupo do time (A, B, C, etc.):');
            if (!grupo) return;

            fetch('nome_do_arquivo.php', {
                method: 'POST',
                body: JSON.stringify({
                    acao: 'adicionarGol',
                    grupo: grupo,
                    time: selecaoTime
                })
            })
            .then(response => response.text())
            .then(result => {
                console.log(result);
                // Atualize a interface conforme necessário
            })
            .catch(error => {
                console.error('Erro ao adicionar gol:', error);
            });
        }

        // Outras funções JavaScript omitidas para brevidade
    </script>
</body>

</html>
