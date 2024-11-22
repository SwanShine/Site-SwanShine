<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../home/forms/login/login.html');
    exit();
}

// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email do usuário da sessão
$email = $_SESSION['user_email'];

// Preparar a query para desativar a conta
$sql = "UPDATE clientes SET status = 'desativado', data_desativacao = CURDATE() WHERE email = ?";

// Preparar a declaração
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro ao preparar a declaração: " . $conn->error);
}

// Vincular o parâmetro
$stmt->bind_param("s", $email);

// Executar a declaração
if ($stmt->execute()) {
    // Deslogar o usuário após a desativação
    session_destroy();
    echo "Conta desativada com sucesso. Você será redirecionado à página inicial.";
    header("Refresh: 3; URL=../../../index.html");
} else {
    echo "Erro ao desativar a conta: " . $stmt->error;
}

// Fechar a declaração e a conexão
$stmt->close();
$conn->close();
?>
