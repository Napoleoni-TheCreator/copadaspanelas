<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../pages/adm/login.php");
exit();
?>
