<?php
/**
 * QR Camera Scanner - separate tab for live QR camera scanning
 */
require_once __DIR__ . "/../app/controllers/AuthController.php";
require_once __DIR__ . "/../app/core/Session.php";

Session::start();

if (!AuthController::isAuthenticated()) {
    header('Location: ../../login_form.php');
    exit;
}

$current_page = 'qr_scanner.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
$page_title = 'QR Scanner';
$body_class = 'hold-transition';
$page_head_extra = "<link rel=\"stylesheet\" href=\"../assets/style.css\">\n<link rel=\"stylesheet\" href=\"../assets/hr-template.css\">\n<style>
  html, body {
    height: 100%;
    margin: 0;
    overflow-x: hidden;
  }

  .wrapper {
    display: block !important;
    position: relative !important;
    width: 100vw !important;
    min-height: 100vh !important;
    overflow-x: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .content-wrapper,
  body.layout-fixed .wrapper .content-wrapper,
  body.sidebar-mini .wrapper .content-wrapper,
  .content-wrapper.iframe-mode {
    min-height: 100vh !important;
    background: #f4f6f9 !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    left: 0 !important;
  }

  .main-footer,
  .main-header,
  .main-sidebar,
  .control-sidebar,
  .btn-iframe-close {
    display: none !important;
  }

  body,
  html {
    margin: 0;
    padding: 0;
    width: 100%;
    min-height: 100%;
    overflow-x: hidden;
  }

  .content {
    padding: 0 !important;
    margin: 0 !important;
  }

  .container-fluid {
    max-width: none !important;
    width: 100% !important;
    padding: 0 10px 16px !important;
    margin: 0 auto !important;
  }

  section.content,
  .content {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .kiosk-container {
    display: flex;
    gap: 18px;
    width: 100% !important;
    height: calc(100vh - 24px);
    padding: 10px 0 0 0;
    margin: 0;
    max-width: 100vw !important;
  }

  .kiosk-container {
    display: flex;
    gap: 18px;
    width: 100% !important;
    height: calc(100vh - 24px);
    padding: 10px 0 0 0;
    margin: 0;
  }

  .kiosk-camera,
  .kiosk-info {
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  .kiosk-camera {
    flex: 5;
  }

  .kiosk-info {
    flex: 2.2;
  }

  .card.h-100 {
    height: 100%;
  }

  .card-header.kiosk-header {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background: linear-gradient(135deg, #e8f2ff, #eef7ff);
    border-bottom: 1px solid rgba(13, 71, 161, 0.15);
    padding: 18px 24px;
  }

  .kiosk-back-btn {
    border-radius: 999px;
    padding: 10px 22px;
    color: #ffffff;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    border: none;
    box-shadow: 0 12px 24px rgba(13, 71, 161, 0.12);
    font-weight: 700;
  }

  .kiosk-back-btn:hover {
    background: linear-gradient(135deg, #0b3b8f, #1657b2);
  }

  .camera-card-title {
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .camera-card-title small {
    color: #0d47a1;
    font-size: 0.95rem;
  }

  .camera-card-toolbar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }

  .scanner-panel {
    width: 100%;
    flex: 1;
    border: 2px solid rgba(13, 71, 161, 0.28);
    border-radius: 18px;
    overflow: hidden;
    background: linear-gradient(135deg, #0d47a1, #0b3c91);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 0;
    position: relative;
    min-height: 60vh;
    max-height: calc(100vh - 260px);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
  }

  .scanner-panel #cameraScanner {
    width: 100% !important;
    height: 100% !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 1;
  }

  .scanner-panel #cameraScanner > div,
  .scanner-panel video,
  .scanner-panel canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
  }

  .scanner-panel .html5-qrcode-scanner__camera__scan-region,
  .scanner-panel .html5-qrcode-scanner__camera__viewfinder,
  .scanner-panel .html5-qrcode-region-selection {
    display: none !important;
  }

  .scanner-panel .scanner-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 320px;
    height: 320px;
    transform: translate(-50%, -50%);
    border: 3px solid rgba(255,255,255,0.95);
    border-radius: 18px;
    box-shadow: 0 0 0 9999px rgba(0,0,0,0.18) inset;
    pointer-events: none;
    z-index: 20;
  }

  .scanner-actions {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 18px;
  }

  .scanner-actions .btn {
    min-width: 160px;
    border-radius: 12px;
    padding: 14px 24px;
    font-weight: 700;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .scanner-actions .btn:hover {
    transform: translateY(-1px);
  }

  .scanner-actions .btn i {
    margin-right: 10px;
  }

  .scanner-actions .btn-primary {
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    border: none;
    box-shadow: 0 14px 26px rgba(13, 71, 161, 0.18);
    color: #fff;
  }

  .scanner-actions .btn-primary:hover {
    background: linear-gradient(135deg, #0b3b8f, #1657b2);
  }

  .scanner-actions .btn-secondary {
    background: #f4f6f9;
    color: #0d47a1;
    border: 1px solid rgba(13, 71, 161, 0.12);
  }

  .scanner-actions .btn-secondary:hover {
    background: #e9eef8;
  }

  .employee-info-card {
    border-radius: 14px;
    border: 1px solid rgba(13, 71, 161, 0.15);
    box-shadow: 0 8px 24px rgba(13, 71, 161, 0.08);
  }

  .employee-info-card .card-header {
    background: #eef4ff;
    border-bottom: 1px solid rgba(13, 71, 161, 0.12);
    color: #0d47a1;
    font-weight: 700;
  }

  .employee-info-body {
    flex: 1;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    color: #374151;
    font-size: 15px;
    padding: 24px;
    overflow-y: auto;
  }

  .employee-info-shell {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .employee-info-shell .greeting-label {
    font-size: 0.95rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #0d47a1;
  }

  .employee-avatar-circle {
    width: 92px;
    height: 92px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: 0 12px 24px rgba(13, 71, 161, 0.16);
    margin-bottom: 2px;
  }

  .employee-name {
    margin: 0;
    color: #111827;
    font-size: 1.2rem;
    font-weight: 700;
  }

  .employee-subtext {
    margin: 0;
    color: #6b7280;
    line-height: 1.6;
  }

  .info-pill-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .info-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 7px 12px;
    font-size: 0.9rem;
    font-weight: 600;
    background: #eef4ff;
    color: #0d47a1;
  }

  .info-pill.warning {
    background: #fff7ed;
    color: #c2410c;
  }

  .info-pill.success {
    background: #ecfdf3;
    color: #047857;
  }

  .scan-summary-card {
    border: 1px solid rgba(13, 71, 161, 0.12);
    background: #f8fbff;
    border-radius: 12px;
    padding: 12px 14px;
  }

  .scan-summary-label {
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .scan-summary-value {
    font-weight: 600;
    color: #111827;
  }

  .employee-detail-grid {
    display: grid;
    gap: 8px;
  }

  .employee-detail-item {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 8px;
  }

  .employee-detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .employee-detail-label {
    color: #6b7280;
    font-weight: 600;
  }

  .employee-detail-value {
    color: #111827;
    font-weight: 600;
    text-align: right;
  }

  .recent-scan-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .recent-scan-item {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #ffffff;
    padding: 10px 12px;
  }

  .recent-scan-name {
    font-weight: 700;
    color: #111827;
  }

  .recent-scan-meta {
    font-size: 0.83rem;
    color: #64748b;
    margin-top: 4px;
  }

  .recent-scan-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 6px;
  }

  .recent-scan-pill.in {
    background: #ecfdf3;
    color: #047857;
  }

  .recent-scan-pill.out {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .scanner-toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 320px;
    pointer-events: none;
  }

  .scanner-toast {
    background: #111827;
    color: #ffffff;
    border-radius: 12px;
    padding: 12px 14px;
    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.2);
    transform: translateX(16px);
    opacity: 0;
    transition: all 0.25s ease;
  }

  .scanner-toast.show {
    transform: translateX(0);
    opacity: 1;
  }

  .scanner-toast.warning {
    background: #c2410c;
  }

  .scanner-toast-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 4px;
  }

  .scanner-toast-message {
    font-size: 0.95rem;
    line-height: 1.4;
  }

  /* Dark-mode contrast for the standalone camera scanner page. */
  body.dark-mode,
  body.dark-mode .content-wrapper {
    background: #121212 !important;
    color: #e5e7eb !important;
  }

  body.dark-mode .card,
  body.dark-mode .employee-info-card {
    background: #1e1e1e !important;
    color: #e5e7eb !important;
    border-color: #3f4650 !important;
  }

  body.dark-mode .card-header.kiosk-header {
    background: linear-gradient(135deg, #1f2937, #263b55) !important;
    border-bottom-color: #43566b !important;
  }

  body.dark-mode .camera-card-title,
  body.dark-mode .camera-card-title h2,
  body.dark-mode .employee-info-card .card-header,
  body.dark-mode .employee-info-body,
  body.dark-mode .employee-info-shell,
  body.dark-mode .employee-name,
  body.dark-mode .scan-summary-value,
  body.dark-mode .employee-detail-value,
  body.dark-mode .recent-scan-name {
    color: #f3f4f6 !important;
  }

  body.dark-mode .camera-card-title small,
  body.dark-mode .employee-info-shell .greeting-label,
  body.dark-mode .employee-subtext,
  body.dark-mode .employee-detail-label,
  body.dark-mode .scan-summary-label,
  body.dark-mode .recent-scan-meta {
    color: #c3d0df !important;
  }

  body.dark-mode .employee-info-card .card-header {
    background: #263b55 !important;
    border-bottom-color: #43566b !important;
  }

  body.dark-mode .scan-summary-card,
  body.dark-mode .info-pill {
    background: #203854 !important;
    border-color: #456789 !important;
    color: #dceeff !important;
  }

  body.dark-mode .info-pill.warning {
    background: #4a301c !important;
    color: #ffd7a3 !important;
  }

  body.dark-mode .info-pill.success,
  body.dark-mode .recent-scan-pill.in {
    background: #194532 !important;
    color: #b9f5d0 !important;
  }

  body.dark-mode .recent-scan-pill.out {
    background: #203e69 !important;
    color: #c8e0ff !important;
  }

  body.dark-mode .employee-detail-item {
    border-bottom-color: #3f4650 !important;
  }

  body.dark-mode .recent-scan-item {
    background: #252525 !important;
    border-color: #454d58 !important;
  }

  body.dark-mode .scanner-actions .btn-secondary {
    background: #303944 !important;
    border-color: #596675 !important;
    color: #e5effa !important;
  }

  body.dark-mode .scanner-actions .btn-secondary:hover {
    background: #3b4858 !important;
  }
</style>";

$page_footer_extra = <<<'SCRIPT'
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    let html5QrCode = null;
    let scanInProgress = false;
    let scanHistory = [];
    let scanCooldownUntil = 0;
    let lastDecodedText = null;
    let lastDecodedTime = 0;
    let lastToastKey = '';
    let lastToastAt = 0;
    const cooldownSeconds = 5;
    const startBtn = document.getElementById('startCam');
    const stopBtn = document.getElementById('stopCam');
    const infoPanel = document.getElementById('employeeInfo');
    const cameraElement = document.getElementById('cameraScanner');
    const overlayElement = document.querySelector('.scanner-overlay');
    const storageKey = 'qr_scanner_state';
    const toastContainer = document.createElement('div');
    toastContainer.className = 'scanner-toast-container';
    document.body.appendChild(toastContainer);

    function getGreeting() {
      const hour = new Date().getHours();
      if (hour < 12) return 'Good morning';
      if (hour < 18) return 'Good afternoon';
      return 'Good evening';
    }

    function formatDisplayDateTime(date) {
      return date.toLocaleString('en-US', {
        weekday: 'long',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });
    }

    function isScanAllowed() {
      const now = Date.now();
      return !scanInProgress && now >= scanCooldownUntil;
    }

    function startScanCooldown(seconds = cooldownSeconds) {
      scanCooldownUntil = Date.now() + (seconds * 1000);
    }

    function isDuplicateScan(decodedText) {
      const now = Date.now();
      if (!decodedText) return false;
      if (decodedText === lastDecodedText && now - lastDecodedTime < cooldownSeconds * 1000) {
        return true;
      }
      lastDecodedText = decodedText;
      lastDecodedTime = now;
      return false;
    }

    function loadScanState() {
      try {
        const saved = localStorage.getItem(storageKey);
        if (!saved) return { active: true, scans: [] };
        const parsed = JSON.parse(saved);
        return {
          active: parsed.active !== false,
          scans: Array.isArray(parsed.scans) ? parsed.scans : []
        };
      } catch (error) {
        console.warn('Unable to load scanner state:', error);
        return { active: true, scans: [] };
      }
    }

    function saveScanState(activeState) {
      try {
        localStorage.setItem(storageKey, JSON.stringify({
          active: activeState,
          scans: scanHistory.slice(0, 2)
        }));
      } catch (error) {
        console.warn('Unable to save scanner state:', error);
      }
    }

    function pushScanHistory(entry) {
      const normalizedEntry = {
        ...entry,
        employee_id: entry.employee_id || null
      };

      scanHistory = [normalizedEntry].concat(
        scanHistory.filter(item => String(item.employee_id || '') !== String(normalizedEntry.employee_id || ''))
      ).slice(0, 2);

      saveScanState(true);
    }

    function showToast(message, type = 'info', throttleMs = 1600) {
      if (!message) return;
      const now = Date.now();
      const toastKey = type + ':' + message;
      if (toastKey === lastToastKey && now - lastToastAt < throttleMs) {
        return;
      }

      lastToastKey = toastKey;
      lastToastAt = now;

      const toast = document.createElement('div');
      toast.className = 'scanner-toast ' + (type === 'warning' ? 'warning' : '');
      toast.innerHTML = '<div class="scanner-toast-title">' + (type === 'warning' ? 'Notice' : 'Scanner') + '</div>'
        + '<div class="scanner-toast-message">' + message + '</div>';
      toastContainer.appendChild(toast);
      requestAnimationFrame(() => toast.classList.add('show'));
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 250);
      }, 4000);
    }

    function formatCooldownMessage(payload) {
      if (!payload || !payload.time_left_seconds) {
        return payload?.message || 'Unable to process scan.';
      }

      const remaining = Math.max(1, Math.ceil(payload.time_left_seconds));
      if (payload.time_left_text) {
        return payload.message + ' Available in ' + remaining + ' second' + (remaining === 1 ? '' : 's') + '.';
      }

      return payload.message + ' Available in ' + remaining + ' second' + (remaining === 1 ? '' : 's') + '.';
    }

    function buildRecentScansMarkup() {
      if (!scanHistory.length) {
        return '';
      }

      const items = scanHistory.slice(0, 2).map(entry => {
        const timeLabel = entry.timestamp ? formatDisplayDateTime(new Date(entry.timestamp)) : 'Unknown time';
        const pillClass = entry.action === 'TIME_OUT' ? 'out' : 'in';
        const actionLabel = entry.action === 'TIME_OUT' ? 'Timed Out' : 'Timed In';
        return '<div class="recent-scan-item">'
          + '<div class="recent-scan-name">' + (entry.employee_name || 'Employee') + '</div>'
          + '<div class="recent-scan-meta">' + (entry.department ? entry.department + ' • ' : '') + timeLabel + '</div>'
          + '<span class="recent-scan-pill ' + pillClass + '">' + actionLabel + '</span>'
          + '</div>';
      }).join('');

      return '<div class="scan-summary-card">'
        + '<div class="scan-summary-label">Recent successful scans</div>'
        + '<div class="recent-scan-list">' + items + '</div>'
        + '</div>';
    }

    function renderIdleState() {
      if (!infoPanel) return;
      const now = new Date();
      infoPanel.innerHTML = '<div class="employee-info-shell">'
        + '<div class="greeting-label">' + getGreeting() + '</div>'
        + '<div class="employee-avatar-circle"><i class="fas fa-user"></i></div>'
        + '<h4 class="employee-name">Ready to Scan</h4>'
        + '<p class="employee-subtext">Scan an employee QR code to record attendance.</p>'
        + '<div class="scan-summary-card">'
        + '<div class="scan-summary-label">Last scan</div>'
        + '<div class="scan-summary-value">Waiting for a QR code</div>'
        + '</div>'
        + buildRecentScansMarkup()
        + '<div class="scan-summary-card">'
        + '<div class="scan-summary-label">Current time</div>'
        + '<div class="scan-summary-value">' + formatDisplayDateTime(now) + '</div>'
        + '</div>'
        + '</div>';
    }

    function renderEmployeeDetails(employeeInfo, scanMessage, scanTime, action) {
      if (!infoPanel) return;
      const avatarMarkup = employeeInfo?.avatar && String(employeeInfo.avatar).trim() && !String(employeeInfo.avatar).includes('default-user.png')
        ? '<img src="' + employeeInfo.avatar + '" alt="Employee photo" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">'
        : '<i class="fas fa-user"></i>';
      const actionLabel = action === 'TIME_OUT' ? 'Timed Out' : 'Timed In';
      infoPanel.innerHTML = '<div class="employee-info-shell">'
        + '<div class="greeting-label">' + getGreeting() + '</div>'
        + '<div class="employee-avatar-circle">' + avatarMarkup + '</div>'
        + '<h4 class="employee-name">' + (employeeInfo?.full_name || 'Employee') + '</h4>'
        + '<p class="employee-subtext">' + (scanMessage || 'Attendance recorded successfully.') + '</p>'
        + '<div class="employee-detail-grid">'
        + '<div class="employee-detail-item"><span class="employee-detail-label">Employee ID</span><span class="employee-detail-value">' + (employeeInfo?.employee_id || 'N/A') + '</span></div>'
        + '<div class="employee-detail-item"><span class="employee-detail-label">Department</span><span class="employee-detail-value">' + (employeeInfo?.department || 'N/A') + '</span></div>'
        + '<div class="employee-detail-item"><span class="employee-detail-label">Position</span><span class="employee-detail-value">' + (employeeInfo?.position || 'N/A') + '</span></div>'
        + '</div>'
        + '<div class="scan-summary-card">'
        + '<div class="scan-summary-label">Current status</div>'
        + '<div class="scan-summary-value">' + actionLabel + '</div>'
        + '</div>'
        + '<div class="scan-summary-card">'
        + '<div class="scan-summary-label">Scanned at</div>'
        + '<div class="scan-summary-value">' + scanTime + '</div>'
        + '</div>'
        + buildRecentScansMarkup()
        + '</div>';
    }

    function updateOverlaySize() {
      if (!overlayElement || !cameraElement) return;
      const width = cameraElement.clientWidth || window.innerWidth;
      const height = cameraElement.clientHeight || window.innerHeight;
      const size = Math.max(240, Math.min(width, height) * 0.82);
      overlayElement.style.width = `${size}px`;
      overlayElement.style.height = `${size}px`;
    }

    async function selectCameraConfig() {
      try {
        const devices = await Html5Qrcode.getCameras();
        console.log('Available cameras:', devices);
        if (devices && devices.length) {
          const deviceId = devices[0].id;
          if (deviceId) {
            return { deviceId: { exact: deviceId } };
          }
        }
      } catch (error) {
        console.warn('Camera selection failed, falling back to facingMode:', error);
      }
      return { facingMode: 'environment' };
    }

    async function startCamera(){
      if (html5QrCode || scanInProgress) return;
      saveScanState(true);
      updateOverlaySize();
      html5QrCode = new Html5Qrcode('cameraScanner');
      const width = cameraElement.clientWidth || window.innerWidth;
      const height = cameraElement.clientHeight || window.innerHeight;
      const qrboxSize = Math.max(240, Math.min(width, height) * 0.82);
      const config = {
        fps: 10,
        qrbox: { width: qrboxSize, height: qrboxSize },
        aspectRatio: width / height
      };

      try {
        const cameraConfig = await selectCameraConfig();
        console.log('Starting camera with config:', cameraConfig, config);

        await html5QrCode.start(cameraConfig, config,
          decodedText => {
            if (!isScanAllowed()) {
              const remaining = Math.max(1, Math.ceil((scanCooldownUntil - Date.now()) / 1000));
              const waitingMessage = 'Please wait ' + remaining + ' second' + (remaining === 1 ? '' : 's') + ' before scanning again.';
              showToast(waitingMessage, 'warning', 2500);
              return;
            }

            if (isDuplicateScan(decodedText)) {
              showToast('Duplicate QR scan ignored. Please move away and scan again.', 'warning', 2500);
              startScanCooldown(cooldownSeconds);
              return;
            }

            scanInProgress = true;
            startScanCooldown(cooldownSeconds);

            console.log('QR decoded:', decodedText);
            const requestUrl = 'processStaticQR.php?id=' + encodeURIComponent(decodedText);
            console.log('QR request URL:', requestUrl);
            console.log('QR request params:', { id: decodedText, method: 'GET' });

            fetch(requestUrl).then(r => r.json()).then(j => {
              if (j && !j.success && j.time_left_seconds) {
                const warningMessage = formatCooldownMessage(j);
                showToast(warningMessage, 'warning');
                if (j.employee_info) {
                  const action = j.action || 'TIME_OUT';
                  const scanTime = formatDisplayDateTime(new Date());
                  renderEmployeeDetails(j.employee_info, warningMessage, scanTime, action);
                }
                return;
              }

              if (j && !j.success) {
                const failureMessage = j.message || 'Attendance request failed.';
                showToast(failureMessage, 'warning');
                if (j.employee_info) {
                  const action = j.action || 'TIME_IN';
                  const scanTime = formatDisplayDateTime(new Date());
                  renderEmployeeDetails(j.employee_info, failureMessage, scanTime, action);
                }
                return;
              }

              if (j && j.employee_info) {
                const action = j.action || 'TIME_IN';
                const scanMessage = j.message || 'QR scanned successfully.';
                const scanTime = formatDisplayDateTime(new Date());
                const entry = {
                  employee_name: j.employee_info.full_name || 'Employee',
                  employee_id: j.employee_info.employee_id || null,
                  department: j.employee_info.department || null,
                  action: action,
                  timestamp: new Date().toISOString()
                };

                pushScanHistory(entry);
                renderEmployeeDetails(j.employee_info, scanMessage, scanTime, action);
              }
            }).catch(error => {
              console.error('processStaticQR fetch error:', error);
            }).finally(() => {
              scanInProgress = false;
              setTimeout(() => {
                if (!html5QrCode) {
                  startCamera();
                }
              }, 1000);
            });
          },
          errorMessage => {
            console.debug('QR scan error:', errorMessage);
          }
        );

        startBtn.disabled = true;
        stopBtn.disabled = false;
      } catch(e){
        console.error('Camera start failed:', e);
        html5QrCode = null;
        scanInProgress = false;
      }
    }

    async function stopCamera(){
      if (!html5QrCode) return;
      try { await html5QrCode.stop(); } catch(e){ console.warn(e); }
      try { html5QrCode.clear(); } catch(e){}
      html5QrCode = null;
      saveScanState(false);
      renderIdleState();
      startBtn.disabled = false;
      stopBtn.disabled = true;
    }

    startBtn.addEventListener('click', function(){ startCamera(); });
    stopBtn.addEventListener('click', function(){ stopCamera(); });

    const storedState = loadScanState();
    scanHistory = Array.isArray(storedState.scans) ? storedState.scans : [];
    renderIdleState();
    startCamera();

    document.getElementById('backBtn').addEventListener('click', function(){
      window.history.back();
    });
  });
