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
$email = $_SESSION['user_email']; // Recuperar o e-mail do usuário logado

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

// Buscar o ID do profissional logado
$stmt = $conn->prepare("SELECT id FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profissional = $result->fetch_assoc();

if (!$profissional) {
    echo "Profissional não encontrado.";
    exit();
}

$profissional_id = $profissional['id'];

// Atualizar o status do pedido para "em negociação" apenas para o profissional
$stmt = $conn->prepare("UPDATE pedidos SET status = 'em negociação' WHERE id = ? AND id IN (SELECT pedido_id FROM recusas_profissionais WHERE profissional_id != ?) AND status = 'pendente'");
$stmt->bind_param("ii", $id_pedido, $profissional_id);

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
