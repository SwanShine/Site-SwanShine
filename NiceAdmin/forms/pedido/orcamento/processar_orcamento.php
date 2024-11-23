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

// Obter o ID do profissional a partir da sessão (assumindo que o email está armazenado na sessão)
$email_profissional = $_SESSION['user_email'];

// Recuperar o ID do profissional no banco de dados com base no email
$stmt_profissional = $conn->prepare("SELECT id FROM profissionais WHERE email = ?");
$stmt_profissional->bind_param("s", $email_profissional);
$stmt_profissional->execute();
$stmt_profissional->store_result();
$stmt_profissional->bind_result($profissional_id);
$stmt_profissional->fetch();
$stmt_profissional->close();

// Verificar se o profissional foi encontrado
if (!$profissional_id) {
    echo "Profissional não encontrado.";
    exit();
}

// Atualizar os campos valor_orcamento e detalhes_orcamento na tabela pedidos
$stmt_pedido = $conn->prepare("UPDATE pedidos SET valor_orcamento = ?, detalhes_orcamento = ? WHERE id = ?");
$stmt_pedido->bind_param("ssi", $valor_orcamento, $detalhes_orcamento, $id_pedido);

if ($stmt_pedido->execute()) {
    // Inserir o orçamento na tabela orcamentos
    $stmt_orcamento = $conn->prepare("INSERT INTO orcamentos (pedido_id, profissional_id, valor_orcamento, detalhes_orcamento) VALUES (?, ?, ?, ?)");
    $stmt_orcamento->bind_param("iiis", $id_pedido, $profissional_id, $valor_orcamento, $detalhes_orcamento);
    
    if ($stmt_orcamento->execute()) {
        // Redirecionar de volta à página principal com uma mensagem de sucesso
        header('Location: ../../../pedidos/pedido_pendente.php');
        exit();
    } else {
        echo "Erro ao inserir o orçamento: " . $stmt_orcamento->error;
    }

    $stmt_orcamento->close();
} else {
    echo "Erro ao atualizar o pedido: " . $stmt_pedido->error;
}

// Fechar as conexões
$stmt_pedido->close();
$conn->close();
?>
