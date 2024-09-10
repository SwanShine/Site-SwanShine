<?php
// Iniciar a sessão
session_start();

// Verificar se a solicitação é uma postagem de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Recuperar o email da sessão
    $email = $_SESSION['user_email'];

    // Atualizar os dados do perfil
    $nome = $_POST['fullName'];
    $endereco = $_POST['address'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['phone'];
    $genero = $_POST['gender'];

    $sql = "UPDATE clientes SET nome = ?, endereco = ?, cpf = ?, telefone = ?, genero = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $endereco, $cpf, $telefone, $genero, $email);
    $stmt->execute();

    // Processar o upload da imagem
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);

        // Verificar se o diretório de uploads existe, caso contrário, criá-lo
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            // Atualizar o caminho da imagem no banco de dados
            $sql = "UPDATE clientes SET profileImage = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $targetFile, $email);
            $stmt->execute();
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    // Redirecionar após a atualização
    header("Location: ../perfil.php");
    exit;
}
?>
