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
    var navOverlay = document.querySelector('.nav-overlay');

    function setNavOpen(open) {
        document.body.classList.toggle('nav-open', open);
        if (navToggle) {
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        }
        document.body.style.overflow = open ? 'hidden' : '';
    }

    function closeNav() {
        setNavOpen(false);
    }

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            setNavOpen(!document.body.classList.contains('nav-open'));
        });

        if (navOverlay) {
            navOverlay.addEventListener('click', closeNav);
        }

        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeNav);
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeNav();
            }
        });
    }

    document.querySelectorAll('[data-slider]').forEach(function (slider) {
        var track = slider.querySelector('.slider-track');
        var slides = slider.querySelectorAll('.slider-slide');
        var prevBtn = slider.querySelector('.slider-prev');
        var nextBtn = slider.querySelector('.slider-next');
        var dotsWrap = slider.querySelector('[data-slider-dots]');
        var index = 0;
        var total = slides.length;

        if (!track || total === 0) return;

        function goTo(i) {
            index = (i + total) % total;
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            if (dotsWrap) {
                dotsWrap.querySelectorAll('.slider-dot').forEach(function (dot, di) {
                    dot.classList.toggle('active', di === index);
                });
            }
        }

        if (dotsWrap) {
            for (var d = 0; d < total; d++) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'slider-dot' + (d === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to slide ' + (d + 1));
                (function (di) {
                    dot.addEventListener('click', function () { goTo(di); });
                })(d);
                dotsWrap.appendChild(dot);
            }
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(index - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(index + 1); });

        var touchStartX = 0;
        slider.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        slider.addEventListener('touchend', function (e) {
            var diff = e.changedTouches[0].screenX - touchStartX;
            if (Math.abs(diff) > 50) {
                goTo(diff < 0 ? index + 1 : index - 1);
            }
        }, { passive: true });

        if (total > 1) {
            setInterval(function () { goTo(index + 1); }, 5000);
        } else {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
        }
    });
});
