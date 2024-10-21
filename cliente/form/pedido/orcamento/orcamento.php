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

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Verificar se um ID de pedido foi passado
if (isset($_GET['id'])) {
    $pedido_id = $_GET['id']; // Usar 'id' como parâmetro

    // Buscar o orçamento do pedido específico
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $pedido_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
    } else {
        echo "Pedido não encontrado ou não pertence a este cliente.";
        exit();
    }

    $stmt->close();
} else {
    echo "Nenhum pedido selecionado.";
    exit();
}

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
    <link href="../../../assets/img/favicon.png" rel="icon" />
    <!-- Link para o favicon da página -->
    <link href="../../../assets/img/favicon.png" rel="apple-touch-icon" />
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <!-- Preconecta ao domínio das fontes do Google -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />
    <!-- Importa as fontes do Google (Open Sans, Nunito e Poppins) com diferentes estilos e pesos -->

    <!-- Arquivos CSS de Fornecedores -->
    <link href="../../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <!-- CSS do Bootstrap -->
    <link href="../../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <!-- Ícones do Bootstrap -->
    <link href="../../../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <!-- CSS dos Boxicons -->
    <link href="../../../assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Snow -->
    <link href="../../../assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Bubble -->
    <link href="../../../assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <!-- CSS do Remixicon -->
    <link href="../../../assets/vendor/simple-datatables/style.css" rel="stylesheet" />
    <!-- CSS do Simple Datatables -->

    <!-- Arquivo CSS -->
    <link href="../../../assets/css/style.css" rel="stylesheet" />
    <link href="../../../assets/css/main.css" rel="stylesheet" />
    <!-- Link para o arquivo CSS -->

    <style>
/* Estilo do contêiner dos cartões */
.card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px; /* Bordas arredondadas */
    padding: 1.5rem;
    margin: 1rem 0; /* Margem vertical entre os cartões */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Sombra suave */
    transition: transform 0.2s ease; /* Efeito ao passar o mouse */
}

/* Efeito ao passar o mouse sobre o cartão */
.card:hover {
    transform: translateY(-4px); /* Leve elevação */
}

/* Estilo do título do serviço */
.card h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 0.5rem;
}

/* Estilo do ID do pedido */
.card h3 span {
    font-size: 0.75em; /* Tamanho menor para o ID */
    color: #666; /* Cor mais clara */
}

/* Estilo dos parágrafos dentro do cartão */
.card p {
    margin: 0.5rem 0; /* Margem vertical entre os parágrafos */
    color: #000; /* Cor de texto padrão */
}

/* Estilo do grupo de botões */
.button-group {
    display: flex;
    justify-content: space-between; /* Distribui espaço entre os botões */
    margin-top: 1rem; /* Espaço acima dos botões */
}

/* Estilo dos botões */
.button-group button {
    padding: 0.5rem 0.8rem; /* Diminuindo o padding */
    border: none;
    border-radius: 5px; /* Bordas arredondadas */
    color: #fff;
    font-size: 0.9rem; /* Diminuindo o tamanho da fonte */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease; /* Transições suaves */
}

/* Estilo do botão Aceitar */
.button-group .accept {
    background-color: #28a745; /* Verde */
}

.button-group .accept:hover {
    background-color: #218838; /* Verde escuro */
    transform: scale(1.05); /* Leve aumento ao passar o mouse */
}

/* Estilo do botão Recusar */
.button-group .reject {
    background-color: #dc3545; /* Vermelho */
}

.button-group .reject:hover {
    background-color: #c82333; /* Vermelho escuro */
    transform: scale(1.05); /* Leve aumento ao passar o mouse */
}

/* Estilo da mensagem quando não há pedidos */
.no-orders {
    text-align: center; /* Centraliza o texto */
    color: #999; /* Cor mais clara para mensagem */
    font-size: 1.2rem; /* Tamanho de fonte maior */
    margin: 2rem 0; /* Margem vertical */
}

    </style>

</head>

