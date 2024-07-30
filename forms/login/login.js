document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('popup');
    const cadastreSeLink = document.getElementById('cadastre-se-link');
    const closeBtn = document.querySelector('.close-btn');

    cadastreSeLink.addEventListener('click', function(event) {
        event.preventDefault();
        popup.style.display = 'block';
    });

    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == popup) {
            popup.style.display = 'none';
        }
    });
});
