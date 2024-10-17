<?php
// Iniciar a sessão
session_start(); // Inicia uma sessão PHP para armazenar dados de usuário durante a navegação.

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) { // Verifica se o email do usuário está salvo na sessão.
    header('Location: ../../home/forms/login/login.html'); // Redireciona para a página de login.
    exit(); // Interrompe a execução do script para garantir que o redirecionamento ocorra.
}

// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com"; // Nome do servidor do banco de dados.
$username = "admin"; // Nome de usuário do banco de dados.
$password = "gLAHqWkvUoaxwBnm9wKD"; // Senha do banco de dados.
$dbname = "swanshine"; // Nome do banco de dados.

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname); // Cria uma nova conexão MySQL.

if ($conn->connect_error) { // Verifica se a conexão falhou.
    die("Conexão falhou: " . $conn->connect_error); // Exibe mensagem de erro.
}

// Recuperar o email da sessão
$email = $_SESSION['user_email']; // Obtém o email do usuário da sessão.

// Verificar se o cliente existe na tabela "clientes"
$stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?"); // Prepara a consulta.

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error); // Exibe erro se a consulta falhar.
}

$stmt->bind_param("s", $email); // Substitui "?" pelo email do cliente.
$stmt->execute(); // Executa a consulta.
$result = $stmt->get_result(); // Armazena o resultado.

if ($result->num_rows > 0) { // Verifica se o cliente foi encontrado.
    $cliente = $result->fetch_assoc(); // Obtém os dados do cliente.
    $cliente_id = $cliente['id']; // Armazena o ID do cliente.
} else {
    echo "Cliente não encontrado."; // Mensagem se o cliente não for encontrado.
    exit(); // Encerra o script.
}

// Fechar a consulta do cliente
$stmt->close(); // Fecha o comando de consulta ao banco de dados.

// Buscar os serviços pendentes feitos pelo cliente
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE email = ? AND status = 'pendente' ORDER BY data_pedido DESC");

if (!$stmt) {
    die("Erro na preparação da consulta de pedidos: " . $conn->error); // Exibe erro se a consulta falhar.
}

$stmt->bind_param("s", $email); // Substitui "?" pelo email do cliente.
$stmt->execute(); // Executa a consulta.
$result = $stmt->get_result(); // Armazena o resultado.

// Verificar se o cliente tem pedidos pendentes
$servicos_solicitados = []; // Cria um array para armazenar os serviços solicitados.

if ($result->num_rows > 0) { // Verifica se foram encontrados pedidos pendentes.
    while ($row = $result->fetch_assoc()) { // Percorre os resultados.
        $servicos = explode(', ', $row['servicos']); // Divide os serviços em um array.
        $servicos_solicitados = array_merge($servicos_solicitados, $servicos); // Adiciona os serviços ao array.
    }
} else {
    echo "Nenhum serviço pendente encontrado para este cliente."; // Mensagem se não houver pedidos pendentes.
    exit(); // Encerra o script.
}

// Fechar a consulta de pedidos do cliente
$stmt->close(); // Fecha o comando de consulta ao banco de dados.

// Filtrar serviços duplicados
$servicos_solicitados = array_unique($servicos_solicitados); // Remove duplicatas.

// Mostrar os serviços solicitados
echo "<h3>Serviços Pendentes</h3>"; // Título para os serviços solicitados.
if (!empty($servicos_solicitados)) { // Verifica se o array de serviços não está vazio.
    echo "<ul>"; // Inicia uma lista não ordenada.
    foreach ($servicos_solicitados as $servico) { // Percorre o array de serviços.
        echo "<li>" . htmlspecialchars($servico) . "</li>"; // Exibe cada serviço como um item de lista.
    }
    echo "</ul>"; // Fecha a lista.
} else {
    echo "Nenhum serviço encontrado."; // Mensagem se o array estiver vazio.
}

// Fechar a conexão
$conn->close(); // Fecha a conexão com o banco de dados.
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SwanShine - Cliente</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <!-- Link para o favicon da página -->
  <link href="../assets/img/favicon.png" rel="apple-touch-icon">
  <!-- Link para o ícone de toque da Apple -->

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
  <link href="../assets/css/services.css" rel="stylesheet">


</head>

<body>
    
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <!-- Cabeçalho com ID "header", classe para fixar no topo e aplicar estilo flex para alinhamento dos itens. -->

    <div class="d-flex align-items-center justify-content-between">
      <!-- Div que alinha os itens de forma flexível e justifica o conteúdo entre os elementos. -->

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Link que redireciona para a página "index.php" com a classe "logo", exibindo logo e texto. -->

        <img src="../assets/img/logo_preta.png" alt="" />
        <!-- Imagem do logo com o caminho "../assets/img/logo_preta.png". O atributo "alt" está vazio. -->

        <span class="d-none d-lg-block">SwanShine</span>
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
              src="../assets/img/usuario.png"
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
                href="forms/log_out.php">
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
            <a href="../servicos.php"><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a>
          </li>
          <li>
            <a href="#"><i class="bi bi-circle"></i><span>...</span></a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="../mensagem.php">
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
      <h1>Pedidos Pendentes</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
          <li class="breadcrumb-item">Pedido</li>
          <li class="breadcrumb-item active">Pedidos Pendentes</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
    <div class="row">
        <div class="card-container">

            <?php if (isset($_GET['message']) && $_GET['message'] === 'Pedido recusado com sucesso'): ?>
                <div class="card notification-card">
                    <h3>Pedido Recusado</h3>
                    <p>O pedido foi recusado com sucesso.</p>
                    <a href="../index.php" class="back-link">&#8592; Voltar para os serviços</a>
                </div>
            <?php elseif (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="card">
                        <div class="status"><?= htmlspecialchars($pedido['status']) ?></div> <!-- Novo elemento para o status -->
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
                    <h3>Nenhum serviço solicitado</h3>
                    <p>Atualmente, não há nenhum pedido disponível para os serviços oferecidos.</p>
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
            &copy; Copyright <strong><span>Swan Shine</span></strong>. Todos os Diretos Reservados.
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