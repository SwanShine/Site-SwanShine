<?php
// Conectar ao banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Consultar clientes desativados há mais de 30 dias
$sql = "SELECT id, data_desativacao FROM clientes WHERE status = 'desativado' AND DATEDIFF(CURDATE(), data_desativacao) > 30";
$result = $conn->query($sql);

// Excluir contas que atendem ao critério
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        
        // Excluir o usuário
        $deleteSql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        echo "Conta com ID $user_id foi excluída.\n";
    }
} else {
    echo "Nenhuma conta para excluir.\n";
}

// Fechar a conexão
$conn->close();
?>
