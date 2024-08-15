<?php
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

// Obter e sanitizar dados do formulário
$nome = trim($_POST['Nome']);
$endereco = trim($_POST['Endereço']);
$email = filter_var(trim($_POST['Email']), FILTER_SANITIZE_EMAIL);
$cpf = trim($_POST['cpf']);
$telefone = trim($_POST['Telefone']);
$genero = trim($_POST['genero']);
$senha = trim($_POST['senha']);
$confirmar_senha = trim($_POST['Confirmar_senha']);

// Validar dados
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("E-mail inválido.");
}

if ($senha !== $confirmar_senha) {
    die("As senhas não coincidem.");
}

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_BCRYPT);

// Preparar e executar a inserção
$sql = "INSERT INTO clientes (nome, endereco, email, cpf, telefone, genero, senha) VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Associe os parâmetros com os valores
    $stmt->bind_param("sssssss", $nome, $endereco, $email, $cpf, $telefone, $genero, $senha_hash);

    if ($stmt->execute()) {
        echo "Novo registro criado com sucesso!";
    } else {
        echo "Erro ao executar a consulta: " . $stmt->error;
    }

    // Fechar a declaração
    $stmt->close();
} else {
    echo "Erro ao preparar a consulta: " . $conn->error;
}

// Fechar a conexão
$conn->close();
?>
