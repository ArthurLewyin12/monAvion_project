// assets/js/app.js
document.addEventListener('DOMContentLoaded', () => {

    // == Animation du header au scroll ==
    const header = document.querySelector('.main-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) { // On ajoute la classe après 50px de scroll
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        });
    }


    // == Animation d'apparition des sections au scroll ==
    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if (animatedElements.length) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target); // On arrête d'observer l'élément une fois animé
                }
            });
        }, {
            rootMargin: '0px',
            threshold: 0.1 // Déclenche l'animation quand 10% de l'élément est visible
        });

        animatedElements.forEach(element => {
            observer.observe(element);
        });
    }

    // == Menu dropdown pour le mobile ==
    const menuToggleButton = document.querySelector('.menu-toggle-button');
    const mainNavList = document.querySelector('#main-navigation');

    if (menuToggleButton && mainNavList) {
        menuToggleButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Empêche le clic de se propager au document
            const isOpen = mainNavList.classList.toggle('is-open');
            menuToggleButton.setAttribute('aria-expanded', isOpen);
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', (event) => {
            if (mainNavList.classList.contains('is-open') && !mainNavList.contains(event.target)) {
                mainNavList.classList.remove('is-open');
                menuToggleButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
