var formSignin = document.querySelector('#signin')
var formSignup = document.querySelector('#signup')
var btnColor = document.querySelector('.btnColor')

document.querySelector('#btnSignin')
  .addEventListener('click', () => {
    formSignin.style.left = "25px"
    formSignup.style.left = "450px"
    btnColor.style.left = "0px"
})

document.querySelector('#btnSignup')
  .addEventListener('click', () => {
    formSignin.style.left = "-450px"
    formSignup.style.left = "25px"
    btnColor.style.left = "110px"
})

function saveAndRedirect() {
  // Capturar os valores do primeiro formulário
  const email = document.getElementById('email1').value;
  const password = document.getElementById('password1').value;

  // Salvar os valores no localStorage
  localStorage.setItem('email1', email);
  localStorage.setItem('password1', password);

  // Redirecionar para o segundo formulário
  window.location.href = 'form2.html';
}

document.addEventListener('DOMContentLoaded', () => {
  // Verifica se está na página do segundo formulário
  if (window.location.pathname.includes('form2.html')) {
      // Recuperar os valores do localStorage
      const email = localStorage.getItem('email1');
      const password = localStorage.getItem('password1');

      // Preencher o segundo formulário com os valores recuperados
      if (email) document.getElementById('email2').value = email;
      if (password) {
          document.getElementById('password2').value = password;
          document.getElementById('confirmPassword').value = password;
      }
  }
});