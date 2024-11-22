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

// Buscar o ID do profissional logado e os serviços oferecidos
$stmt = $conn->prepare("SELECT id, servicos FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se o profissional foi encontrado e possui serviços
if ($result->num_rows > 0) {
    $profissional = $result->fetch_assoc();
    $profissional_id = $profissional['id'];
    $servicos_oferecidos = explode(', ', $profissional['servicos']); // Converter os serviços oferecidos em array
} else {
    echo "Nenhum serviço encontrado para este profissional.";
    exit();
}

// Fechar a consulta de serviços do profissional
$stmt->close();

// Consultar os pedidos recusados pelo profissional logado
$placeholders = implode(', ', array_fill(0, count($servicos_oferecidos), '?')); // Gerar placeholders
$types = str_repeat('s', count($servicos_oferecidos)) . 'i'; // Tipo para bind_param (strings e inteiro)

// Preparar a consulta
$query = "
    SELECT p.*
    FROM pedidos p
    JOIN recusas_profissionais rp ON p.id = rp.pedido_id
    WHERE p.servicos IN ($placeholders)
    AND rp.profissional_id = ?
    ORDER BY p.data_pedido DESC
";
$stmt = $conn->prepare($query);

// Criar um array com os serviços oferecidos e adicionar o ID do profissional no final
$params = array_merge($servicos_oferecidos, [$profissional_id]);

// Vincular os parâmetros usando call_user_func_array
$bind_names[] = &$types;
foreach ($params as $key => $value) {
    $bind_names[] = &$params[$key];
}

call_user_func_array([$stmt, 'bind_param'], $bind_names);

// Executar a consulta
$stmt->execute();

// Obter os pedidos recusados
$result = $stmt->get_result();
$pedidos_recusados = $result->fetch_all(MYSQLI_ASSOC); // Obter todos os pedidos recusados como array associativo

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

// Fechar a conexão
$stmt->close();
$conn->close();


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <!-- Define a codificação de caracteres para UTF-8 -->
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <!-- Configura a viewport para garantir que a página seja renderizada corretamente em diferentes dispositivos -->

    <title>Swan Shine - Profissional</title>
    <!-- Define o título da página -->
    <meta content="" name="description" />
    <!-- Meta tag para a descrição da página (vazia neste exemplo) -->
    <meta content="" name="keywords" />
    <!-- Meta tag para palavras-chave da página (vazia neste exemplo) -->

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon" />
    <!-- Link para o favicon da página -->
    <link href="../assets/img/favicon.png" rel="apple-touch-icon" />
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <!-- Preconecta ao domínio das fontes do Google -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />
    <!-- Importa as fontes do Google (Open Sans, Nunito e Poppins) com diferentes estilos e pesos -->

    <!-- Arquivos CSS de Fornecedores -->
    <link
        href="../assets/vendor/bootstrap/css/bootstrap.min.css"
        rel="stylesheet" />
    <!-- CSS do Bootstrap -->
    <link
        href="../assets/vendor/bootstrap-icons/bootstrap-icons.css"
        rel="stylesheet" />
    <!-- Ícones do Bootstrap -->
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <!-- CSS dos Boxicons -->
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Snow -->
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Bubble -->
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <!-- CSS do Remixicon -->
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet" />
    <!-- CSS do Simple Datatables -->

    <!-- Arquivo CSS -->
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="../assets/css/main.css" rel="stylesheet" />
    <!-- Link para o arquivo CSS -->

    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .section.dashboard {
            padding: 20px;
        }

        .row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Cartões */
        .card-container {
            max-width: 1200px;
            width: 100%;
            padding: 10px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            flex: 1 1 calc(30% - 20px);
            position: relative;
        }

        .status {
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .servico-destaque {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-content p {
            margin: 5px 0;
        }

        .buttons {
            display: flex;
            gap: 10px;
        }

        .button {
            margin-top: 10px;
            margin-bottom: 80px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button.orcamento {
            background-color: #4caf50;
            color: white;
        }

        .button.orcamento:hover {
            background-color: #45a049;
        }

        .button.recusar {
            background-color: #f44336;
            color: white;
        }

        .button.recusar:hover {
            background-color: #e53935;
        }

        /* Estilos para telas pequenas (320px) */
        @media (max-width: 320px) {
            .card {
                padding: 8px;
                flex: 1 1 calc(100% - 20px);
                /* 1 cartão por linha */
            }

            .titulo {
                font-size: 1.2em;
                margin-bottom: 10px;
            }

            .servico-destaque {
                font-size: 1em;
            }

            .card-content p {
                font-size: 0.85em;
                margin: 3px 0;
            }

            .button {
                padding: 6px 12px;
                font-size: 0.85em;
            }

            .status {
                font-size: 0.85em;
            }

            .buttons {
                gap: 5px;
            }

            .close-card {
                font-size: 0.8em;
                top: 8px;
                right: 8px;
            }
        }

        /* Estilos para 375px */
        @media (max-width: 375px) {
            .card {
                padding: 10px;
                flex: 1 1 calc(100% - 20px);
                /* 1 cartão por linha */
            }

            .titulo {
                font-size: 1.3em;
            }

            .servico-destaque {
                font-size: 1.1em;
            }

            .card-content p {
                font-size: 0.9em;
                margin: 4px 0;
            }

            .button {
                padding: 8px 16px;
                font-size: 0.9em;
            }

            .status {
                font-size: 0.9em;
            }

            .buttons {
                gap: 8px;
            }

            .close-card {
                font-size: 0.9em;
            }
        }

        /* Estilos para 425px */
        @media (max-width: 425px) {
            .card {
                padding: 12px;
                flex: 1 1 calc(50% - 20px);
                /* 2 cartões por linha */
            }

            .titulo {
                font-size: 1.4em;
            }

            .servico-destaque {
                font-size: 1.1em;
            }

            .card-content p {
                font-size: 1em;
                margin: 5px 0;
            }

            .button {
                padding: 10px 18px;
                font-size: 1em;
            }

            .status {
                font-size: 1em;
            }

            .buttons {
                gap: 10px;
            }

            .close-card {
                font-size: 1em;
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
    <div id="root"></div>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="../index.php" class="logo d-flex align-items-center">
                <img src="../assets/img/logo_preta.png" alt="" />
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
                            <a href="../forms/marcar_notificacoes_como_lidas.php"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
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
                            <a href="../forms/marcar_notificacoes_como_lidas.php">Mostrar todas as notificações</a>
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
                            <a href="../mensagem.php"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li class="dropdown-footer">
                            <a href="../mensagem.php">Mostrar todas as mensagens</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown pe-3">
                    <a
                        class="nav-link nav-profile d-flex align-items-center pe-0"
                        href="../perfil.php"
                        data-bs-toggle="dropdown">
                        <img
                            src="../assets/img/usuario.png"
                            alt="Profile"
                            class="rounded-circle" />
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="../perfil.php">
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
                                href="../perfil.php">
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
                                href="../suporte.php">
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
                                href="../forms/log_out.php">
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
                    <i class="bi bi-menu-button-wide"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
                    </li>
                    <li>
                        <a href="pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
                    </li>
                    <li>
                        <a href="pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
                    </li>
                    <li>
                        <a href="pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
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
            <h1>Pedidos Recusado</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Pedido</li>
                    <li class="breadcrumb-item active">Pedidos Recusado</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="card-container">

                    <?php if (isset($_GET['message']) && $_GET['message'] === 'Pedido recusado com sucesso'): ?>
                        <div class="card notification-card">
                            <h3>Pedido Recusado</h3>
                            <p>O pedido foi recusado com sucesso.</p>
                            <a href="../index.php" class="back-link">&#8592; Voltar para os serviços</a>
                        </div>
                    <?php elseif (!empty($pedidos_recusados)): ?> <!-- Usando $pedidos_recusados -->
                        <?php foreach ($pedidos_recusados as $pedido): ?> <!-- Usando $pedidos_recusados -->
                            <div class="card">
                                <div class="status"><?= htmlspecialchars($pedido['status']) ?></div> <!-- Exibe o status -->
                                <div class="servico-destaque"><?= htmlspecialchars($pedido['servicos']) ?></div>
                                <div class="card-content">
                                    <p><strong>Tipo:</strong> <span><?= htmlspecialchars($pedido['tipo']) ?></span></p>
                                    <p><strong>Estilo:</strong> <span><?= htmlspecialchars($pedido['estilo']) ?></span></p>
                                    <p><strong>Atendimento:</strong> <span><?= htmlspecialchars($pedido['atendimento']) ?></span></p>
                                    <p><strong>Urgência:</strong> <span><?= htmlspecialchars($pedido['urgencia']) ?></span></p>
                                    <p><strong>Detalhes:</strong> <span><?= htmlspecialchars($pedido['detalhes']) ?></span></p>
                                    <p><strong>CEP:</strong> <span><?= htmlspecialchars($pedido['cep']) ?></span></p>
                                    <p><strong>Nome:</strong> <span><?= htmlspecialchars($pedido['nome']) ?></span></p>
                                    <p><strong>E-mail:</strong> <span><?= htmlspecialchars($pedido['email']) ?></span></p>
                                    <p><strong>Telefone:</strong> <span><?= htmlspecialchars($pedido['telefone']) ?></span></p>
                                </div>
                                <div class="buttons">
                                    <button class="button orcamento" data-id="<?= htmlspecialchars($pedido['id']) ?>">Enviar Mensagem</button>
                                    <!-- Removendo o botão "Recusar" pois o pedido já foi recusado -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card no-pedidos">
                            <h3>Nenhum pedido Recusado</h3> <!-- Mensagem atualizada -->
                            <p>Atualmente, não há nenhum pedido Recusado para os serviços oferecidos.</p>
                            <span class="close-card" onclick="this.parentElement.style.display='none'">X</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- JavaScript para lidar com os botões de orçamento -->
        <script>
            document.querySelectorAll('.orcamento').forEach(button => {
                button.addEventListener('click', function() {
                    // Captura o ID do pedido armazenado no atributo 'data-id' do botão
                    const id = this.getAttribute('data-id');

                    // Redireciona para a página 'contato_cliente.php', passando o ID como parâmetro na URL
                    window.location.href = `../forms/contato_cliente.php?id=${id}`;
                });
            });
        </script>




    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Swan Shine</span></strong>. All Rights Reserved
        </div>
    </footer>

    <a
        href="#"
        class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/main1.js"></script>
</body>

</html>