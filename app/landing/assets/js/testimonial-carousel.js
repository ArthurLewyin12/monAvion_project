// assets/js/testimonial-carousel.js

document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('testimonialCarousel');
    if (!carousel) return;

    const cards = carousel.querySelectorAll('.testimonial-card');
    const prevBtn = document.querySelector('.testimonial-prev');
    const nextBtn = document.querySelector('.testimonial-next');
    const dots = document.querySelectorAll('.testimonial-dot');

    let currentIndex = 0;
    let autoplayInterval;
    const AUTOPLAY_DELAY = 5000; // 5 secondes

    // Fonction pour afficher le témoignage à l'index donné
    function showTestimonial(index) {
        // Retirer la classe active de tous les témoignages
        cards.forEach(card => {
            card.classList.remove('testimonial-active');
        });

        // Retirer la classe active de tous les dots
        dots.forEach(dot => {
            dot.classList.remove('testimonial-dot-active');
        });

        // Ajouter la classe active au témoignage actuel
        cards[index].classList.add('testimonial-active');

        // Ajouter la classe active au dot correspondant
        if (dots[index]) {
            dots[index].classList.add('testimonial-dot-active');
        }

        currentIndex = index;
    }

    // Fonction pour aller au témoignage suivant
    function nextTestimonial() {
        const nextIndex = (currentIndex + 1) % cards.length;
        showTestimonial(nextIndex);
    }

    // Fonction pour aller au témoignage précédent
    function prevTestimonial() {
        const prevIndex = (currentIndex - 1 + cards.length) % cards.length;
        showTestimonial(prevIndex);
    }

    // Fonction pour démarrer l'autoplay
    function startAutoplay() {
        autoplayInterval = setInterval(nextTestimonial, AUTOPLAY_DELAY);
    }

    // Fonction pour arrêter l'autoplay
    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }

    // Événement bouton précédent
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevTestimonial();
            stopAutoplay();
            startAutoplay(); // Redémarre l'autoplay après interaction
        });
    }

    // Événement bouton suivant
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextTestimonial();
            stopAutoplay();
            startAutoplay(); // Redémarre l'autoplay après interaction
        });
    }

    // Événements pour les dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showTestimonial(index);
            stopAutoplay();
            startAutoplay(); // Redémarre l'autoplay après interaction
        });
    });

    // Gestion du clavier (accessibilité)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevTestimonial();
            stopAutoplay();
            startAutoplay();
        } else if (e.key === 'ArrowRight') {
            nextTestimonial();
            stopAutoplay();
            startAutoplay();
        }
    });

    // Pause l'autoplay quand on survole le carousel
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);

    // Pause l'autoplay quand l'onglet n'est pas visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    // Démarrer l'autoplay au chargement
    startAutoplay();

    // Support du swipe sur mobile
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const swipeThreshold = 50; // pixels minimum pour détecter un swipe

        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe vers la gauche -> suivant
            nextTestimonial();
            stopAutoplay();
            startAutoplay();
        }

        if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe vers la droite -> précédent
            prevTestimonial();
            stopAutoplay();
            startAutoplay();
        }
    }
});
