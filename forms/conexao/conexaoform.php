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

// Prepara a consulta
$stmt = $conn->prepare("INSERT INTO clientes (Nome, Endereço, Email, CPF, Telefone) VALUES (?, ?, ?, ?, ?)");
if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

// Coleta os dados do formulário e define os parâmetros
$name = $conn->real_escape_string($_POST['name']);
$endereco = $conn->real_escape_string($_POST['endereco']);
$email = $conn->real_escape_string($_POST['email']);
$cpf = $conn->real_escape_string($_POST['cpf']);
$number = $conn->real_escape_string($_POST['number']);

// Verifica se todos os dados foram recebidos
if (isset($name, $endereco, $email, $cpf, $number)) {
    // Vincula os parâmetros
    $stmt->bind_param("sssss", $name, $endereco, $email, $cpf, $number);

    // Executa a inserção
    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro: " . $stmt->error;
    }

    // Fecha a declaração
    $stmt->close();
} else {
    echo "Erro: Todos os campos são obrigatórios.";
}

// Fecha a conexão
$conn->close();
?>
