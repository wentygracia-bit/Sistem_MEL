            </div> <!-- End content-body -->
        </main>
    </div> <!-- End app-container -->

    <script>
        // Common interactive helper scripts
        function confirmAction(message) {
            return confirm(message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?');
        }

                // Collapsible Sidebar & Mobile Drawer Controller
        (function() {
            const body = document.body;
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const navbarToggleBtn = document.getElementById('navbarToggleBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            function updateToggleTooltip(collapsed) {
                if (sidebarToggleBtn) {
                    sidebarToggleBtn.setAttribute('title', collapsed ? 'Buka Sidebar' : 'Tutup Sidebar');
                    sidebarToggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                }
            }

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    body.classList.toggle('sidebar-mobile-open');
                } else {
                    const isCollapsed = body.classList.contains('sidebar-collapsed');
                    if (isCollapsed) {
                        body.classList.remove('sidebar-collapsed');
                        localStorage.setItem('sistem_mel_sidebar_collapsed', 'false');
                        updateToggleTooltip(false);
                    } else {
                        body.classList.add('sidebar-collapsed');
                        localStorage.setItem('sistem_mel_sidebar_collapsed', 'true');
                        updateToggleTooltip(true);
                    }
                }
            }

            function closeMobileSidebar() {
                body.classList.remove('sidebar-mobile-open');
            }

            // Restore desktop state
            if (window.innerWidth > 768) {
                const savedState = localStorage.getItem('sistem_mel_sidebar_collapsed');
                if (savedState === 'true') {
                    body.classList.add('sidebar-collapsed');
                    updateToggleTooltip(true);
                } else {
                    updateToggleTooltip(false);
                }
            }

            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            if (navbarToggleBtn) {
                navbarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    closeMobileSidebar();
                });
            }

            // Auto-close drawer on mobile when clicking nav links
            document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeMobileSidebar();
                    }
                });
            });
        })();
    </script>
    <?php if (isset($GLOBALS['app_start_time'])): 
        $execTime = round((microtime(true) - $GLOBALS['app_start_time']) * 1000, 2);
        $dbTime = isset($GLOBALS['db_connect_time']) ? round($GLOBALS['db_connect_time'] * 1000, 2) : 0;
    ?>
    <div style="position:fixed; bottom:12px; right:12px; background:rgba(15,23,42,0.9); color:#38bdf8; font-family:monospace; font-size:12px; padding:6px 12px; border-radius:6px; z-index:99999; box-shadow:0 4px 6px -1px rgba(0,0,0,0.2); border:1px solid #334155; pointer-events:none;">
        ? Total: <strong><?= $execTime ?> ms</strong> | DB: <strong><?= $dbTime ?> ms</strong>
    </div>
    <?php endif; ?>
</body>
</html>



