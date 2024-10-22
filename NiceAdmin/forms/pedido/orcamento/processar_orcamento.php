<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../../home/forms/login/login.html');
    exit();
}

// Verificar se o ID do pedido foi fornecido
if (!isset($_POST['id_pedido']) || empty($_POST['id_pedido'])) {
    echo "ID do pedido não fornecido.";
    exit();
}

$id_pedido = intval($_POST['id_pedido']); // Sanitizar o ID do pedido

// Obter os valores do orçamento do formulário
$valor_orcamento = $_POST['valor'];
$detalhes_orcamento = $_POST['detalhes'];

// Remover a formatação do valor
$valor_orcamento = str_replace(['R$', '.', ','], ['', '', '.'], $valor_orcamento); // Corrigido para usar $valor_orcamento

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

// Atualizar os campos valor_orcamento e detalhes_orcamento na tabela pedidos
$stmt = $conn->prepare("UPDATE pedidos SET valor_orcamento = ?, detalhes_orcamento = ? WHERE id = ?");
$stmt->bind_param("ssi", $valor_orcamento, $detalhes_orcamento, $id_pedido);

if ($stmt->execute()) {
    // Redirecionar de volta à página principal com uma mensagem de sucesso
    header('Location: ../../../pedidos/pedido_andamento.php');
} else {
    echo "Erro ao enviar o orçamento: " . $stmt->error;
}

// Fechar as conexões
$stmt->close();
$conn->close();
?>
