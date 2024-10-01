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

// Consultar os dados dos profissionais
$sql = "SELECT * FROM profissionais";
$result = $conn->query($sql);

$profissionais = [
  'Barbeiro' => [],
  'Maquiagem' => [],
  'Lash_Designer' => [],
  'Nail_Designer' => [],
  'Trancista' => [],
  'Esteticista' => [],
  'Cabeleireira' => [],
  'Depilação' => []
];

if ($result->num_rows > 0) {
  // Separar os profissionais por serviço
  while ($row = $result->fetch_assoc()) {
    $servico = $row['servicos'];
    if (array_key_exists($servico, $profissionais)) {
      $profissionais[$servico][] = $row;
    }
  }
} else {
  echo "Nenhum profissional encontrado.";
}

// Fechar conexão
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
    /* Estilos gerais do container */

    /* Estilização para todos os cards */
    .card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin: 10px;
      /* Reduzi o espaçamento para telas menores */
      width: 100%;
      /* Ocupa toda a largura disponível */
      max-width: 300px;
      /* Limita o tamanho máximo dos cards */
      padding: 15px;
      /* Reduzi o padding para otimizar o espaço */
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    /* Estilo para o título dentro do card */
    .card h2 {
      color: #333;
      margin: 0 0 8px;
      font-size: 1.5rem;
      /* Ajusta o tamanho do título para telas menores */
    }

    /* Estilo para os parágrafos dentro do card */
    .card p {
      color: #777;
      margin: 0;
      font-size: 0.875rem;
      /* Texto menor para se ajustar melhor a pequenas telas */
    }

    /* Estilização específica para os cards de serviço */
    .services {
      overflow-y: auto;
      max-height: 100vh;
      padding: 15px;
    }

    .services .section-title {
      text-align: center;
      margin-bottom: 15px;
    }

    .services .section-title h2 {
      font-size: 1.75rem;
      /* Ajuste de título para telas menores */
      color: #333;
    }

    /* Estilização para os detalhes do card (separando por tópicos) */
    .card-details .field {
      border: 1px solid #ddd;
      padding: 12px;
      /* Reduzi o padding para otimizar o espaço */
      margin-bottom: 10px;
      border-radius: 5px;
      font-size: 0.875rem;
      text-align: left;
    }

    /* Remove a margem inferior no último campo */
    .card-details .field:last-child {
      margin-bottom: 0;
    }

    /* Estilo para o título do card */
    .card-title {
      margin-bottom: 15px;
      font-size: 1.25rem;
    }

    /* Estilização para o botão dentro do card */
    .btn.card-button {
      display: block;
      width: 100%;
      margin-top: 10px;
      padding: 8px;
      font-size: 1rem;
    }

    /* Garante que o conteúdo do card fique bem organizado */
    .service-card {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Gerenciamento de largura e organização dos cards em colunas */
    .services .row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      /* Diminui o espaçamento entre os cards */
      justify-content: center;
    }

    /* Responsividade para telas menores (320px e abaixo) */
    @media (max-width: 320px) {
      .card {
        width: 100%;
        /* Ocupa toda a largura da tela em dispositivos pequenos */
        max-width: 100%;
        /* Remove o limite máximo para ajustar na tela */
        margin: 5px;
        /* Reduz ainda mais o espaçamento para pequenos dispositivos */
        padding: 10px;
        /* Reduz o padding */
      }

      .card h2 {
        font-size: 1.25rem;
        /* Diminui o tamanho do título */
      }

      .card p {
        font-size: 0.75rem;
        /* Diminui o tamanho do texto */
      }

      .card-details .field {
        font-size: 0.75rem;
        /* Diminui o tamanho do texto nos detalhes */
        padding: 10px;
      }

      .btn.card-button {
        font-size: 0.875rem;
        /* Diminui o tamanho do botão */
        padding: 8px;
      }

      .services .row {
        flex-direction: column;
        gap: 5px;
        /* Diminui o espaço entre os cards */
      }
    }

    /* Responsividade para telas médias */
    @media (min-width: 321px) and (max-width: 768px) {
      .card {
        width: 100%;
        max-width: 100%;
        margin: 10px;
      }
    }

    /* Responsividade para telas maiores */
    @media (min-width: 769px) and (max-width: 1200px) {
      .card {
        width: calc(50% - 40px);
      }
    }

    @media (min-width: 1201px) {
      .card {
        width: calc(33.33% - 40px);
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
              <a href="mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li class="dropdown-footer">
              <a href="mensagem.html">Mostrar todas as mensagens</a>
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
                href="manutencao.html">
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
            <a href="pedidos/pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
          </li>
          <li>
            <a href="pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
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
            <a href="servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
          </li>
          <li>
            <a href="#"><i class="bi bi-circle"></i><span>...</span></a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="mensagens.html">
          <i class="bi bi-envelope"></i>
          <span>Mensagens</span>
        </a>
      </li>

      <!-- Perfil -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="perfil.php">
          <i class="bi bi-person"></i>
          <span>Perfil</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="suporte.html">
          <i class="bi bi-chat-dots"></i>
          <span>Suporte</span>
        </a>
      </li>

    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Veja e Contrate os Melhores Profissionais</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Painel</li>
          <li class="breadcrumb-item active">Veja e Contrate</li>
        </ol>
      </nav>
    </div>

    <!-- Services Section -->
    <section id="services" class="services section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Serviços</h2>
        <p>Procure por seus serviços e encontre os melhores profissionais.</p>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">
          <?php foreach ($profissionais as $servico => $lista): ?>
            <div class="col-md-12">
              <h2 class="service-category"><?php echo ucfirst($servico); ?></h2>
              <?php if (count($lista) > 0): ?>
                <?php foreach ($lista as $profissional): ?>
                  <div class="service-card card mb-4 shadow-sm">
                    <div class="card-body">
                      <h3 class="card-title"><?php echo htmlspecialchars($profissional['nome']); ?></h3>
                      <div class="card-details">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($profissional['email']); ?></p>
                        <p><strong>Celular:</strong> <?php echo htmlspecialchars($profissional['celular']); ?></p>
                        <p><strong>Data de Aniversário:</strong> <?php echo htmlspecialchars($profissional['data_de_aniversario']); ?></p>
                        <p><strong>Gênero:</strong> <?php echo htmlspecialchars($profissional['genero']); ?></p>
                        <p><strong>Endereço:</strong> <?php echo htmlspecialchars($profissional['rua']); ?>, <?php echo htmlspecialchars($profissional['numero']); ?>, <?php echo htmlspecialchars($profissional['complemento']); ?> - <?php echo htmlspecialchars($profissional['bairro']); ?>, <?php echo htmlspecialchars($profissional['cidade']); ?>/<?php echo htmlspecialchars($profissional['estado']); ?> - <?php echo htmlspecialchars($profissional['cep']); ?></p>
                        <p><strong>CPF:</strong> <?php echo htmlspecialchars($profissional['cpf']); ?></p>
                        <p><strong>Redes Sociais:</strong></p>
                        <p>
                          <?php if (!empty($profissional['tiktok'])): ?> TikTok: <a href="<?php echo htmlspecialchars($profissional['tiktok']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['tiktok']); ?></a><br> <?php endif; ?>
                          <?php if (!empty($profissional['instagram'])): ?> Instagram: <a href="<?php echo htmlspecialchars($profissional['instagram']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['instagram']); ?></a><br> <?php endif; ?>
                          <?php if (!empty($profissional['facebook'])): ?> Facebook: <a href="<?php echo htmlspecialchars($profissional['facebook']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['facebook']); ?></a><br> <?php endif; ?>
                          <?php if (!empty($profissional['linkedin'])): ?> LinkedIn: <a href="<?php echo htmlspecialchars($profissional['linkedin']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['linkedin']); ?></a><br> <?php endif; ?>
                          <?php if (!empty($profissional['whatsapp'])): ?> WhatsApp: <a href="https://wa.me/<?php echo htmlspecialchars($profissional['whatsapp']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['whatsapp']); ?></a><br> <?php endif; ?>
                        </p>
                      </div>
                      <a href="mensagem.php?id=<?php echo urlencode($profissional['id']); ?>" class="btn btn-primary card-button">
                        <i class="fas fa-envelope"></i> Enviar Mensagem
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="service-card card mb-4 shadow-sm">
                  <div class="card-body">
                    <p>Nenhum profissional disponível para <?php echo htmlspecialchars($servico); ?>.</p>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
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