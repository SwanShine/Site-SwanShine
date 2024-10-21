<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../../home/forms/login/login.html');
    exit();
}

// Inicializar a mensagem
$mensagem = "";

// Verificar se o ID do pedido foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $mensagem = "ID do pedido não fornecido.";
} else {
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

    // Consultar os valores do pedido
    $stmt = $conn->prepare("SELECT valor_orcamento, detalhes_orcamento FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();

        // Verificar se os campos valor_orcamento e detalhes_orcamento têm valores
        if (!empty($pedido['valor_orcamento']) && !empty($pedido['detalhes_orcamento'])) {
            // Atualizar o status do pedido para "em análise"
            $stmt = $conn->prepare("UPDATE pedidos SET status = 'em análise' WHERE id = ?");
            $stmt->bind_param("i", $id_pedido);

            if ($stmt->execute()) {
                // Redirecionar para o formulário de orçamento
                header('Location: orcamento.php?id=' . $id_pedido);
                exit();
            } else {
                $mensagem = "Erro ao atualizar o status do pedido: " . $stmt->error;
            }
        } else {
            $mensagem = "O pedido não possui valores de orçamento disponíveis.";
        }
    } else {
        $mensagem = "Pedido não encontrado.";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();
}

// Exibir a mensagem como alerta, se existir
if (!empty($mensagem)) {
    echo "<script>alert('$mensagem'); window.history.back();</script>";
    exit();
}
?>
