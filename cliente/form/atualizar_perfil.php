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
    $celular = $_POST['celular'] ?? '';
    $data_de_aniversario = $_POST['data_de_aniversario'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $rua = $_POST['rua'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $servicos = $_POST['servicos'] ?? '';

    // Redes sociais
    $tiktok = $_POST['tiktok'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';

    // Preparar a consulta SQL para atualizar os dados
    $sql = "UPDATE profissionais 
            SET nome = ?, celular = ?, data_de_aniversario = ?, genero = ?, cep = ?, rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, servicos = ?, tiktok = ?, facebook = ?, instagram = ?, linkedin = ?, whatsapp = ? 
            WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Corrigir a string de tipo para corresponder ao número de variáveis
    if ($stmt) {
        $stmt->bind_param("ssssssssssssssssss", 
            $nome, 
            $celular, 
            $data_de_aniversario, 
            $genero, 
            $cep, 
            $rua, 
            $numero, 
            $complemento, 
            $bairro, 
            $cidade, 
            $estado, 
            $servicos, 
            $tiktok, 
            $facebook, 
            $instagram, 
            $linkedin, 
            $whatsapp, 
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
                        $sql = "UPDATE profissionais SET profileImage = ? WHERE email = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $targetFile, $email);
                        $stmt->execute();
                    } else {
                        echo "Erro ao fazer upload da imagem.";
                    }
                } else {
                    echo "Tipo de arquivo não permitido. Apenas JPG, PNG e GIF são permitidos.";
                }
            } elseif (isset($_POST['removeImage'])) {
                // Se o botão de remover imagem for clicado
                $sql = "UPDATE profissionais SET profileImage = NULL WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
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
?>
