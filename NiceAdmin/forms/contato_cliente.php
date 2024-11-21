<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
    exit();
}

$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$email_profissional = $_SESSION['user_email'];
$id_cliente = $_GET['id']; // O id do cliente, passado na URL

// Buscar dados do cliente
$sql_cliente = "SELECT id, telefone, nome, email FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Buscar dados do profissional
$sql_profissional = "SELECT id, nome, celular, email, endereco, cep FROM profissionais WHERE email = ?";
$stmt = $conn->prepare($sql_profissional);
$stmt->bind_param("s", $email_profissional);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Decodificar o JSON do campo 'endereco' para obter os detalhes
if (!empty($profissional['endereco'])) {
    $endereco = json_decode($profissional['endereco'], true);
    $rua = $endereco['rua'] ?? 'Não informado';
    $numero = $endereco['numero'] ?? 'Não informado';
    $complemento = $endereco['complemento'] ?? 'Não informado';
    $bairro = $endereco['bairro'] ?? 'Não informado';
    $cidade = $endereco['cidade'] ?? 'Não informado';
    $estado = $endereco['estado'] ?? 'Não informado';
} else {
    $rua = $numero = $complemento = $bairro = $cidade = $estado = 'Não informado';
}

// Recuperar mensagens trocadas entre o profissional e o cliente
$sql_mensagens = "SELECT * FROM mensagens WHERE 
                  (remetente = ? AND profissional_id = ?) OR 
                  (remetente = ? AND profissional_id = ?) 
                  ORDER BY data_envio ASC";
