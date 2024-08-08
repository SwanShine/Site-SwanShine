// Aguarda o carregamento completo do DOM antes de executar o código
document.addEventListener('DOMContentLoaded', function() {
    // Seleciona o formulário e os campos do formulário pelo ID
    const form = document.querySelector('form');
    const email = document.getElementById('email');
    const repeatEmail = document.getElementById('repeat-email');
    const password = document.getElementById('password');
    const repeatPassword = document.getElementById('repeat-password');
    const cpf = document.getElementById('cpf');
    const cell = document.getElementById('cell');

    // Adiciona um ouvinte de evento para o envio do formulário
    form.addEventListener('submit', function(event) {
        let valid = true; // Inicializa a variável que indica se o formulário é válido

        // Verifica se os e-mails inseridos são iguais
        if (email.value !== repeatEmail.value) {
            alert('Os e-mails não coincidem.'); // Exibe uma mensagem de alerta se não coincidirem
            valid = false; // Define a variável valid como falsa
        }

        // Verifica se as senhas inseridas são iguais
        if (password.value !== repeatPassword.value) {
            alert('As senhas não coincidem.'); // Exibe uma mensagem de alerta se não coincidirem
            valid = false; // Define a variável valid como falsa
        }

        // Verifica se o CPF tem um formato válido
        if (!validateCPF(cpf.value)) {
            alert('CPF inválido.'); // Exibe uma mensagem de alerta se o CPF for inválido
            valid = false; // Define a variável valid como falsa
        }

        // Verifica se o número de celular tem um formato válido
        if (!validatePhone(cell.value)) {
            alert('Número de celular inválido.'); // Exibe uma mensagem de alerta se o celular for inválido
            valid = false; // Define a variável valid como falsa
        }

        // Se alguma validação falhar, impede o envio do formulário
        if (!valid) {
            event.preventDefault(); // Previne o envio do formulário
        }
    });

    // Função para validar o CPF com uma expressão regular simples
    function validateCPF(cpf) {
        // Verifica se o CPF está no formato XXX.XXX.XXX-XX
        return /^(\d{3}\.\d{3}\.\d{3}-\d{2})$/.test(cpf);
    }

    // Função para validar o número de celular com uma expressão regular simples
    function validatePhone(phone) {
        // Verifica se o telefone está no formato +XX (XX) XXXXX-XXXX
        return /^\+\d{2} \(\d{2}\) \d{5}-\d{4}$/.test(phone);
    }
});
