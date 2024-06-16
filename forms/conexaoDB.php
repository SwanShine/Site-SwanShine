<?php

// Dados para conexão com o banco de dados
$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";

try {
    // Conexão com o banco de dados
    $conn = new mysqli($host, $user, $password, $database);

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    // Verifica se a conexão está estabelecida corretamente
    if ($conn->ping()) {
        echo "Conexão bem-sucedida!";
    } else {
        echo "Erro na conexão: " . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
} catch (Exception $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

?>
