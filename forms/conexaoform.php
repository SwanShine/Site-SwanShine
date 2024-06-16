<?php
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Prepara e vincula
$stmt = $conn->prepare("INSERT INTO usuarios (name, endereco, email, cpf, number) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $endereco, $email, $cpf, $number);

// Coleta os dados do formulário e define os parâmetros
$name = $_POST['name'];
$endereco = $_POST['endereco'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];
$number = $_POST['number'];


// Executa a inserção
if ($stmt->execute()) {
    echo "Cadastro realizado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

// Fecha a conexão
$stmt->close();
$conn->close();
?>
