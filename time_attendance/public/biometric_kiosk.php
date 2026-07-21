<?php
require_once '../app/controllers/AuthController.php';
require_once '../app/core/Session.php';
require_once '../app/models/BiometricModule.php';
require_once '../../auth/database.php';

Session::start();

if (!AuthController::isAuthenticated()) {
    header('Location: ../../login_form.php');
    exit;
}

// Allow HR/time role or kiosk usage
if (!AuthController::hasRole('time')) {
    // still allow access if user is employee for kiosk viewing (optional)
}

$database = Database::getInstance();
$db = $database->getConnection();
$biometricModule = new BiometricModule($db);
$biometricModule->ensureTables();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Biometric Kiosk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('../bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #111827;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .kiosk-container {
            width: 100%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 30px;
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.18);
            padding: 30px;
            position: relative;
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            overflow: hidden;
            background-clip: border-box;
        }

        .kiosk-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .kiosk-header h1 {
            font-size: 42px;
            margin-bottom: 10px;
            color: #0d47a1;
        }

        .kiosk-header p {
            font-size: 18px;
            color: #334155;
        }

        .kiosk {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            text-align: center;
        }

        .photo-wrap {
            width: 100%;
            height: calc(70vh - 40px);
            max-height: 680px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2), 0 30px 80px rgba(38, 78, 187, 0.14);
            position: relative;
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
        }

        .photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .profile-info {
            width: 100%;
            max-width: 900px;
            color: #0f172a;
        }

        .profile-name {
            font-size: 46px;
            font-weight: 800;
            margin: 6px 0;
        }

        .profile-meta {
            font-size: 20px;
            color: #475569;
        }

        .small-time {
            font-size: 16px;
            color: #475569;
            margin-top: 8px;
        }

        .greeting-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95));
            color: #ffffff;
            padding: 18px 14px;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            z-index: 20;
            box-shadow: 0 -10px 30px rgba(15, 23, 42, 0.25);
        }

        .waiting {
            font-size: 32px;
            color: #2563eb;
            font-weight: 700;
            padding: 0 20px;
        }

        .control-button {
            position: fixed;
            top: 22px;
            left: 22px;
            background: rgba(255,255,255,0.96);
            color: #0f172a;
            padding: 12px 18px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            border: 1px solid rgba(59, 130, 246, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            z-index: 20;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .control-button:hover {
            transform: translateY(-1px);
            background: #ffffff;
        }

        .control-button.secondary {
            left: auto;
            right: 22px;
            background: #2563eb;
            color: #ffffff;
            border-color: rgba(37, 99, 235, 0.45);
        }

        .control-button.secondary:hover {
            background: #1d4ed8;
        }

        .exit-button {
            position: fixed;
            bottom: 22px;
            left: 22px;
            padding: 10px 14px;
            border-radius: 12px;
            border: none;
            background: rgba(37, 99, 235, 0.92);
            color: #ffffff;
            cursor: pointer;
            z-index: 20;
            display: none;
        }

        @media (max-width: 900px) {
            .kiosk-header h1 {
                font-size: 34px;
            }

            .profile-name {
                font-size: 36px;
            }

            .photo-wrap {
                height: 55vh;
            }
        }

        @media (max-width: 600px) {
            .kiosk-container {
                border-radius: 24px;
                padding: 20px;
            }

            .control-button {
                top: 14px;
                left: 14px;
                right: 14px;
                width: calc(50% - 18px);
            }

            .control-button.secondary {
                top: 14px;
                right: 14px;
            }

            .greeting-bar {
                font-size: 18px;
                padding: 14px 12px;
            }
        }
    </style>
</head>
<body>
    <a class="control-button" href="biometrics.php" id="backBtn"><i class="fas fa-arrow-left"></i> Back</a>
    <a class="control-button secondary" id="kioskToggle" href="javascript:void(0);"><i class="fas fa-tv"></i> Kiosk</a>
    <!-- Small persistent exit control for kiosk users (visible when UI hidden) -->
    <button id="exitKioskBtn" class="exit-button" title="Exit Kiosk">Exit</button>
    <div class="kiosk-container">
        <div class="kiosk-header">
            <h1>Live Biometric Kiosk</h1>
            <p>Place your thumb on the scanner and see your profile appear instantly.</p>
        </div>

        <div class="kiosk">
            <div class="photo-wrap" id="photoWrap">
                <div class="waiting" id="waitingMsg">Waiting for scan...</div>
            </div>

            <div class="profile-info" id="profileInfo" style="display:none;">
                <div class="profile-name" id="profileName"></div>
                <div class="profile-meta" id="profileMeta"></div>
                <div class="small-time" id="profileTime"></div>
            </div>
        </div>
    </div>

    <div class="greeting-bar" id="greetingBar">Welcome — place your thumb on the scanner</div>

    <script>
        let lastLogId = null;
        let kioskActive = false;
        let hideTimeout = null;

        function showUI() {
            document.body.style.cursor = '';
            const back = document.getElementById('backBtn');
            const kt = document.getElementById('kioskToggle');
            const exitBtn = document.getElementById('exitKioskBtn');
            if (back) back.style.display = kioskActive ? 'none' : 'block';
            if (kt) kt.style.display = kioskActive ? 'none' : 'block';
            if (exitBtn) exitBtn.style.display = 'none';
        }

        function hideUI() {
            document.body.style.cursor = 'none';
            const back = document.getElementById('backBtn');
            const kt = document.getElementById('kioskToggle');
            const exitBtn = document.getElementById('exitKioskBtn');
            if (back) back.style.display = 'none';
            if (kt) kt.style.display = 'none';
            // show a small exit button so non-technical users can leave kiosk
            if (exitBtn) exitBtn.style.display = 'block';
        }

        function scheduleHide() {
            if (hideTimeout) clearTimeout(hideTimeout);
            hideTimeout = setTimeout(() => {
                if (kioskActive) hideUI();
            }, 2500);
        }

        // Toggle kiosk mode (called by user gesture)
        async function toggleKiosk() {
            kioskActive = !kioskActive;
            const greetingBar = document.getElementById('greetingBar');
            if (kioskActive) {
                // try to enter fullscreen
                try {
                    if (document.documentElement.requestFullscreen) await document.documentElement.requestFullscreen();
                    else if (document.documentElement.webkitRequestFullscreen) document.documentElement.webkitRequestFullscreen();
                } catch (e) {
                    console.warn('Fullscreen request denied', e);
                }
                // hide UI after short delay
                scheduleHide();
            } else {
                // exit fullscreen
                try { if (document.exitFullscreen) await document.exitFullscreen(); else if (document.webkitExitFullscreen) document.webkitExitFullscreen(); } catch(e){}
                showUI();
            }
            // update toggle button text/icon
            const kt = document.getElementById('kioskToggle');
            if (kt) kt.innerHTML = kioskActive ? '<i class="fas fa-times"></i> Exit Kiosk' : '<i class="fas fa-tv"></i> Kiosk';
        }

        // Reveal UI on interaction
        window.addEventListener('mousemove', (e) => {
            if (!kioskActive) return;
            showUI();
            scheduleHide();
        });
        window.addEventListener('touchstart', (e) => {
            if (!kioskActive) return;
            showUI();
            scheduleHide();
        });

        // Keyboard shortcut: Ctrl+Shift+K toggles kiosk
        window.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'k') {
                toggleKiosk();
            }
            // Esc exits fullscreen and kiosk
            if (e.key === 'Escape' && kioskActive) {
                toggleKiosk();
            }
        });

        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement && kioskActive) {
                // if fullscreen was exited externally, turn kiosk off
                kioskActive = false;
                showUI();
                const kt = document.getElementById('kioskToggle');
                if (kt) kt.innerHTML = '<i class="fas fa-tv"></i> Kiosk';
            }
        });

        // attach kiosk toggle
        document.addEventListener('DOMContentLoaded', () => {
            const kt = document.getElementById('kioskToggle');
            if (kt) kt.addEventListener('click', toggleKiosk);
            const exitBtn = document.getElementById('exitKioskBtn');
            if (exitBtn) exitBtn.addEventListener('click', async (e) => { e.preventDefault(); if (kioskActive) await toggleKiosk(); });
            // initial show
            showUI();
        });
        async function poll() {
            try {
                const res = await fetch('../app/api/get_recent_biometric_log.php?limit=1');
                const j = await res.json();
                if (j.success && j.logs && j.logs.length) {
                    const log = j.logs[0];
                    if (log.id && log.id === lastLogId) return; // same
                    lastLogId = log.id;
                    renderLog(log);
                }
            } catch (e) {
                console.error('Poll error', e);
            }
        }

        async function renderLog(log) {
            const photoWrap = document.getElementById('photoWrap');
            const profileInfo = document.getElementById('profileInfo');
            const profileName = document.getElementById('profileName');
            const profileMeta = document.getElementById('profileMeta');
            const profileTime = document.getElementById('profileTime');
            const waitingMsg = document.getElementById('waitingMsg');
            const greetingBar = document.getElementById('greetingBar');

            // If matched employee_id present, fetch full profile
            if (log.employee_id) {
                try {
                    const p = await fetch('../app/api/get_employee_profile.php?employee_id=' + encodeURIComponent(log.employee_id));
                    const pj = await p.json();
                    if (pj.success && pj.profile) {
                        const prof = pj.profile;
                        // photo
                        let src = '';
                        if (prof.profile_pic) {
                            if (prof.profile_pic.startsWith('http') || prof.profile_pic.startsWith('/')) src = prof.profile_pic;
                            else src = '../../assets/dist/img/' + prof.profile_pic;
                        }
                        photoWrap.innerHTML = src ? `<img src="${src}" alt="${prof.full_name}">` : `<div style="font-size:56px;color:#9fb0c8;">${(prof.full_name||'').split(' ').map(s=>s[0]).slice(0,2).join('').toUpperCase()}</div>`;

                        profileName.textContent = prof.full_name || 'Unknown';
                        profileMeta.textContent = (prof.position ? prof.position + ' — ' : '') + (prof.department || '');
                        const t = log.log_datetime || log.created_at || new Date().toISOString();
                        profileTime.textContent = new Date(t).toLocaleString();

                        // Greeting
                        const h = new Date(t).getHours();
                        let greet = 'Hello';
                        if (h < 12) greet = 'Good morning! Have a great day!';
                        else if (h < 18) greet = 'Good afternoon!';
                        else greet = 'Good evening!';
                        greetingBar.textContent = greet;

                        waitingMsg.style.display = 'none';
                        profileInfo.style.display = 'block';
                        // keep visible for a while then hide
                        setTimeout(() => {
                            // fade to waiting state
                            profileInfo.style.display = 'none';
                            photoWrap.innerHTML = '<div class="waiting" id="waitingMsg">Waiting for scan...</div>';
                            greetingBar.textContent = 'Welcome — place your thumb on the scanner';
                            lastLogId = null;
                        }, 7000);
                    } else {
                        // unmatched
                        photoWrap.innerHTML = `<div style="font-size:40px;color:#ffb4a2;">Unmatched</div>`;
                        greetingBar.textContent = 'Unrecognized biometric ID';
                        profileInfo.style.display = 'none';
                        setTimeout(()=>{
                            photoWrap.innerHTML = '<div class="waiting" id="waitingMsg">Waiting for scan...</div>';
                            greetingBar.textContent = 'Welcome — place your thumb on the scanner';
                            lastLogId = null;
                        },5000);
                    }
                } catch (e) {
                    console.error('Profile fetch error', e);
                }
            } else {
                // no employee id
                photoWrap.innerHTML = `<div style="font-size:40px;color:#ffb4a2;">No match</div>`;
                greetingBar.textContent = 'No employee matched';
                profileInfo.style.display = 'none';
                setTimeout(()=>{
                    photoWrap.innerHTML = '<div class="waiting" id="waitingMsg">Waiting for scan...</div>';
                    greetingBar.textContent = 'Welcome — place your thumb on the scanner';
                    lastLogId = null;
                },5000);
            }
        }

        // Start polling every 1.5s
        poll();
        setInterval(poll, 1500);
    </script>
</body>
</html>
