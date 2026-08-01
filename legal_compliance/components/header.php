<?php
/**
 * Header Component
 * Navigation bar with collapsible sidebar, clock, fullscreen, and theme toggle
 * Used in payroll and other modules
 */

// Include database connection
require_once __DIR__ . '/../../auth/database.php';

// Detect the current folder level based on PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Determine base path based on current folder
if ($currentFolder === 'views') {
    $basePath = '../../';
} elseif ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
    $basePath = '../';
} elseif ($currentFolder === 'legal_compliance') {
    $basePath = '../';
} else {
    $basePath = '../';
}

if (!function_exists('getHomePagePath')) {
    // Determine home page path based on current folder
    function getHomePagePath($currentFolder) {
        if ($currentFolder === 'payroll') {
            return '../payroll/payroll.php';
        } elseif ($currentFolder === 'leave_management') {
            return '../leave_management/leave_management.php';
        } elseif ($currentFolder === 'employee_management' || $currentFolder === 'employee') {
            return '../employee/employee.php';
        } elseif ($currentFolder === 'views') {
            return '../legal_compliance.php';
        } elseif ($currentFolder === 'legal_compliance') {
            return 'legal_compliance.php';
        } else {
            return 'legal_compliance.php';
        }
    }
}

$homePage = getHomePagePath($currentFolder);

// Get theme FROM lc_session
$theme = $_SESSION['user']['theme'] ?? 'light';
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= $homePage ?>" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Clock -->
        <li class="nav-item">
            <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- Dark Mode Toggle -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Theme">
                <i class="fas fa-moon" id="themeIcon"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<script>
(function() {
    // Clock update function
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElement = document.getElementById('clock');
        if (clockElement) {
            clockElement.textContent = hours + ':' + minutes + ':' + seconds;
        }
    }
    
    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
    
    const toggleBtn = document.getElementById("darkToggle");
    const icon = document.getElementById("themeIcon");
    
    // Determine update_theme path based on current location
    const currentFolder = '<?= $currentFolder ?>';
    let updateThemePath = 'update_theme.php';
    
    if (currentFolder === 'views') {
        updateThemePath = '../../update_theme.php';
    } else if (currentFolder === 'leave_management' || currentFolder === 'employee_management' || currentFolder === 'payroll') {
        updateThemePath = '../../update_theme.php';
    } else if (currentFolder === 'legal_compliance') {
        updateThemePath = '../update_theme.php';
    }
    
    // Initialize theme FROM lc_localStorage on page load
    function initTheme() {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            document.body.classList.add("dark-mode");
            if(icon) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        } else if (savedTheme === "light") {
            document.body.classList.remove("dark-mode");
            if(icon) {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            }
        } else {
            // Default - check body class
            if (document.body.classList.contains("dark-mode") && icon) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        }
    }
    
    // Call init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }
    
    // Toggle handler
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            document.body.classList.toggle("dark-mode");
            let mode = "light";
            
            if (document.body.classList.contains("dark-mode")) {
                mode = "dark";
                if(icon) {
                    icon.classList.remove("fa-moon");
                    icon.classList.add("fa-sun");
                }
            } else {
                if(icon) {
                    icon.classList.remove("fa-sun");
                    icon.classList.add("fa-moon");
                }
            }
            
            // Save locally
            localStorage.setItem("theme", mode);
            
            // Save to database
            fetch(updateThemePath, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "theme=" + mode,
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(err => console.error('Theme save error:', err));
        });
    }
})();
</script>
