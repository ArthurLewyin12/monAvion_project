    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> MonVolEnLigne - Tous droits réservés</p>
    </footer>
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('.dropdown');

            allDropdowns.forEach(d => {
                if (d.id !== id) {
                    d.classList.remove('active');
                }
            });

            dropdown.classList.toggle('active');
        }

        // Fermer les dropdowns en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            const isDropdown = event.target.closest('.dropdown');
            if (!isDropdown) {
                document.querySelectorAll('.dropdown').forEach(d => {
                    d.classList.remove('active');
                });
            }
        });
    </script>

    </body>

    </html>