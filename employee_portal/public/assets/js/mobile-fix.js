// Mobile Compatibility Fixes - Bootstrap 5 Aware
document.addEventListener('DOMContentLoaded', function() {
    
    // IMPORTANT: Do NOT disable buttons or interfere with Bootstrap modals
    // Only handle form double-submission on NON-modal forms
    
    const forms = document.querySelectorAll('form:not(#leaveRequestForm)');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('[type="submit"]');
            if (submitButton && !submitButton.hasAttribute('data-submitting')) {
                submitButton.setAttribute('data-submitting', 'true');
                submitButton.disabled = true;
                
                // Re-enable after 3 seconds if no response
                setTimeout(() => {
                    submitButton.removeAttribute('data-submitting');
                    submitButton.disabled = false;
                }, 3000);
            }
        });
    });

    // Live clock update (if element exists)
    const liveClock = document.getElementById('liveClock');
    if (liveClock) {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        updateClock();
        setInterval(updateClock, 1000);
    }

    // Sidebar toggle for mobile (AdminLTE)
    const sidebarToggle = document.querySelector('.sidebar-toggle, [data-widget="pushmenu"]');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const body = document.querySelector('body');
            body.classList.toggle('sidebar-collapse');
        });
    }

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    });
});

// Prevent double-tap zoom ONLY on non-interactive elements
// Do NOT interfere with buttons, modals, or form submissions
document.addEventListener('touchend', function (event) {
    // Skip if it's a button, link, or form element
    if (event.target.tagName === 'BUTTON' || 
        event.target.tagName === 'A' || 
        event.target.tagName === 'INPUT' ||
        event.target.tagName === 'SELECT' ||
        event.target.tagName === 'TEXTAREA' ||
        event.target.closest('button') ||
        event.target.closest('a') ||
        event.target.closest('input') ||
        event.target.closest('.modal')) {
        return; // Let Bootstrap handle these
    }
    
    // Only prevent double-tap on text/content areas
    let now = Date.now();
    if (window.lastTouchEnd && now - window.lastTouchEnd <= 300) {
        event.preventDefault();
    }
    window.lastTouchEnd = now;
}, false);
