<?php
/**
 * Sidebar Component
 * Can be used from legal_compliance root, views subfolder, or leave_management folder
 * Automatically detects path depth and adjusts asset paths accordingly
 */

// Detect the current folder from PHP_SELF
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Detect if we're in a subfolder (views/)
$isInViews = ($currentFolder === 'views');

// Detect if we're in leave_management folder
$isInLeaveManagement = ($currentFolder === 'leave_management');

// Detect if we're in employee_management folder
$isInEmployeeManagement = ($currentFolder === 'employee_management');

// Detect if we're in payroll folder
$isInPayroll = ($currentFolder === 'payroll');

// Determine base path based on current folder
if ($isInViews) {
    $basePath = '../../';
} elseif ($isInLeaveManagement || $isInEmployeeManagement || $isInPayroll) {
    $basePath = '../';
} else {
    $basePath = '../';
}

$homePage = getLegalCompliancePath($currentFolder);
$logoutPath = ($currentFolder === 'views') ? '../../logout.php' : (($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') ? '../../logout.php' : '../logout.php');

// Determine the correct path to leave_management based on current location
// Note: leave_management.php is in the legal_compliance folder, not a separate leave_management folder
function getLeaveManagementPath($currentFolder) {
    if ($currentFolder === 'leave_management') {
        return 'leave_management.php';
    } elseif ($currentFolder === 'views') {
        return '../leave_management.php';
    } else {
        return 'leave_management.php';
    }
}

// Determine the correct path to legal_compliance based on current location
function getLegalCompliancePath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/legal_compliance.php';
    } elseif ($currentFolder === 'views') {
        return '../legal_compliance.php';
    } else {
        return 'legal_compliance.php';
    }
}

// Determine the correct path to policy_admin based on current location
function getPolicyAdminPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/views/policy_admin.php';
    } elseif ($currentFolder === 'views') {
        return 'policy_admin.php';
    } else {
        return 'views/policy_admin.php';
    }
}

// Determine the correct path to compliance (main router)
function getCompliancePath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/compliance.php';
    } elseif ($currentFolder === 'views') {
        return '../compliance.php';
    } else {
        return 'compliance.php';
    }
}

// Determine the correct path to compliance dashboard (main router)
function getComplianceDashboardPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/compliance.php?page=dashboard';
    } elseif ($currentFolder === 'views') {
        return '../compliance.php?page=dashboard';
    } else {
        return 'compliance.php?page=dashboard';
    }
}

// Determine the path to compliance items
function getComplianceItemsPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/compliance.php?page=items';
    } elseif ($currentFolder === 'views') {
        return '../compliance.php?page=items';
    } else {
        return 'compliance.php?page=items';
    }
}

// Determine the path to alerts center
function getAlertsCenterPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/compliance.php?page=alerts';
    } elseif ($currentFolder === 'views') {
        return '../compliance.php?page=alerts';
    } else {
        return 'compliance.php?page=alerts';
    }
}

// Determine the path to audit logs
function getAuditLogsPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/compliance.php?page=audit';
    } elseif ($currentFolder === 'views') {
        return '../compliance.php?page=audit';
    } else {
        return 'compliance.php?page=audit';
    }
}

// Determine the correct path to incidents based on current location
function getIncidentsPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/views/incidents.php';
    } elseif ($currentFolder === 'views') {
        return 'incidents.php';
    } else {
        return 'views/incidents.php';
    }
}

// Determine the correct path to reports based on current location
function getReportsPath($currentFolder) {
    if ($currentFolder === 'leave_management' || $currentFolder === 'employee_management' || $currentFolder === 'payroll') {
        return '../legal_compliance/views/reports.php';
    } elseif ($currentFolder === 'views') {
        return 'reports.php';
    } else {
        return 'views/reports.php';
    }
}
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= $homePage ?>" class="nav-link">Home</a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="nav-link" id="clock">--:--:--</div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="darkToggle" role="button" title="Toggle Theme">
                <i class="fas fa-moon" id="themeIcon"></i>
            </a>
        </li>
    </ul>
</nav>

<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= $homePage ?>" class="brand-link">
        <img src="<?= $basePath ?>assets/pics/bcpLogo.png" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    HR Admin <?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?>
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= getLegalCompliancePath($currentFolder) ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'legal_compliance.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-gavel"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= getCompliancePath($currentFolder) ?>?page=compliance" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'compliance.php' || ($_GET['page'] ?? '') == 'compliance' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Compliance</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= getPolicyAdminPath($currentFolder) ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'policy_admin.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Policy Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= getLeaveManagementPath($currentFolder) ?>" class="nav-link <?= ($currentFolder === 'leave_management' || basename($_SERVER['PHP_SELF']) === 'leave_management.php') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Leave Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="views/reports.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Reports</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= getIncidentsPath($currentFolder) ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'incidents.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>Incident Cases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= $logoutPath ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Theme Toggle Script with Persistence -->
<script>
(function() {
    const toggleBtn = document.getElementById("darkToggle");
    const icon = document.getElementById("themeIcon");
    
    // Get the base path from PHP - pass it through data attribute
    const currentFolder = '<?= $currentFolder ?>';
    let updateThemePath = '../update_theme.php';
    
    if (currentFolder === 'views') {
        updateThemePath = '../../update_theme.php';
    } else if (currentFolder === 'leave_management' || currentFolder === 'employee_management' || currentFolder === 'payroll') {
        updateThemePath = '../../update_theme.php';
    }
    
    // Initialize theme from localStorage on page load
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
            
            // Save to database - use the PHP-determined path
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

