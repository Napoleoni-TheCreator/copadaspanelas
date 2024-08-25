<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../public/css/csslongin.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="../actions/cadastro_login/login.php" method="POST">
            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">login</button>
        </form>
    </div>
</body>
</html>
