<?php
session_start(); // Iniciar a sessão para garantir acesso à variável $_SESSION

// Verificar se o formulário foi enviado
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
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verificar se a nova senha e a confirmação são iguais
    if ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = "A nova senha e a confirmação da nova senha não coincidem.";
        header("Location: ../perfil.php"); // Redirecionar de volta com mensagem
        exit;
    }

    // Atualizar a senha no banco de dados
    $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $sql = "UPDATE profissionais SET senha = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashedNewPassword, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Senha alterada com sucesso.";
    } else {
        $_SESSION['message'] = "Erro ao alterar a senha. Tente novamente.";
    }

    // Fechar a conexão
    $stmt->close();
    $conn->close();

    // Redirecionar após a atualização
    header("Location: ../perfil.php");
    exit;
}
?>
