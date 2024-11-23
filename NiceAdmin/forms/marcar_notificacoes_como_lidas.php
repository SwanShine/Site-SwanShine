<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
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

// Obter o ID do profissional logado
$profissional_id = $_SESSION['profissional_id'];

// Atualizar todas as notificações para "lida" do profissional logado
$update_query = "UPDATE mensagens SET lida = 1 WHERE profissional_id = ? AND lida = 0";
$stmt_update = $conn->prepare($update_query);
$stmt_update->bind_param("i", $profissional_id);
$stmt_update->execute();
$stmt_update->close();

// Fechar a conexão
$conn->close();

// Redirecionar de volta para a página anterior
header("Location: ../index.php"); // Altere para o caminho correto da página principal
exit();
?>
