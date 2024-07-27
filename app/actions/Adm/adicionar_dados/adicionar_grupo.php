<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Grupo</title>
    <style>
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
}

h1 {
    font-size: 2em;
    color: #444;
    margin-top: 20px;
}

.form-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin-top: 5%;
}

fieldset {
    border: none;
    padding: 0;
    margin: 0;
}

legend {
    font-size: 1.5em;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 10px;
}

input[type="number"], select {
    padding: 10px;
    font-size: 1em;
    border-radius: 5px;
    border: 1px solid #ddd;
    background-color: #fff;
    width: 100%;
    max-width: 250px;
    margin-bottom: 10px;
}

button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 15px;
    font-size: 16px;
    margin: 10px 0;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

.error-message, .success-message {
    font-size: 1.1em;
    margin-bottom: 20px;
    padding: 10px;
    border-radius: 5px;
}

.error-message {
    color: #e74c3c;
    background-color: #fdd;
}

.success-message {
    color: #2ecc71;
    background-color: #dff0d8;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    background-color: #fafafa;
}

th {
    background-color: #e0e0e0;
}

iframe {
    display: none;
}

    </style>
</head>
<body>
<?php include '../../../pages/header_classificacao.php'; ?>
<header class="header">
        <div class="containerr">
            <div class="logo">
                <a href="../../../pages/HomePage.php"><img src="../../../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Grupo Ninja Logo"></a>
            </div>
            <nav class="nav-icons">
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/rodadas_adm.php"><img src="../../../../public/img/header/rodadas.png" alt="Soccer Icon"></a>
                    <span>Rodadas</span>
                </div>
                <div class="nav-item">
                    <a href="../../../actions/cadastro_adm/login.php"><img src="../../../../public/img/header/campo.png" alt="Field Icon"></a>
                    <span>Classificação</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/listar_times.php"><img src="../../../../public/img/header/classificados.png" alt="Chess Icon"></a>
                    <span>editar times</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_dados_finais.php"><img src="../../../../public/img/header/oitavas.png" alt="Trophy Icon"></a>
                    <span>editar finais</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_jogador/crud_jogador.php"><img src="../../../../public/img/prancheta.svg" alt="Trophy Icon"></a>
                    <span>Editar jogadores</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/adicionar_dados/adicionar_grupo.php"><img src="../../../../public/img/grupo.svg" alt="Trophy Icon"></a>
                    <span>Criar grupos</span>
                </div>
                <div class="nav-item">
                    <a href="../../Adm/cadastro_time/adicionar_times.php"><img src="../../../../public/img/adtime.svg" alt="Trophy Icon"></a>
                    <span>Adicionar times</span>
                </div>
            </nav>
            <button class="btn-toggle-mode" onclick="toggleDarkMode()">Modo Escuro</button>
        </div>
    </header>
    <div class="form-container">
        <h1>Adicionar Grupo</h1>
        <form id="formConfiguracao" method="post" action="">
            <fieldset>
                <legend>Configuração dos Grupos:</legend>
                <label for="equipesPorGrupo">Número de equipes por grupo (máx 16):</label>
                <input type="number" id="equipesPorGrupo" name="equipesPorGrupo" min="1" max="16" required><br>
                <label for="numeroGrupos">Número de grupos (máx 18):</label>
                <input type="number" id="numeroGrupos" name="numeroGrupos" min="1" max="18" required><br>
                <label for="faseFinal">Fase Final:</label>
                <select id="faseFinal" name="faseFinal" required>
                    <option value="oitavas">Oitavas de Final</option>
                    <option value="quartas">Quartas de Final</option>
                </select><br>
                <button type="submit">Calcular e Criar Grupos</button>
            </fieldset>
        </form>
    </div>

    <div id="mensagem" class="success-message">
        <!-- Mensagens de sucesso ou erro -->
    </div>
    <div id="grupos">
        <?php
        include '../../../config/conexao.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipesPorGrupo']) && isset($_POST['numeroGrupos']) && isset($_POST['faseFinal'])) {
            $equipesPorGrupo = intval($_POST['equipesPorGrupo']);
            $numeroGrupos = intval($_POST['numeroGrupos']);
            $faseFinal = $_POST['faseFinal'];

            $MAX_EQUIPES_POR_GRUPO = 16;
            $MAX_GRUPOS = 18;
            $MIN_TIMES_OITAVAS = 16;
            $MIN_TIMES_QUARTAS = 8;

            if ($equipesPorGrupo > $MAX_EQUIPES_POR_GRUPO || $numeroGrupos > $MAX_GRUPOS) {
                echo "<p class='error-message'>O número máximo de equipes por grupo é 16 e o número máximo de grupos é 18.</p>";
            } else {
                $totalEquipes = $equipesPorGrupo * $numeroGrupos;

                if (($faseFinal === 'oitavas' && $totalEquipes < $MIN_TIMES_OITAVAS) || 
                    ($faseFinal === 'quartas' && $totalEquipes < $MIN_TIMES_QUARTAS)) {
                    $minimo = ($faseFinal === 'oitavas') ? $MIN_TIMES_OITAVAS : $MIN_TIMES_QUARTAS;
                    echo "<p class='error-message'>Não é possível criar grupos. O número total de equipes deve ser pelo menos $minimo para a fase final selecionada.</p>";
                } else {
                    // Condições para verificar se a divisão é exata para a fase final
                    if ($faseFinal === 'oitavas') {
                        if (($totalEquipes % 16) !== 0) {
                            echo "<p class='error-message'>Para a fase de oitavas, o número total de equipes deve ser múltiplo de 16.</p>";
                        } elseif (($numeroGrupos * $equipesPorGrupo) % 8 !== 0) {
                            echo "<p class='error-message'>Número de grupos ou equipes por grupo não permite uma divisão exata para a fase de oitavas.</p>";
                        } else {
                            $sql = "REPLACE INTO configuracoes (id, equipes_por_grupo, numero_grupos, fase_final) VALUES (1, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("iis", $equipesPorGrupo, $numeroGrupos, $faseFinal);
                            $stmt->execute();

                            $conn->query("DELETE FROM grupos");

                            for ($i = 1; $i <= $numeroGrupos; $i++) {
                                $nomeGrupo = "Grupo " . chr(64 + $i);
                                $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";
                                $conn->query($sql);
                            }
                            echo "<p class='success-message'>Grupos criados com sucesso!</p>";
                        }
                    } elseif ($faseFinal === 'quartas') {
                        if (($totalEquipes % 2) !== 0) {
                            echo "<p class='error-message'>Para a fase de quartas, o número total de equipes deve ser múltiplo de 8.</p>";
                        } elseif (($numeroGrupos * $equipesPorGrupo) % 2 !== 0) {
                            echo "<p class='error-message'>Número de grupos ou equipes por grupo não permite uma divisão exata para a fase de quartas.</p>";
                        } else {
                            $sql = "REPLACE INTO configuracoes (id, equipes_por_grupo, numero_grupos, fase_final) VALUES (1, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("iis", $equipesPorGrupo, $numeroGrupos, $faseFinal);
                            $stmt->execute();

                            $conn->query("DELETE FROM grupos");

                            for ($i = 1; $i <= $numeroGrupos; $i++) {
                                $nomeGrupo = "Grupo " . chr(64 + $i);
                                $sql = "INSERT INTO grupos (nome) VALUES ('$nomeGrupo')";
                                $conn->query($sql);
                            }
                            echo "<p class='success-message'>Grupos criados com sucesso!</p>";
                        }
                    }
                }
            }
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
