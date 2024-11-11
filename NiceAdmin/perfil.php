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

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Usar prepared statements para buscar o profissional pelo email
$stmt = $conn->prepare("
    SELECT id, nome, email, celular, data_de_aniversario, genero, cep, rua, numero, complemento, referencia, bairro, cidade, estado, servicos, cpf, tiktok, facebook, instagram, linkedin, whatsapp 
    FROM profissionais 
    WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();

// Obter o resultado da consulta
$result = $stmt->get_result();

// Verificar se retornou algum resultado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id']; // Agora estamos buscando o ID
    $nome = $row['nome'];
    $email = $row['email'];
    $celular = $row['celular'];
    $data_de_aniversario = $row['data_de_aniversario'];
    $genero = $row['genero'];
    $cep = $row['cep'];
    $rua = $row['rua'];
    $numero = $row['numero'];
    $complemento = $row['complemento'];
    $referencia = $row['referencia'];
    $bairro = $row['bairro'];
    $cidade = $row['cidade'];
    $estado = $row['estado'];
    $servicos = $row['servicos'];
    $cpf = $row['cpf'];
    $tiktok = $row['tiktok'];
    $facebook = $row['facebook'];
    $instagram = $row['instagram'];
    $linkedin = $row['linkedin'];
    $whatsapp = $row['whatsapp'];
} else {
    // Definir valores padrão se nenhum resultado for encontrado
    $nome = $email = $celular = $data_de_aniversario = $genero = $cep = $rua = $numero = $complemento = $referencia = $bairro = $cidade = $estado = $servicos = $cpf = $tiktok = $facebook = $instagram = $linkedin = $whatsapp = "Não encontrado";
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

// Fechar a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>SwanShine - Perfil</title>
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
    <style>
        .bordered-field {
            border: 1px solid #000;
            /* Cor da borda */
            padding: 10px;
            /* Espaçamento interno */
            border-radius: 5px;
            /* Bordas arredondadas (opcional) */
            background-color: #fff;
            /* Cor de fundo (opcional) */
            margin: 20px 0;
            /* Margem superior e inferior para espaçamento entre os campos */
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
            <h1>Perfil</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Usuário</li>
                    <li class="breadcrumb-item active">Perfil</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                            <img src="assets/img/usuario.png" alt="Perfil" class="rounded-circle">
                            <h2><?php echo htmlspecialchars($nome); ?></h2>
                            <h3><?php echo htmlspecialchars($servicos); ?></h3>
                            <div class="social-links mt-2">
                                <a href="<?php echo htmlspecialchars($tiktok); ?>" class="tiktok" target="_blank" title="TikTok"><i class="bi bi-tiktok"></i></a>
                                <a href="<?php echo htmlspecialchars($facebook); ?>" class="facebook" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="<?php echo htmlspecialchars($instagram); ?>" class="instagram" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                                <a href="<?php echo htmlspecialchars($linkedin); ?>" class="linkedin" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                <a href="<?php echo htmlspecialchars($whatsapp); ?>" class="whatsapp" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-xl-8">

                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Abas Com Bordas -->
                            <ul class="nav nav-tabs nav-tabs-bordered">

                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Visão Geral</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Editar Perfil</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Alterar Senha</button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Configurações</button>
                                </li>

                            </ul>

                            <div class="tab-content pt-2">

                                <!-- Visão Geral-->
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <h5 class="card-title">Detalhes do Perfil</h5>

                                    <div style="max-height: 300px; overflow-y: auto;"> <!-- Adicione esta div para scroll -->
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Nome Completo</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($nome); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Email</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($email); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Celular</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($celular); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Data de Nascimento</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($data_de_aniversario); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Gênero</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($genero); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">CPF</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($cpf); ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Endereço</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field">
                                                    <?php echo htmlspecialchars($rua); ?>, <?php echo htmlspecialchars($numero); ?>, <?php echo htmlspecialchars($complemento); ?> <br>
                                                    <?php echo htmlspecialchars($bairro); ?>, <?php echo htmlspecialchars($cidade); ?> - <?php echo htmlspecialchars($estado); ?>, <?php echo htmlspecialchars($cep); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Serviços</div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="bordered-field"><?php echo htmlspecialchars($servicos); ?></div>
                                            </div>
                                        </div>
                                    </div> <!-- Fecha a div para scroll -->
                                </div><!-- Visão Geral-->

                                <!-- Editar perfil -->
                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        <!-- Formulário de Edição do Perfil -->
                                        <form action="forms/atualizar_perfil.php" method="POST">
                                            <div class="row mb-3">
                                                <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nome Completo</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo htmlspecialchars($nome); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="celular" class="col-md-4 col-lg-3 col-form-label">Celular</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="celular" type="text" class="form-control" id="celular" value="<?php echo htmlspecialchars($celular); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="data_de_aniversario" class="col-md-4 col-lg-3 col-form-label">Data de Nascimento</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="data_de_aniversario" type="date" class="form-control" id="data_de_aniversario" value="<?php echo htmlspecialchars($data_de_aniversario); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="genero" class="col-md-4 col-lg-3 col-form-label">Gênero</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <select name="genero" id="genero" class="form-control">
                                                        <option value="Masculino" <?php echo $genero === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                                        <option value="Feminino" <?php echo $genero === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                                                        <option value="Outro" <?php echo $genero === 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="cep" class="col-md-4 col-lg-3 col-form-label">CEP</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="cep" type="text" class="form-control" id="cep" value="<?php echo htmlspecialchars($cep); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="rua" class="col-md-4 col-lg-3 col-form-label">Rua</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="rua" type="text" class="form-control" id="rua" value="<?php echo htmlspecialchars($rua); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="numero" class="col-md-4 col-lg-3 col-form-label">Número</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="numero" type="text" class="form-control" id="numero" value="<?php echo htmlspecialchars($numero); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="complemento" class="col-md-4 col-lg-3 col-form-label">Complemento</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="complemento" type="text" class="form-control" id="complemento" value="<?php echo htmlspecialchars($complemento); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="bairro" class="col-md-4 col-lg-3 col-form-label">Bairro</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="bairro" type="text" class="form-control" id="bairro" value="<?php echo htmlspecialchars($bairro); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="cidade" class="col-md-4 col-lg-3 col-form-label">Cidade</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="cidade" type="text" class="form-control" id="cidade" value="<?php echo htmlspecialchars($cidade); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="estado" class="col-md-4 col-lg-3 col-form-label">Estado</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="estado" type="text" class="form-control" id="estado" value="<?php echo htmlspecialchars($estado); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="servicos" class="col-md-4 col-lg-3 col-form-label">Serviços</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="servicos" type="text" class="form-control" id="servicos" value="<?php echo htmlspecialchars($servicos); ?>">
                                                </div>
                                            </div>

                                            <!-- Redes Sociais -->
                                            <div class="row mb-3">
                                                <label for="tiktok" class="col-md-4 col-lg-3 col-form-label">TikTok</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="tiktok" type="text" class="form-control" id="tiktok" value="<?php echo htmlspecialchars($tiktok); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="facebook" class="col-md-4 col-lg-3 col-form-label">Facebook</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="facebook" type="text" class="form-control" id="facebook" value="<?php echo htmlspecialchars($facebook); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="instagram" class="col-md-4 col-lg-3 col-form-label">Instagram</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="instagram" type="text" class="form-control" id="instagram" value="<?php echo htmlspecialchars($instagram); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="linkedin" type="text" class="form-control" id="linkedin" value="<?php echo htmlspecialchars($linkedin); ?>">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="whatsapp" class="col-md-4 col-lg-3 col-form-label">WhatsApp</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="whatsapp" type="text" class="form-control" id="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>">
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary" onclick="return confirmProfileUpdate();">Salvar Alterações</button>
                                            </div>
                                        </form>
                                    </div><!-- Formulário de Edição do Perfil -->
                                </div><!-- Editar perfil -->


                                <!-- Mudar Senha -->
                                <div class="tab-pane fade pt-3" id="profile-change-password">
                                    <h5 class="card-title">Mudar Senha</h5>
                                    <form action="form/update_password.php" method="POST" onsubmit="return confirmUpdate() && validatePassword();">
                                        <div class="row mb-3">
                                            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Nova Senha</label>
                                            <div class="col-md-8 col-lg-9 position-relative">
                                                <input name="newPassword" type="password" class="form-control profile-input" id="newPassword" required>
                                                <i class="fas fa-eye toggle-password" onclick="togglePassword('newPassword')"></i>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="confirmPassword" class="col-md-4 col-lg-3 col-form-label">Confirmar Nova Senha</label>
                                            <div class="col-md-8 col-lg-9 position-relative">
                                                <input name="confirmPassword" type="password" class="form-control profile-input" id="confirmPassword" required>
                                                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmPassword')"></i>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Alterar Senha</button>
                                        </div>
                                    </form>
                                </div><!-- Mudar Senha -->

                                <!-- Configurações -->
                                <div class="tab-pane fade pt-3" id="profile-settings">
                                    <h5 class="card-title">Configurações</h5>

                                    <div class="text-center">
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Desativar Conta</button>
                                    </div>

                                    <script>
                                        function confirmDelete() {
                                            // Pergunta ao usuário se ele realmente deseja deletar a conta
                                            const confirmation = confirm("Tem certeza de que deseja desativar sua conta? Esta ação não pode ser desfeita.");

                                            // Se o usuário confirmar, faz a requisição para deletar a conta
                                            if (confirmation) {
                                                // Obtenha o ID do usuário corretamente
                                                const userId = <?php echo json_encode($id); ?>; // Certifique-se de que $id esteja definido e contém o ID do usuário

                                                fetch('forms/desativar_conta.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                        },
                                                        body: JSON.stringify({
                                                            user_id: userId
                                                        }),
                                                    })
                                                    .then(response => {
                                                        if (!response.ok) {
                                                            throw new Error('Erro ao deletar a conta');
                                                        }
                                                        return response.json();
                                                    })
                                                    .then(data => {
                                                        // Ação após a exclusão bem-sucedida
                                                        alert('Conta deletada com sucesso!');
                                                        // Redirecionar ou atualizar a página, se necessário
                                                        // window.location.reload();
                                                    })
                                                    .catch(error => {
                                                        console.error('Erro:', error);
                                                        alert('Não foi possível deletar a conta. Tente novamente mais tarde.');
                                                    });
                                            }
                                        }
                                    </script>
                                </div> <!-- Configurações -->
                            </div><!-- Final das Abas -->

                        </div>
                    </div>

                </div>
            </div>
        </section>


    </main><!-- End #main -->
    <script>
        function validatePassword() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                alert('As senhas não coincidem!');
                return false;
            }
            return true;
        }

        function confirmUpdate() {
            return confirm('Tem certeza de que deseja atualizar sua senha?');
        }

        function togglePassword(id) {
            const passwordField = document.getElementById(id);
            const icon = passwordField.nextElementSibling;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function confirmProfileUpdate() {
            return confirm('Tem certeza de que deseja salvar as alterações no seu perfil?');
        }

        function confirmSettingsUpdate() {
            return confirm('Tem certeza de que deseja salvar as alterações nas configurações?');
        }
    </script>

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

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>