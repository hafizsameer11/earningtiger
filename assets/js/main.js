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

    var navToggle = document.querySelector('.nav-toggle');
    var mainNav = document.querySelector('.main-nav');

    function closeNav() {
        document.body.classList.remove('nav-open');
    }

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function () {
            document.body.classList.toggle('nav-open');
        });

        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeNav);
        });

        document.addEventListener('click', function (e) {
            if (!document.body.classList.contains('nav-open')) return;
            if (!mainNav.contains(e.target) && !navToggle.contains(e.target)) {
                closeNav();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) closeNav();
        });
    }
});
