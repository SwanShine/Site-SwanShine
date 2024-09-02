
<?php
// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recebe os dados do formulário
$services = isset($_POST['service']) ? implode(", ", $_POST['service']) : '';
$estilo = $_POST['estilo'] ?? '';
$atendimento = $_POST['atendimento'] ?? '';
$urgencia = $_POST['urgencia'] ?? '';
$detalhes = $_POST['detalhes'] ?? '';
$CEP = $_POST['CEP'] ?? '';
$nome = $_POST['Nome'] ?? '';
$email = $_POST['Email'] ?? '';
$telefone = $_POST['Telefone'] ?? '';

// Prepara e vincula
$stmt = $conn->prepare("INSERT INTO pedidos (services, estilo, atendimento, urgencia, detalhes, CEP, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $services, $estilo, $atendimento, $urgencia, $detalhes, $CEP, $nome, $email, $telefone);

// Executa a inserção
if ($stmt->execute()) {
    echo "Pedido inserido com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fecha a conexão
$stmt->close();
$conn->close();
?>