$stmt = $conn->prepare($sql_mensagens);
$stmt->bind_param("iiii", $profissional['id'], $id_cliente, $id_cliente, $profissional['id']);
$stmt->execute();
$mensagens = $stmt->get_result();
$stmt->close();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Swan Shine - Profissional</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <!-- Link para o favicon da página -->
    <link href="../assets/img/favicon.png" rel="apple-touch-icon">
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <link href="../assets/css/services.css" rel="stylesheet">

    <style>
        /* Reset básico */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
        }

        /* Estilos para o container principal */
        .main {
            padding: 20px;
        }

        /* Breadcrumb */
        .breadcrumb {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: #0056b3;
        }

        .breadcrumb-item.active {
            color: #495057;
        }

        /* Título da página */
        .pagetitle h1 {
            font-size: 28px;
            font-weight: bold;
            color: #222;
            margin-bottom: 10px;
            text-transform: capitalize;
        }

        /* Estilos para o título da seção */
        .section-title h2 {
            font-size: 24px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 10px;
        }

        .section-title p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        /* Formulário de contato */
        form {
            width: 100%;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        form:hover {
            transform: translateY(-5px);
        }

        /* Labels e campos de entrada */
        form label {
            font-size: 18px;
            /* Aumentado */
            color: #333;
            font-weight: 500;
            display: block;
            margin: 20px 0 5px;
        }

        form input[type="text"],
        form input[type="email"],
        form textarea {
            width: 100%;
            padding: 15px;
            /* Aumentado */
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 18px;
            /* Aumentado */
            color: #333;
            background-color: #f8f9fa;
            transition: border-color 0.3s, background-color 0.3s;
        }

        form input[type="text"]:hover,
        form input[type="email"]:hover,
        form textarea:hover {
            border-color: #007bff;
            background-color: #fff;
        }

        form input[readonly] {
            background-color: #e9ecef;
        }

        /* Campo de descrição */
        form textarea {
            height: 120px;
            /* Aumentado */
            resize: vertical;
        }

        /* Botão Enviar */
        form button[type="submit"] {
            width: 100%;
            padding: 18px;
            /* Aumentado */
            font-size: 18px;
            /* Aumentado */
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s ease;
            margin-top: 20px;
        }

        form button[type="submit"]:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Responsividade para tablets e telas médias */
        @media (max-width: 768px) {
            .pagetitle h1 {
                font-size: 24px;
            }

            .section-title h2 {
                font-size: 20px;
            }

            form {
                padding: 25px;
            }

            form button[type="submit"] {
                font-size: 16px;
            }
        }

        /* Responsividade para telas pequenas (425px e menores) */
        @media (max-width: 425px) {
            .pagetitle h1 {
                font-size: 22px;
            }

            .section-title h2 {
                font-size: 18px;
            }

            .breadcrumb {
                font-size: 12px;
            }

            form {
                padding: 15px;
            }

            form label {
                font-size: 16px;
                /* Aumentado */
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 16px;
                /* Aumentado */
                padding: 12px;
                /* Aumentado */
            }

            form button[type="submit"] {
                font-size: 16px;
                /* Aumentado */
                padding: 14px;
                /* Aumentado */
            }
        }

        /* Responsividade para telas muito pequenas (375px) */
        @media (max-width: 375px) {
            .pagetitle h1 {
                font-size: 20px;
            }

            .section-title h2 {
                font-size: 16px;
            }

            form label {
                font-size: 15px;
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 15px;
                padding: 10px;
                /* Aumentado */
            }

            form button[type="submit"] {
                font-size: 15px;
                padding: 12px;
                /* Aumentado */
            }
        }

        /* Responsividade para telas muito pequenas (320px) */
        @media (max-width: 320px) {
            .pagetitle h1 {
                font-size: 18px;
            }

            .section-title h2 {
                font-size: 14px;
            }

            .breadcrumb {
                font-size: 11px;
            }

            form label {
                font-size: 14px;
                /* Aumentado */
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 14px;
                /* Aumentado */
                padding: 8px;
            }

            form button[type="submit"] {
                font-size: 14px;
                /* Aumentado */
                padding: 10px;
                /* Aumentado */
            }
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <!-- Cabeçalho com ID "header", classe para fixar no topo e aplicar estilo flex para alinhamento dos itens. -->

        <div class="d-flex align-items-center justify-content-between">
            <!-- Div que alinha os itens de forma flexível e justifica o conteúdo entre os elementos. -->

            <a href="../index.php" class="logo d-flex align-items-center">
                <!-- Link que redireciona para a página "index.php" com a classe "logo", exibindo logo e texto. -->

                <img src="../assets/img/logo_preta.png" alt="" />
                <!-- Imagem do logo com o caminho "../assets/img/logo_preta.png". O atributo "alt" está vazio. -->

                <span class="d-none d-lg-block">Swan Shine</span>
                <!-- Texto "SwanShine" que só aparece em telas grandes, escondido em telas menores. -->
            </a>

            <i class="bi bi-list toggle-sidebar-btn"></i>
            <!-- Ícone do Bootstrap Icons para alternar o sidebar. -->
        </div>

        <nav class="header-nav ms-auto">
            <!-- Barra de navegação à direita (margem esquerda automática para empurrar conteúdo). -->

            <ul class="d-flex align-items-center">
                <!-- Lista não ordenada com itens alinhados ao centro, usando display flex. -->

                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown">
                    <!-- Item da lista que contém o dropdown de notificações. -->

                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <!-- Link com ícone de sino que abre o menu suspenso de notificações ao clicar. -->

                        <i class="bi bi-bell"></i>
                        <!-- Ícone de sino representando as notificações. -->

                        <span class="badge bg-primary badge-number">0</span>
                        <!-- Badge com o número de notificações (aqui definido como 0) com fundo azul. -->
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <!-- Menu suspenso alinhado à direita (end) com uma seta indicativa, contendo notificações. -->

                        <li class="dropdown-header">
                            <!-- Cabeçalho do dropdown que exibe a contagem de notificações. -->

                            Você tem 0 notificações
                            <!-- Texto que informa o número de notificações. -->

                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                            <!-- Link para ver todas as notificações com uma badge arredondada ao lado do texto. -->
                        </li>

                        <li class="dropdown-footer">
                            <!-- Rodapé do dropdown, oferecendo a opção de mostrar todas as notificações. -->

                            <a href="#">Mostrar todas as notificações</a>
                            <!-- Link para mostrar todas as notificações. -->
                        </li>
                    </ul>
                </li>

                <!-- Messages Dropdown -->
                <li class="nav-item dropdown">
                    <!-- Item da lista que contém o dropdown de mensagens. -->

                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <!-- Link com ícone de chat que abre o menu suspenso de mensagens ao clicar. -->

                        <i class="bi bi-chat-left-text"></i>
                        <!-- Ícone de chat para representar mensagens. -->

                        <span class="badge bg-success badge-number">0</span>
                        <!-- Badge com o número de mensagens (aqui definido como 0) com fundo verde. -->
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <!-- Menu suspenso alinhado à direita com uma seta indicativa, contendo mensagens. -->

                        <li class="dropdown-header">
                            <!-- Cabeçalho do dropdown que exibe a contagem de mensagens. -->

                            Você tem 0 mensagens
                            <!-- Texto informando o número de mensagens. -->

                            <a href="../mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                            <!-- Link para ver todas as mensagens com uma badge arredondada ao lado do texto. -->
                        </li>

                        <li>
                            <hr class="dropdown-divider" />
                            <!-- Linha divisória dentro do dropdown. -->
                        </li>

                        <li class="dropdown-footer">
                            <!-- Rodapé do dropdown, oferecendo a opção de mostrar todas as mensagens. -->

                            <a href="mensagem.html">Mostrar todas as mensagens</a>
                            <!-- Link para mostrar todas as mensagens. -->
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown pe-3">
                    <!-- Item da lista que contém o dropdown de perfil. O "pe-3" aplica padding à direita. -->

                    <a
                        class="nav-link nav-profile d-flex align-items-center pe-0"
                        href="perfil.php"
                        data-bs-toggle="dropdown">
                        <!-- Link com imagem de perfil que abre o menu suspenso do perfil ao clicar. -->

                        <img
                            src="../assets/img/usuario.png"
                            alt="Profile"
                            class="rounded-circle" />
                        <!-- Imagem de perfil (usuário) em formato circular. -->
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <!-- Menu suspenso alinhado à direita com uma seta indicativa, contendo opções de perfil. -->

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="../perfil.php">
                                <!-- Link para a página de perfil com ícone e texto alinhados. -->

                                <i class="bi bi-person"></i>
                                <!-- Ícone de pessoa (perfil). -->

                                <span>Meu Perfil</span>
                                <!-- Texto "Meu Perfil". -->
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider" />
                            <!-- Linha divisória dentro do dropdown. -->
                        </li>

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="../perfil.php">
                                <!-- Link para configurações da conta com ícone e texto alinhados. -->

                                <i class="bi bi-gear"></i>
                                <!-- Ícone de engrenagem (configurações). -->

                                <span>Configurações da Conta</span>
                                <!-- Texto "Configurações da Conta". -->
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider" />
                            <!-- Linha divisória dentro do dropdown. -->
                        </li>

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="../suporte.php">
                                <!-- Link para a página de ajuda com ícone e texto alinhados. -->

                                <i class="bi bi-question-circle"></i>
                                <!-- Ícone de círculo com ponto de interrogação (ajuda). -->

                                <span>Precisa de Ajuda?</span>
                                <!-- Texto "Precisa de Ajuda?". -->
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider" />
                            <!-- Linha divisória dentro do dropdown. -->
                        </li>

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="log_out.php">
                                <!-- Link para a página de logout com ícone e texto alinhados. -->

                                <i class="bi bi-box-arrow-right"></i>
                                <!-- Ícone de seta saindo de uma caixa (sair/logout). -->

                                <span>Sair</span>
                                <!-- Texto "Sair". -->
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>


    <!-- ======= Barra Lateral ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="../index.php">
                    <i class="bi bi-grid"></i>
                    <span>Início</span>
                </a>
            </li>

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#components-nav"
                    data-bs-toggle="collapse"
                    href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Serviços</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="../servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#components-nav"
                    data-bs-toggle="collapse"
                    href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="../pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
                    </li>
                    <li>
                        <a href="../pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
                    </li>
                    <li>
                        <a href="../pedidos/pedido_excluido.php"><i class="bi bi-circle"></i><span>Pedidos Excluidos</span></a>
                    </li>
                    <li>
                        <a href="../pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
                    </li>
                </ul>
            </li>

            <!-- Perfil -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="../perfil.php">
                    <i class="bi bi-person"></i>
                    <span>Perfil</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="../suporte.php">
                    <i class="bi bi-chat-dots"></i>
                    <span>Suporte</span>
                </a>
            </li>

        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Converse com o Cliente</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Painel</li>
                    <li class="breadcrumb-item active">Convesa</li>
                </ol>
            </nav>
        </div>
        <!-- Services Section -->
        <section id="services" class="services section">
            <form action="" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($profissional['nome']); ?>"><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profissional['email']); ?>"><br>

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($profissional['celular']); ?>"><br>

                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars("$rua, $numero, $complemento, $bairro, $cidade, $estado"); ?>"><br>

                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($profissional['cep']); ?>"><br>

                <label for="descricao">Descrição do serviço:</label>
                <textarea id="descricao" name="descricao" placeholder="Escreva o que deseja..."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea><br>

                <button type="submit" name="enviar_whatsapp">Enviar para WhatsApp</button>
            </form>

            <?php
            if (isset($_POST['enviar_whatsapp'])) {
                // Verifica se o cliente tem um telefone preenchido
                if (!empty($cliente['telefone'])) {
                    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : 'Nenhuma descrição fornecida';

                    // Formatação em tópicos para o WhatsApp
                    $mensagem = "Olá, meu nome é " . $profissional['nome'] . ".\n";
                    $mensagem .= "Estou interessado no serviço e gostaria de saber mais.\n\n";
                    $mensagem .= "*Dados do Profissional:*\n";
                    $mensagem .= "• Nome: " . $profissional['nome'] . "\n";
                    $mensagem .= "• Email: " . $profissional['email'] . "\n";
                    $mensagem .= "• Telefone: " . $profissional['celular'] . "\n";
                    $mensagem .= "• Endereço: $rua, $numero, $complemento, $bairro, $cidade, $estado\n";
                    $mensagem .= "• CEP: " . $profissional['cep'] . "\n";
                    $mensagem .= "\n*Descrição do Serviço:*\n" . $descricao;

                    // Remove caracteres não numéricos do número de celular do cliente
                    $numero_celular = preg_replace('/\D/', '', $cliente['telefone']);

                    // Gera o link do WhatsApp com o número formatado corretamente
                    $whatsapp_link = "https://wa.me/" . $numero_celular . "?text=" . urlencode($mensagem);
                    echo "<script>window.open('$whatsapp_link', '_blank');</script>";
                } else {
                    // Caso o cliente não tenha telefone, exibe o popup
                    echo "
        <div id='popup' class='popup'>
            <div class='popup-content'>
                <p>O cliente não disponibilizou um número de celular.</p>
                <button onclick='fecharPopup()'>Fechar</button>
            </div>
        </div>
        <script>
            // Exibe o popup ao carregar a página
            document.getElementById('popup').style.display = 'block';

            // Função para fechar o popup
            function fecharPopup() {
                document.getElementById('popup').style.display = 'none';
            }
        </script>
        <style>
            .popup {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }
            .popup-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .popup-content p {
                margin: 0 0 20px;
            }
            .popup-content button {
                background-color: #007bff;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .popup-content button:hover {
                background-color: #0056b3;
            }
        </style>
        ";
                }
            }
            ?>






        </section><!-- /Services Section -->



    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Swan Shine</span></strong>. Todos os Direitos Reservados
        </div>
    </footer><!-- Fim do Rodapé -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>

</body>

</html>