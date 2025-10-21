        </main>
        <!-- Fin du main-content -->

        <!-- Footer minimaliste -->
        <footer class="footer">
            <div class="footer-content">
                <p class="footer-text">
                    &copy; <?= date('Y') ?> MonVolEnLigne - Espace Agence
                </p>
                <div class="footer-links">
                    <a href="/app/landing/politique-confidentialite.php" class="footer-link">Confidentialité</a>
                    <span class="footer-separator">•</span>
                    <a href="/app/landing/conditions-utilisation.php" class="footer-link">Conditions</a>
                    <span class="footer-separator">•</span>
                    <a href="/app/landing/contact.php" class="footer-link">Support</a>
                </div>
            </div>
        </footer>

        </div>
        <!-- Fin du main-wrapper -->

        <!-- JavaScript pour les dropdowns -->
        <script>
            // Toggle dropdown menu
            function toggleDropdown(dropdownId) {
                const dropdown = document.getElementById(dropdownId);
                const allDropdowns = document.querySelectorAll('.dropdown');

                // Fermer les autres dropdowns
                allDropdowns.forEach(d => {
                    if (d.id !== dropdownId) {
                        d.classList.remove('active');
                    }
                });

                // Toggle le dropdown actuel
                dropdown.classList.toggle('active');
            }

            // Fermer les dropdowns au clic extérieur
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown').forEach(d => {
                        d.classList.remove('active');
                    });
                }
            });

            // Empêcher la propagation du clic dans le dropdown
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });
        </script>

        </body>

        </html>