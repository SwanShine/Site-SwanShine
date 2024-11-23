<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../home/forms/login/login.html');
    exit();
}

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

// Recuperar o email do usuário da sessão
$email = $_SESSION['user_email'];

// Preparar a consulta SQL para excluir o usuário
$sql = "DELETE FROM clientes WHERE email = ?"; // Substitua 'clientes' pelo nome correto da sua tabela de usuários

// Preparar a declaração
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Associar os parâmetros e executar a declaração
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        // Excluir a conta com sucesso
        
        // Encerrar a sessão
        session_unset();
        session_destroy();

        // Exibir o pop-up e redirecionar
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Conta Deletada</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin-top: 20%;
                    background-color: #f5f5f5;
                }
                .popup {
                    display: inline-block;
                    padding: 20px;
                    background-color: #fff;
                    border: 1px solid #ddd;
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                    border-radius: 10px;
                }
                .popup h1 {
                    color: #333;
                }
                .countdown {
                    font-size: 2em;
                    color: #007BFF;
                }
            </style>
            <script>
                let countdown = 5;
                function startCountdown() {
                    const countdownElement = document.getElementById('countdown');
                    const interval = setInterval(() => {
                        countdown--;
                        countdownElement.textContent = countdown;
                        if (countdown <= 0) {
                            clearInterval(interval);
                            window.location.href = '../../../index.html';
                        }
                    }, 1000);
                }
            </script>
        </head>
        <body onload='startCountdown()'>
            <div class='popup'>
                <h1>Conta deletada com sucesso!</h1>
                <p>Você será redirecionado em <span id='countdown' class='countdown'>5</span> segundos...</p>
            </div>
        </body>
        </html>
        ";
        exit();
    } else {
        echo "Erro ao deletar a conta: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
}

// Fechar a conexão
$conn->close();
?>
