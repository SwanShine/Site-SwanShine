// script.js

document.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('popup');
    var openPopup = document.getElementById('openPopup');
    var closePopup = document.querySelector('.close');
    var providerBtn = document.getElementById('providerBtn');
    var clientBtn = document.getElementById('clientBtn');

    openPopup.addEventListener('click', function() {
        popup.style.display = 'flex';
    });

    closePopup.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == popup) {
            popup.style.display = 'none';
        }
    });

    providerBtn.addEventListener('click', function() {
        window.location.href = '../forms/form_profissional/formteste.html';
        popup.style.display = 'none';
    });

    clientBtn.addEventListener('click', function() {
        window.location.href = '../forms/form_cliente/index.html';
        popup.style.display = 'none';
    });
});
