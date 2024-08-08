<?php
// Dados para conexão com o banco de dados
$host = getenv('DB_HOST') ?: "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = getenv('DB_USER') ?: "admin";
$password = getenv('DB_PASSWORD') ?: "gLAHqWkvUoaxwBnm9wKD";
$database = getenv('DB_NAME') ?: "swanshine";

$conn = new mysqli($host, $user, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter e sanitizar o email da URL
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email inválido.");
}

// Preparar a declaração SQL para evitar injeção de SQL
$stmt = $conn->prepare("SELECT nome, email, cpf, celular, data_de_aniversario, genero, cep, rua, numero, complemento, referencia, bairro, cidade, estado, servicos FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);

// Executar a declaração
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Exibir os dados do profissional
    $row = $result->fetch_assoc();
    echo "<h1>Perfil do Profissional</h1>";
    echo "<p>Nome: " . htmlspecialchars($row['nome']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
    echo "<p>CPF: " . htmlspecialchars($row['cpf']) . "</p>";
    echo "<p>Telefone: " . htmlspecialchars($row['celular']) . "</p>";
    echo "<p>Nascimento: " . htmlspecialchars($row['data_de_aniversario']) . "</p>";
    echo "<p>Gênero: " . htmlspecialchars($row['genero']) . "</p>";
    echo "<p>CEP: " . htmlspecialchars($row['cep']) . "</p>";
    echo "<p>Rua: " . htmlspecialchars($row['rua']) . "</p>";
    echo "<p>Número: " . htmlspecialchars($row['numero']) . "</p>";
    echo "<p>Complemento: " . htmlspecialchars($row['complemento']) . "</p>";
    echo "<p>Referência: " . htmlspecialchars($row['referencia']) . "</p>";
    echo "<p>Bairro: " . htmlspecialchars($row['bairro']) . "</p>";
    echo "<p>Cidade: " . htmlspecialchars($row['cidade']) . "</p>";
    echo "<p>Estado: " . htmlspecialchars($row['estado']) . "</p>";
    echo "<p>Serviço: " . htmlspecialchars($row['servicos']) . "</p>";
} else {
    echo "Profissional não encontrado.";
}

// Fechar a declaração e a conexão
$stmt->close();
$conn->close();
?>
