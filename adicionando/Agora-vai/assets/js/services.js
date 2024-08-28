// form-handler.js

document.addEventListener('DOMContentLoaded', function() {
    // Seletor das seções do formulário
    const sections = document.querySelectorAll('.section-container');
    let currentSection = 0;
  
    // Função para mostrar a seção atual e atualizar a barra de progresso
    function showSection(index) {
      sections.forEach((section, i) => {
        section.style.display = i === index ? 'block' : 'none';
      });
      // Atualiza a barra de progresso
      const progress = document.querySelector('.progress');
      progress.style.width = `${(index / (sections.length - 1)) * 100}%`;
    }
  
    // Função para validar a seção atual
    function validateForm(section) {
      let isValid = true;
  
      // Exemplo de validação: Verifica campos obrigatórios
      const inputs = section.querySelectorAll('input, textarea');
      inputs.forEach(input => {
        if (input.hasAttribute('required') && !input.value.trim()) {
          alert(`Por favor, preencha o campo ${input.name}`);
          isValid = false;
        }
      });
  
      return isValid;
    }
  
    // Mostra a primeira seção ao carregar
    showSection(currentSection);
  
    // Adiciona eventos aos botões de navegação
    document.querySelectorAll('.next-button').forEach(button => {
      button.addEventListener('click', function() {
        if (validateForm(sections[currentSection])) {
          currentSection++;
          if (currentSection >= sections.length) {
            currentSection = sections.length - 1;
          }
          showSection(currentSection);
        }
      });
    });
  
    document.querySelectorAll('.back-button').forEach(button => {
      button.addEventListener('click', function() {
        currentSection--;
        if (currentSection < 0) {
          currentSection = 0;
        }
        showSection(currentSection);
      });
    });
  });
  