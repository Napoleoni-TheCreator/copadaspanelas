<?php
// session_start();
include("session_check.php");
?>
<?php
// session_start();
include("../../config/conexao.php");

// Ativar exibição de erros (para depuração)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerar um token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função para gerar o código do administrador
function gerarCodigoAdm($conn) {
    $ano_atual = date("Y"); // Ano atual
    $prefixo = "cpTelsr";  // Prefixo fixo

    // Consultar IDs existentes para o ano atual
    $stmt = $conn->prepare("
        SELECT cod_adm
        FROM admin
        WHERE cod_adm LIKE CONCAT(?, '%')
    ");
    if (!$stmt) {
        die("Erro na preparação da declaração: " . $conn->error);
    }

    $ano_prefixo = $ano_atual . $prefixo; // Concatenar o ano e prefixo

    // Passar o valor como uma variável por referência
    $stmt->bind_param("s", $ano_prefixo);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids_existentes = [];
    while ($row = $result->fetch_assoc()) {
        // Extrair o número do ID existente
        $id_atual = (int)substr($row['cod_adm'], strlen($ano_atual . $prefixo));
        $ids_existentes[] = $id_atual;
    }
        
    // Encontrar o próximo ID disponível
    $proximo_id = 1;
    while (in_array($proximo_id, $ids_existentes)) {
        $proximo_id++;
    }

    // Formatar o novo código
    return $ano_atual . $prefixo . $proximo_id;
}

$codigo_adm = gerarCodigoAdm($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Administrador</title>
    <link rel="stylesheet" href="../../../public/css/cadastro_adm/cadastro_adm.css">
    <link rel="stylesheet" href="../../../public/css/cssfooter.css">

</head>
<body>
<?php 
require_once 'header_classificacao.php';
?>
    <div class="form-container">
        <form action="../../actions/cadastro_adm/processar_registro_adm.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="cod_adm">Código do Administrador:</label>
            <input type="text" id="cod_adm" name="cod_adm" value="<?php echo htmlspecialchars($codigo_adm); ?>" readonly>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" maxlength="30" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" maxlength="40" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" maxlength="20" required>
            <button type="submit">Cadastrar</button>

            <?php if (isset($_GET['error'])): ?>
                <p class="message error">
                    <?php
                    switch ($_GET['error']) {
                        case 'token':
                            echo "Token CSRF inválido.";
                            break;
                        case 'email':
                            echo "Email inválido. O email deve terminar com .com.";
                            break;
                        case 'dominio':
                            echo "O email deve ser do Gmail ou Hotmail.";
                            break;
                        case 'email_existente':
                            echo "Email já cadastrado. Por favor, use outro email.";
                            break;
                        case 'nome_existente':
                            echo "Nome já cadastrado. Por favor, escolha outro nome.";
                            break;
                        case 'db':
                            echo "Erro ao cadastrar o administrador. Tente novamente mais tarde.";
                            break;
                    }
                    ?>
                </p>
            <?php elseif (isset($_GET['success'])): ?>
                <p class="message success">Administrador cadastrado com sucesso!</p>
            <?php endif; ?>
        </form>
    </div>

    <?php require_once '../footer.php' ?>
</body>
</html>
