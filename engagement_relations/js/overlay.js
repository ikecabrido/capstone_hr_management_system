// ============================================
// SESSION OVERLAY
// Shows loading overlay while verifying session
// ============================================

const overlay = document.createElement('div');
overlay.id = 'sessionBlockingOverlay';
overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 999999; display: flex; align-items: center; justify-content: center;';
overlay.innerHTML = '<p style="text-align: center; color: #666;">Verifying session...</p>';
document.documentElement.appendChild(overlay);
