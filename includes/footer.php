    </main> <!-- End .app-main -->
</div> <!-- End .app-container -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ── Theme Toggle ─────────────────────────────────────────────
const themeToggleBtn = document.getElementById('themeToggleBtn');
const themeIcon = document.getElementById('themeIcon');

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('gbu-theme', theme);
    if (themeIcon) {
        if (theme === 'light') {
            themeIcon.className = 'fas fa-moon';
            if (themeToggleBtn) themeToggleBtn.title = 'Switch to Dark Mode';
        } else {
            themeIcon.className = 'fas fa-sun';
            if (themeToggleBtn) themeToggleBtn.title = 'Switch to Light Mode';
        }
    }
}

// Init icon state on load
(function() {
    const saved = localStorage.getItem('gbu-theme') || 'dark';
    applyTheme(saved);
})();

if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function() {
        const current = document.documentElement.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
}
</script>

<script src="js/main.js"></script>
</body>
</html>
