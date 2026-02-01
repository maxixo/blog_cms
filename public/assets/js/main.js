document.addEventListener('DOMContentLoaded', function () {
    var backToTop = document.querySelector('.back-to-top');
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (backToTop) {
        var toggleBackToTop = function () {
            if (window.scrollY > 400) {
                backToTop.classList.add('is-visible');
                backToTop.setAttribute('aria-hidden', 'false');
            } else {
                backToTop.classList.remove('is-visible');
                backToTop.setAttribute('aria-hidden', 'true');
            }
        };

        toggleBackToTop();
        window.addEventListener('scroll', toggleBackToTop, { passive: true });

        backToTop.addEventListener('click', function () {
            var behavior = prefersReducedMotion.matches ? 'auto' : 'smooth';
            window.scrollTo({ top: 0, behavior: behavior });
        });
    }

    var forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                return;
            }

            var submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton || submitButton.dataset.loading === 'true') {
                return;
            }

            var loadingText = submitButton.dataset.loadingText || 'Loading...';
            submitButton.dataset.originalText = submitButton.textContent;
            submitButton.textContent = loadingText;
            submitButton.dataset.loading = 'true';
            submitButton.classList.add('is-loading');
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.disabled = true;
        });
    });

    // User Dropdown Menu Toggle
    var userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        var userAvatar = userMenu.querySelector('.user-avatar');
        var userDropdown = userMenu.querySelector('.user-dropdown');

        if (userAvatar && userDropdown) {
            // Toggle dropdown when avatar is clicked
            userAvatar.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = userDropdown.classList.contains('is-open');
                
                if (isOpen) {
                    userDropdown.classList.remove('is-open');
                    userAvatar.classList.remove('is-open');
                    userAvatar.setAttribute('aria-expanded', 'false');
                } else {
                    userDropdown.classList.add('is-open');
                    userAvatar.classList.add('is-open');
                    userAvatar.setAttribute('aria-expanded', 'true');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!userMenu.contains(e.target)) {
                    userDropdown.classList.remove('is-open');
                    userAvatar.classList.remove('is-open');
                    userAvatar.setAttribute('aria-expanded', 'false');
                }
            });

            // Close dropdown when clicking on a dropdown link
            var dropdownLinks = userDropdown.querySelectorAll('.user-dropdown-link');
            dropdownLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    userDropdown.classList.remove('is-open');
                    userAvatar.classList.remove('is-open');
                    userAvatar.setAttribute('aria-expanded', 'false');
                });
            });
        }
    }
});