<body>
    <div id="root"></div>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="index.html" class="logo d-flex align-items-center">
                <img src="../../../assets/img/logo_preta.png" alt="" />
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

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
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

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <li class="dropdown-header">
                            Você tem 0 mensagens
                            <a href="../../../mensagens.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver
                                    todas</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li class="dropdown-footer">
                            <a href="../../../mensagens.html">Mostrar todas as mensagens</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="perfil.php"
                        data-bs-toggle="dropdown">
                        <img src="../../../assets/img/usuario.png" alt="Profile" class="rounded-circle" />
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../../../perfil.php">
                                <i class="bi bi-person"></i>
                                <span>Meu Perfil</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../../../perfil.php">
                                <i class="bi bi-gear"></i>
                                <span>Configurações da Conta</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../../../suporte.php">
                                <i class="bi bi-question-circle"></i>
                                <span>Precisa de Ajuda?</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../../log_out.php">
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
                <a class="nav-link collapsed" href="../../../index.php">
                    <i class="bi bi-grid"></i>
                    <span>Início</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Serviços</span><i
                        class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="../../../servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-menu-button-wide"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="../../../pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos
                                Pendentes</span></a>
                    </li>
                    <li>
                        <a href="../../../pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em
                                Andamento</span></a>
                    </li>
                    <li>
                        <a href="../../../pedidos/pedido_excluido.php"><i class="bi bi-circle"></i><span>Pedidos
                                Excluidos</span></a>
                    </li>
                    <li>
                        <a href="../../../pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos
                                Concluidos</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="../../../mensagem.php">
                    <i class="bi bi-envelope"></i>
                    <span>Mensagens</span>
                </a>
            </li>

            <!-- Perfil -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="../../../perfil.php">
                    <i class="bi bi-person"></i>
                    <span>Perfil</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="../../../suporte.php">
                    <i class="bi bi-chat-dots"></i>
                    <span>Suporte</span>
                </a>
            </li>

        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Painel</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../../index.php">Home</a></li>
                    <li class="breadcrumb-item">Pedido</li>
                    <li class="breadcrumb-item active">Visualizar Orçamento</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="card-container">
                    <?php if (isset($pedido)): ?>
                        <div class="card">
                            <h3>
                                <?= htmlspecialchars($pedido['servicos']) ?>
                                <span style="font-size: 0.5em; color: #666;">(ID:
                                    <?= htmlspecialchars($pedido['id']) ?>)</span>
                            </h3>
                            <p><strong>Valor Orçamento:</strong> R$
                                <?= htmlspecialchars(number_format($pedido['valor_orcamento'], 2, ',', '.')) ?></p>
                            <p><strong>Detalhes:</strong> <?= htmlspecialchars($pedido['detalhes']) ?></p>

                            <div class="button-group">
                                <form action="aceitar.php" method="post">
                                    <input type="hidden" name="pedido_id" value="<?= htmlspecialchars($pedido['id']) ?>">
                                    <button type="submit" class="accept">Aceitar</button>
                                </form>
                                <form action="recusar.php" method="post">
                                    <input type="hidden" name="pedido_id" value="<?= htmlspecialchars($pedido['id']) ?>">
                                    <button type="submit" class="reject">Recusar</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>



                </div>
            </div>
        </section>


        <script>
            // Função para formatar o valor com o prefixo "R$"
            function formatarValor(campo) {
                let valor = campo.value.replace(/\D/g, ''); // Remove caracteres não numéricos
                valor = (valor / 100).toFixed(2); // Divide por 100 e formata para duas casas decimais
                valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Adiciona pontos para separação de milhar
                campo.value = `R$ ${valor.replace('.', ',')}`; // Adiciona o prefixo "R$" e substitui o ponto por vírgula
            }
        </script>

    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Swan Shine</span></strong>. All Rights Reserved
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="../../../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/vendor/chart.js/chart.umd.js"></script>
    <script src="../../../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../../../assets/vendor/quill/quill.js"></script>
    <script src="../../../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../../../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../../../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../../../assets/js/main.js"></script>
    <script src="../../../assets/js/main1.js"></script>
</body>

</html>