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

    // Recuperar as senhas do formulário
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Verificar se a nova senha e a confirmação correspondem
    if ($newPassword !== $confirmPassword) {
        echo "As novas senhas não coincidem.";
        exit();
    }

    // Buscar a senha atual do banco de dados
    $sql = "SELECT senha FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verificar a senha atual
    if (password_verify($currentPassword, $hashedPassword)) {
        // Hash da nova senha
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Atualizar a senha no banco de dados
        $sql = "UPDATE clientes SET senha = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newHashedPassword, $email);

        if ($stmt->execute()) {
            echo "Senha alterada com sucesso!";
        } else {
            echo "Erro ao atualizar a senha: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "A senha atual está incorreta.";
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
