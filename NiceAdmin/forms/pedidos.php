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

// Obter filtro da URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Preparar a consulta com base no filtro
if ($filter == 'barbeiro') {
    $sql = "SELECT * FROM pedidos WHERE tipo_servico LIKE '%Corte de Cabelo%' OR tipo_servico LIKE '%Barba%' ORDER BY data_criacao DESC";
} elseif ($filter == 'maquiagem') {
    $sql = "SELECT * FROM pedidos WHERE tipo_servico LIKE '%Maquiagem%' ORDER BY data_criacao DESC";
} else {
    $sql = "SELECT * FROM pedidos ORDER BY data_criacao DESC";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<h3>Pedido #" . $row['id'] . "</h3>";
        echo "<p><strong>Tipo de Serviço:</strong> " . $row['tipo_servico'] . "</p>";
        echo "<p><strong>Estilo:</strong> " . $row['estilo'] . "</p>";
        echo "<p><strong>Atendimento:</strong> " . $row['atendimento'] . "</p>";
        echo "<p><strong>Urgência:</strong> " . $row['urgencia'] . "</p>";
        echo "<p><strong>Detalhes:</strong> " . $row['detalhes'] . "</p>";
        echo "<p><strong>CEP:</strong> " . $row['cep'] . "</p>";
        echo "<p><strong>Nome:</strong> " . $row['nome'] . "</p>";
        echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";
        echo "<p><strong>Telefone:</strong> " . $row['telefone'] . "</p>";
        echo "<p><strong>Data:</strong> " . $row['data_criacao'] . "</p>";
        echo "</div>";
    }
} else {
    echo "Nenhum pedido encontrado.";
}

$conn->close();
?>
