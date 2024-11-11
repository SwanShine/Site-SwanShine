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

// Verificar a conexão com o banco de dados
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email do usuário logado
$email = $_SESSION['user_email'];

// Função para lidar com erros e mensagens
function setMessage($msg, $isError = false) {
    return '<div class="alert ' . ($isError ? 'alert-danger' : 'alert-info') . '">' . $msg . '</div>';
}

// Verificar se o ID do pedido foi enviado via POST
if (isset($_POST['pedido_id'])) {
    $pedido_id = $_POST['pedido_id'];

    // Validar o ID do pedido (garantir que seja um número inteiro positivo)
    if (!filter_var($pedido_id, FILTER_VALIDATE_INT) || $pedido_id <= 0) {
        echo setMessage("ID do pedido inválido.", true);
        exit();
    }

    // Buscar o orçamento associado ao pedido
    $stmt_orcamento = $conn->prepare("SELECT o.profissional_id 
                                      FROM orcamentos o 
                                      WHERE o.pedido_id = ?");
    $stmt_orcamento->bind_param("i", $pedido_id);
    $stmt_orcamento->execute();
    $orcamento_result = $stmt_orcamento->get_result();

    if ($orcamento_result->num_rows > 0) {
        // Recuperar o ID do profissional
        $orcamento = $orcamento_result->fetch_assoc();
        $profissional_id = $orcamento['profissional_id'];

        // Atualizar o status do pedido para 'pendente' e remover o orçamento
        $update_stmt = $conn->prepare("UPDATE pedidos 
                                       SET status = 'pendente', valor_orcamento = NULL, detalhes_orcamento = NULL 
                                       WHERE id = ? AND email = ?");
        $update_stmt->bind_param("is", $pedido_id, $email);

        if ($update_stmt->execute()) {
            // Remover o orçamento da tabela de orçamentos
            $delete_stmt = $conn->prepare("DELETE FROM orcamentos WHERE pedido_id = ? AND profissional_id = ?");
            $delete_stmt->bind_param("ii", $pedido_id, $profissional_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Enviar mensagem ao profissional
            $message = "Infelizmente, seu orçamento não foi aceito para este pedido.";
            $insert_msg_stmt = $conn->prepare("INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
                                              VALUES (?, ?, ?, NOW())");
            $insert_msg_stmt->bind_param("sis", $email, $profissional_id, $message);
            $insert_msg_stmt->execute();
            $insert_msg_stmt->close();

            // Mensagem de sucesso
            echo setMessage("Pedido recusado com sucesso! O orçamento do profissional foi removido.");
        } else {
            echo setMessage("Erro ao atualizar o pedido: " . $conn->error, true);
        }

        $update_stmt->close();
    } else {
        echo setMessage("Orçamento não encontrado para este pedido.", true);
    }

    $stmt_orcamento->close();
} else {
    echo setMessage("Nenhum pedido selecionado.", true);
}

// Fechar a conexão
$conn->close();
?>
