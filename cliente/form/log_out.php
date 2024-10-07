<?php
// Iniciar a sessão
session_start();

// Verificar se a sessão está ativa
if (isset($_SESSION['user_email'])) {
    // Limpar todas as variáveis de sessão
    session_unset();

    // Destruir a sessão
    session_destroy();

    // Opcional: Registre o logout no log do sistema
    // file_put_contents('logout.log', date('Y-m-d H:i:s') . " - Logout realizado para: " . $_SESSION['user_email'] . PHP_EOL, FILE_APPEND);
}

// Redirecionar para a página de login
header('Location: ../../home/forms/login/login.html');
exit();
?>
