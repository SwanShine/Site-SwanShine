<?php
// Iniciar a sessão
session_start(); // Inicia uma sessão PHP para armazenar dados de usuário durante a navegação.

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) { // Verifica se o email do usuário está salvo na sessão.
    header('Location: ../../home/forms/login/login.html'); // Redireciona para a página de login.
    exit(); // Interrompe a execução do script para garantir que o redirecionamento ocorra.
}

// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com"; // Nome do servidor do banco de dados.
$username = "admin"; // Nome de usuário do banco de dados.
$password = "gLAHqWkvUoaxwBnm9wKD"; // Senha do banco de dados.
$dbname = "swanshine"; // Nome do banco de dados.

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname); // Cria uma nova conexão MySQL.

if ($conn->connect_error) { // Verifica se a conexão falhou.
    die("Conexão falhou: " . $conn->connect_error); // Exibe mensagem de erro.
}

// Recuperar o email da sessão
$email = $_SESSION['user_email']; // Obtém o email do usuário da sessão.

// Verificar se o cliente existe na tabela "clientes"
$stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ?");
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error); // Exibe erro se a consulta falhar.
}

$stmt->bind_param("s", $email); // Substitui "?" pelo email do cliente.
$stmt->execute(); // Executa a consulta.
$result = $stmt->get_result(); // Armazena o resultado.

if ($result->num_rows > 0) {
    $cliente = $result->fetch_assoc(); // Obtém os dados do cliente.
} else {
    echo "Cliente não encontrado."; // Mensagem se o cliente não for encontrado.
    exit(); // Encerra o script.
}

// Fechar a consulta do cliente
$stmt->close();

// Buscar os serviços pendentes feitos pelo cliente
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE email = ? AND status = 'em análise' ORDER BY data_pedido DESC");
if (!$stmt) {
    die("Erro na preparação da consulta de pedidos: " . $conn->error); // Exibe erro se a consulta falhar.
}

$stmt->bind_param("s", $email); // Substitui "?" pelo email do cliente.
$stmt->execute(); // Executa a consulta.
$result = $stmt->get_result(); // Armazena o resultado.

// Verificar se o cliente tem pedidos pendentes
$pedidos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row; // Armazena cada pedido no array
    }
} else {
    echo "<div>Nenhum serviço pendente encontrado para este cliente.</div>"; // Mensagem se não houver pedidos pendentes.
}

// Fechar a consulta de pedidos do cliente
$stmt->close();

