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

// Processar o upload da imagem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['imagem'])) {
  if ($_FILES['imagem']['error'] == 0) {
    $upload_dir = 'form/uploads/';
    $imagem_nome = basename($_FILES['imagem']['name']);
    $target_file = $upload_dir . $imagem_nome;

    // Mover a imagem para o diretório de uploads
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
      // Usar prepared statements para atualizar o caminho da imagem do cliente pelo email
      $stmt = $conn->prepare("UPDATE clientes SET imagem = ? WHERE email = ?");
      $stmt->bind_param("ss", $target_file, $email);
      $stmt->execute();

      // Verificar se a atualização foi bem-sucedida
      if ($stmt->affected_rows > 0) {
        echo "Imagem enviada com sucesso!";
      } else {
        echo "Erro ao atualizar a imagem.";
      }
    } else {
      echo "Erro ao mover o arquivo para o diretório de uploads.";
    }
  } else {
    echo "Erro ao enviar a imagem.";
  }
}

// Usar prepared statements para buscar o cliente pelo email
$stmt = $conn->prepare("SELECT id, nome, endereco, email, cpf, telefone, genero, imagem, cep FROM clientes WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Obter o resultado da consulta
$result = $stmt->get_result();

// Verificar se retornou algum resultado
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $id = $row['id'];
  $nome = $row['nome'];
  $endereco = $row['endereco'];
  $email = $row['email'];
  $cpf = $row['cpf'];
  $telefone = $row['telefone'];
  $genero = $row['genero'];
  $imagem = $row['imagem']; // Obter o caminho da imagem
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

  <title>Swan Shine - Perfil</title>
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
    /* Estilo geral */
    .section.profile {
      padding: 20px;
    }

    .card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .card-title {
      font-weight: bold;
    }

    .profile-input {
      border-radius: 5px;
    }

    .nav-tabs .nav-link {
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-right: 5px;
    }

    .nav-tabs .nav-link.active {
      background-color: #fd2b50;
      color: #fff;
    }

    .btn-primary {
      background-color: #ef972f;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 1rem;
    }

    .btn-danger {
      background-color: #ef972f;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 1rem;
    }

    /* Estilo responsivo */
    @media (max-width: 1200px) {

      /* Ajuste em telas grandes */
      .col-xl-4,
      .col-xl-8 {
        flex: 0 0 50%;
        max-width: 50%;
      }
    }

    @media (max-width: 992px) {

      /* Ajuste em telas médias */
      .col-xl-4,
      .col-xl-8 {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }

    @media (max-width: 768px) {

      /* Ajuste em tablets */
      .profile-label {
        font-size: 0.9rem;
      }

      .profile-input {
        font-size: 0.9rem;
        padding: 8px;
      }

      .btn-primary,
      .btn-danger {
        font-size: 0.9rem;
        padding: 8px 15px;
      }

      .nav-tabs .nav-link {
        font-size: 0.9rem;
        margin-bottom: 5px;
      }
    }

    @media (max-width: 576px) {

      /* Ajuste em dispositivos móveis */
      .profile-label {
        font-size: 0.8rem;
      }

      .profile-input {
        font-size: 0.8rem;
        padding: 6px;
      }

      .btn-primary,
      .btn-danger {
        font-size: 0.8rem;
        padding: 6px 10px;
      }

      .card-title {
        font-size: 1rem;
        text-align: center;
      }

      .nav-tabs .nav-link {
        font-size: 0.8rem;
        padding: 8px;
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
              <hr class="dropdown-divider" />
            </li>
            <li class="dropdown-footer">
              <a href="mensagem.html">Mostrar todas as mensagens</a>
            </li>
          </ul>
        </li>

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="perfil.php" data-bs-toggle="dropdown">
            <img src="assets/img/usuario.png" alt="Profile" class="rounded-circle" />
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
                <i class="bi bi-person"></i>
                <span>Meu Perfil</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="perfil.php">
                <i class="bi bi-gear"></i>
                <span>Configurações da Conta</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="suporte.php">
                <i class="bi bi-question-circle"></i>
                <span>Precisa de Ajuda?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider" />
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="form/log_out.php">
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
        <!--<div class="col-xl-4">
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
              <img src="assets/img/usuario.png" alt="Perfil" class="rounded-circle">
              <h2>Usuário</h2>
              <h3>...</h3>
              <div class="social-links mt-2">
                <a href="#" class="email"><i class="bi bi-email"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>
        </div> -->
        <!--<div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <?php if ($imagem): ?>
                <img src="<?= htmlspecialchars($imagem) ?>" alt="Imagem do Cliente" style="max-width: 200px; max-height: 200px;">
              <?php else: ?>
                <img src="assets/img/usuario.png" alt="Profile" class="rounded-circle">
              <?php endif; ?>

              <h2><?php echo htmlspecialchars($nome); ?></h2> <h6> Id: #  <?php echo htmlspecialchars(string: $id); ?></h6>

            </div>

          </div> -->
      </div>
      <div class="col-xl-8">
        <div class="card">
          <div class="card-body pt-3">
            <!-- Abas com Bordas -->
            <ul class="nav nav-tabs nav-tabs-bordered">

              <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Visão Geral</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Editar Perfil</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Mudar Senha</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Configurações</button>
              </li>

            </ul>

            <div class="tab-content pt-2">

              <!-- Visão Geral -->
              <div class="tab-pane fade show active" id="profile-overview">

                <h5 class="card-title">Detalhes do Perfil</h5>
                <form action="update_profile.php" method="POST">
                  <div class="row mb-3">
                    <label for="id" class="col-lg-3 col-md-4 col-form-label profile-label">ID:</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="id" type="text" class="form-control profile-input" id="id" value="<?php echo htmlspecialchars($id); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="fullName" class="col-lg-3 col-md-4 col-form-label profile-label">Nome Completo</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="fullName" type="text" class="form-control profile-input" id="fullName" value="<?php echo htmlspecialchars($nome); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="address" class="col-lg-3 col-md-4 col-form-label profile-label">Endereço</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="address" type="text" class="form-control profile-input" id="address" value="<?php echo htmlspecialchars($endereco); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="cep" class="col-lg-3 col-md-4 col-form-label profile-label">CEP:</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="cep" type="text" class="form-control profile-input" id="cep" value="<?php echo htmlspecialchars($cep); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="email" class="col-lg-3 col-md-4 col-form-label profile-label">Email</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="email" type="email" class="form-control profile-input" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="cpf" class="col-lg-3 col-md-4 col-form-label profile-label">CPF</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="cpf" type="text" class="form-control profile-input" id="cpf" value="<?php echo htmlspecialchars($cpf); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="phone" class="col-lg-3 col-md-4 col-form-label profile-label">Telefone</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="phone" type="text" class="form-control profile-input" id="phone" value="<?php echo htmlspecialchars($telefone); ?>" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="gender" class="col-lg-3 col-md-4 col-form-label profile-label">Gênero</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="gender" type="text" class="form-control profile-input" id="gender" value="<?php echo htmlspecialchars($genero); ?>" readonly>
                    </div>
                  </div>
                </form>
              </div><!-- Visão Geral -->

              <!-- Editar Perfil -->
              <div class="tab-pane fade" id="profile-edit">
                <h5 class="card-title">Editar Perfil</h5>
                <form action="form/atualizar_perfil.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate();">
                  <div class="row mb-3">
                    <label for="fullName" class="col-lg-3 col-md-4 col-form-label">Nome Completo</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo htmlspecialchars($nome); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="address" class="col-lg-3 col-md-4 col-form-label">Endereço</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="address" type="text" class="form-control" id="address" value="<?php echo htmlspecialchars($endereco); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="cep" class="col-lg-3 col-md-4 col-form-label">CEP:</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="cep" type="text" class="form-control" id="cep" value="<?php echo htmlspecialchars($cep); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="email" class="col-lg-3 col-md-4 col-form-label">Email</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="email" type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="cpf" class="col-lg-3 col-md-4 col-form-label">CPF</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="cpf" type="text" class="form-control" id="cpf" value="<?php echo htmlspecialchars($cpf); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="phone" class="col-lg-3 col-md-4 col-form-label">Telefone</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="phone" type="text" class="form-control" id="phone" value="<?php echo htmlspecialchars($telefone); ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="gender" class="col-lg-3 col-md-4 col-form-label">Gênero</label>
                    <div class="col-lg-9 col-md-8">
                      <input name="gender" type="text" class="form-control" id="gender" value="<?php echo htmlspecialchars($genero); ?>">
                    </div>
                  </div>

                  <!-- <div class="row mb-3">
                      <label for="imagem" class="col-lg-3 col-md-4 col-form-label">Imagem de Perfil</label>
                      <div class="col-lg-9 col-md-8">
                        <input name="imagem" type="file" class="form-control" id="imagem">
                      </div>
                    </div>-->

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                  </div>
                </form>
              </div><!-- Editar Perfil -->

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
                  <button type="button" class="btn btn-danger" onclick="confirmDesa()">Desativar Conta</button>
                  <button type="button" class="btn btn-danger" onclick="confirmDelete()">Deletar Conta</button>
                </div>

                <script>
                  function confirmDesa() {
                    // Pergunta ao usuário se ele realmente deseja desativar a conta
                    const confirmation = confirm("Tem certeza de que deseja desativar sua conta?");

                    // Se o usuário confirmar, redireciona para a página de desativação
                    if (confirmation) {
                      // Redireciona para o arquivo de desativação de conta
                      window.location.href = 'form/conta/desativar_conta.php';
                    }
                  }
                  function confirmDelete() {
                    // Pergunta ao usuário se ele realmente deseja desativar a conta
                    const confirmation = confirm("Tem certeza de que deseja deletar sua conta? Esta ação não pode ser desfeita.");

                    // Se o usuário confirmar, redireciona para a página de desativação
                    if (confirmation) {
                      // Redireciona para o arquivo de desativação de conta
                      window.location.href = 'form/conta/deletar_conta.php';
                    }
                  }
                </script>



              </div> <!-- Configurações -->

            </div>
          </div>
        </div>
      </div>
      </div>
    </section>

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
        return confirm('Tem certeza que deseja alterar?');
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
    </script>

  </main><!-- End #main -->

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

