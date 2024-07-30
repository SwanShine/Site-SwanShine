// Selecionar o formulário e a tabela
const form = document.getElementById('formServico');
const tabelaServicos = document.getElementById('tabelaServicos').getElementsByTagName('tbody')[0];

// Adicionar um ouvinte de evento para o envio do formulário
form.addEventListener('submit', function(event) {
  event.preventDefault(); // Prevenir o envio padrão do formulário

  // Coletar os dados do formulário
  const servico = document.getElementById('servico').value;
  const horario = document.getElementById('horario').value;
  const preco = document.getElementById('preco').value;
  const imagem = document.getElementById('imagem').files[0] ? document.getElementById('imagem').files[0].name : '';

  // Criar uma nova linha na tabela
  const novaLinha = tabelaServicos.insertRow();

  // Adicionar células à nova linha
  novaLinha.insertCell().textContent = servico;
  novaLinha.insertCell().textContent = horario;
  novaLinha.insertCell().textContent = preco;
  novaLinha.insertCell().textContent = imagem;

  // Adicionar botões de ação à nova linha
  const acoes = novaLinha.insertCell();
  acoes.innerHTML = `
    <button class="btn btn-primary btn-sm" onclick="editarServico(this)">Editar</button>
    <button class="btn btn-danger btn-sm" onclick="excluirServico(this)">Excluir</button>
  `;

  // Limpar o formulário após o envio
  form.reset();
});

// Função para editar um serviço
function editarServico(button) {
  const linha = button.parentElement.parentElement;
  const servico = linha.cells[0].textContent;
  const horario = linha.cells[1].textContent;
  const preco = linha.cells[2].textContent;

  // Preencher o formulário com os dados da linha selecionada
  document.getElementById('servico').value = servico;
  document.getElementById('horario').value = horario;
  document.getElementById('preco').value = preco;

  // Remover a linha da tabela
  tabelaServicos.deleteRow(linha.rowIndex);
}

// Função para excluir um serviço
function excluirServico(button) {
  if (confirm('Você tem certeza que deseja excluir este serviço?')) {
    const linha = button.parentElement.parentElement;
    tabelaServicos.deleteRow(linha.rowIndex);
  }
}
