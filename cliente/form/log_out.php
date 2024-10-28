<?php
// Iniciar a sessão
session_start();

// Verificar se a sessão está ativa
if (isset($_SESSION['user_email'])) {
    // Armazenar o email do usuário antes de destruir a sessão (caso queira usá-lo para log ou outros fins)
    $user_email = $_SESSION['user_email'];

    // Limpar todas as variáveis de sessão
    session_unset();

    // Destruir a sessão
    session_destroy();

    // Opcional: Registre o logout no log do sistema
    // file_put_contents('logout.log', date('Y-m-d H:i:s') . " - Logout realizado para: " . $user_email . PHP_EOL, FILE_APPEND);
}

// Redirecionar para a página de login
header('Location: ../../index.html');
exit();
?>
