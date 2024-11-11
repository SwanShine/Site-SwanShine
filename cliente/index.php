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
  'Lash Designer' => [],
  'Nail Designer' => [],
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

// Selecionar apenas 3 profissionais aleatórios por categoria
$profissionais_selecionados = [];
foreach ($profissionais as $servico => $lista) {
  if (count($lista) > 3) {
    shuffle($lista); // Embaralha os profissionais
    $profissionais_selecionados[$servico] = array_slice($lista, 0, 3); // Pega os 3 primeiros após embaralhar
  } else {
    $profissionais_selecionados[$servico] = $lista;
  }
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


</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Cabeçalho com ID "header", classe para fixar no topo e aplicar estilo flex para alinhamento dos itens. -->

    <div class="d-flex align-items-center justify-content-between">
      <!-- Div que alinha os itens de forma flexível e justifica o conteúdo entre os elementos. -->

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Link que redireciona para a página "index.php" com a classe "logo", exibindo logo e texto. -->

        <img src="assets/img/logo_preta.png" alt="" />
        <!-- Imagem do logo com o caminho "assets/img/logo_preta.png". O atributo "alt" está vazio. -->

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
              src="assets/img/usuario.png"
              alt="Profile"
              class="rounded-circle" />
            <!-- Imagem de perfil (usuário) em formato circular. -->
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <!-- Menu suspenso alinhado à direita com uma seta indicativa, contendo opções de perfil. -->

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="perfil.php">
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
                href="perfil.php">
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
                href="manutencao.html">
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
                href="form/log_out.php">
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
            <a href="servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
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
            <a href="pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
          </li>
          <li>
            <a href="pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
          </li>
          <li>
            <a href="pedidos/pedido_excluido.php"><i class="bi bi-circle"></i><span>Pedidos Excluidos</span></a>
          </li>
          <li>
            <a href="pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
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
              <h2 class="service-category text-center mb-4"><?php echo ucfirst($servico); ?></h2>
              <div class="row">
                <?php if (count($lista) > 0): ?>
                  <?php foreach ($lista as $profissional): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
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
                              <?php if (!empty($profissional['tiktok'])): ?>
                                TikTok: <a href="<?php echo htmlspecialchars($profissional['tiktok']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['tiktok']); ?></a><br>
                              <?php endif; ?>
                              <?php if (!empty($profissional['instagram'])): ?>
                                Instagram: <a href="<?php echo htmlspecialchars($profissional['instagram']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['instagram']); ?></a><br>
                              <?php endif; ?>
                              <?php if (!empty($profissional['facebook'])): ?>
                                Facebook: <a href="<?php echo htmlspecialchars($profissional['facebook']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['facebook']); ?></a><br>
                              <?php endif; ?>
                              <?php if (!empty($profissional['linkedin'])): ?>
                                LinkedIn: <a href="<?php echo htmlspecialchars($profissional['linkedin']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['linkedin']); ?></a><br>
                              <?php endif; ?>
                              <?php if (!empty($profissional['whatsapp'])): ?>
                                WhatsApp: <a href="https://wa.me/<?php echo htmlspecialchars($profissional['whatsapp']); ?>" target="_blank"><?php echo htmlspecialchars($profissional['whatsapp']); ?></a><br>
                              <?php endif; ?>
                            </p>
                          </div>
                          <a href="form/contato_profissional.php?id=<?php echo urlencode($profissional['id']); ?>" class="btn btn-primary card-button">
                            <i class="fas fa-envelope"></i> Conversar com Profissional
                          </a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="col-md-12">
                    <div class="service-card card mb-4 shadow-sm">
                      <div class="card-body text-center">
                        <p class="text-muted">Nenhum profissional disponível para <?php echo htmlspecialchars($servico); ?>.</p>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
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
  <script>
    // Função para mostrar um perfil de cada vez
    let currentIndex = 0;
    const perfis = document.querySelectorAll('.perfil');

    function showNextPerfil() {
      // Esconde o perfil atual
      perfis[currentIndex].style.display = 'none';

      // Calcula o próximo perfil
      currentIndex = (currentIndex + 1) % perfis.length;

      // Exibe o próximo perfil
      perfis[currentIndex].style.display = 'block';
    }

    // Inicia mostrando o primeiro perfil
    perfis[currentIndex].style.display = 'block';

    // Alterna os perfis a cada 90 segundos (90000 milissegundos)
    setInterval(showNextPerfil, 90000);
  </script>
</body>

</html>