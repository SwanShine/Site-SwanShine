<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";

// Conecta ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Preparar e executar a consulta para todos os serviços
$sql = "SELECT * FROM serviços";
$result = $conn->query($sql);

$servicos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
}

echo json_encode($servicos);

$conn->close();
?>
