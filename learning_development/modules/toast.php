<?php
/**
 * Toast notification HTML template
 * Include this at the top of your page after alerts
 */
?>

<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<script>
/**
 * Display a toast notification
 * @param {string} message - The message to display
 * @param {string} type - 'success', 'danger', 'warning', 'info' (default)
 * @param {number} duration - How long to show in milliseconds (default: 4000)
 */
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    
    const toastId = 'toast-' + Date.now();
    const bgClass = {
        'success': 'bg-success',
        'danger': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const iconClass = {
        'success': 'fa-check-circle',
        'danger': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `alert ${bgClass} text-white d-flex align-items-center gap-2 mb-2 animate-toast`;
    toast.style.cssText = `
        min-width: 300px;
        padding: 12px 16px;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
    `;
    toast.role = 'alert';
    
    toast.innerHTML = `
        <i class="fas ${iconClass}" style="font-size: 18px; flex-shrink: 0;"></i>
        <span style="flex: 1; font-size: 14px;">${escapeHtml(message)}</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" style="padding: 0;"></button>
    `;
    
    container.appendChild(toast);
    
    // Auto-remove after duration
    const timeoutId = setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) {
            el.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => el.remove(), 300);
        }
    }, duration);
    
    // Manual close button
    toast.querySelector('.btn-close')?.addEventListener('click', () => {
        clearTimeout(timeoutId);
        toast.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => toast.remove(), 300);
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
