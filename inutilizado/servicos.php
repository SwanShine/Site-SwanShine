<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";

// Conexão com o banco de dados usando MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Verificação da conexão
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Consulta SQL para obter serviços
$sql = "SELECT Nome, Preço, Descrição, imagem FROM serviços";
$result = $conn->query($sql);

// Inicialização do array de serviços
$servicos = [];

// Processamento dos resultados da consulta
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
    // Libere o resultado da consulta
    $result->free();
} else {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    $conn->close();
    exit();
}

// Retorno dos serviços em formato JSON
echo json_encode($servicos);

// Fechamento da conexão com o banco de dados
$conn->close();
?>
