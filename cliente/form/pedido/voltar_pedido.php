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
$email = $_SESSION['user_email']; // Recuperar o e-mail do cliente logado

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

// Verificar se o cliente é o responsável pelo pedido
$stmt = $conn->prepare("SELECT id FROM pedidos WHERE id = ? AND email = ?");
$stmt->bind_param("is", $id_pedido, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Pedido não encontrado ou não pertence ao cliente.";
    exit();
}

// Atualizar o status do pedido para "pendente"
$update_query = "UPDATE pedidos SET status = 'Pendente' WHERE id = ?";
$stmt_update = $conn->prepare($update_query);
$stmt_update->bind_param('i', $id_pedido);

// Apagar os orçamentos associados ao pedido
$delete_orcamento_query = "DELETE FROM orcamentos WHERE pedido_id = ?";
$stmt_delete_orcamento = $conn->prepare($delete_orcamento_query);
$stmt_delete_orcamento->bind_param('i', $id_pedido);

// Executar as atualizações
if ($stmt_update->execute() && $stmt_delete_orcamento->execute()) {
    // Redirecionar de volta à página principal com uma mensagem de sucesso
    header('Location: ../../pedidos/pedido_pendente.php?message=Pedido voltado com sucesso');
} else {
    echo "Erro ao atualizar o pedido ou remover orçamentos: " . $conn->error;
}

// Fechar as conexões
$stmt->close();
$stmt_update->close();
$stmt_delete_orcamento->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagem de Pedido</title>
</head>
<body>
<script>
// Função para exibir o popup
function mostrarPopup(mensagem) {
    alert(mensagem); // Exibe um popup com a mensagem passada
}

// Verifica se há a mensagem na URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message) {
        mostrarPopup(message);
    }
};
</script>

</body>
</html>
