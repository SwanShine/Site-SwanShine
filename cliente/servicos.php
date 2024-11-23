<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Swan Shine - Cliente</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/servicos/favicon.png" rel="icon">
  <link href="assets/img/favicon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>
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
                href="form/log_out.php">
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
      <h1>Contrate seu Serviço</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
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
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="100">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/barbearia.png" alt="barbearia"></div>
              <div>
                <h4 class="title"><a href="servicos/barbeiro/barbeiro.php"
                    class="stretched-link">Barbeiro</a></h4>
                <p class="description">O barbeiro transforma cada corte de cabelo e barba em uma obra-prima personalizada, oferecendo uma experiência única de cuidado e estilo.</p>
              </div>
            </div>
          </div>
          <!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/maquiagem.png" alt="maquiagem"></div>
              <div>
                <h4 class="title"><a href="servicos/maquiagem/maquiagem.php"
                    class="stretched-link">Maquiagem</a></h4>
                <p class="description">O maquiador realça sua beleza natural com técnicas personalizadas, proporcionando uma experiência única de cuidado e estilo para elevar sua confiança.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="300">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/depilacao.png" alt="depilação"></div>
              <div>
                <h4 class="title"><a href="servicos/depilacao/depilacao.php"
                    class="stretched-link">Depilação</a></h4>
                <p class="description">O depilador oferece uma experiência de cuidado e suavidade, garantindo resultados impecáveis e confortáveis que elevam sua confiança.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="400">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/nail_designer.png" alt="nail designer"></div>
              <div>
                <h4 class="title"><a href="servicos/nail_designer/nail_designer.php"
                    class="stretched-link">Nail designer</a></h4>
                <p class="description">O nail designer cria verdadeiras obras de arte nas suas unhas, oferecendo uma experiência única de cuidado e estilo que eleva sua confiança.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="500">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i
                  class="bi bi-brightness-high"></i></div>
              <div>
                <h4 class="title"><a href="servicos/lash_designer/lash_designer.php"
                    class="stretched-link">Lash Designer</a></h4>
                <p class="description">O designer de sobrancelhas esculpe suas sobrancelhas com precisão artística, proporcionando uma experiência única de cuidado e estilo que realça sua beleza natural.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="600">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/cabelereira.png" alt="cabelereira"></div>
              <div>
                <h4 class="title"><a href="servicos/cabelereira/cabelereira.php"
                    class="stretched-link">Cabelereira</a></h4>
                <p class="description">A cabeleireira transforma seu cabelo com talento e criatividade, oferecendo uma experiência única de cuidado e estilo que realça sua beleza e confiança.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="600">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/trancista.png" alt="trancista"></div>
              <div>
                <h4 class="title"><a href="servicos/trancista/trancista.php"
                    class="stretched-link">Trancista</a></h4>
                <p class="description">A trancista cria penteados únicos e artísticos, proporcionando uma experiência de cuidado e estilo que destaca sua individualidade e beleza.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-6 " data-aos="fade-up" data-aos-delay="600">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><img src="assets/img/servicos/esteticista.png" alt="esteticista"></div>
              <div>
                <h4 class="title"><a href="servicos/esteticista/esteticista.php"
                    class="stretched-link">Esteticista</a></h4>
                <p class="description">A esteticista cuida da sua pele com expertise, oferecendo uma experiência de cuidado e beleza que realça sua confiança e bem-estar.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->




  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Swan Shine</span></strong>. All Rights Reserved
    </div>
  </footer>

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
  <script src="assets/js/main1.js"></script>

</body>

</html>