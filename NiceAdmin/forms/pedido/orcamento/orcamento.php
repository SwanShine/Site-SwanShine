<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../../../home/forms/login/login.html');
    exit();
}

// Verificar se o ID do pedido foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID do pedido não fornecido.";
    exit();
}

$id_pedido = intval($_GET['id']); // Sanitizar o ID do pedido

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

// Buscar detalhes do pedido para pré-preencher o formulário, se necessário
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result = $stmt->get_result();
$pedido = $result->fetch_assoc();

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
    <link href="../../../assets/img/favicon.png" rel="icon" />
    <!-- Link para o favicon da página -->
    <link href="../../../assets/img/favicon.png" rel="apple-touch-icon" />
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <!-- Preconecta ao domínio das fontes do Google -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet" />
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
        /* Estilo do contêiner do formulário */
        .form-container {
            max-width: 100%;
            /* Garante que o contêiner use toda a largura disponível */
            margin: 2rem auto;
            /* Margem automática centraliza o contêiner e define a margem superior e inferior */
            padding: 2rem;
            /* Espaçamento interno do contêiner */
            background: #fff;
            /* Cor de fundo branca */
            border-radius: 8px;
            /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Sombra suave */
            box-sizing: border-box;
            /* Inclui padding e borda no cálculo da largura e altura */
        }

        /* Estilo do título */
        h2 {
            margin-top: 0;
            /* Remove a margem superior */
            color: #333;
            /* Cor do texto */
            font-size: 1.5rem;
            /* Tamanho da fonte */
        }

        /* Estilo do formulário */
        form {
            display: flex;
            /* Exibe os elementos do formulário em uma coluna */
            flex-direction: column;
            /* Alinha os itens na vertical por padrão */
        }

        /* Estilo dos grupos de campos */
        .form-group {
            margin-bottom: 1rem;
            /* Espaçamento abaixo de cada grupo de campo */
            display: flex;
            /* Exibe os itens dentro do grupo de campos em linha */
            flex-wrap: wrap;
            /* Permite que os itens se movam para a linha seguinte se não houver espaço suficiente */
            gap: 1rem;
            /* Espaçamento entre os itens */
        }

        /* Estilo dos rótulos dos campos */
        .form-group label {
            flex: 1 1 100%;
            /* Faz com que o rótulo ocupe toda a largura disponível */
            margin-bottom: 0.5rem;
            /* Espaçamento abaixo do rótulo */
            color: #666;
            /* Cor do texto */
            font-weight: bold;
            /* Texto em negrito */
        }

        /* Estilo dos campos de entrada e áreas de texto */
        .form-group input,
        .form-group textarea {
            flex: 1 1 calc(50% - 1rem);
            /* Ajusta os campos para ocuparem metade da largura menos o espaçamento */
            padding: 0.75rem;
            /* Espaçamento interno dos campos */
            border: 1px solid #ccc;
            /* Borda cinza clara */
            border-radius: 4px;
            /* Bordas arredondadas */
            box-sizing: border-box;
            /* Inclui padding e borda no cálculo da largura e altura */
            transition: border-color 0.3s ease;
            /* Transição suave para a cor da borda */
        }

        /* Estilo dos campos quando estão em foco */
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            /* Cor da borda quando o campo está em foco */
            outline: none;
            /* Remove o contorno padrão do navegador */
        }

        /* Estilo da área de texto */
        .form-group textarea {
            resize: vertical;
            /* Permite que o usuário redimensione a altura da área de texto */
            min-height: 100px;
            /* Define uma altura mínima */
        }

        /* Estilo dos botões */
        .form-group button {
            flex: 1 1 calc(50% - 1rem);
            /* Ajusta os botões para ocuparem metade da largura menos o espaçamento */
            padding: 0.75rem 1.5rem;
            /* Espaçamento interno dos botões */
            border: none;
            /* Remove a borda padrão */
            border-radius: 4px;
            /* Bordas arredondadas */
            background-color: #007bff;
            /* Cor de fundo azul */
            color: #fff;
            /* Cor do texto */
            font-size: 1rem;
            /* Tamanho da fonte */
            cursor: pointer;
            /* Alterar o cursor para uma mãozinha quando passar sobre o botão */
            transition: background-color 0.3s ease, transform 0.3s ease;
            /* Transição suave para a cor de fundo e transformação */
        }

        /* Estilo dos botões quando o mouse está sobre eles */
        .form-group button:hover {
            background-color: #0056b3;
            /* Cor de fundo azul escuro quando o botão é hover */
            transform: scale(1.05);
            /* Aumenta levemente o tamanho do botão */
        }

        /* Estilo dos botões quando são pressionados */
        .form-group button:active {
            background-color: #00408d;
            /* Cor de fundo ainda mais escura quando o botão é pressionado */
        }

        /* Estilo do link de voltar */
        .back-link {
            display: inline-block;
            /* Exibe o link em linha com margens ao redor */
            margin-top: 1rem;
            /* Margem superior */
            color: #007bff;
            /* Cor do texto */
            text-decoration: none;
            /* Remove o sublinhado */
            font-weight: bold;
            /* Texto em negrito */
            transition: color 0.3s ease;
            /* Transição suave para a cor do texto */
        }

        /* Estilo do link de voltar quando o mouse está sobre ele */
        .back-link:hover {
            color: #0056b3;
            /* Cor do texto azul escuro quando o link é hover */
        }

        /* Estilos para tornar o formulário responsivo */
        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
                /* Reduz o padding em telas menores */
            }

            .form-group {
                flex-direction: column;
                /* Empilha os itens verticalmente em telas menores */
                margin-bottom: 0.75rem;
                /* Reduz o espaçamento entre os grupos de campos */
            }

            .form-group input,
            .form-group textarea,
            .form-group button {
                flex: 1 1 100%;
                /* Ajusta os campos e botões para ocupar 100% da largura disponível */
            }

            .form-group button {
                padding: 0.5rem 1rem;
                /* Ajusta o padding dos botões */
                font-size: 0.875rem;
                /* Ajusta o tamanho da fonte dos botões */
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 0.5rem;
                /* Reduz ainda mais o padding para telas muito pequenas */
            }

            .form-group {
                margin-bottom: 0.5rem;
                /* Reduz o espaçamento entre os grupos de campos */
            }

            .form-group input,
            .form-group textarea,
            .form-group button {
                flex: 1 1 100%;
                /* Ajusta os campos e botões para ocupar 100% da largura disponível */
            }

            .form-group button {
                padding: 0.5rem;
                /* Ajusta o padding dos botões */
                font-size: 0.75rem;
                /* Ajusta o tamanho da fonte dos botões */
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
                            <a href="../../../mensagens.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
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
                    <a
                        class="nav-link nav-profile d-flex align-items-center pe-0"
                        href="perfil.php"
                        data-bs-toggle="dropdown">
                        <img
                            src="../../../assets/img/usuario.png"
                            alt="Profile"
                            class="rounded-circle" />
                    </a>

                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                        <li>
                            <a
                                class="dropdown-item d-flex align-items-center"
                                href="../../../perfil.php">
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
                                href="../../../perfil.php">
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
                                href="../../../manutencao.html">
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
                                href="../../log_out.php">
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
                    <a href="../../../pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
                </li>
                <li>
                    <a href="../../../pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
                </li>
                <li>
                    <a href="../../../pedidos/pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
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
                    <a href="../../../servico/"><i class="bi bi-circle"></i><span>Cadastre Seu Serviço</span></a>
                </li>
                <li>
                    <a href="../../../servico/"><i class="bi bi-circle"></i><span>Serviços Cadastrados</span></a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="../../../mensagens.html">
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
            <a class="nav-link collapsed" href="../../../suporte.html">
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
                    <li class="breadcrumb-item active">Painel</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
    <div class="row">
        <div class="form-container">
            <h2>Enviar Orçamento</h2>
            <form action="processar_orcamento.php" method="post">
                <input type="hidden" name="id_pedido" value="<?= htmlspecialchars($id_pedido) ?>">
                <div class="form-group">
                    <label for="valor">Valor:</label>
                    <input type="text" id="valor" name="valor" required oninput="formatarValor(this)">
                </div>
                <div class="form-group">
                    <label for="detalhes">Detalhes:</label>
                    <textarea id="detalhes" name="detalhes" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Enviar Orçamento</button>
                </div>
                <a href="../../../index.php" class="back-link">&#8592; Voltar para os serviços</a>
            </form>
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

    <a
        href="#"
        class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i>
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