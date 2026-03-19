// ============================================
// MAIN JAVASCRIPT FILE
// Common functions used across all pages
// ============================================

// Hamburger menu toggle function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
}

// Close sidebar function
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.add('hidden');
    }
}

// Update time function
function updateTime() {
    const timeElement = document.getElementById('updateTime');
    if (timeElement) {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        hours = String(hours).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
        timeElement.textContent = timeString;
    }
}

// Initialize menu toggle on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize and start updating the time immediately
    updateTime();
    setInterval(updateTime, 1000);
    
    const menuToggle = document.getElementById('menuToggle');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    // Close sidebar when clicking on a link (only on mobile)
    if (window.innerWidth <= 768) {
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
    }
});

window.addEventListener('load', function() {
    // Ensure time is updated on full page load as well
    updateTime();
});
