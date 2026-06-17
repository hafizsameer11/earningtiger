document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.receipt-thumb').forEach(function (img) {
        img.addEventListener('click', function () {
            window.open(this.src, '_blank');
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
