<?php
// Configurações do banco de dados
$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";

// Conecta ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e valida os dados do formulário
    $servico = $conn->real_escape_string($_POST['servico']);
    $horario = $conn->real_escape_string($_POST['horario']);
    $preco = $conn->real_escape_string($_POST['preco']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $id_cliente = isset($_POST['id_cliente']) ? $conn->real_escape_string($_POST['id_cliente']) : null;
    $id_profissional = isset($_POST['id_profissional']) ? $conn->real_escape_string($_POST['id_profissional']) : null;

    // Lida com o upload de arquivo
    $caminhoImagem = '';
    $imagemBinaria = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Diretório onde os arquivos serão armazenados
        $uploadFile = $uploadDir . basename($_FILES['imagem']['name']);

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
            $caminhoImagem = basename($_FILES['imagem']['name']);
            
            // Lê o conteúdo do arquivo para armazenar como binário
            $imagemBinaria = file_get_contents($_FILES['imagem']['tmp_name']);
        } else {
            echo "Erro ao fazer upload do arquivo.";
            exit();
        }
    }

    // Prepara a consulta SQL
    $sql = "INSERT INTO serviços (Nome, Preço, Descrição, Id_clientes, id_profissionais, caminho_imagem, imagem, horario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepara a declaração
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro na preparação da declaração: " . $conn->error);
    }

    // Vincula os parâmetros
    $stmt->bind_param('sssisssb', $servico, $preco, $descricao, $id_cliente, $id_profissional, $caminhoImagem, $imagemBinaria, $horario);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Serviço cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o serviço: " . $stmt->error;
    }

    // Fecha a declaração e a conexão
    $stmt->close();
    $conn->close();
}
?>
