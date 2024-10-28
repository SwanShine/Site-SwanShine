<?php
// Logout e limpar sessão
session_start();
session_unset();
session_destroy();
header('Location: ../../index.html');
exit();
?>
