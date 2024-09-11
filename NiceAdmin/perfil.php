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
    SELECT nome, email, celular, data_de_aniversario, genero, cep, rua, numero, complemento, referencia, bairro, cidade, estado, servicos, cpf, tiktok, facebook, instagram, linkedin, whatsapp 
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
    <link href="../assets/img/favicon.png" rel="icon">
    <!-- Link para o favicon da página -->
    <link href="../assets/img/favicon.png" rel="apple-touch-icon">
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
                        <a href="service.html"><i class="bi bi-circle"></i><span>Cadastre Seu Serviço</span></a>
                    </li>
                    <li>
                        <a href="service-cadastrado.html"><i class="bi bi-circle"></i><span>Serviços Cadastrados</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="manutencao.html">
                    <i class="bi bi-chat-dots"></i>
                    <span>Suporte</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="manutencao.html">
                    <i class="bi bi-envelope"></i>
                    <span>Mensagens</span>
                </a>
            </li>
        </ul>
    </aside>
    <!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Perfil</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
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

                                <div class="tab-pane fade show active profile-overview" id="profile-overview">


                                    <h5 class="card-title">Detalhes do Perfil</h5>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Nome Completo</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($nome); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($email); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Celular</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($celular); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Data de Aniversário</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($data_de_aniversario); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Gênero</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($genero); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">CPF</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($cpf); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Endereço</div>
                                        <div class="col-lg-9 col-md-8">
                                            <?php echo htmlspecialchars($rua); ?>, <?php echo htmlspecialchars($numero); ?>, <?php echo htmlspecialchars($complemento); ?> <br>
                                            <?php echo htmlspecialchars($bairro); ?>, <?php echo htmlspecialchars($cidade); ?> - <?php echo htmlspecialchars($estado); ?>, <?php echo htmlspecialchars($cep); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Serviços</div>
                                        <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($servicos); ?></div>
                                    </div>


                                </div>

                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit"><!-- Formulário de Edição do Perfil -->


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
                                            <label for="data_de_aniversario" class="col-md-4 col-lg-3 col-form-label">Data de Aniversário</label>
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
                                                <textarea name="servicos" class="form-control" id="servicos" style="height: 100px"><?php echo htmlspecialchars($servicos); ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Redes Sociais -->
                                        <div class="row mb-3">
                                            <div class="row mb-3">
                                                <label for="tiktok" class="col-md-4 col-lg-3 col-form-label">TikTok</label>
                                                <div class="col-md-8 col-lg-9">
                                                    <input name="tiktok" type="text" class="form-control" id="tiktok" value="<?php echo htmlspecialchars($tiktok); ?>">
                                                </div>
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
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>
                                    </form>
                                </div><!-- Formulário de Edição do Perfil -->

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

                                    <form>
                                        <div class="row mb-3">
                                            <label for="notifications" class="col-md-4 col-lg-3 col-form-label">Notificações por Email</label>

                                            <div class="col-md-8 col-lg-9">

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="changesMade" checked>
                                                    <label class="form-check-label" for="changesMade">Alterações feitas na sua conta</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="newProducts" checked>
                                                    <label class="form-check-label" for="newProducts">Informações sobre novos produtos e serviços</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="proOffers">
                                                    <label class="form-check-label" for="proOffers">Ofertas e promoções especiais</label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>

                                    </form>
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