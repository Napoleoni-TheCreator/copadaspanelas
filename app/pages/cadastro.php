<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" type="text/css" href="../../public/css/cadastrocss.css">
    <link rel="stylesheet" href="../../public/css/cssheader.css">
    <link rel="stylesheet" href="../../public/css/cssfooter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Cadastro</h1>
        <form class="register-form" method="POST" action="">
            <input type="text" placeholder="Nome" required>
            <input type="text" placeholder="Sobrenome" required>
            <input type="text" placeholder="Nome de Usuário" required>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Senha" required>
            <input type="password" placeholder="Confirmação de Senha" required>
            
            <button type="submit">REGISTRAR</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
