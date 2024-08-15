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
    $servico = $conn->real_escape_string(trim($_POST['servico']));
    $preco = $conn->real_escape_string(trim($_POST['preco']));
    $descricao = isset($_POST['descricao']) ? $conn->real_escape_string(trim($_POST['descricao'])) : null;
    $horario = isset($_POST['horario']) ? $conn->real_escape_string(trim($_POST['horario'])) : null;

    // Lida com o upload de arquivo
    $caminhoImagem = '';
    $imagemBinaria = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Diretório onde os arquivos serão armazenados
        $uploadFile = $uploadDir . basename($_FILES['imagem']['name']);

        // Verifica se o diretório de uploads existe, caso contrário, cria-o
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
            $caminhoImagem = basename($_FILES['imagem']['name']);
            // Lê o conteúdo do arquivo para armazenar como binário
            $imagemBinaria = file_get_contents($_FILES['imagem']['tmp_name']);
        } else {
            die("Erro ao fazer upload do arquivo.");
        }
    }

    // Prepara a consulta SQL
    $sql = "INSERT INTO serviços (Nome, Preço, Descrição, caminho_imagem, imagem, horario) VALUES (?, ?, ?, ?, ?, ?)";

    // Prepara a declaração
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro na preparação da declaração: " . $conn->error);
    }

    // Vincula os parâmetros
    // Para binários, é necessário usar 'b' como tipo de dado na bind_param
    $stmt->bind_param('sssssb', $servico, $preco, $descricao, $caminhoImagem, $imagemBinaria, $horario);

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
