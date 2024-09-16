<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../../home/forms/login/login.html');
    exit();
}

// Verificar se os dados do orçamento foram fornecidos
if (!isset($_POST['id_pedido']) || !isset($_POST['valor']) || !isset($_POST['detalhes'])) {
    echo "Dados do orçamento não fornecidos.";
    exit();
}

$id_pedido = intval($_POST['id_pedido']); // Sanitizar o ID do pedido
$valor = $_POST['valor']; // Sanitizar o valor do orçamento
$detalhes = $_POST['detalhes']; // Sanitizar os detalhes do orçamento

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

// Atualizar o pedido com o orçamento
$stmt = $conn->prepare("UPDATE pedidos SET valor_orcamento = ?, detalhes_orcamento = ?, status = 'em análise' WHERE id = ?");
$stmt->bind_param("dsi", $valor, $detalhes, $id_pedido);

if ($stmt->execute()) {
    // Redirecionar de volta à página principal com uma mensagem de sucesso
    header('Location: ../../../index.php?message=Orçamento enviado com sucesso');
} else {
    echo "Erro ao enviar o orçamento: " . $stmt->error;
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
