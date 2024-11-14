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

// Usar prepared statements para buscar o cliente pelo email
$stmt = $conn->prepare("SELECT nome, endereco, email, cpf, telefone, genero, imagem, cep FROM clientes WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Obter o resultado da consulta
$result = $stmt->get_result();

// Verificar se retornou algum resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $nome = $row['nome'];
  $endereco = $row['endereco'];
  $email = $row['email'];
  $cpf = $row['cpf'];
  $telefone = $row['telefone'];
  $genero = $row['genero'];
  $imagem = $row['imagem']; // Obter a imagem como BLOB
  $cep = $row['cep'];
} else {
  $nome = $endereco = $email = $cpf = $telefone = $genero = $cep = "Não encontrado";
  $imagem = null; // Imagem padrão caso não haja
}

// Fechar a conexão
$stmt->close();
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
    /* Estilos Gerais */
    body {
      font-family: inter, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      color: #333;
    }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    h1 {
      color: #000;
    }

    /* Links */
    a {
      text-decoration: none;
      color: #000;
    }

    a:hover {
      text-decoration: none;
      color: #000;
    }

    /* Perguntas frequentes (FAQ) */
    .faq {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
      margin-top: 30px;
    }

    .faq h2 {
      color: #000;
      text-align: center;
      margin-bottom: 20px;
    }

    .faq ul {
      list-style-type: none;
      padding: 0;
    }

    .faq li {
      margin-bottom: 20px;
    }

    .faq li strong {
      color: #000;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .faq-content {
      display: none;
      padding-top: 10px;
      color: #000;
      line-height: 1.6;
    }

    .faq li.active .faq-toggle {
      transform: rotate(90deg);
    }

    /* Cards de Feedback */
    .feedback-cards {
      display: flex;
      justify-content: center;
      margin-top: 40px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .feedback-card {
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .feedback-card:hover {
      transform: scale(1.05);
    }

    .feedback-card p {
      color: #000;
      font-size: 0.9rem;
    }

    .feedback-card a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #2ecc71;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 15px;
    }

    /* Cards de Serviço */
    .service-category {
      font-size: 2.2rem;
      color: #333;
    }

    .service-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      margin-bottom: 20px;
      /* Espaço entre os cards */
    }

    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .card-title {
      font-size: 1.5rem;
      color: #007bff;
      margin-bottom: 10px;
    }

    .card-details p {
      color: #555;
      margin-bottom: 5px;
    }

    .btn.card-button {
      display: inline-block;
      margin-top: 15px;
      padding: 12px 20px;
      font-size: 1.1rem;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .btn.card-button:hover {
      background-color: #0056b3;
    }

    /* Responsividade */
    @media (max-width: 1024px) {
      .feedback-card {
        width: 48%;
      }

      .service-card {
        width: 48%;
      }
    }

    @media (max-width: 768px) {
      .service-card {
        width: 100%;
        /* Os cards ocupam toda a largura */
        margin-bottom: 15px;
        /* Maior espaçamento entre os cards */
      }

      .feedback-card {
        width: 100%;
        /* Feedback cards ocupando 100% */
        margin-bottom: 20px;
      }

      .faq li {
        font-size: 1rem;
      }

      h1 {
        font-size: 1.8rem;
        /* Ajuste de tamanho do título */
      }
    }

    @media (max-width: 500px) {
      h1 {
        font-size: 1.5rem;
        /* Título menor em telas muito pequenas */
      }

      .faq li strong {
        font-size: 1rem;
        /* Ajuste do tamanho da fonte */
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

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="perfil.php" data-bs-toggle="dropdown">
            <!-- Link com imagem de perfil que abre o menu suspenso do perfil ao clicar. -->

            <img src="assets/img/usuario.png" alt="Profile" class="rounded-circle" />
            <!-- Imagem de perfil (usuário) em formato circular. -->
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <!-- Menu suspenso alinhado à direita com uma seta indicativa, contendo opções de perfil. -->

            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
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
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
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
              <a class="dropdown-item d-flex align-items-center" href="manutencao.html">
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
              <a class="dropdown-item d-flex align-items-center" href="form/log_out.php">
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
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Serviços</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
      <h1>Seja Bem vindo! <?php echo htmlspecialchars($nome); ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Painel</li>
          <li class="breadcrumb-item active">Suporte</li>
        </ol>
      </nav>
    </div>

    <!-- Services Section -->
    <section id="services" class="services section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Suporte</h2>
        <p>Entre em contato e tire suas dúvidas aqui no SAC.</p>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">
          <!-- Perguntas Frequentes (FAQ) -->
          <div class="col-12">
            <div class="faq">
              <h2>Perguntas Frequentes</h2>
              <ul>
                <li>
                  <strong class="faq-question">
                    Como solicitar um serviço?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p> <b> Solicitar um serviço é fácil! </b> Basta seguir os passos abaixo:</p>
                    <ul>
                      <li>1. Clique no menu lateral e selecione <a href="servicos.php">"Contratar Serviço"</a>.</li>
                      <li>2. Escolha o serviço que deseja solicitar.</li>
                      <li>3. Preencha todos os campos obrigatórios do formulário.</li>
                      <li>4. Envie o formulário para concluir sua solicitação.</li>
                    </ul>
                    <p>Em poucos minutos, seu pedido estará pronto para ser processado!</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Como funciona um pedido pendente?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Um pedido pendente significa que você solicitou um serviço, mas ainda está aguardando o profissional enviar o orçamento. Após o orçamento ser enviado, você poderá escolher o que melhor o agrada, e o pedido será movido para "Em Andamento".</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Como funciona um pedido em andamento?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Quando um pedido está "Em Andamento", isso indica que o profissional enviou um orçamento e você já o escolheu. A solicitação agora está pronta para ser realizada.</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Excluí meu pedido, e agora?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Se você excluiu um pedido por engano, não se preocupe! Acesse a seção <a href="pedidos/pedido_excluido.php">"Pedidos Excluídos"</a> e clique em "Voltar Pedido". Seu pedido será restaurado e estará novamente disponível.</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Como concluir um pedido?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Para concluir um pedido, siga os seguintes passos:</p>
                    <ul>
                      <li>Solicite o serviço.</li>
                      <li>Aceite um orçamento.</li>
                      <li>Realize o serviço.</li>
                      <li>Após a conclusão, clique em "Concluir" na seção de <a href="pedidos/pedido_andamento.php">Pedidos em Andamento</a>.</li>
                    </ul>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Como alterar minha senha?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Para alterar sua senha, vá até a aba <b>Perfil</b> e clique em <b>"Mudar Senha"</b> ou <a href="perfil.php">clique aqui</a>. Em seguida, insira sua nova senha.</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    Como mudar minhas informações?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Para atualizar suas informações, vá até a aba <b>Perfil</b> e clique em <b>"Editar Perfil"</b> ou <a href="perfil.php">clique aqui</a> para editar seus dados.</p>
                  </div>
                </li>

                <li>
                  <strong class="faq-question">
                    É seguro cadastrar meus dados na plataforma?
                    <i class="faq-toggle bi bi-chevron-right"></i>
                  </strong>
                  <div class="faq-content">
                    <p>Sim! A nossa plataforma preza pela segurança de seus dados. Tratamos suas informações com o máximo de cuidado e de acordo com as melhores práticas de segurança.</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Cards de Feedback -->
          <div class="col-md-6">
            <div class="feedback-card" style="text-align: center;">
              <h3>Precisa de mais ajuda?</h3>
              <p>Se você não encontrou a resposta para sua dúvida, envie uma mensagem para nossa central de atendimento.</p>
              <a href="mailto:sac@swanshine.com.br">Enviar mensagem</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <script>
      // Função para exibir/esconder as respostas das perguntas
      const faqQuestions = document.querySelectorAll('.faq-question');

      faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
          const content = question.nextElementSibling;
          const icon = question.querySelector('.faq-toggle');

          // Alterna a visibilidade da resposta
          content.style.display = content.style.display === 'block' ? 'none' : 'block';

          // Alterna o ícone da seta
          if (content.style.display === 'block') {
            icon.classList.replace('bi-chevron-right', 'bi-chevron-down');
          } else {
            icon.classList.replace('bi-chevron-down', 'bi-chevron-right');
          }

          // Fecha outros itens abertos
          faqQuestions.forEach(otherQuestion => {
            if (otherQuestion !== question) {
              const otherContent = otherQuestion.nextElementSibling;
              const otherIcon = otherQuestion.querySelector('.faq-toggle');
              otherContent.style.display = 'none';
              otherIcon.classList.replace('bi-chevron-down', 'bi-chevron-right');
            }
          });
        });
      });
    </script>
    <!-- /Services Section -->


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