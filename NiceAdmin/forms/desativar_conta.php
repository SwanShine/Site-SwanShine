<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
    exit();
}

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

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Usar prepared statements para buscar o ID e status do profissional pelo email
$stmt = $conn->prepare("SELECT id, status, data_desativacao FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se retornou algum resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id']; // Obtenha o ID do usuário
    $status = $row['status'];
    $data_desativacao = $row['data_desativacao'];

    // Se o status for 'desativado' e já passaram mais de 30 dias da data de desativação, deletar a conta
    if ($status == 'desativado' && $data_desativacao) {
        $data_atual = new DateTime();
        $data_desativacao = new DateTime($data_desativacao);
        $intervalo = $data_atual->diff($data_desativacao);

        // Verificar se passaram mais de 30 dias
        if ($intervalo->days > 30) {
            // Deletar mensagens e pedidos associados ao profissional
            $stmt_delete_msgs = $conn->prepare("DELETE FROM mensagens WHERE profissional_id = ?");
            $stmt_delete_msgs->bind_param("i", $id);
            $stmt_delete_msgs->execute();

            $stmt_delete_pedidos = $conn->prepare("DELETE FROM pedidos WHERE profissional_id = ?");
            $stmt_delete_pedidos->bind_param("i", $id);
            $stmt_delete_pedidos->execute();

            // Deletar a conta do profissional
            $sql = "DELETE FROM profissionais WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Conta deletada após 30 dias de inatividade.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao deletar a conta.']);
                }
                $stmt->close(); // Feche a declaração
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta.']);
            }
        } else {
            // Se ainda não passaram 30 dias, apenas retornar a mensagem de conta desativada
            echo json_encode(['success' => false, 'message' => 'Sua conta foi desativada, mas você pode reativá-la ao fazer login dentro de 30 dias.']);
        }
    } else {
        // Se o status for 'ativo', desativar a conta e marcar a data de desativação
        if ($status == 'ativo') {
            // Verificar se há dados associados antes de desativar
            $stmt_check_msgs = $conn->prepare("SELECT COUNT(*) FROM mensagens WHERE profissional_id = ?");
            $stmt_check_msgs->bind_param("i", $id);
            $stmt_check_msgs->execute();
            $result_msgs = $stmt_check_msgs->get_result();
            $row_msgs = $result_msgs->fetch_assoc();

            $stmt_check_pedidos = $conn->prepare("SELECT COUNT(*) FROM pedidos WHERE profissional_id = ?");
            $stmt_check_pedidos->bind_param("i", $id);
            $stmt_check_pedidos->execute();
            $result_pedidos = $stmt_check_pedidos->get_result();
            $row_pedidos = $result_pedidos->fetch_assoc();

            // Verificar se o profissional tem mensagens ou pedidos
            if ($row_msgs['COUNT(*)'] > 0 || $row_pedidos['COUNT(*)'] > 0) {
                // Se houver dados associados, não permitir desativação
                echo json_encode(['success' => false, 'message' => 'Não é possível desativar a conta enquanto houver dados associados.']);
            } else {
                // Atualizar o status para 'desativado' e marcar a data de desativação
                $sql = "UPDATE profissionais SET status = 'desativado', data_desativacao = NOW() WHERE id = ?";
                if ($stmt_update = $conn->prepare($sql)) {
                    $stmt_update->bind_param("i", $id);
                    if ($stmt_update->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Conta desativada com sucesso.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erro ao desativar a conta.']);
                    }
                    $stmt_update->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta de desativação.']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Conta já está desativada ou deletada.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
}

// Fechar a conexão
$conn->close();
?>
