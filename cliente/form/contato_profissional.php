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

$email_cliente = $_SESSION['user_email'];
$id_profissional = $_GET['id'];

// Buscar dados do cliente
$sql_cliente = "SELECT nome, email, telefone, endereco, cep FROM clientes WHERE email = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("s", $email_cliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Buscar dados do profissional
$sql_profissional = "SELECT nome, celular, whatsapp FROM profissionais WHERE id = ?";
$stmt = $conn->prepare($sql_profissional);
$stmt->bind_param("i", $id_profissional);
$stmt->execute();
$profissional = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Swan Shine - Cliente</title>
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
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
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
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Labels e campos de entrada */
        form label {
            font-size: 16px;
            color: #333;
            font-weight: 500;
            display: block;
            margin: 15px 0 5px;
        }

        form input[type="text"],
        form input[type="email"],
        form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s;
        }

        form input[type="text"]:hover,
        form input[type="email"]:hover,
        form textarea:hover {
            border-color: #007bff;
        }

        form input[readonly] {
            background-color: #e9ecef;
        }

        /* Campo de descrição */
        form textarea {
            height: 100px;
            resize: vertical;
        }

        /* Botão Enviar */
        form button[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 15px;
        }

        form button[type="submit"]:hover {
            background-color: #0056b3;
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
                padding: 15px;
            }

            form button[type="submit"] {
                font-size: 14px;
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
                padding: 10px;
            }

            form label {
                font-size: 14px;
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 14px;
                padding: 10px;
            }

            form button[type="submit"] {
                font-size: 14px;
                padding: 10px;
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
                font-size: 13px;
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 13px;
                padding: 8px;
            }

            form button[type="submit"] {
                font-size: 13px;
                padding: 8px;
            }
        }

        /* Responsividade para telas muito pequenas (320px) */
        @media (max-width: 320px) {
            .pagetitle h1 {
                font-size: 18px;
            }

            .section-title h2 {
                font-size: 15px;
            }

            .breadcrumb {
                font-size: 11px;
            }

            form label {
                font-size: 12px;
            }

            form input[type="text"],
            form input[type="email"],
            form textarea {
                font-size: 12px;
                padding: 6px;
            }

            form button[type="submit"] {
                font-size: 12px;
                padding: 6px;
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
            <h1>Converse com o Profissional</h1>
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

            <h2>Entre em contato com, <?php echo htmlspecialchars($profissional['nome']); ?></h2>
            <form action="" method="post">
                <label>Nome:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['nome']); ?>" readonly><br>

                <label>Email:</label>
                <input type="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" readonly><br>

                <label>Telefone:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" readonly><br>

                <label>Endereço:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['endereco']); ?>" readonly><br>

                <label>CEP:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['cep']); ?>" readonly><br>

                <label>Descrição do serviço:</label>
                <textarea name="descricao" placeholder="Escreva o que deseja..."></textarea><br>

                <button type="submit" name="enviar_whatsapp">Enviar para WhatsApp</button>
            </form>

            <?php
            if (isset($_POST['enviar_whatsapp'])) {
                if (!empty($profissional['celular'])) {
                    $descricao = $_POST['descricao'];

                    // Formatação em tópicos para o WhatsApp
                    $mensagem = "Olá, meu nome é " . $cliente['nome'] . ".\n";
                    $mensagem .= "Estou interessado no serviço e gostaria de saber mais.\n\n";
                    $mensagem .= "*Dados do Cliente:*\n";
                    $mensagem .= "• Nome: " . $cliente['nome'] . "\n";
                    $mensagem .= "• Email: " . $cliente['email'] . "\n";
                    $mensagem .= "• Telefone: " . $cliente['telefone'] . "\n";
                    $mensagem .= "• Endereço: " . $cliente['endereco'] . "\n";
                    $mensagem .= "• CEP: " . $cliente['cep'] . "\n";
                    $mensagem .= "\n*Descrição do Serviço:*\n" . $descricao;

                    // Remove caracteres não numéricos do número de celular
                    $numero_celular = preg_replace('/\D/', '', $profissional['celular']);

                    // Gera o link do WhatsApp com o número formatado corretamente
                    $whatsapp_link = "https://wa.me/" . $numero_celular . "?text=" . urlencode($mensagem);
                    echo "<script>window.open('$whatsapp_link', '_blank');</script>";
                } else {
                    echo "<p>O profissional não disponibilizou um número de celular.</p>";
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