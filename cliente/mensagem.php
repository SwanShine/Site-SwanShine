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
$sql_cliente = "SELECT id FROM clientes WHERE email = '$email'";
$result_cliente = $conn->query($sql_cliente);
$row_cliente = $result_cliente->fetch_assoc();
$id_cliente = $row_cliente['id'];

// Obter o ID do profissional da URL
$id_profissional = isset($_GET['profissional_id']) ? intval($_GET['profissional_id']) : 0;

// Enviar nova mensagem via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $_POST['mensagem'];
    $query = "INSERT INTO mensagens (id_cliente, id_profissional, mensagem, remetente)
              VALUES ($id_cliente, $id_profissional, '$mensagem', 'cliente')";
    $conn->query($query);

    // Atualiza a tabela de conversas
    $update_conversas = "INSERT INTO conversas (id_cliente, id_profissional, ultima_mensagem) 
                         VALUES ($id_cliente, $id_profissional, NOW()) 
                         ON DUPLICATE KEY UPDATE ultima_mensagem = NOW()";
    $conn->query($update_conversas);
    exit;
}

// Consultar os profissionais por serviço
$sql = "SELECT * FROM profissionais";
$result = $conn->query($sql);

$profissionais = [
    'Barbeiro' => [],
    'Maquiagem' => [],
    'Lash_Designer' => [],
    'Nail_Designer' => [],
    'Trancista' => [],
    'Esteticista' => [],
    'Cabeleireira' => [],
    'Depilação' => []
];

if ($result->num_rows > 0) {
    // Separar os profissionais por serviço
    while ($row = $result->fetch_assoc()) {
        $servico = $row['servicos'];
        if (array_key_exists($servico, $profissionais)) {
            $profissionais[$servico][] = $row;
        }
    }
} else {
    echo "Nenhum profissional encontrado.";
}

// Fechar conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat com Profissional</title>
    <style>
        /* Estilos básicos para o chat */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .chat-container {
            display: flex;
            width: 80%;
            height: 80%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat {
            flex: 3;
            background-color: white;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background-color: #4CAF50;
            padding: 15px;
            color: white;
            font-size: 18px;
            text-align: center;
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
        }

        .chat-footer button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            margin-left: 10px;
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

        .conversas {
            flex: 1;
            background-color: #f1f1f1;
            padding: 10px;
            overflow-y: auto;
        }

        .conversa-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .conversa-item:hover {
            background-color: #ddd;
        }

        .conversa-item p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="chat-container">
        <!-- Seção de mensagens -->
        <div class="chat">
            <div class="chat-header">
                Chat com Profissional (ID: <?php echo $id_profissional; ?>)
            </div>
            <div class="chat-body" id="chat-body">
                <!-- Carregar mensagens do banco de dados -->
                <?php
                $query = "SELECT * FROM mensagens WHERE id_cliente = $id_cliente AND id_profissional = $id_profissional ORDER BY data_envio ASC";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    $classe = $row['remetente'] == 'cliente' ? 'mensagem-cliente' : 'mensagem-profissional';
                    echo "<div class='mensagem $classe'><p>{$row['mensagem']}</p></div>";
                }
                ?>
            </div>
            <div class="chat-footer">
                <input type="text" id="mensagem" placeholder="Digite sua mensagem...">
                <button id="enviar">Enviar</button>
            </div>
        </div>

        <!-- Seção de conversas recentes -->
        <div class="conversas">
            <h3>Conversas Recentes</h3>
            <?php
            $query = "SELECT profissionais.nome, profissionais.id FROM conversas
                      JOIN profissionais ON conversas.id_profissional = profissionais.id
                      WHERE conversas.id_cliente = $id_cliente ORDER BY conversas.ultima_mensagem DESC";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                echo "<div class='conversa-item' onclick='abrirConversa({$row['id']})'>";
                echo "<p>{$row['nome']} (ID: {$row['id']})</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Função para enviar mensagem via AJAX
    $('#enviar').on('click', function () {
        var mensagem = $('#mensagem').val();
        if (mensagem.trim() === '') return;

        $.ajax({
            url: 'mensagem.php?profissional_id=<?php echo $id_profissional; ?>',
            type: 'POST',
            data: {mensagem: mensagem},
            success: function () {
                $('#chat-body').append('<div class="mensagem mensagem-cliente"><p>' + mensagem + '</p></div>');
                $('#mensagem').val('');
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight); // Scroll automático para a última mensagem
            }
        });
    });

    // Função para abrir uma conversa com outro profissional
    function abrirConversa(id_profissional) {
        window.location.href = 'mensagem.php?profissional_id=' + id_profissional;
    }

    // Função para atualizar mensagens em tempo real (polling)
    setInterval(function () {
        $('#chat-body').load('mensagem.php?profissional_id=<?php echo $id_profissional; ?> #chat-body');
    }, 3000); // Atualiza a cada 3 segundos
</script>

</body>
</html>
