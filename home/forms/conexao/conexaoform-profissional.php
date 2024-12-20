<?php

// Iniciar a sessão
session_start();

// Dados para conexão com o banco de dados
$host = getenv('DB_HOST') ?: "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = getenv('DB_USER') ?: "admin";
$password = getenv('DB_PASSWORD') ?: "gLAHqWkvUoaxwBnm9wKD";
$database = getenv('DB_NAME') ?: "swanshine";

// Função para sanitizar dados
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

try {
    // Conexão com o banco de dados
    $conn = new mysqli($host, $user, $password, $database);

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        throw new Exception("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    // Verifica se a conexão está estabelecida corretamente
    if ($conn->ping()) {
        //echo "Conexão bem-sucedida!"; // Remover ou comentar para produção
    } else {
        throw new Exception("Erro na conexão: " . $conn->error);
    }

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Coleta e sanitiza os dados do formulário
        $name = sanitize_input($_POST["name"]);
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);
        $cpf = sanitize_input($_POST["cpf"]); // Adicionado o campo cpf
        $repeat_email = sanitize_input($_POST["repeat-email"]);
        $repeat_password = sanitize_input($_POST["repeat-password"]);
        $cell = sanitize_input($_POST["cell"]);
        $birthdate = sanitize_input($_POST["birthdate"]);
        $gender = sanitize_input($_POST["gender"]);

        // Campos de endereço
        $cep = sanitize_input($_POST["cep"]);
        $street = sanitize_input($_POST["street"]);
        $number = sanitize_input($_POST["number"]);
        $complement = sanitize_input($_POST["complement"]);
        $reference = sanitize_input($_POST["reference"]);
        $neighborhood = sanitize_input($_POST["neighborhood"]);
        $city = sanitize_input($_POST["city"]);
        $state = sanitize_input($_POST["state"]);

        // Serviço
        $servico = sanitize_input($_POST["servico"]);

        // Verifica se os e-mails e senhas conferem
        if ($email !== $repeat_email) {
            throw new Exception("Os e-mails não conferem.");
        }
        if ($password !== $repeat_password) {
            throw new Exception("As senhas não conferem.");
        }

        // Cria o array de endereço (exceto o CEP)
        $endereco = [
            "rua" => $street,
            "numero" => $number,
            "complemento" => $complement,
            "referencia" => $reference,
            "bairro" => $neighborhood,
            "cidade" => $city,
            "estado" => $state
        ];

        // Converte o array para JSON
        $enderecoJson = json_encode($endereco);

        // Prepara a declaração SQL
        $stmt = $conn->prepare("INSERT INTO profissionais (nome, email, senha, celular, data_de_aniversario, genero, cep, endereco, servicos, cpf) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            throw new Exception("Erro ao preparar a declaração: " . $conn->error);
        }

        // Vincula os parâmetros
        $stmt->bind_param("ssssssssss", $name, $email, $password, $cell, $birthdate, $gender, $cep, $enderecoJson, $servico, $cpf);

        // Executa a declaração
        if ($stmt->execute()) {
            // Armazenar o email na sessão
            $_SESSION['user_email'] = $email;

            // Fecha a declaração e a conexão
            $stmt->close();
            $conn->close();

            // Redirecionar para a página do cliente
            header("Location: ../../../NiceAdmin/index.php");
            exit();
        } else {
            throw new Exception("Erro ao executar a declaração: " . $stmt->error);
        }
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
