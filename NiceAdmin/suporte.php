<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
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

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Buscar o nome e serviços oferecidos pelo profissional logado
$stmt = $conn->prepare("SELECT nome, servicos FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se encontrou o profissional
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Salvar o nome do profissional na sessão
    $_SESSION['nome'] = $row['nome'];
    $servicos = $row['servicos']; // Usar se necessário
}

// Obter notificações de mensagens não lidas para o profissional logado
$profissional_id = $_SESSION['profissional_id'];
$query_notificacoes = "SELECT conteudo, data_envio FROM mensagens WHERE profissional_id = ? AND lida = 0 ORDER BY data_envio DESC LIMIT 5";
$stmt_notificacoes = $conn->prepare($query_notificacoes);
$stmt_notificacoes->bind_param("i", $profissional_id);
$stmt_notificacoes->execute();
$result_notificacoes = $stmt_notificacoes->get_result();

// Armazenar todas as notificações não lidas em um array
$notificacoes = $result_notificacoes->fetch_all(MYSQLI_ASSOC);

// Contar o número de notificações não lidas para exibir no badge de notificações
$num_notificacoes = count($notificacoes);

// Fechar a consulta e conexão
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
    <link href="assets/img/favicon.png" rel="icon">
    <!-- Link para o favicon da página -->
    <link href="assets/img/favicon.png" rel="apple-touch-icon">
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/services.css" rel="stylesheet">

    <style>
        .container {
            padding: 20px;
            /* Espaçamento interno da container */
        }

        .service-category {
            font-size: 2.2rem;
            /* Tamanho do título da categoria */
            color: #333;
            /* Cor do texto */
        }

        .service-card {
            background-color: #fff;
            /* Fundo do card */
            border-radius: 10px;
            /* Bordas arredondadas */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Sombra do card */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            /* Transição suave */
        }

        .service-card:hover {
            transform: translateY(-5px);
            /* Levanta o card ao passar o mouse */
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            /* Sombra mais intensa ao hover */
        }

        .card-title {
            font-size: 1.5rem;
            /* Tamanho do título do card */
            color: #007bff;
            /* Cor do título */
            margin-bottom: 10px;
            /* Espaçamento abaixo do título */
        }

        .card-details p {
            color: #555;
            /* Cor do texto das descrições */
            margin-bottom: 5px;
            /* Espaçamento entre as descrições */
        }

        .btn.card-button {
            display: inline-block;
            /* Exibe o botão em linha */
            margin-top: 15px;
            /* Espaço acima do botão */
            padding: 12px 20px;
            /* Espaçamento interno do botão */
            font-size: 1.1rem;
            /* Tamanho da fonte do botão */
            background-color: #007bff;
            /* Cor de fundo do botão */
            color: #fff;
            /* Cor do texto do botão */
            border-radius: 5px;
            /* Bordas arredondadas */
            transition: background-color 0.3s ease;
            /* Transição suave para a cor de fundo */
        }

        .btn.card-button:hover {
            background-color: #0056b3;
            /* Cor de fundo ao passar o mouse */
        }

        @media (max-width: 768px) {
            .service-card {
                margin-bottom: 15px;
                /* Espaçamento entre os cards em telas pequenas */
            }
        }
    </style>
    <style>
        /* Estilos gerais */
        .nav-item .nav-link.nav-icon {
            position: relative;
            /* Faz com que o ícone de notificação seja posicionado em relação ao seu contêiner pai */
            padding: 0.5rem;
            /* Adiciona preenchimento ao redor do ícone de notificação */
            font-size: 20px;
            /* Define o tamanho da fonte para o ícone de notificação */
        }

        .nav-item .badge-number {
            position: absolute;
            /* Posiciona o número de notificações de forma absoluta dentro do contêiner */
            top: 0;
            /* Coloca o número no topo */
            right: 0;
            /* Coloca o número no canto direito */
            font-size: 12px;
            /* Define o tamanho da fonte do número */
            padding: 4px 6px;
            /* Adiciona um preenchimento ao redor do número */
            border-radius: 50%;
            /* Faz o número aparecer dentro de um círculo */
        }

        .dropdown-menu.notifications {
            width: 280px;
            /* Define a largura do menu suspenso de notificações */
            max-width: 1000%;
            /* Isso efetivamente desabilita qualquer limite de largura máxima */
            padding: 0;
            /* Remove qualquer preenchimento dentro do menu suspenso */
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
            /* Adiciona uma sombra suave para dar profundidade ao menu */
        }

        .dropdown-menu .dropdown-header {
            font-weight: bold;
            /* Deixa o texto no cabeçalho em negrito */
            font-size: 14px;
            /* Define o tamanho da fonte para o cabeçalho */
            padding: 10px;
            /* Adiciona um preenchimento dentro do cabeçalho */
            border-bottom: 1px solid #ddd;
            /* Adiciona uma linha de separação abaixo do cabeçalho */
            display: flex;
            /* Usa flexbox para o layout */
            justify-content: space-between;
            /* Espaça igualmente os itens no cabeçalho */
            align-items: center;
            /* Alinha os itens verticalmente ao centro */
        }

        .dropdown-menu .dropdown-footer {
            text-align: center;
            /* Centraliza o texto no rodapé */
            padding: 10px;
            /* Adiciona um preenchimento ao rodapé */
            font-size: 14px;
            /* Define o tamanho da fonte para o rodapé */
            color: #007bff;
            /* Define a cor do texto como azul */
        }

        .dropdown-menu .dropdown-footer a {
            color: inherit;
            /* Faz com que o link herde a cor do texto do rodapé */
            text-decoration: none;
            /* Remove o sublinhado do link */
        }

        /* Notificação individual */
        .dropdown-menu li a {
            display: flex;
            /* Exibe as notificações em um layout flexível */
            padding: 10px;
            /* Adiciona um preenchimento em torno de cada notificação */
            text-decoration: none;
            /* Remove o sublinhado do link da notificação */
            color: #333;
            /* Define a cor do texto como um cinza escuro */
            transition: background-color 0.3s;
            /* Adiciona uma transição suave ao mudar a cor de fundo */
        }

        .dropdown-menu li a:hover {
            background-color: #f8f9fa;
            /* Muda a cor de fundo ao passar o mouse sobre a notificação */
        }

        .dropdown-menu li a div {
            display: flex;
            /* Usa flexbox dentro do item da notificação */
            flex-direction: column;
            /* Organiza o conteúdo de forma vertical */
        }

        .dropdown-menu li a p,
        .dropdown-menu li a span {
            margin: 0;
            /* Remove a margem padrão dos elementos */
            font-size: 12px;
            /* Define o tamanho da fonte para 12px */
        }

        .dropdown-menu li a span {
            font-weight: 500;
            /* Deixa o texto em negrito */
        }

        /* Responsividade para telas menores */
        @media (max-width: 768px) {
            .nav-item .nav-link.nav-icon {
                font-size: 16px;
                /* Reduz o tamanho do ícone para telas menores */
            }

            .dropdown-menu.notifications {
                width: 240px;
                /* Reduz a largura do menu suspenso para telas menores */
            }
        }

        @media (max-width: 425px) {
            .nav-item .nav-link.nav-icon {
                font-size: 14px;
                /* Reduz ainda mais o tamanho do ícone para telas menores */
            }

            .dropdown-menu.notifications {
                width: 200px;
                /* Reduz a largura do menu suspenso para telas ainda menores */
            }

            .dropdown-menu .dropdown-header,
            .dropdown-menu .dropdown-footer {
                font-size: 12px;
                /* Diminui o tamanho da fonte no cabeçalho e rodapé */
            }

            .dropdown-menu li a p,
            .dropdown-menu li a span {
                font-size: 11px;
                /* Diminui o tamanho da fonte dos elementos de notificação */
            }
        }

        @media (max-width: 375px) {
            .nav-item .nav-link.nav-icon {
                font-size: 13px;
                /* Reduz o tamanho do ícone para telas muito pequenas */
            }

            .dropdown-menu.notifications {
                width: 180px;
                /* Reduz ainda mais a largura do menu suspenso para telas pequenas */
            }

            .badge-number {
                font-size: 10px;
                /* Reduz o tamanho da fonte do número de notificações */
            }
        }

        @media (max-width: 32px) {
            .nav-item .nav-link.nav-icon {
                font-size: 12px;
                /* Define o tamanho do ícone para telas muito pequenas */
            }

            .dropdown-menu.notifications {
                width: 100px;
                /* Reduz drasticamente a largura do menu suspenso para telas super pequenas */
            }

            .dropdown-menu .dropdown-header,
            .dropdown-menu .dropdown-footer {
                font-size: 10px;
                /* Reduz o tamanho da fonte no cabeçalho e rodapé */
            }

            .badge-number {
                font-size: 8px;
                /* Reduz o tamanho da fonte do número de notificações para telas muito pequenas */
            }
        }
    </style>

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="assets/img/logo_preta.png" alt="" />
                <span class="d-none d-lg-block">Swan Shine</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-primary badge-number"><?= $num_notificacoes ?></span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            Você tem <?= $num_notificacoes ?> notificações
                            <a href="forms/marcar_notificacoes_como_lidas.php"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                        </li>

                        <?php if ($num_notificacoes > 0): ?>
                            <?php foreach ($notificacoes as $notificacao): ?>
                                <li>
                                    <a href="#">
                                        <div>
                                            <p class="small text-muted mb-0"><?= $notificacao['data_envio'] ?></p>
                                            <span><?= $notificacao['conteudo'] ?></span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="dropdown-footer">
                                Nenhuma nova notificação
                            </li>
                        <?php endif; ?>

                        <li class="dropdown-footer">
                            <a href="forms/marcar_notificacoes_como_lidas.php">Mostrar todas as notificações</a>
                        </li>
                    </ul>
                </li>

                <!-- Messages Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="badge bg-success badge-number">0</span>
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <li class="dropdown-header">
                            Você tem 0 mensagens
                            <a href="mensagem.php"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li class="dropdown-footer">
                            <a href="mensagem.php">Mostrar todas as mensagens</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown pe-3">
                    <a
                        class="nav-link nav-profile d-flex align-items-center pe-0"
                        href="perfil.php"
                        data-bs-toggle="dropdown">
                        <img
                            src="assets/img/usuario.png"
                            alt="Profile"
                            class="rounded-circle" />
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="perfil.php">
                                <i class="bi bi-person"></i>
                                <span>Meu Perfil</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="perfil.php">
                                <i class="bi bi-gear"></i>
                                <span>Configurações da Conta</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="suporte.php">
                                <i class="bi bi-question-circle"></i>
                                <span>Precisa de Ajuda?</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="forms/log_out.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sair</span>
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
                <a class="nav-link collapsed" href="index.php">
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
                    <i class="bi bi-menu-button-wide"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
                    </li>
                    <li>
                        <a href="pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
                    </li>

                    <li>
                        <a href="pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
                    </li>

                    <li>
                        <a href="pedidos/pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
                    </li>
                </ul>
            </li>

            <!-- Perfil -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="perfil.php">
                    <i class="bi bi-person"></i>
                    <span>Perfil</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="suporte.php">
                    <i class="bi bi-chat-dots"></i>
                    <span>Suporte</span>
                </a>
            </li>

        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Seja Bem vindo! <?= htmlspecialchars($_SESSION['nome']) ?></h3>
            </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Painel</li>
                    <li class="breadcrumb-item active">Suporte</li>
                </ol>
            </nav>
        </div>

        <!-- Services Section -->
        <section id="services" class="services section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Suporte</h2>
                <p>Entre em contato e tire suas duvidas aqui no SAC.</p>
            </div><!-- End Section Title -->

            <div class="container">
                <div class="row gy-4">

                </div>
            </div>


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
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>