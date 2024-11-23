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

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email do usuário logado
$email = $_SESSION['user_email'];

// Verificar se um ID de orçamento foi passado
if (isset($_GET['id'])) {
    $orcamento_id = $_GET['id'];

    // Buscar informações do orçamento e do profissional associado
    $stmt = $conn->prepare("
        SELECT o.profissional_id, o.pedido_id 
        FROM orcamentos o
        WHERE o.id = ?
    ");
    $stmt->bind_param("i", $orcamento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o orçamento foi encontrado
    if ($result->num_rows > 0) {
        $orcamento = $result->fetch_assoc();
        $profissional_id = $orcamento['profissional_id'];
        $pedido_id = $orcamento['pedido_id'];

        // Inserir mensagem de recusa para o profissional
        $decline_message = "Infelizmente, seu orçamento para o pedido #$pedido_id foi recusado.";
        $insert_msg_stmt = $conn->prepare("
            INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
            VALUES (?, ?, ?, NOW())
        ");
        $insert_msg_stmt->bind_param("sis", $email, $profissional_id, $decline_message);

        if ($insert_msg_stmt->execute()) {
            // Excluir o orçamento da tabela
            $delete_stmt = $conn->prepare("DELETE FROM orcamentos WHERE id = ?");
            $delete_stmt->bind_param("i", $orcamento_id);

            if ($delete_stmt->execute()) {
                // Redirecionar para pedidos pendentes sem alterar o status
                header('Location: ../../../pedidos/pedido_pendente.php');
                exit();
            } else {
                echo "Erro ao excluir o orçamento: " . $conn->error;
            }

            $delete_stmt->close();
        } else {
            echo "Erro ao enviar a mensagem de recusa: " . $conn->error;
        }

        $insert_msg_stmt->close();
    } else {
        echo "Orçamento não encontrado.";
    }

    $stmt->close();
} else {
    echo "Nenhum orçamento selecionado.";
}

$conn->close();
?>
