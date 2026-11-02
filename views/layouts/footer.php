            </div> <!-- End content-body -->
        </main>
    </div> <!-- End app-container -->

    <script>
        // Common interactive helper scripts
        function confirmAction(message) {
            return confirm(message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?');
        }

        // Collapsible Sidebar Controller (Single Chevron Toggle)
        (function() {
            const body = document.body;
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

            function updateToggleTooltip(collapsed) {
                if (sidebarToggleBtn) {
                    sidebarToggleBtn.setAttribute('title', collapsed ? 'Buka Sidebar' : 'Tutup Sidebar');
                    sidebarToggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                }
            }

            // Restore state from localStorage
            const isSmallScreen = window.innerWidth <= 768;
            const savedState = localStorage.getItem('sistem_mel_sidebar_collapsed');
            
            if (savedState !== null) {
                const shouldCollapse = savedState === 'true';
                if (shouldCollapse) {
                    body.classList.add('sidebar-collapsed');
                    body.classList.remove('sidebar-expanded');
                } else {
                    body.classList.remove('sidebar-collapsed');
                    body.classList.add('sidebar-expanded');
                }
                updateToggleTooltip(shouldCollapse);
            } else if (isSmallScreen) {
                body.classList.add('sidebar-collapsed');
                updateToggleTooltip(true);
            } else {
                updateToggleTooltip(false);
            }

            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const currentlyCollapsed = body.classList.contains('sidebar-collapsed') || 
                        (window.innerWidth <= 768 && !body.classList.contains('sidebar-expanded'));

                    if (currentlyCollapsed) {
                        body.classList.remove('sidebar-collapsed');
                        body.classList.add('sidebar-expanded');
                        localStorage.setItem('sistem_mel_sidebar_collapsed', 'false');
                        updateToggleTooltip(false);
                    } else {
                        body.classList.add('sidebar-collapsed');
                        body.classList.remove('sidebar-expanded');
                        localStorage.setItem('sistem_mel_sidebar_collapsed', 'true');
                        updateToggleTooltip(true);
                    }
                });
            }
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



