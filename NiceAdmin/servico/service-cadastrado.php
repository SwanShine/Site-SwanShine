<?php
header('Content-Type: application/json');

// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
    exit();
}

// Configurações do banco de dados
$host = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$user = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$database = "swanshine";

// Conecta ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    error_log("Conexão falhou: " . $conn->connect_error);
    die("Erro na conexão com o banco de dados.");
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Buscar o ID do profissional logado
$stmt = $conn->prepare("SELECT id FROM profissionais WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_profissional = $row['id'];
} else {
    echo json_encode(["error" => "Profissional não encontrado."]);
    exit();
}

$stmt->close();

// Preparar e executar a consulta para os serviços do profissional logado
$sql = "SELECT * FROM serviços WHERE id_profissionais = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_profissional);
$stmt->execute();
$result = $stmt->get_result();

$servicos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
} else {
    echo json_encode(["message" => "Nenhum serviço encontrado para este profissional."]);
    exit();
}

echo json_encode($servicos);

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
    <link href="../assets/img/favicon.png" rel="icon" />
    <!-- Link para o favicon da página -->
    <link href="../assets/img/favicon.png" rel="apple-touch-icon" />
    <!-- Link para o ícone de toque da Apple -->

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect" />
    <!-- Preconecta ao domínio das fontes do Google -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet" />
    <!-- Importa as fontes do Google (Open Sans, Nunito e Poppins) com diferentes estilos e pesos -->

    <!-- Arquivos CSS de Fornecedores -->
    <link
        href="../assets/vendor/bootstrap/css/bootstrap.min.css"
        rel="stylesheet" />
    <!-- CSS do Bootstrap -->
    <link
        href="../assets/vendor/bootstrap-icons/bootstrap-icons.css"
        rel="stylesheet" />
    <!-- Ícones do Bootstrap -->
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <!-- CSS dos Boxicons -->
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Snow -->
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <!-- CSS do Quill (editor de texto) - tema Bubble -->
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <!-- CSS do Remixicon -->
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet" />
    <!-- CSS do Simple Datatables -->

    <!-- Arquivo CSS -->
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="../assets/css/main.css" rel="stylesheet" />
    <!-- Link para o arquivo CSS -->

    <style>

    </style>

</head>

<body>
 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="../index.php" class="logo d-flex align-items-center">
        <img src="../assets/img/logo_preta.png" alt="" />
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
              <a href="../mensagem.html"><span class="badge rounded-pill bg-primary p-2 ms-2">Ver todas</span></a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li class="dropdown-footer">
              <a href="../mensagem.html">Mostrar todas as mensagens</a>
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
              src="../assets/img/usuario.png"
              alt="Profile"
              class="rounded-circle" />
          </a>

          <ul
            class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

            <li>
              <a
                class="dropdown-item d-flex align-items-center"
                href="../perfil.php">
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
                href="../perfil.php">
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
                href="../manutencao.html">
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
                href="../forms/log_out.php">
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
            <a href="../pedidos/pedido_pendente.php"><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a>
          </li>
          <li>
            <a href="../pedidos/pedido_andamento.php"><i class="bi bi-circle"></i><span>Pedidos Em Andamento</span></a>
          </li>
          <li>
            <a href="../pedidos/pedido_recusado.php"><i class="bi bi-circle"></i><span>Pedidos Recusados</span></a>
          </li>
          <li>
            <a href="../pedidos/pedido_concluido.php"><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a>
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
            <a href="service.php"><i class="bi bi-circle"></i><span>Cadastre Seu Serviço</span></a>
          </li>
          <li>
            <a href="service-cadastrado.php"><i class="bi bi-circle"></i><span>Serviços Cadastrados</span></a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="../mensagens.html">
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
      <h1>Serviço Cadastrado</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
          <li class="breadcrumb-item">Serviço</li>
          <li class="breadcrumb-item active">Serviços Cadastrado</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->


    <section class="section">
      <div class="container">
        <div id="servico-card" class="row">
          <!-- Card do serviço será adicionado aqui pelo JavaScript -->
        </div>
      </div>
    </section>
    
<style>
 
 /* Estilo para a seção que contém o horário e o preço no card */
.card-details {
  /* Define que os elementos filhos serão exibidos em uma linha (layout flexível) */
  display: flex;
  /* Distribui o espaço restante entre os itens filhos, colocando o máximo de espaço possível entre eles */
  justify-content: space-between;
  /* Adiciona uma margem inferior para separar visualmente esta seção do restante do conteúdo no card */
  margin-bottom: 10px;
}

/* Estilo para cada item de detalhe no card (horário e preço) */
.card-detail-item {
  /* Faz com que o item de detalhe ocupe um espaço flexível, permitindo que os itens ao lado se ajustem de acordo */
  flex: 1;
  /* Alinha o texto centralmente dentro de cada item de detalhe */
  text-align: center;
}

/* Estilo para os parágrafos dentro dos itens de detalhe */
.card-detail-item p {
  /* Remove as margens padrão dos parágrafos para garantir que não haja espaço extra ao redor do texto */
  margin: 0;
}

</style>
<section class="section">
    <div class="container">
        <div class="row" id="servico-card">
            <!-- Os cartões de serviço serão inseridos aqui -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('forms/service-cadastrado.php')
        .then(response => response.json())
        .then(servicos => {
            const container = document.getElementById('servico-card');
            if (servicos && servicos.length > 0) {
                servicos.forEach(servico => {
                    const card = document.createElement('div');
                    card.className = 'col-lg-4 col-md-6 d-flex align-items-stretch mb-4';
                    card.innerHTML = `
                        <div class="card">
                            <img src="uploads/${servico.caminho_imagem || 'placeholder.png'}" class="card-img-top" alt="Imagem do Serviço">
                            <div class="card-body">
                                <h5 class="card-title">${servico.nome}</h5>
                                <div class="card-details">
                                    <div class="card-detail-item">
                                        <p class="card-text">Horário</p>
                                        <p class="card-text">${servico.horario}</p>
                                    </div>
                                    <div class="card-detail-item">
                                        <p class="card-text">Preço</p>
                                        <p class="card-text">R$ ${parseFloat(servico.preco).toFixed(2)}</p>
                                    </div>
                                </div>
                                <p class="card-text">Descrição: ${servico.descricao}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            } else {
                const noServiceCard = document.createElement('div');
                noServiceCard.className = 'col-12 mb-4';
                noServiceCard.innerHTML = `
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Nenhum Serviço Encontrado</h5>
                            <p class="card-text">Atualmente, não há serviços cadastrados para exibir.</p>
                        </div>
                    </div>
                `;
                container.appendChild(noServiceCard);
            }
        })
        .catch(error => console.error('Erro ao buscar os serviços:', error));
});
</script>

<!-- Inclua o Bootstrap se ainda não estiver incluído -->
<script src="path/to/bootstrap.bundle.min.js"></script>


  </main><!-- End #main -->

 <!-- ======= Footer ======= -->
 <footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright <strong><span>Swan Shine</span></strong>. All Rights Reserved
  </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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