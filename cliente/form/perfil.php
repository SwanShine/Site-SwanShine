<?php
session_start(); // Certifique-se de iniciar a sessão

// Função para conectar ao banco de dados
function getDatabaseConnection() {
    // Dados de conexão com o banco de dados
    $servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
    $username = "admin";
    $password = "gLAHqWkvUoaxwBnm9wKD";
    $dbname = "swanshine";
    
    // Definição do Data Source Name (DSN)
    $dsn = "mysql:host=$servername;dbname=$dbname";
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Log de erro detalhado para análise
        error_log('Conexão falhou: ' . $e->getMessage());
        // Mensagem amigável para o usuário
        echo 'Desculpe, tivemos um problema ao conectar ao banco de dados. Por favor, tente novamente mais tarde.';
        exit;
    }
}

// Verificar se o usuário está logado e os dados de CPF e e-mail estão na sessão
if (!isset($_SESSION['cpf']) || !isset($_SESSION['email'])) {
    echo 'Acesso não autorizado.';
    exit;
}

$cpf = $_SESSION['cpf'];
$email = $_SESSION['email'];

// Conectar ao banco de dados
$pdo = getDatabaseConnection();

try {
    // Preparar e executar a consulta na tabela usuarios
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE cpf = :cpf AND email = :email');
    $stmt->execute(['cpf' => $cpf, 'email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo 'Usuário não encontrado.';
        exit;
    }

    // Obter o ID do cliente da tabela usuarios
    $clienteId = $user['id']; // Supondo que 'id_cliente' é o campo que relaciona com a tabela cliente

    // Preparar e executar a consulta na tabela cliente
    $stmtCliente = $pdo->prepare('SELECT * FROM cliente WHERE id = :id');
    $stmtCliente->execute(['id' => $clienteId]);
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo 'Detalhes do cliente não encontrados.';
        exit;
    }

    // Acesso aos dados do cliente
    $nome = htmlspecialchars($cliente['nome']);
    $endereco = htmlspecialchars($cliente['endereco']);
    $emailCliente = htmlspecialchars($cliente['email']);
    $cpf = htmlspecialchars($cliente['cpf']);
    $telefone = htmlspecialchars($cliente['telefone']);
    $genero = htmlspecialchars($cliente['genero']);
    $senha = htmlspecialchars($cliente['senha']); // A senha geralmente não é exibida por motivos de segurança
} catch (PDOException $e) {
    // Log de erro detalhado para análise
    error_log('Erro na consulta: ' . $e->getMessage());
    // Mensagem amigável para o usuário
    echo 'Desculpe, tivemos um problema ao buscar suas informações. Por favor, tente novamente mais tarde.';
    exit;
}
?>
