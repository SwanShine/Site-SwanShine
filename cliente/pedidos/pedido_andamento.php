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

// Buscar os pedidos feitos pelo cliente
$stmt = $conn->prepare("SELECT servicos FROM pedidos WHERE email = ? AND status = 'em análise' ORDER BY data_pedido DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se o cliente tem pedidos
if ($result->num_rows > 0) {
    $servicos_solicitados = [];
    while ($row = $result->fetch_assoc()) {
        $servicos = explode(', ', $row['servicos']); // Serviços solicitados convertidos em array
        $servicos_solicitados = array_merge($servicos_solicitados, $servicos); // Adicionar aos serviços solicitados
    }
} else {
    echo "Nenhum serviço encontrado para este cliente.";
    exit();
}

// Fechar a consulta de pedidos do cliente
$stmt->close();

// Filtrar serviços duplicados
$servicos_solicitados = array_unique($servicos_solicitados);

// Mostrar os serviços solicitados
echo "<h3>Serviços Solicitados</h3>";
if (!empty($servicos_solicitados)) {
    echo "<ul>";
    foreach ($servicos_solicitados as $servico) {
        echo "<li>" . htmlspecialchars($servico) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum serviço encontrado.";
}

// Fechar a conexão
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
                        <span class="badge bg-primary badge-number">0</span>
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            Você tem 0 notificações
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                        </li>
                        <li class="dropdown-footer">
                            <a href="#">Mostrar todas as notificações</a>
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
                            <a href="../mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li class="dropdown-footer">
                            <a href="../mensagem.html">Mostrar todas as mensagens</a>
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
                                href="../manutencao.html">
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
                    <a href="pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
                </li>
                <li>
                    <a href="pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
                </li>
            </ul>
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
                    <a href="../servico/service.php"><i class="bi bi-circle"></i><span>Cadastre Seu Serviço</span></a>
                </li>
                <li>
                    <a href="../servico/service-cadastrado.php"><i class="bi bi-circle"></i><span>Serviços Cadastrados</span></a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="../mensagens.html">
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
            <a class="nav-link collapsed" href="../suporte.html">
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
            <h1 class="titulo" style="text-align: center;">Seus Pedidos em Andamento</h1> <!-- Título atualizado -->
            <?php if (isset($_GET['message']) && $_GET['message'] === 'Pedido recusado com sucesso'): ?>
                <div class="card notification-card">
                    <h3>Pedido Recusado</h3>
                    <p>O pedido foi recusado com sucesso.</p>
                    <a href="../index.php" class="back-link">&#8592; Voltar para os serviços</a>
                </div>
            <?php elseif (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="card">
                        <div class="status"><?= htmlspecialchars($pedido['status']) ?></div> <!-- Exibe o status -->
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
                            <button class="button orcamento" data-id="<?= htmlspecialchars($pedido['id']) ?>">Orçamento</button>
                            <button class="button recusar" data-id="<?= htmlspecialchars($pedido['id']) ?>">Recusar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card no-pedidos">
                    <h3>Nenhum pedido em andamento</h3> <!-- Mensagem atualizada -->
                    <p>Atualmente, não há nenhum pedido em andamento para os serviços oferecidos.</p>
                    <span class="close-card" onclick="this.parentElement.style.display='none'">X</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- JavaScript para lidar com os botões de orçamento e recusa -->
<script>
    document.querySelectorAll('.recusar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Tem certeza que deseja recusar este pedido?')) {
                window.location.href = `../forms/pedido/recusar_pedido.php?id=${id}`;
            }
        });
    });

    document.querySelectorAll('.orcamento').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            window.location.href = `../forms/pedido/orcamento/orcamento_pedido.php?id=${id}`;
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