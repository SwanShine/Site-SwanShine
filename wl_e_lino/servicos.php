<?php
header('Content-Type: application/json');

$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";


$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT Nome, Preço, Descrição, imagem FROM serviços";
$result = $conn->query($sql);

$servicos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
}

echo json_encode($servicos);

$conn->close();
?>
