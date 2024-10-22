<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../home/forms/login/login.html');
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

// Verificar se o profissional é o responsável pelo pedido
$stmt = $conn->prepare("SELECT id, servicos FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profissional = $result->fetch_assoc();

if (!$profissional) {
    echo "Profissional não encontrado.";
    exit();
}

$profissional_id = $profissional['id'];
$servicos_oferecidos = explode(', ', $profissional['servicos']);

// Verificar se o pedido pertence aos serviços oferecidos pelo profissional
$placeholders = implode(', ', array_fill(0, count($servicos_oferecidos), '?')); // Gerar placeholders
$types = str_repeat('s', count($servicos_oferecidos)); // Tipo para bind_param

$query = "SELECT id FROM pedidos WHERE id = ? AND servicos IN ($placeholders)";
$stmt = $conn->prepare($query);
$stmt->bind_param('i' . $types, $id_pedido, ...$servicos_oferecidos); // Passa todos os parâmetros corretamente
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Pedido não encontrado ou não pertence ao profissional.";
    exit();
}

// Verificar se o profissional já recusou este pedido
$check_stmt = $conn->prepare("SELECT id FROM recusas_profissionais WHERE profissional_id = ? AND pedido_id = ?");
$check_stmt->bind_param("ii", $profissional_id, $id_pedido);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Você já recusou este pedido.";
    exit();
}

// Inserir a recusa na tabela recusas_profissionais
$insert_stmt = $conn->prepare("INSERT INTO recusas_profissionais (profissional_id, pedido_id) VALUES (?, ?)");
$insert_stmt->bind_param("ii", $profissional_id, $id_pedido);
$insert_stmt->execute();

// Atualizar o status do pedido para 'pendente'
$update_stmt = $conn->prepare("UPDATE pedidos SET status = 'pendente' WHERE id = ?");
$update_stmt->bind_param("i", $id_pedido);
$update_stmt->execute();

// Redirecionar de volta à página principal com uma mensagem de sucesso
header('Location: ../../index.php?message=Pedido recusado e atualizado para pendente');

// Fechar as conexões
$update_stmt->close();
$insert_stmt->close();
$check_stmt->close();
$stmt->close();
$conn->close();
?>
