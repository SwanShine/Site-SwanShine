<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_email'])) {
    header('Location: ../../home/forms/login/login.html');
    exit();
}

// Dados de conexão com o banco de dados
$servername = "swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com";
$username = "admin";
$password = "gLAHqWkvUoaxwBnm9wKD";
$dbname = "swanshine";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão falhou
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o email da sessão
$email = $_SESSION['user_email'];

// Função para obter os dados do cliente
function getClientData($conn, $email) {
    $stmt = $conn->prepare("SELECT nome, telefone, cep FROM clientes WHERE email = ?"); // Alterado de 'endereco' para 'cep'
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        echo "Cliente não encontrado.";
        exit();
    }
}

// Obtém os dados do cliente
$cliente = getClientData($conn, $email);

// Acessa os dados do cliente
$nome = $cliente['nome'];
$telefone = $cliente['telefone'];
$cep = $cliente['cep']; // Agora referenciando o campo 'cep'

// Fecha a conexão
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Swan Shine - Cliente</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />

    <!-- Favicons -->
    <link href="../../assets/img/favicon.png" rel="icon" />
    <link href="../../assets/img/favicon.png" rel="apple-touch-icon" />

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />

    <!-- Vendor CSS Files -->
    <link
      href="../../assets/vendor/bootstrap/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link href="../../assets/vendor/aos/aos.css" rel="stylesheet" />
    <link
      href="../../assets/vendor/glightbox/css/glightbox.min.css"
      rel="stylesheet"
    />
    <link
      href="../../assets/vendor/swiper/swiper-bundle.min.css"
      rel="stylesheet"
    />
    <link
      href="../../assets/vendor/boxicons/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link href="../../assets/vendor/quill/quill.snow.css" rel="stylesheet" />
    <link href="../../assets/vendor/quill/quill.bubble.css" rel="stylesheet" />
    <link href="../../assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link
      href="../../assets/vendor/simple-datatables/style.css"
      rel="stylesheet"
    />

    <!-- Custom CSS -->
    <link href="../../assets/css/main.css" rel="stylesheet" />
    <link href="../../assets/css/style.css" rel="stylesheet" />
    <link href="../../assets/css/services.css" rel="stylesheet" />

    <!--Links JS-->
    <script src="https://cdn.jsdelivr.net/npm/inputmask/dist/inputmask.min.js"></script>

    <style>
      /* Estilos Gerais */

      /* Estiliza os contêineres das seções, inicialmente escondidos */
      .section-container {
        display: none; /* Oculta a seção por padrão */
        position: relative; /* Permite o posicionamento relativo dos elementos internos */
        padding: 20px; /* Espaçamento interno */
        background-color: #fff; /* Cor de fundo branca */
        border-radius: 10px; /* Bordas arredondadas */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra suave */
      }

      /* Torna a seção visível quando a classe 'active' é adicionada */
      .active {
        display: block; /* Exibe a seção */
      }

      /* Estiliza a barra de progresso */
      .progress-bar {
        position: relative; /* Permite o posicionamento relativo da barra de progresso interna */
        height: 10px; /* Altura da barra de progresso */
        background: #f0f0f0; /* Cor de fundo da barra */
        border-radius: 5px; /* Bordas arredondadas */
        margin-bottom: 20px; /* Espaçamento inferior */
      }

      /* Estiliza a parte preenchida da barra de progresso */
      .progress {
        height: 100%; /* Altura total da barra de progresso */
        width: 0; /* Largura inicial da parte preenchida (ajustada dinamicamente) */
        background: #ff66c4; /* Cor de fundo da parte preenchida */
        border-radius: 5px; /* Bordas arredondadas */
        transition: width 0.3s ease; /* Transição suave para a mudança de largura */
      }

      /* Estiliza o contêiner do formulário */
      .form-container {
        text-align: center; /* Centraliza o texto dentro do contêiner */
      }

      /* Estiliza o título dentro do contêiner do formulário */
      .form-container h2 {
        margin-bottom: 10px; /* Espaçamento inferior */
      }

      /* Estiliza a lista de serviços dentro do contêiner do formulário */
      .form-container .service-list {
        margin-bottom: 20px; /* Espaçamento inferior */
      }

      /* Estiliza a seção de botões dentro do contêiner do formulário */
      .form-container .buttons {
        margin-top: 20px; /* Espaçamento superior */
        display: flex; /* Utiliza Flexbox para layout dos botões */
      }

      /* Estiliza os botões dentro da seção de botões */
      .form-container .buttons button {
        padding: 10px 20px; /* Espaçamento interno do botão */
        border: none; /* Remove a borda padrão */
        background-color: #ffbd59; /* Cor de fundo laranja */
        color: #fff; /* Cor do texto */
        border-radius: 5px; /* Bordas arredondadas */
        cursor: pointer; /* Cursor em forma de mão para indicar clicável */
        margin-right: 10px; /* Espaçamento à direita entre os botões */
      }

      /* Remove a margem direita do último botão */
      .form-container .buttons button:last-child {
        margin-right: 0; /* Remove a margem direita do último botão */
      }

      /* Estiliza o texto de rodapé dentro do contêiner do formulário */
      .form-container .footer-text {
        margin-top: 20px; /* Espaçamento superior */
        color: #777; /* Cor do texto cinza claro */
      }

      /* Estiliza as dicas informativas dentro do contêiner do formulário */
      .form-container .hint {
        margin-top: 10px; /* Espaçamento superior */
        color: #000; /* Cor do texto cinza */
      }

      /* Estiliza o contêiner de Call to Action (CTA) */
      .cta-container {
        text-align: center; /* Centraliza o texto dentro do contêiner */
        margin-bottom: 20px; /* Espaçamento inferior */
      }

      /* Estiliza o título da seção de CTA */
      .cta-title {
        font-size: 24px; /* Tamanho da fonte */
        margin-bottom: 10px; /* Espaçamento inferior */
      }

      /* Estiliza o subtítulo da seção de CTA */
      .cta-subtitle {
        font-size: 18px; /* Tamanho da fonte */
        margin-bottom: 20px; /* Espaçamento inferior */
      }

      /* Estiliza o botão da seção de CTA */
      .cta-button {
        padding: 10px 20px; /* Espaçamento interno do botão */
        border: none; /* Remove a borda padrão */
        background-color: #ff66c4; /* Cor de fundo verde */
        color: #fff; /* Cor do texto */
        border-radius: 5px; /* Bordas arredondadas */
        text-decoration: none; /* Remove o sublinhado */
        font-size: 16px; /* Tamanho da fonte */
      }

      /* Estiliza os campos de entrada e área de texto */
      input[type="text"],
      input[type="tel"],
      input[type="email"],
      textarea,
      select {
        width: 100%; /* Largura total */
        padding: 10px; /* Espaçamento interno */
        border: 1px solid #ddd; /* Borda cinza clara */
        border-radius: 5px; /* Bordas arredondadas */
        margin-top: 5px; /* Espaçamento superior */
        box-sizing: border-box; /* Inclui padding e border na largura total */
      }

      /* Estiliza o texto do placeholder dos campos de entrada */
      input[type="text"]::placeholder,
      input[type="tel"]::placeholder,
      input[type="email"]::placeholder,
      textarea::placeholder {
        color: #aaa; /* Cor do texto do placeholder */
      }

      .card-container {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background-color: #ff66c4;
      }

      .card-item {
        margin-bottom: 10px;
      }
    </style>
  </head>

  <body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
      <div class="d-flex align-items-center justify-content-between">
        <a href="../../index.php" class="logo d-flex align-items-center">
          <img src="../../assets/img/logo_preta.png" alt="" />
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
              class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications"
            >
              <li class="dropdown-header">
                Você tem 0 notificações
                <a href="#"
                  ><span class="badge rounded-pill bg-primary p-2 ms-2"
                    >Ver todas</span
                  ></a
                >
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
              class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages"
            >
              <li class="dropdown-header">
                Você tem 0 mensagens
                <a href="../../mensagem.php"
                  ><span class="badge rounded-pill bg-primary p-2 ms-2"
                    >Ver todas</span
                  ></a
                >
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li class="dropdown-footer">
                <a href="../../mensagem.php">Mostrar todas as mensagens</a>
              </li>
            </ul>
          </li>

          <!-- Profile Dropdown -->
          <li class="nav-item dropdown pe-3">
            <a
              class="nav-link nav-profile d-flex align-items-center pe-0"
              href="../../perfil.php"
              data-bs-toggle="dropdown"
            >
              <img
                src="../../assets/img/usuario.png"
                alt="Profile"
                class="rounded-circle"
              />
            </a>

            <ul
              class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile"
            >
              <li>
                <a
                  class="dropdown-item d-flex align-items-center"
                  href="../../perfil.php"
                >
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
                  href="../../perfil.php"
                >
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
                  href="../../suporte.php"
                >
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
                  href="../../form/log_out.php"
                >
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
          <a class="nav-link collapsed" href="../../index.php">
            <i class="bi bi-grid"></i>
            <span>Início</span>
          </a>
        </li>

        <li class="nav-item">
          <a
            class="nav-link collapsed"
            data-bs-target="#components-nav"
            data-bs-toggle="collapse"
            href="#"
          >
            <i class="bi bi-menu-button-wide"></i><span>Serviços</span
            ><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul
            id="components-nav"
            class="nav-content collapse"
            data-bs-parent="#sidebar-nav"
          >
            <li>
              <a href="../../servicos.php"
                ><i class="bi bi-circle"></i><span>Contrate o Serviço</span></a
              >
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a
            class="nav-link collapsed"
            data-bs-target="#components-nav"
            data-bs-toggle="collapse"
            href="#"
          >
            <i class="bi bi-menu-button-wide"></i><span>Pedidos</span
            ><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul
            id="components-nav"
            class="nav-content collapse"
            data-bs-parent="#sidebar-nav"
          >
            <li>
              <a href="../../pedidos/pedido_pendente.php"
                ><i class="bi bi-circle"></i><span>Pedidos Pendentes</span></a
              >
            </li>
            <li>
              <a href="../../pedidos/pedido_andamento.php"
                ><i class="bi bi-circle"></i
                ><span>Pedidos Em Andamento</span></a
              >
            </li>
            <li>
              <a href="../../pedidos/pedido_excluido.php"
                ><i class="bi bi-circle"></i><span>Pedidos Excluidos</span></a
              >
            </li>
            <li>
              <a href="../../pedidos/pedido_concluido.php"
                ><i class="bi bi-circle"></i><span>Pedidos Concluidos</span></a
              >
            </li>
          </ul>
        </li>

        <!-- Perfil -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="../../perfil.php">
            <i class="bi bi-person"></i>
            <span>Perfil</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link collapsed" href="../../suporte.php">
            <i class="bi bi-chat-dots"></i>
            <span>Suporte</span>
          </a>
        </li>
      </ul>
    </aside>
    <!-- End Sidebar-->

    <!-- Main Content -->
    <main id="main" class="main">
      <div class="pagetitle">
        <h1>Contrate seu Serviço</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../../index.html">Home</a></li>
            <li class="breadcrumb-item">Serviço</li>
            <li class="breadcrumb-item active">Contrate o Serviço</li>
          </ol>
        </nav>
      </div>

      <!-- Services Section -->
      <section class="section">
        <div class="main-container">
          <!-- Seção de CTA -->
          <div class="cta-container">
            <div class="cta-title">Você é Designer de Unhas?</div>
            <div class="cta-subtitle">
              A Swan Shine recebe mais de 2 mil pedidos por mês e pode ajudar a
              aumentar sua renda
            </div>
            <a
              href="../../../../forms/form_profissional/form_profissional.html"
              class="cta-button"
              title="Cadastre-se como Designer de Unhas"
              >Quero me cadastrar</a
            >
          </div>

          <form action="form.php" method="POST">
            <!-- Seção do Formulário 1 -->
            <div id="step-1" class="section-container form-container active">
              <div class="progress-bar">
                <div class="progress" id="progress-bar"></div>
              </div>
              
              <h2>Qual tipo de serviço de unhas você procura?</h2>
              <div class="service-list">
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Manicure"
                    required
                  />
                  Manicure</label
                >
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Pedicure"
                    required
                  />
                  Pedicure</label
                >
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Design de Unhas em Gel"
                    required
                  />
                  Design de Unhas em Gel</label
                >
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Design de Unhas Acrílicas"
                    required
                  />
                  Design de Unhas Acrílicas</label
                >
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Alongamento de Unhas"
                    required
                  />
                  Alongamento de Unhas</label
                >
                <label
                  ><input
                    type="checkbox"
                    name="tipo"
                    value="Outros"
                    required
                  />
                  Outros</label
                >
              </div>
              <div class="buttons">
                <button onclick="nextStep()">Continuar</button>
              </div>
              
            </div>

            <!-- Seção do Formulário 2 -->
            <div id="step-2" class="section-container form-container">
              <div class="progress-bar">
                <div class="progress" id="progress-bar"></div>
              </div>
              
              <h2>Qual o estilo desejado para as unhas?</h2>
              <div class="service-list">
                <label
                  ><input
                    type="radio"
                    name="estilo"
                    value="Simples"
                    required
                  />
                  Simples</label
                >
                <label
                  ><input
                    type="radio"
                    name="estilo"
                    value="Decoradas"
                    required
                  />
                  Decoradas</label
                >
                <label
                  ><input
                    type="radio"
                    name="estilo"
                    value="Francesinha"
                    required
                  />
                  Francesinha</label
                >
                <label
                  ><input
                    type="radio"
                    name="estilo"
                    value="Arte em Unhas"
                    required
                  />
                  Arte em Unhas</label
                >
                <label
                  ><input
                    type="radio"
                    name="estilo"
                    value="Outros"
                    required
                  />
                  Outros</label
                >
              </div>
              <div class="buttons">
                <button onclick="prevStep()">Voltar</button>
                <button onclick="nextStep()">Continuar</button>
              </div>
              
            </div>

            <!-- Seção do Formulário 3 -->
          <div id="step-3" class="section-container form-container">
            <div class="progress-bar">
              <div class="progress" id="progress-bar-3"></div>
            </div>
            
            <h2>Onde você gostaria de ser atendido(a)?</h2>
            <div class="service-list">
              <label>
                <input type="radio" name="atendimento" value="Em Casa" required />
                Em casa
              </label>
              <label>
                <input type="radio" name="atendimento" value="Espaço Profissional" required />
                Espaço do Profissional
              </label>
              <label>
                <input type="radio" name="atendimento" value="Sem Preferencia" required />
                Sem Preferência
              </label>
            </div>
            <div class="buttons">
              <button type="button" onclick="prevStep()">Voltar</button>
              <button type="button" onclick="nextStep()">Continuar</button>
            </div>
            
          </div>
                        <!-- Seção do Formulário 4 -->
                        <div id="step-4" class="section-container form-container">
                        <div class="progress-bar">
                            <div class="progress" id="progress-bar-4"></div>
                        </div>
                        <h2>Qual a urgência do seu pedido?</h2>
                        <div class="service-list">
                            <select name="urgencia" id="urgencia" required>
                                <option value="" disabled selected>Selecione uma opção</option>
                                <option value="Urgente">Urgente</option>
                                <option value="8 horas">Próximas 8 horas</option>
                                <option value="12 horas">Próximas 12 horas</option>
                                <option value="2 dias">Próximas 2 dias</option>
                                <option value="Semana">Próxima Semana</option>
                                <option value="Não Urgente">Não Urgente</option>
                            </select>
                        </div>
                        <div class="buttons">
                            <button type="button" onclick="prevStep()">Voltar</button>
                            <button type="button" onclick="nextStep()">Continuar</button>
                        </div>
                        </div>
                        <!-- Seção do Formulário 5 -->
                        <div id="step-5" class="section-container form-container">
                                    <div class="progress-bar">
                                        <div class="progress" id="progress-bar-5"></div>
                                    </div>
                                    <h2>Explique o que você precisa</h2>
                                    <p>Você está indo bem, agora falta pouco!</p>
                                    <textarea
                                        name="detalhes"
                                        placeholder='Traga todos os detalhes do seu pedido. Tente "Preciso de..."'
                                        required
                                    ></textarea>
                                    <div class="hint">
                                        🛈 Quanto mais informações você fornecer, melhor será o seu pedido!
                                    </div>
                                    <div class="buttons">
                                        <button type="button" onclick="prevStep()">Voltar</button>
                                        <button type="button" onclick="nextStep()">Continuar</button>
                                    </div>
                        </div>
                        <!-- Seção do Formulário 6 -->
                        <div id="step-6" class="section-container form-container">
    <div class="progress-bar">
        <div class="progress" id="progress-bar-6"></div>
    </div>
    <h2>Informe seu CEP</h2>
    <p>Você está indo bem, agora falta pouco!</p>

    <label for="cep">CEP:</label>
    <input
        type="text"
        name="cep"
        id="cep"
        value="<?php echo htmlspecialchars($cep); ?>" 
        placeholder="Digite seu CEP"
        required
        pattern="\d{5}-\d{3}" 
        maxlength="10"
    />

    <div class="buttons">
        <button type="button" onclick="prevStep()">Voltar</button>
        <button type="button" onclick="nextStep()">Continuar</button>
    </div>
                        </div>
                        <!-- Seção do Formulário 7 -->
                        <div id="step-7" class="section-container form-container">
                            <div class="progress-bar">
                                <div class="progress" id="progress-bar-7"></div>
                            </div>
                            <h2>Seu Nome</h2>
                            <p>Seu nome foi recuperado automaticamente.</p>
                            <label for="nome">Nome:</label>
                            <input
                                type="text"
                                name="nome"
                                id="nome"
                                value="<?php echo htmlspecialchars($nome); ?>" 
                                readonly
                                placeholder="Digite seu Nome Completo"
                                required
                            />
                            <div class="buttons">
                                <button type="button" onclick="prevStep()">Voltar</button>
                                <button type="button" onclick="nextStep()">Continuar</button>
                            </div>
                        </div>
                        <!-- Seção do Formulário 8 -->
                        <div id="step-8" class="section-container form-container">
                            <div class="progress-bar">
                                <div class="progress" id="progress-bar-8"></div>
                            </div>
                            <h2>Informe seu E-mail</h2>
                            <p>Você está indo bem, agora falta pouco!</p>
                            <label for="email">E-mail:</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="<?php echo htmlspecialchars($email); ?>" 
                                placeholder="Digite seu E-mail"
                                required
                                readonly
                            />
                            <div class="buttons">
                                <button type="button" onclick="prevStep()">Voltar</button>
                                <button type="button" onclick="nextStep()">Continuar</button>
                            </div>
                        </div>
                        <!-- Seção do Formulário 9 -->
                        <div id="step-9" class="section-container form-container">
    <div class="progress-bar">
        <div class="progress" id="progress-bar-9"></div>
    </div>
    <h2>Informe seu Telefone</h2>
    <p>Você está indo bem, agora falta pouco!</p>
    <label for="telefone">Telefone:</label>
    <input
        type="text"
        name="telefone"
        id="telefone"
        value="<?php echo htmlspecialchars($telefone); ?>" 
        placeholder="Digite seu Telefone"
        required
        pattern="\(\d{2}\) \d{5}-\d{4}"
        readonly
    />
    <div class="buttons">
        <button type="button" onclick="prevStep()">Voltar</button>
        <button type="button" onclick="nextStep()">Continuar</button>
    </div>
                        </div>
                        <!-- Seção do Formulário 10 -->
                        <div id="step-10" class="section-container form-container">
                                    <div class="progress-bar">
                                        <div class="progress" id="progress-bar-10"></div>
                                    </div>
                                    <h2>Deseja enviar as informações?</h2>
                                    <p>Você está quase lá! Revise seus dados antes de enviar.</p>
                                    <div id="summary-container" style="display: none"></div>
                                    <!-- Para mostrar um resumo das informações -->
                                    <div class="buttons">
                                        <button type="button" onclick="prevStep()">Voltar</button>
                                        <button type="button" onclick="submitForm()">Enviar</button>
                                    </div>
                        </div>
                </form>

          <script>
            // Variáveis globais
            let currentStep = 0;
            const steps = document.querySelectorAll(".section-container");
            const progressBars = document.querySelectorAll(".progress");

            function showStep(step) {
              // Oculta todas as seções do formulário
              steps.forEach((section, index) => {
                section.style.display = index === step ? "block" : "none";
              });

              // Atualiza a barra de progresso
              const progressPercent = ((step + 1) / steps.length) * 100;
              progressBars.forEach((progress) => {
                progress.style.width = `${progressPercent}%`;
              });

              // Atualiza o resumo das informações na última seção
              if (step === steps.length - 1) {
                updateSummary();
              }
            }

            function nextStep() {
              // Valida se todos os campos obrigatórios estão preenchidos
              if (validateCurrentStep()) {
                currentStep++;
                if (currentStep >= steps.length) {
                  currentStep = steps.length - 1; // Impede que o índice ultrapasse o número de etapas
                }
                showStep(currentStep);
              }
            }

            function prevStep() {
              if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
              }
            }

            function validateCurrentStep() {
              const currentInputs = steps[currentStep].querySelectorAll(
                "input, select, textarea"
              );
              let isValid = true;
              currentInputs.forEach((input) => {
                if (input.required && !input.value) {
                  isValid = false;
                  input.classList.add("input-error"); // Adiciona classe para estilizar o erro
                } else {
                  input.classList.remove("input-error"); // Remove classe se o campo está válido
                }
              });
              return isValid;
            }

            function updateSummary() {
              const summaryContainer =
                document.getElementById("summary-container");
              summaryContainer.innerHTML = ""; // Limpa o conteúdo anterior

              // Coleta informações dos campos e gera um resumo
              const fields = [
                { name: "tipo", label: "Tipo de Serviço" },
                { name: "estilo", label: "Estilo Desejado" },
                { name: "atendimento", label: "Local de Atendimento" },
                { name: "urgencia", label: "Urgência do Pedido" },
                { name: "detalhes", label: "Detalhes do Pedido" },
                { name: "cep", label: "CEP" },
                { name: "nome", label: "Nome" },
                { name: "email", label: "E-mail" },
                { name: "telefone", label: "Telefone" },
              ];

              fields.forEach((field) => {
                const inputValue = document.querySelector(
                  `[name="${field.name}"]`
                );
                if (inputValue) {
                  let value = inputValue.value;
                  if (Array.isArray(value)) {
                    value = value.join(", "); // Para checkboxes, junta os valores
                  }
                  summaryContainer.innerHTML += `<p><strong>${field.label}:</strong> ${value}</p>`;
                }
              });

              summaryContainer.style.display = "block"; // Exibe o resumo
            }

            function submitForm() {
              // Aqui você pode fazer mais validações ou processar o envio
              const form = document.querySelector("form");
              if (validateCurrentStep()) {
                form.submit(); // Envia o formulário
              }
            }

            // Inicializa o formulário mostrando o primeiro passo
            showStep(currentStep);

            document
              .querySelector('[name="cep"]')
              .addEventListener("input", function (e) {
                let value = e.target.value.replace(/\D/g, ""); // Remove tudo que não for dígito
                if (value.length > 5) {
                  value = value.slice(0, 5) + "-" + value.slice(5, 8);
                }
                e.target.value = value;
              });

            document
              .querySelector('[name="telefone"]')
              .addEventListener("input", function (e) {
                let value = e.target.value.replace(/\D/g, ""); // Remove tudo que não for dígito
                if (value.length > 10) {
                  value =
                    "(" +
                    value.slice(0, 2) +
                    ") " +
                    value.slice(2, 7) +
                    "-" +
                    value.slice(7, 11);
                } else if (value.length > 5) {
                  value =
                    "(" +
                    value.slice(0, 2) +
                    ") " +
                    value.slice(2, 6) +
                    "-" +
                    value.slice(6, 10);
                }
                e.target.value = value;
              });
          </script>
        </div>
      </section>
    </main>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-md-6 footer-info">
            <h3>Swan Shine</h3>
            <p class="footer-text">
              © Swan Shine 2024. Todos os direitos reservados.
            </p>
          </div>
        </div>
      </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/vendor/aos/aos.js"></script>
    <script src="../../assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="../../assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="../../assets/vendor/php-email-form/validate.js"></script>
    <script src="../../assets/vendor/quill/quill.min.js"></script>
    <script src="../../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../../assets/vendor/php-email-form/validate.js"></script>

    <!-- Main JavaScript -->
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/main1.js"></script>
  </body>
</html>
