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

session_start();

$email = $_POST['email'];
$senha = $_POST['senha'];

// Depuração: Verificar os valores recebidos
error_log("Email: " . $email);
error_log("Senha: " . $senha);

// Busca na tabela "profissionais"
$query_profissional = "SELECT * FROM profissionais WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($query_profissional);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result_profissional = $stmt->get_result();

if ($result_profissional->num_rows > 0) {
    // Se o usuário for um profissional, armazena o email e redireciona
    $user = $result_profissional->fetch_assoc();
    $_SESSION['user_id'] = $user['id_profissional'];
    $_SESSION['user_email'] = $email;  // Armazenar o email do usuário
    $_SESSION['user_type'] = 'profissional';
    error_log("Redirecionando para a página dos profissionais.");
    header('Location: ../../../NiceAdmin/index.html');
    exit();
}

// Busca na tabela "clientes"
$query_cliente = "SELECT * FROM clientes WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($query_cliente);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result_cliente = $stmt->get_result();

if ($result_cliente->num_rows > 0) {
    // Se o usuário for um cliente, armazena o email e redireciona
    $user = $result_cliente->fetch_assoc();
    $_SESSION['user_id'] = $user['id_cliente'];
    $_SESSION['user_email'] = $email;  // Armazenar o email do usuário
    $_SESSION['user_type'] = 'cliente';
    error_log("Redirecionando para a página dos clientes.");
    header('Location: ../../../cliente/index.html');
    exit();
}

// Se o email e a senha não forem encontrados em nenhuma tabela
$_SESSION['login_error'] = "Email ou senha incorretos!";
error_log("Email ou senha incorretos.");
header('Location: ../login/login.html');
exit();
?>
