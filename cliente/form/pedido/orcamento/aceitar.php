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

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Verificar se um ID de orçamento foi passado
if (isset($_GET['id'])) {
    $orcamento_id = $_GET['id'];

    // Buscar informações do orçamento e do profissional associado
    $stmt = $conn->prepare("SELECT p.id AS profissional_id, o.pedido_id 
                            FROM orcamentos o
                            JOIN profissionais p ON o.profissional_id = p.id
                            WHERE o.id = ?");
    $stmt->bind_param("i", $orcamento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se o orçamento foi encontrado
    if ($result->num_rows > 0) {
        $orcamento = $result->fetch_assoc();
        $profissional_id = $orcamento['profissional_id'];
        $pedido_id = $orcamento['pedido_id'];

        // Atualizar o status do pedido para 'Em andamento' e armazenar o ID do profissional aceito
        $update_stmt = $conn->prepare("UPDATE pedidos 
                                      SET status = 'Em Andamento', profissional_id = ? 
                                      WHERE id = ?");
        $update_stmt->bind_param("ii", $profissional_id, $pedido_id);

        if ($update_stmt->execute()) {
            // Inserir uma mensagem para o profissional que teve o orçamento aceito
            $message = "Seu orçamento foi aceito. O pedido está agora em andamento.";
            $insert_msg_stmt = $conn->prepare("INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
                                              VALUES (?, ?, ?, NOW())");
            $insert_msg_stmt->bind_param("sis", $email, $profissional_id, $message);

            if ($insert_msg_stmt->execute()) {
                // Agora, vamos notificar os outros profissionais sobre o status
                // Buscar todos os outros orçamentos para o mesmo pedido que não foram aceitos
                $other_stmt = $conn->prepare("SELECT o.profissional_id 
                                              FROM orcamentos o
                                              WHERE o.pedido_id = ? AND o.id != ?");
                $other_stmt->bind_param("ii", $pedido_id, $orcamento_id);
                $other_stmt->execute();
                $other_result = $other_stmt->get_result();

                // Enviar mensagem para os outros profissionais
                $decline_message = "Infelizmente, seu orçamento não foi aprovado. O pedido está em andamento com outro profissional.";
                while ($row = $other_result->fetch_assoc()) {
                    $other_profissional_id = $row['profissional_id'];

                    // Inserir a mensagem de recusa para os outros profissionais
                    $insert_decline_msg_stmt = $conn->prepare("INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
                                                              VALUES (?, ?, ?, NOW())");
                    $insert_decline_msg_stmt->bind_param("sis", $email, $other_profissional_id, $decline_message);
                    $insert_decline_msg_stmt->execute();
                    $insert_decline_msg_stmt->close();
                }

                // Redirecionar para a página de pedido em andamento
                header('Location: ../../../pedidos/pedido_andamento.php');
                exit();
            } else {
                echo "Erro ao enviar a mensagem de aceitação: " . $conn->error;
            }
        } else {
            echo "Erro ao atualizar o status do pedido: " . $conn->error;
        }

        $update_stmt->close();
        $insert_msg_stmt->close();
        $other_stmt->close();
    } else {
        echo "Orçamento não encontrado.";
    }

    $stmt->close();
} else {
    echo "Nenhum orçamento selecionado.";
}

$conn->close();
?>
