<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../home/forms/login/login.html');
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

// Recuperar o email da sessão e obter o ID do cliente
$email = $_SESSION['user_email'];
$sql_cliente = "SELECT id FROM clientes WHERE email = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param('s', $email);
$stmt->execute();
$result_cliente = $stmt->get_result();
$row_cliente = $result_cliente->fetch_assoc();
$id_cliente = $row_cliente['id'];

// Obter o ID do profissional da URL
$id_profissional = isset($_GET['profissional_id']) ? intval($_GET['profissional_id']) : 0;

// Enviar nova mensagem via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $_POST['mensagem'];

    // Validação da mensagem
    if (!empty($mensagem)) {
        $query = "INSERT INTO mensagens (remetente, profissional_id, mensagem) VALUES ('cliente', ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is', $id_profissional, $mensagem);
        
        // Executar a consulta e verificar erros
        if ($stmt->execute()) {
            // Atualiza a tabela de conversas
            $update_conversas = "INSERT INTO conversas (id_cliente, id_profissional, ultima_mensagem) 
                                 VALUES (?, ?, NOW()) 
                                 ON DUPLICATE KEY UPDATE ultima_mensagem = NOW()";
            $stmt = $conn->prepare($update_conversas);
            $stmt->bind_param('ii', $id_cliente, $id_profissional);
            $stmt->execute();
            echo "Mensagem enviada com sucesso.";
        } else {
            echo "Erro ao enviar mensagem: " . $stmt->error; // Adicionei mensagem de erro
        }
    } else {
        echo "Mensagem não pode estar vazia."; // Adicionei verificação para mensagem vazia
    }
    exit; // Certifique-se de sair após o envio
}

// Carregar mensagens do banco de dados
$query_mensagens = "SELECT * FROM mensagens WHERE profissional_id = ? ORDER BY data_envio ASC";
$stmt = $conn->prepare($query_mensagens);
$stmt->bind_param('i', $id_profissional);
$stmt->execute();
$result_mensagens = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat com Profissional</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 400px; /* Largura fixa para o chat */
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: white;
        }

        .chat-header {
            background-color: #4CAF50;
            padding: 15px;
            color: white;
            text-align: center;
            font-size: 18px;
        }

        .chat-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }

        .chat-footer {
            display: flex;
            padding: 10px;
            background-color: #e9e9e9;
        }

        .chat-footer input {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .chat-footer button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 20px;
            cursor: pointer;
        }

        .chat-footer button:hover {
            background-color: #45a049;
        }

        .mensagem {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 60%;
        }

        .mensagem-cliente {
            background-color: #dfffd6;
            align-self: flex-end;
        }

        .mensagem-profissional {
            background-color: #ddd;
            align-self: flex-start;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="chat-header">
        Chat com o Profissional (ID: <?php echo htmlspecialchars($id_profissional); ?>)
    </div>
    <div class="chat-body" id="chat-body">
        <!-- Carregar mensagens do banco de dados -->
        <?php while ($row_mensagens = $result_mensagens->fetch_assoc()): ?>
            <div class="mensagem <?php echo ($row_mensagens['remetente'] == 'cliente') ? 'mensagem-cliente' : 'mensagem-profissional'; ?>">
                <?php echo htmlspecialchars($row_mensagens['mensagem']); ?>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="chat-footer">
        <input type="text" id="mensagem" placeholder="Digite sua mensagem...">
        <button id="enviar">Enviar</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#enviar').on('click', function () {
        var mensagem = $('#mensagem').val();
        if (mensagem.trim() === '') return;

        $.ajax({
            url: 'mensagem.php?profissional_id=<?php echo $id_profissional; ?>',
            type: 'POST',
            data: {mensagem: mensagem},
            success: function (response) {
                console.log(response); // Adicionei log para verificar a resposta
                $('#chat-body').append('<div class="mensagem mensagem-cliente"><p>' + mensagem + '</p></div>');
                $('#mensagem').val('');
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight); // Scroll automático para a última mensagem
            },
            error: function (xhr, status, error) {
                console.error("Erro no envio da mensagem:", error); // Adicionei log para erros
            }
        });
    });

    // Atualização automática das mensagens a cada 3 segundos
    setInterval(function () {
        $('#chat-body').load('mensagem.php?profissional_id=<?php echo $id_profissional; ?> #chat-body');
    }, 3000); // Atualiza a cada 3 segundos
</script>

</body>
</html>

<?php
// Fechar a conexão após todas as operações
$conn->close();
?>
