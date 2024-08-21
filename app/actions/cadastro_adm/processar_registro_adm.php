<?php
session_start();
include("../../config/conexao.php");

// Ativar exibição de erros (para depuração)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerar um token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função para validar email
function validarEmail($email) {
    // Verifica se o email tem o formato correto e termina com .com
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && preg_match('/@.*\.com$/', $email);
}

// Função para validar domínio do email
function validarDominioEmail($email) {
    // Domínios válidos
    $dominios_validos = ['gmail.com', 'hotmail.com'];
    
    // Extrair o domínio do email
    $dominio = substr(strrchr($email, "@"), 1);

    // Verificar se o domínio está na lista de domínios válidos
    return in_array($dominio, $dominios_validos);
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

// Verificar CSRF token
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=token');
    exit();
}

// Validar campos do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

// Validar email
if (!validarEmail($email)) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=email');
    exit();
}

if (!validarDominioEmail($email)) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=dominio');
    exit();
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Verificar se o nome, email ou senha já existem
$stmt = $conn->prepare("
    SELECT COUNT(*) as count, 
           SUM(CASE WHEN nome = ? THEN 1 ELSE 0 END) as nome_existente,
           SUM(CASE WHEN email = ? THEN 1 ELSE 0 END) as email_existente,
           SUM(CASE WHEN senha = ? THEN 1 ELSE 0 END) as senha_existente
    FROM admin
");
$stmt->bind_param("sss", $nome, $email, $senha_hash);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['nome_existente'] > 0) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=nome_existente');
    exit();
}

if ($row['email_existente'] > 0) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=email_existente');
    exit();
}

if ($row['senha_existente'] > 0) {
    header('Location: ../../pages/adm/cadastro_adm.php?error=senha_existente');
    exit();
}

// Preparar e executar a inserção no banco de dados
$stmt = $conn->prepare("
    INSERT INTO admin (cod_adm, nome, email, senha) 
    VALUES (?, ?, ?, ?)
");
if (!$stmt) {
    die("Erro na preparação da declaração: " . $conn->error);
}

$cod_adm = gerarCodigoAdm($conn);
$stmt->bind_param("ssss", $cod_adm, $nome, $email, $senha_hash);

if ($stmt->execute()) {
    header('Location: ../../pages/adm/cadastro_adm.php?success=1');
    exit();
} else {
    header('Location: ../../pages/adm/cadastro_adm.php?error=db');
    exit();
}
?>
