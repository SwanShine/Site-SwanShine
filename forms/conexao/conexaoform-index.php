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

$cpf = $_POST['cpf']; // Recebe o CPF do formulário

// Depuração: Verificar os valores recebidos
error_log("CPF: " . $cpf);

// Função para remover caracteres especiais do CPF
function limparCPF($cpf) {
    return preg_replace('/[^0-9]/', '', $cpf);
}

// Limpar o CPF
$cpf = limparCPF($cpf);

// Busca na tabela "profissionais"
$query_profissional = "SELECT * FROM profissionais WHERE cpf = ?";
$stmt = $conn->prepare($query_profissional);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$result_profissional = $stmt->get_result();

if ($result_profissional->num_rows > 0) {
    // Se o usuário for um profissional, redireciona para a página dos profissionais
    $user = $result_profissional->fetch_assoc();
    $_SESSION['user_id'] = $user['id_profissional'];
    $_SESSION['user_type'] = 'profissional';
    error_log("Redirecionando para a página dos profissionais.");
    header('Location: ../../NiceAdmin/index.html');
    exit();
}

// Busca na tabela "clientes"
$query_cliente = "SELECT * FROM clientes WHERE cpf = ?";
$stmt = $conn->prepare($query_cliente);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$result_cliente = $stmt->get_result();

if ($result_cliente->num_rows > 0) {
    // Se o usuário for um cliente, redireciona para a página dos clientes
    $user = $result_cliente->fetch_assoc();
    $_SESSION['user_id'] = $user['id_cliente'];
    $_SESSION['user_type'] = 'cliente';
    error_log("Redirecionando para a página dos clientes.");
    header('Location: ../../cliente/index.html');
    exit();
}

// Se o CPF não for encontrado em nenhuma tabela
$_SESSION['login_error'] = "CPF não encontrado! Por favor, cadastre-se.";
error_log("CPF não encontrado.");
header('Location: ../login/login.html'); // Redireciona para a página de cadastro
exit();
?>
