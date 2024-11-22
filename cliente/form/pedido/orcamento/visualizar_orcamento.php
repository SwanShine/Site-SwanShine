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

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o ID do pedido da URL
$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar se o ID do pedido é válido
if ($pedido_id <= 0) {
    echo "Pedido inválido.";
    exit();
}

// Recuperar os orçamentos relacionados a esse pedido
$stmt = $conn->prepare("SELECT o.id, o.valor_orcamento, o.detalhes_orcamento, p.nome AS profissional_nome 
                        FROM orcamentos o
                        JOIN profissionais p ON o.profissional_id = p.id
                        WHERE o.pedido_id = ?");
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se existem orçamentos para esse pedido
$orcamentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orcamentos[] = $row;
    }
} else {
    echo "Nenhum orçamento encontrado para este pedido.";
}

// Fechar a consulta e a conexão
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

    <title>Swan Shine - Cliente</title>
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
    /* Estilos gerais */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    padding: 20px;
    margin: 0;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

/* Estilo da seção de orçamentos */
.orcamentos {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0 auto;
}

/* Estilo dos cartões de orçamento */
.orcamento-card {
    border: 1px solid #ccc;
    padding: 20px;
    border-radius: 10px;
    background-color: #fff;
    width: 100%;
    box-sizing: border-box;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
}

.orcamento-card:hover {
    transform: translateY(-5px);
}

.orcamento-card h4 {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}

.orcamento-card p {
    font-size: 14px;
    color: #555;
}

.orcamento-card strong {
    font-weight: bold;
}

/* Estilo para os botões dentro dos cartões */
.orcamento-buttons {
    margin-top: 15px;
    display: flex;
    gap: 10px; /* Espaço uniforme entre os botões */
    justify-content: center; /* Centraliza os botões no cartão */
}

.orcamento-buttons a {
    text-decoration: none;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s;
    text-align: center;
    flex: 1; /* Os botões terão tamanhos iguais */
    max-width: 150px; /* Define uma largura máxima para evitar botões muito grandes */
}

.orcamento-buttons a.recusar {
    background-color: #f44336;
}

.orcamento-buttons a:hover {
    background-color: #45a049;
}

.orcamento-buttons a.recusar:hover {
    background-color: #e53935;
}

/* Estilo para a mensagem quando não houver orçamentos */
.sem-orcamentos {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    background-color: #ffcccb;
    color: #ff0000;
    border-radius: 10px;
    padding: 20px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Responsividade */
@media (max-width: 1024px) {
    .orcamento-card {
        width: calc(50% - 20px);
    }
}

@media (max-width: 768px) {
    .orcamento-card {
        width: calc(50% - 20px);
    }
}

@media (max-width: 425px) {
    .orcamento-card {
        width: 100%;
    }

    .sem-orcamentos {
        font-size: 16px;
        height: 150px;
    }

    .orcamento-buttons {
        flex-direction: column; /* Botões empilhados em telas menores */
        gap: 10px;
    }

    .orcamento-buttons a {
        max-width: none; /* Permitir largura total em telas pequenas */
    }
}

@media (max-width: 375px) {
    .orcamento-card {
        width: 100%;
    }

    .sem-orcamentos {
        font-size: 14px;
        height: 130px;
    }
}

@media (max-width: 320px) {
    .orcamento-card {
        width: 100%;
    }

    .sem-orcamentos {
        font-size: 12px;
        height: 120px;
    }
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
                    <h1>Orçamentos para o Pedido #<?= htmlspecialchars($pedido_id) ?></h1>

                    <?php if (!empty($orcamentos)): ?>
                        <div class="orcamentos">
                            <?php foreach ($orcamentos as $orcamento): ?>
                                <div class="orcamento-card">
                                    <h4>Orçamento de <?= htmlspecialchars($orcamento['profissional_nome']) ?></h4>
                                    <p><strong>Valor:</strong> R$ <?= number_format($orcamento['valor_orcamento'], 2, ',', '.') ?></p>
                                    <p><strong>Detalhes:</strong> <?= htmlspecialchars($orcamento['detalhes_orcamento']) ?></p>

                                    <div class="orcamento-buttons">
                                        <!-- Botões de aceitar e recusar com links para os arquivos PHP -->
                                        <a href="aceitar.php?id=<?= $orcamento['id'] ?>" class="aceitar">Aceitar</a>
                                        <a href="recusar.php?id=<?= $orcamento['id'] ?>" class="recusar">Recusar</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="sem-orcamentos">
                            Nenhum orçamento encontrado para este pedido.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>


    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Swan Shine</span></strong>. Todos os Direitos Reservados
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