<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../../home/forms/login/login.html');
    exit();
}

// Verificar se o ID do pedido foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID do pedido não fornecido.";
    exit();
}

$id_pedido = intval($_GET['id']); // Sanitizar o ID do pedido

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

// Atualizar o status do pedido para "em análise"
$stmt = $conn->prepare("UPDATE pedidos SET status = 'em análise' WHERE id = ?");
$stmt->bind_param("i", $id_pedido);

if ($stmt->execute()) {
    // Redirecionar para o formulário de orçamento
    header('Location: orcamento.php?id=' . $id_pedido);
} else {
    echo "Erro ao atualizar o status do pedido: " . $stmt->error;
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
