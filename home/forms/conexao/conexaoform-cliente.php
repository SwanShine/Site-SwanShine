<?php
// Iniciar a sessão
session_start();

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
$confirmar_senha = isset($_POST['confirmar_senha']) ? trim($_POST['confirmar_senha']) : '';

// Validar dados
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("E-mail inválido.");
}

if ($senha !== $confirmar_senha) {
    die("As senhas não coincidem.");
}


// Verificar se o e-mail já existe
$email_check_sql = "SELECT COUNT(*) FROM clientes WHERE email = ?";
if ($email_check_stmt = $conn->prepare($email_check_sql)) {
    $email_check_stmt->bind_param("s", $email);
    $email_check_stmt->execute();
    $email_check_stmt->bind_result($email_count);
    $email_check_stmt->fetch();
    $email_check_stmt->close();

    if ($email_count > 0) {
        die("O e-mail já está registrado. Escolha outro e-mail.");
    }
} else {
    die("Erro ao preparar a verificação de e-mail: " . $conn->error);
}

// Verificar se o CPF já existe
$cpf_check_sql = "SELECT COUNT(*) FROM clientes WHERE cpf = ?";
if ($cpf_check_stmt = $conn->prepare($cpf_check_sql)) {
    $cpf_check_stmt->bind_param("s", $cpf);
    $cpf_check_stmt->execute();
    $cpf_check_stmt->bind_result($cpf_count);
    $cpf_check_stmt->fetch();
    $cpf_check_stmt->close();

    if ($cpf_count > 0) {
        die("O CPF já está registrado. Escolha outro CPF.");
    }
} else {
    die("Erro ao preparar a verificação de CPF: " . $conn->error);
}

// Preparar e executar a inserção
$sql = "INSERT INTO clientes (nome, endereco, email, cpf, telefone, genero, senha) VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssss", $nome, $endereco, $email, $cpf, $telefone, $genero, $senha);

    if ($stmt->execute()) {
        // Armazenar o email na sessão
        $_SESSION['user_email'] = $email;

        // Fechar a declaração e a conexão
        $stmt->close();
        $conn->close();

        // Redirecionar para a página do cliente (no diretório "cliente")
        header("Location: ../../../cliente/index.php");
        exit();
    } else {
        echo "Erro ao executar a consulta: " . $stmt->error;
    }
} else {
    echo "Erro ao preparar a consulta: " . $conn->error;
}

// Fechar a conexão se algo falhar
$conn->close();
?>
