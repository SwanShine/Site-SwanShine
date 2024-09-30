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
$servicos = "Depilação"; // Valor fixo para serviços
$tipo = $_POST['tipo'] ?? ''; // Novo campo para tipo
$estilo = $_POST['estilo'] ?? '';
$atendimento = $_POST['atendimento'] ?? '';
$urgencia = $_POST['urgencia'] ?? '';
$detalhes = $_POST['detalhes'] ?? '';
$cep = $_POST['cep'] ?? '';
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';

// Prepara e vincula
$stmt = $conn->prepare("INSERT INTO pedidos (servicos, tipo, estilo, atendimento, urgencia, detalhes, cep, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $servicos, $tipo, $estilo, $atendimento, $urgencia, $detalhes, $cep, $nome, $email, $telefone);

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
