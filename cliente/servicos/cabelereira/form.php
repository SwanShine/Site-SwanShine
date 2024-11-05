<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
    exit();
}

// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão falhou
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Função para obter os dados do cliente
function getClientData($conn, $email) {
    $stmt = $conn->prepare("SELECT nome, telefone, cep FROM clientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        echo "Cliente não encontrado.";
        exit();
    }
}

// Função para inserir os dados do pedido
function insertOrder($conn, $servicos, $tipo, $estilo, $atendimento, $urgencia, $detalhes, $cep, $nome, $email, $telefone) {
    $stmt = $conn->prepare("INSERT INTO pedidos (servicos, tipo, estilo, atendimento, urgencia, detalhes, cep, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Erro na preparação da inserção: " . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $servicos, $tipo, $estilo, $atendimento, $urgencia, $detalhes, $cep, $nome, $email, $telefone);

    if ($stmt->execute()) {
        header('Location: ../../pedidos/pedido_pendente.php');
        exit();
    } else {
        echo "Erro ao inserir pedido: " . $stmt->error;
    }
}

// Obtém os dados do cliente
$cliente = getClientData($conn, $email);

// Recebe os dados do formulário e insere
$tipo = isset($_POST['tipo']) ? (is_array($_POST['tipo']) ? implode(', ', $_POST['tipo']) : $_POST['tipo']) : ''; // Verifica se 'tipo' é array, se sim, usa implode, senão, usa o valor diretamente

insertOrder(
    $conn,
    "Cabelereira", // Valor fixo para serviços
    $tipo, // Tipo de serviço
    $_POST['estilo'] ?? '', // Estilo, caso exista
    $_POST['atendimento'] ?? '', // Atendimento, caso exista
    $_POST['urgencia'] ?? '', // Urgência, caso exista
    $_POST['detalhes'] ?? '', // Detalhes, caso exista
    $cliente['cep'], // Usando o CEP do cliente
    $cliente['nome'], // Nome do cliente
    $email, // Email do cliente
    $cliente['telefone'] // Telefone do cliente
);

// Fecha a conexão
$conn->close();
?>