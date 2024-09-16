<?php
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
    echo "Profissional não encontrado.";
    exit();
}

$stmt->close();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e valida os dados do formulário
    $servico = trim($_POST['servico']);
    $preco = trim($_POST['preco']);
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $horario = isset($_POST['horario']) ? trim($_POST['horario']) : '';

    // Validação
    if (empty($servico) || empty($preco) || !is_numeric($preco) || $preco <= 0) {
        die("Dados inválidos. Verifique os campos e tente novamente.");
    }

    // Lida com o upload de arquivo
    $caminhoImagem = '';
    $imagemBinaria = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['imagem']['name']);
        $fileType = mime_content_type($_FILES['imagem']['tmp_name']);
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            die("Tipo de arquivo não permitido.");
        }

        if ($_FILES['imagem']['size'] > 2 * 1024 * 1024) {
            die("O arquivo é muito grande. O tamanho máximo permitido é 2MB.");
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
            $caminhoImagem = basename($_FILES['imagem']['name']);
            $imagemBinaria = file_get_contents($_FILES['imagem']['tmp_name']);
        } else {
            die("Erro ao fazer upload do arquivo.");
        }
    }

    // Prepara a consulta SQL
    $sql = "INSERT INTO serviços (Nome, Preço, Descrição, id_profissionais, caminho_imagem, imagem, horario) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepara a declaração
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Erro na preparação da declaração: " . $conn->error);
        die("Erro ao preparar a consulta.");
    }

    // Definindo o parâmetro 'b' para o campo binário (imagem)
    $stmt->bind_param('sssisbs', $servico, $preco, $descricao, $id_profissional, $caminhoImagem, $imagemBinaria, $horario);

    if ($stmt->execute()) {
        echo "Serviço cadastrado com sucesso!";
    } else {
        error_log("Erro ao cadastrar o serviço: " . $stmt->error);
        echo "Erro ao cadastrar o serviço.";
    }

    $stmt->close();
    $conn->close();
}
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
      <h1>Cadastre o Seu Serviço</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
          <li class="breadcrumb-item">Serviço</li>
          <li class="breadcrumb-item active">Cadastre o Seu Serviço</li>
        </ol>
      </nav>
    </div>

    <section class="section">
        <div class="container">
            <form action="" method="post" id="formServico" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="servico">Serviço</label>
                            <select id="servico" name="servico" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="barbeiro">Barbeiro</option>
                                <option value="maquiagem">Maquiagem</option>
                                <option value="depilacao">Depilação</option>
                                <option value="nail_designer">Nail Designer</option>
                                <option value="lash_designer">Lash Designer</option>
                                <option value="cabelereira">Cabelereira</option>
                                <option value="trancista">Trancista</option>
                                <option value="esteticista">Esteticista</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="horario">Horário</label>
                            <input type="time" id="horario" name="horario" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preco">Preço</label>
                            <input type="number" id="preco" name="preco" class="form-control" placeholder="Digite o preço" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="imagem">Imagem</label>
                            <input type="file" name="imagem" id="imagem" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="4" placeholder="Descreva o serviço detalhadamente"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Cadastre seu Serviço</button>
                </div>
            </form>
        </div>
    </section>
  </main>
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

  <!-- Script para envio do formulário via Fetch -->
  <script>
    document.getElementById('formServico').addEventListener('submit', function(event) {
      event.preventDefault(); // Previne o comportamento padrão de envio do formulário

      let formData = new FormData(this);

      fetch('service.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(result => {
          // Manipule o resultado aqui (ex.: mostrar uma mensagem de sucesso ou erro)
          console.log(result);
          alert('Serviço cadastrado com sucesso!');
          document.getElementById('formServico').reset(); // Limpa o formulário
        })
        .catch(error => {
          console.error('Erro:', error);
          alert('Ocorreu um erro ao cadastrar o serviço.');
        });
    });
  </script>

</body>

</html>