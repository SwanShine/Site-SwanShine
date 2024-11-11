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

    // Buscar o orçamento associado a esse pedido
    $stmt_orcamento = $conn->prepare("SELECT o.profissional_id 
                                      FROM orcamentos o 
                                      WHERE o.pedido_id = ?");
    $stmt_orcamento->bind_param("i", $pedido_id);
    $stmt_orcamento->execute();
    $orcamento_result = $stmt_orcamento->get_result();

    if ($orcamento_result->num_rows > 0) {
        // Recuperar o ID do profissional associado ao orçamento
        $orcamento = $orcamento_result->fetch_assoc();
        $profissional_id = $orcamento['profissional_id'];

        // Atualizar o status do pedido para 'pendente' e remover o orçamento
        $update_stmt = $conn->prepare("UPDATE pedidos 
                                       SET status = 'pendente', valor_orcamento = NULL, detalhes_orcamento = NULL 
                                       WHERE id = ? AND email = ?");
        $update_stmt->bind_param("is", $pedido_id, $email);

        if ($update_stmt->execute()) {
            // Remover o orçamento da tabela 'orcamentos'
            $delete_stmt = $conn->prepare("DELETE FROM orcamentos WHERE pedido_id = ? AND profissional_id = ?");
            $delete_stmt->bind_param("ii", $pedido_id, $profissional_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Enviar mensagem para o profissional informando que o orçamento foi recusado
            $message = "Infelizmente, seu orçamento não foi aceito para este pedido.";
            $insert_msg_stmt = $conn->prepare("INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
                                              VALUES (?, ?, ?, NOW())");
            $insert_msg_stmt->bind_param("sis", $email, $profissional_id, $message);
            $insert_msg_stmt->execute();
            $insert_msg_stmt->close();

            // Exibir mensagem de sucesso para o cliente sem redirecionamento
            $msg = "Pedido recusado com sucesso! O orçamento do profissional foi removido.";
        } else {
            $msg = "Erro ao atualizar o pedido: " . $conn->error;
        }
        
        $update_stmt->close();
    } else {
        $msg = "Orçamento não encontrado para este pedido.";
    }

    $stmt_orcamento->close();
} else {
    $msg = "Nenhum pedido selecionado.";
}

$conn->close();
?>

<!-- Exibição da mensagem de sucesso ou erro na página -->
<div class="alert alert-info">
    <?php echo isset($msg) ? $msg : ''; ?>
</div>
