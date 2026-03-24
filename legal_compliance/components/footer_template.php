<?php
/**
 * Footer Template for Legal Compliance Views
 * Include this at the end of each view file, before closing HTML
 */

// Detect the current folder level based on PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path based on current folder
if ($currentFolder === 'views') {
    $basePath = '../../';
    $isInViews = true;
} else {
    // For files in root folders (like leave_management, etc.)
    $basePath = '../';
    $isInViews = false;
}
?>

                <!-- Additional content can go here before closing section -->
                </div>
            </section>
        </div>
    </div>

    <!-- REQUIRED SCRIPTS -->
    <script src="<?= $basePath ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= $basePath ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $basePath ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="<?= $basePath ?>assets/dist/js/adminlte.js"></script>
    <script src="<?= $basePath ?>assets/dist/js/time.js"></script>
    
    <!-- Preloader Fallback - Properly hide after page loads -->
    <script>
        (function() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                // Hide preloader after page fully loads
                window.addEventListener('load', function() {
                    // Small delay to ensure smooth transition
                    setTimeout(function() {
                        preloader.style.opacity = '0';
                        preloader.style.transition = 'opacity 0.3s ease';
                        // Remove from DOM after transition
                        setTimeout(function() {
                            preloader.style.display = 'none';
                            document.body.classList.remove('preloader');
                        }, 300);
                    }, 100);
                });
            }
        })();
    </script>
    
    <?php 
    // Output custom scripts if defined
    if (isset($customScripts)) {
        echo $customScripts;
    }
    ?>
</body>
</html>
