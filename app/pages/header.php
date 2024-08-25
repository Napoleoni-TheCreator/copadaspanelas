<?php
session_start(); // Inicie a sessão para verificar a autenticação
?>
<header>
    <div id="Icon">
        <a href="../pages/Homepage.php"><img src="../../public/img/ESCUDO COPA DAS PANELAS.png" alt="Logo"></a>
    </div>
    <h1 id="titulo">COPA DAS PANELAS</h1>

    <div class="cadastro">
        <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="../pages/adm/rodadas_adm.php" class="fas fa-user"> Entrar</a>
        <?php else: ?>
            <a href="../pages/adm/login.php" class="fas fa-user"> Login</a>
        <?php endif; ?>
    </div>
</header>