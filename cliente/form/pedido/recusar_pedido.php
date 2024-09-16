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
$stmt = $conn->prepare("SELECT servicos FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profissional = $result->fetch_assoc();

if (!$profissional) {
    echo "Profissional não encontrado.";
    exit();
}

$servicos_oferecidos = explode(', ', $profissional['servicos']);

// Verificar se o pedido pertence ao profissional
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

// Armazenar o ID do pedido recusado na sessão
if (!isset($_SESSION['recusados'])) {
    $_SESSION['recusados'] = [];
}
$_SESSION['recusados'][] = $id_pedido;

// Redirecionar de volta à página principal com uma mensagem de sucesso
header('Location: ../../index.php?message=Pedido recusado com sucesso');

// Fechar a conexão
$stmt->close();
$conn->close();
?>
