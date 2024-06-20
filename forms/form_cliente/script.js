document.addEventListener('DOMContentLoaded', function () {
    var cpfInput = document.getElementById('CPF');

    cpfInput.addEventListener('input', function () {
        var value = cpfInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos
        if (value.length > 3) {
            value = value.substring(0, 3) + '.' + value.substring(3);
        }
        if (value.length > 7) {
            value = value.substring(0, 7) + '.' + value.substring(7);
        }
        if (value.length > 11) {
            value = value.substring(0, 11) + '-' + value.substring(11);
        }
        cpfInput.value = value;
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var telInput = document.getElementById('telefone');

    telInput.addEventListener('input', function () {
        var value = telInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos

        // Formata o telefone de acordo com o tamanho do valor
        if (value.length === 11) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        } else if (value.length === 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
        } else if (value.length === 9) {
            value = value.replace(/^(\d{1})(\d{4})(\d{4})$/, '($1) $2-$3');
        } else if (value.length === 8) {
            value = value.replace(/^(\d{4})(\d{4})$/, '$1-$2');
        }

        telInput.value = value;
    });
});
