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

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Verificar se um ID de pedido foi passado
if (isset($_POST['pedido_id'])) {
    $pedido_id = $_POST['pedido_id'];

    // Atualizar o status do pedido para 'pendente'
    // E definir valor_orcamento e detalhes_orcamento como NULL
    $stmt = $conn->prepare("
        UPDATE pedidos 
        SET status = 'pendente', valor_orcamento = NULL, detalhes_orcamento = NULL 
        WHERE id = ? AND email = ?
    ");
    $stmt->bind_param("is", $pedido_id, $email);

    if ($stmt->execute()) {
        // Redirecionar para a página de pedidos pendentes
        header('Location: ../../../pedidos/pedido_pendente.php?msg=Pedido recusado com sucesso!');
        exit();
    } else {
        echo "Erro ao atualizar o pedido: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Nenhum pedido selecionado.";
}

$conn->close();
?>
