
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Swan Shine - Cliente</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/favicon.png" rel="apple-touch-icon">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  
  <!-- Main CSS File -->
  <link href="assets/css/ver_profissionais.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  

</head>

<body>
<!-- ======= Header ======= -->

   <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo_preta.png" alt="">
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
              <a href="mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="mensagem.html">Mostrar todas as mensagens</a>
            </li>
          </ul>
        </li>

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/usuario.png" alt="Profile" class="rounded-circle">
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
                <i class="bi bi-person"></i>
                <span>Meu Perfil</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
                <i class="bi bi-gear"></i>
                <span>Configurações da Conta</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="manutencao.html">
                <i class="bi bi-question-circle"></i>
                <span>Precisa de Ajuda?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="../index.html">
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
      <a class="nav-link collapsed" href="index.html">
        <i class="bi bi-grid"></i>
        <span>Início</span>
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
      <a class="nav-link collapsed" href="ver_profissionais.php">
        <i class="bi bi-card-list"></i>
        <span>Serviços</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="manutencao.html">
        <i class="bi bi-chat-dots"></i>
        <span>Suporte</span>
      </a>
    </li>

    <!-- Relatorios
    <li class="nav-item">
      <a class="nav-link collapsed" href="#">
        <i class="bi bi-bar-chart"></i>
        <span>Relatórios</span>
      </a>
    </li>-->

    <li class="nav-item">
      <a class="nav-link collapsed" href="manutencao.html">
        <i class="bi bi-envelope"></i>
        <span>Mensagens</span>
      </a>
    </li>
  </ul>
</aside><!-- End Sidebar-->

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Busque os Melhores Profissionais</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Serviço</li>
        <li class="breadcrumb-item active">Contrate o Serviço</li>
      </ol>
    </nav>
  </div>

  <!-- Services Section -->
  <section id="services" class="services section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <h2>Serviços</h2>
      <p>Procure por seus serviços e encontre os melhores profissionais.</p>
    </div>

    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-3 col-md-6">
          <a href="barbeiro.html" class="card">
            <h2>Barbeiro</h2>
            <p>O barbeiro transforma cada corte de cabelo e barba em uma obra-prima personalizada, oferecendo uma experiência única de cuidado e estilo.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="maquiagem.html" class="card">
            <h2>Maquiagem</h2>
            <p>O maquiador realça sua beleza natural com técnicas personalizadas, proporcionando uma experiência única de cuidado e estilo para elevar sua confiança.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="depilacao.html" class="card">
            <h2>Depilação</h2>
            <p>O depilador oferece uma experiência de cuidado e suavidade, garantindo resultados impecáveis e confortáveis que elevam sua confiança.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="nail_designer.html" class="card">
            <h2>Nail Designer</h2>
            <p>O nail designer cria verdadeiras obras de arte nas suas unhas, oferecendo uma experiência única de cuidado e estilo que eleva sua confiança.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="lash_designer.html" class="card">
            <h2>Lash Designer</h2>
            <p>O designer de sobrancelhas esculpe suas sobrancelhas com precisão artística, proporcionando uma experiência única de cuidado e estilo que realça sua beleza natural.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="cabeleireira.html" class="card">
            <h2>Cabeleireira</h2>
            <p>A cabeleireira transforma seu cabelo com talento e criatividade, oferecendo uma experiência única de cuidado e estilo que realça sua beleza e confiança.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="trancista.html" class="card">
            <h2>Trancista</h2>
            <p>A trancista cria penteados únicos e artísticos, proporcionando uma experiência de cuidado e estilo que destaca sua individualidade e beleza.</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="esteticista.html" class="card">
            <h2>Esteticista</h2>
            <p>A esteticista cuida da sua pele com expertise, oferecendo uma experiência de cuidado e beleza que realça sua confiança e bem-estar.</p>
          </a>
        </div>
      </div>
    </div>
</section>

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

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
</body>
</html>