</script>
SCRIPT;

?>
<?php require_once __DIR__ . '/../layout/page_start.php'; ?>
<div class="content-wrapper p-0" style="min-height:100vh;background:#f4f6f9;">
  <section class="content">
    <div class="container-fluid py-4">
      <div class="kiosk-container">
      <div class="kiosk-camera">
        <div class="card card-primary card-outline h-100" style="margin:0;">
          <div class="card-header kiosk-header">
            <div class="camera-card-title">
              <div class="d-flex align-items-center gap-2">
                <i class="fas fa-video text-primary"></i>
                <span>Camera Scanner</span>
              </div>
            </div>
            <div class="camera-card-toolbar">
              <button id="backBtn" type="button" class="btn btn-outline-secondary btn-sm kiosk-back-btn">Back</button>
            </div>
          </div>
          <div class="card-body d-flex flex-column p-3">
            <div class="scanner-panel">
            <div id="cameraScanner"></div>
            <div class="scanner-overlay"></div>
          </div>
            <div class="scanner-actions mt-4">
              <button id="startCam" class="btn btn-primary btn-lg"><i class="fas fa-play"></i>Start Camera</button>
              <button id="stopCam" class="btn btn-secondary btn-lg" disabled><i class="fas fa-stop"></i>Stop Camera</button>
            </div>
          </div>
        </div>
      </div>

      <div class="kiosk-info">
        <div class="card employee-info-card h-100">
          <div class="card-header">
            <h5 class="card-title mb-0">Employee Info</h5>
          </div>
          <div class="card-body employee-info-body" id="employeeInfo">
            <div class="employee-info-shell">
              <div class="greeting-label">Good morning</div>
              <div class="employee-avatar-circle"><i class="fas fa-user"></i></div>
              <div class="scan-summary-card">
                <div class="scan-summary-label">Last scan</div>
                <div class="scan-summary-value">Waiting for a QR code</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../layout/page_end.php';?>