// Fechar a conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">


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
        }

        .cabody {
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
            /* Permite que os cartões se movam para a linha seguinte */
        }

        .card-container {
            max-width: 1200px;
            width: 100%;
            padding: 10px;
            /* Espaçamento lateral */
        }

        .titulo {
            text-align: center;
            margin-bottom: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            /* Margem ao redor dos cartões */
            flex: 1 1 calc(30% - 20px);
            /* Cada cartão ocupará cerca de 30% da largura, com espaçamento */
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

        .button.excluir {
            background-color: #f44336;
            color: white;
        }

        .button.excluir:hover {
            background-color: #e53935;
        }

        .no-pedidos {
            text-align: center;
        }

        .close-card {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        /* Estilos responsivos */
        @media (max-width: 1200px) {
            .card-container {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .card {
                padding: 15px;
                flex: 1 1 calc(45% - 20px);
                /* Ajusta a largura dos cartões em telas médias */
            }

            .servico-destaque {
                font-size: 1.1em;
            }

            .buttons {
                flex-direction: column;
            }

            .button {
                width: 100%;
                /* Botões ocupam toda a largura */
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 10px;
                flex: 1 1 calc(50% - 20px);
                /* Ajusta a largura dos cartões em telas menores */
            }

            .titulo {
                font-size: 1.5em;
            }

            .servico-destaque {
                font-size: 1em;
            }

            .button {
                padding: 8px 16px;
            }

            .status {
                font-size: 0.9em;
            }
        }

        /* Estilos para 320px */
        @media (max-width: 320px) {
            .card {
                padding: 8px;
                flex: 1 1 calc(100% - 20px);
                /* Ajusta para 1 cartão por linha em telas muito pequenas */
            }

            .titulo {
                font-size: 1.2em;
                margin-bottom: 10px;
            }

            .servico-destaque {
                font-size: 0.9em;
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
    </style>

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <!-- Cabeçalho com ID "header", classe para fixar no topo e aplicar estilo flex para alinhamento dos itens. -->

        <div class="d-flex align-items-center justify-content-between">
            <!-- Div que alinha os itens de forma flexível e justifica o conteúdo entre os elementos. -->

            <a href="index.php" class="logo d-flex align-items-center">
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

                            <a href="mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                            <!-- Link para ver todas as mensagens com uma badge arredondada ao lado do texto. -->
                        </li>

                        <li>
                            <hr class="dropdown-divider" />
                            <!-- Linha divisória dentro do dropdown. -->
                        </li>

                        <li class="dropdown-footer">
                            <!-- Rodapé do dropdown, oferecendo a opção de mostrar todas as mensagens. -->

                            <a href="../mensagem.html">Mostrar todas as mensagens</a>
                            <!-- Link para mostrar todas as mensagens. -->
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown pe-3">
                    <!-- Item da lista que contém o dropdown de perfil. O "pe-3" aplica padding à direita. -->

                    <a
                        class="nav-link nav-profile d-flex align-items-center pe-0"
                        href="../perfil.php"
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
                                href="../form/log_out.php">
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
            <a href="pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
          </li>
          <li>
            <a href="pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
          </li>
          <li>
            <a href="pedido_excluido.php"><i class="bi bi-circle"></i><span>Pedidos Excluidos</span></a>
          </li>
          <li>
            <a href="pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="../mensagem.php">
          <i class="bi bi-envelope"></i>
          <span>Mensagens</span>
        </a>
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
            <h1>Pedidos em Andamento</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Pedido</li>
                    <li class="breadcrumb-item active">Pedidos em Andamento</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="card-container">
                    <?php if (isset($_GET['message']) && $_GET['message'] === 'Pedido Excluido com sucesso'): ?>
                        <div class="card notification-card">
                            <h3>Pedido Excluido</h3>
                            <p>O pedido foi excluido com sucesso.</p>
                            <a href="../index.php" class="back-link">&#8592; Voltar para os serviços</a>
                        </div>
                    <?php elseif (!empty($pedidos)): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="card pedido-card">
                                <div class="status <?= htmlspecialchars($pedido['status']) ?>"><?= htmlspecialchars($pedido['status']) ?></div>
                                <div class="servico-destaque"><?= htmlspecialchars($pedido['servicos']) ?></div>
                                <div class="card-content">
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
                                    <button class="button orcamento" data-id="<?= htmlspecialchars($pedido['id']) ?>">Visualizar Orçamento</button>
                                    <button class="button excluir" data-id="<?= htmlspecialchars($pedido['id']) ?>">Excluir Pedido</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card no-pedidos">
                            <h3>Nenhum serviço solicitado</h3>
                            <p>Atualmente, não há nenhum pedido disponível para os serviços oferecidos.</p>
                            <span class="close-card" onclick="this.parentElement.style.display='none'">X</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- JavaScript para lidar com os botões de orçamento e recusa -->
        <script>
            document.querySelectorAll('.excluir').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if (confirm('Tem certeza que deseja excluir este pedido?')) {
                        window.location.href = `../form/pedido/excluir_pedido.php?id=${id}`;
                    }
                });
            });

            document.querySelectorAll('.orcamento').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    window.location.href = `../form/pedido/orcamento/orcamento_pedido.php?id=${id}`;
                });
            });
        </script>



    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Swan Shine</span></strong>. Todos os Direitos Reservados
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