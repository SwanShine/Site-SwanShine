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

// Recuperar o ID do pedido da URL
$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar se o ID do pedido é válido
if ($pedido_id <= 0) {
    echo "Pedido inválido.";
    exit();
}

// Iniciar a transação para garantir que ambas as ações sejam realizadas com sucesso
$conn->begin_transaction();

try {
    // Atualizar o status do pedido para 'concluído'
    $update_status = "UPDATE pedidos SET status = 'Concluído' WHERE id = ?";
    $stmt = $conn->prepare($update_status);
    if ($stmt === false) {
        throw new Exception("Erro ao preparar a consulta de atualização do status.");
    }
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();

    // Verificar se o pedido foi atualizado
    if ($stmt->affected_rows > 0) {
        // Obter o ID do profissional e o email do cliente associados ao pedido
        $select_profissional = "SELECT p.id AS profissional_id, p.nome AS profissional_nome, pd.email AS cliente_email
                                FROM pedidos pd
                                JOIN profissionais p ON pd.profissional_id = p.id
                                WHERE pd.id = ?";
        $stmt = $conn->prepare($select_profissional);
        if ($stmt === false) {
            throw new Exception("Erro ao preparar a consulta para obter profissional e cliente.");
        }
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se encontrado o profissional
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $profissional_id = $row['profissional_id'];
            $profissional_nome = $row['profissional_nome'];
            $cliente_email = $row['cliente_email'];

            // Obter nome do cliente a partir do email
            $select_cliente = "SELECT nome FROM clientes WHERE email = ?";
            $stmt_cliente = $conn->prepare($select_cliente);
            $stmt_cliente->bind_param("s", $cliente_email);
            $stmt_cliente->execute();
            $result_cliente = $stmt_cliente->get_result();
            $cliente_nome = $result_cliente->num_rows > 0 ? $result_cliente->fetch_assoc()['nome'] : 'Cliente desconhecido';

            // Mensagem de agradecimento ao profissional
            $mensagem = "Olá, $profissional_nome!\n\nO pedido realizado por $cliente_nome foi concluído. Agradecemos pelo seu excelente trabalho!";

            // Inserir a mensagem na tabela de mensagens (usando 'remetente' para armazenar o nome do cliente)
            $insert_mensagem = "INSERT INTO mensagens (remetente, profissional_id, conteudo, data_envio)
                                VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($insert_mensagem);
            if ($stmt === false) {
                throw new Exception("Erro ao preparar a consulta para enviar a mensagem.");
            }
            $stmt->bind_param("sis", $cliente_nome, $profissional_id, $mensagem);
            $stmt->execute();

            // Confirmar que a mensagem foi enviada
            if ($stmt->affected_rows > 0) {
                // Commit da transação
                $conn->commit();
                echo "Pedido concluído com sucesso! O profissional foi notificado.";
            } else {
                throw new Exception("Erro ao enviar a mensagem para o profissional.");
            }
        } else {
            throw new Exception("Profissional não encontrado para o pedido.");
        }
    } else {
        throw new Exception("Erro ao atualizar o status do pedido.");
    }
} catch (Exception $e) {
    // Se ocorrer um erro, faz o rollback da transação
    $conn->rollback();
    echo "Erro: " . $e->getMessage();
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
