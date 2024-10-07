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
    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
    } else {
        // Redirecionar se o email da sessão não estiver presente
        header('Location: ../home/forms/login/login.html');
        exit();
    }

    // Atualizar os dados do perfil com base nos campos do formulário
    $nome = $_POST['fullName'] ?? '';
    $endereco = $_POST['address'] ?? ''; // Corrigido para capturar o endereço
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['phone'] ?? '';
    $genero = $_POST['gender'] ?? '';

    // Preparar a consulta SQL para atualizar os dados na tabela clientes
    $sql = "UPDATE clientes 
            SET nome = ?, endereco = ?, cpf = ?, telefone = ?, genero = ? 
            WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            "ssssss",
            $nome,
            $endereco,
            $cpf,
            $telefone,
            $genero,
            $email
        );

        if ($stmt->execute()) {
            // Verificar se o formulário para upload ou remoção de imagem foi enviado
            if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Tipos permitidos
                $fileType = mime_content_type($_FILES['profileImage']['tmp_name']);

                if (in_array($fileType, $allowedTypes)) {
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
                } else {
                    echo "Tipo de arquivo não permitido. Apenas JPG, PNG e GIF são permitidos.";
                }
            }
        } else {
            echo "Erro ao atualizar o perfil: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }

    // Fechar a conexão
    $conn->close();

    // Redirecionar após a atualização
    header("Location: ../perfil.php");
    exit;
} else {
    echo "Método de solicitação inválido.";
}
