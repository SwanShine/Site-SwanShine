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

// Usar prepared statements para buscar o ID do profissional pelo email
$stmt = $conn->prepare("SELECT id FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se retornou algum resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id']; // Obtenha o ID do usuário

    // Obtenha os dados da requisição
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = $data['user_id'] ?? null; // Use o ID recebido na requisição

    // Verifique se o ID do usuário foi recebido e corresponde ao ID da sessão
    if ($userId === $id) {
        // Prepare a consulta SQL para deletar a conta
        $sql = "DELETE FROM profissionais WHERE id = ?"; // Altere aqui para a tabela correta

        // Use uma declaração preparada para evitar SQL Injection
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId); // "i" indica que o parâmetro é um inteiro

            // Execute a consulta
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar a conta.']);
            }

            $stmt->close(); // Feche a declaração
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID do usuário não corresponde.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
}

// Fechar a conexão
$conn->close();
?>
