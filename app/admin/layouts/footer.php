    </main>
</div>

<script>
// Gestion des dropdowns
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) return;

    const isOpen = dropdown.classList.contains('open');

    // Fermer tous les dropdowns
    document.querySelectorAll('.dropdown').forEach(d => {
        d.classList.remove('open');
    });

    // Ouvrir le dropdown ciblé si il était fermé
    if (!isOpen) {
        dropdown.classList.add('open');
    }
}

// Fermer les dropdowns en cliquant en dehors
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(d => {
            d.classList.remove('open');
        });
    }
});

// Confirmer les actions de suppression
document.querySelectorAll('[data-confirm]').forEach(button => {
    button.addEventListener('click', function(e) {
        const message = this.dataset.confirm;
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
