<?php
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
// Função para sanitizar dados
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e sanitiza os dados do formulário
    $name = sanitize_input($_POST["name"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $cpf = sanitize_input($_POST["cpf"]);
    $repeat_email = sanitize_input($_POST["repeat-email"]);
    $repeat_password = sanitize_input($_POST["repeat-password"]);
    $cell = sanitize_input($_POST["cell"]);
    $birthdate = sanitize_input($_POST["birthdate"]);
    $gender = sanitize_input($_POST["gender"]);
    $cep = sanitize_input($_POST["cep"]);
    $street = sanitize_input($_POST["street"]);
    $number = sanitize_input($_POST["number"]);
    $complement = sanitize_input($_POST["complement"]);
    $reference = sanitize_input($_POST["reference"]);
    $neighborhood = sanitize_input($_POST["neighborhood"]);
    $city = sanitize_input($_POST["city"]);
    $state = sanitize_input($_POST["state"]);
    $servico = sanitize_input($_POST["servico"]);

    // Verifica se os e-mails e senhas conferem
    if ($email != $repeat_email) {
        die("Os e-mails não conferem.");
    }
    if ($password != $repeat_password) {
        die("As senhas não conferem.");
    }

    // Hash da senha
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insere os dados no banco de dados
    $sql = "INSERT INTO profissionais (name, email, password, cpf, cell, birthdate, gender, cep, street, number, complement, reference, neighborhood, city, state, servico) 
            VALUES ('$name', '$email', '$password_hash', '$cpf', '$cell', '$birthdate', '$gender', '$cep', '$street', '$number', '$complement', '$reference', '$neighborhood', '$city', '$state', '$servico')";

    if ($conn->query($sql) === TRUE) {
        echo "Novo profissional cadastrado com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>