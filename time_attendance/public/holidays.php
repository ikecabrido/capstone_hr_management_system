<?php
/**
 * Holidays Management Page
 * Displays holiday calendar, upcoming holidays, and management options
 */

// Start session and auth first
require_once "../app/core/Session.php";
Session::start();

// Check if user is authenticated
require_once "../app/controllers/AuthController.php";
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

$current_page = 'holidays.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'EMPLOYEE';

// Set defaults - will load data async
$allHolidays = array();
$upcomingHolidays = array();
$nextHoliday = null;
$daysUntilNext = 'N/A';
$currentMonth = date('F Y');
?>
<?php
$current_page = 'holidays.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'EMPLOYEE';
$page_title = 'Holidays';
$page_subtitle = 'Holiday calendar and management';
$page_head_extra = <<<HTML
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/dashboard.css">
<link rel="stylesheet" href="../assets/adminlte-overrides.css">
<link rel="stylesheet" href="../assets/hr-template.css">
<style>

        .next-holiday-block {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid rgba(255, 255, 255, 0.5);
        }

        .next-holiday-block .holiday-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .countdown {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #fff;
        }

        .countdown-label {
            font-size: 12px;
            opacity: 0.85;
        }

        .upcoming-holidays {
            margin-top: 20px;
        }

        .upcoming-holidays h4 {
            font-size: 14px;
            margin-bottom: 12px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .holiday-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            border-left: 3px solid rgba(255, 255, 255, 0.3);
        }

        .holiday-item .name {
            flex: 1;
        }

        .holiday-item .days {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }

        .sync-info {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sync-button {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .sync-button:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }

        .sync-button:active {
            transform: translateY(0);
        }

        .calendar-container {
            background: white;
            padding: 20px 30px 30px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
            max-width: 100%;
            height: auto;
            min-height: 750px;
        }

        .calendar-container h3 {
            margin: 0 0 20px 0;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #holidayCalendar {
            width: 100% !important;
            height: 100% !important;
            max-width: calc(100vw - 300px) !important;
        }

        /* Prevent scrolling within calendar */
        .fc-view-harness {
            overflow: hidden !important;
            height: 100% !important;
        }

        .fc-scroller {
            overflow: hidden !important;
        }

        /* FullCalendar responsive sizing */
        .fc {
            font-size: 0.75em;
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
        }

        .fc-header-toolbar {
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
            padding: 10px 0;
        }

        .fc-button {
            padding: 4px 8px !important;
            font-size: 0.85em !important;
        }

        .fc-daygrid-day-frame {
            min-height: 30px !important;
        }

        .fc-daygrid-day {
            padding: 1px !important;
        }

        .fc-col-header-cell {
            padding: 4px 1px !important;
            font-size: 0.75em !important;
        }

        .fc-daygrid-day-number {
            padding: 2px !important;
            font-size: 0.7em !important;
        }

        .fc-event {
            padding: 1px 2px !important;
            font-size: 0.65em !important;
        }

        .fc-daygrid-day.holiday {
            background-color: #fff3cd !important;
        }

        .fc-event {
            border: none !important;
            padding: 2px 4px !important;
        }

        @media (max-width: 1024px) {
            .holiday-container {
                grid-template-columns: 1fr;
                overflow: hidden;
            }
        }

        .no-holidays {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .holiday-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }

        .stat-box .number {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-box .label {
            font-size: 11px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .loading {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .loading i {
            font-size: 32px;
            animation: spin 2s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Footer Styling */
        .main-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 30px;
        }

        .main-footer strong {
            color: #333;
            font-weight: 600;
        }

        .main-footer .float-right {
            float: right;
        }

        @media (max-width: 576px) {
            .main-footer {
                font-size: 12px;
                padding: 10px 15px;
            }

            .main-footer .float-right {
                float: none;
                display: block;
                margin-bottom: 5px;
            }
        }

    </style>
HTML;
?>
<?php
require_once __DIR__ . '/../layout/page_start.php';
require_once __DIR__ . '/../layout/sidebar.php';
$page_title = 'Holidays';
$page_subtitle = 'Holiday calendar and management';
$page_icon = 'fa-calendar-alt';
require_once __DIR__ . '/../layout/content_header.php';
?>

            <!-- Content will be loaded here -->
            <div id="holidayContent" class="glass-panel" style="padding: 30px; border-radius: 18px; text-align: center;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #005ba8; margin-bottom: 15px;"></i>
                <p style="color: #666; font-size: 14px;">Loading holiday data...</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/plugins/toastr/toastr.min.js"></script>
    <script src="../../assets/dist/js/adminlte.js"></script>
    <!-- FullCalendar Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <script>
        // Get color based on holiday category
        function getCategoryColor(category) {
            const colors = {
                'national': '#e74c3c',   // Red
                'regional': '#f39c12',   // Orange
                'optional': '#3498db',   // Blue
                'special': '#9b59b6'     // Purple
            };
            return colors[category] || '#95a5a6'; // Gray default
        }

        window.preloaderHold = true;
        function releaseHolidayPreloader(delay = 0) {
            if (window.releasePreloader) {
                window.releasePreloader(delay);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load holiday data via AJAX once the page is ready
            loadHolidayData();
        });

        // Load holiday data via AJAX
        function loadHolidayData() {
            fetch('../app/api/holiday_api.php?action=get_page_data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('API returned ' + response.status);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success || data.holidays !== undefined) {
                            renderHolidayContent(data);
                        } else {
                            showSetupNeeded();
                        }
                    } catch (e) {
                        console.error('JSON Parse Error:', e, 'Response:', text.substring(0, 200));
                        showSetupNeeded();
                    }
                })
                .catch(error => {
                    console.error('Error loading holidays:', error);
                    showSetupNeeded();
                });
        }

        // Render holiday content
        function renderHolidayContent(data) {
            const container = document.getElementById('holidayContent');
            
            // Check if we have valid data
            if (!data.holidays || !Array.isArray(data.holidays)) {
                data.holidays = [];
            }
            if (!data.upcoming || !Array.isArray(data.upcoming)) {
                data.upcoming = [];
            }

            if (data.holidays.length === 0 && data.upcoming.length === 0) {
                container.innerHTML = `
                    <div class="glass-panel" style="padding: 40px; border-radius: 18px; text-align: center;">
                        <i class="fas fa-calendar-times" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                        <h3 style="color: #666;">No Holidays Found</h3>
                        <p style="color: #999; margin-bottom: 20px;">No holidays have been configured yet. Click the button below to sync holidays from the API.</p>
                        <button class="sync-button" style="background: #005ba8; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;" onclick="syncHolidays()">
                            <i class="fas fa-sync-alt"></i> Sync Holidays from API
                        </button>
                    </div>
                `;
                releaseHolidayPreloader(1200);
                return;
            }

            // Build upcoming list HTML
            let upcomingHtml = '';
            if (data.upcoming && data.upcoming.length > 0) {
                data.upcoming.forEach(holiday => {
                    const daysLeft = Math.ceil((new Date(holiday.holiday_date) - new Date()) / (1000 * 60 * 60 * 24));
                    upcomingHtml += `
                        <div class="holiday-item" style="background: rgba(255, 255, 255, 0.1); padding: 10px; margin-bottom: 8px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; border-left: 3px solid rgba(255, 255, 255, 0.3);">
                            <div class="name" style="flex: 1;">
                                <strong>${holiday.name}</strong><br>
                                <span style="font-size: 11px; opacity: 0.8;">${new Date(holiday.holiday_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>
                            </div>
                            <div class="days" style="background: rgba(255, 255, 255, 0.2); padding: 3px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">${daysLeft} days</div>
                        </div>
                    `;
                });
            }

            const nextHoliday = data.upcoming && data.upcoming.length > 0 ? data.upcoming[0] : null;
            const daysUntilNext = nextHoliday ? Math.ceil((new Date(nextHoliday.holiday_date) - new Date()) / (1000 * 60 * 60 * 24)) : 'N/A';

            container.innerHTML = `
                <div class="holiday-container" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px;">
                    <!-- Left: Holiday Widget -->
                    <div class="holiday-widget" style="background: linear-gradient(135deg, #003d82 0%, #005ba8 100%); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                        <h3 style="margin: 0 0 15px 0; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-bell"></i> Upcoming Holidays
                        </h3>
                        <div class="holiday-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;">
                            <div class="stat-box" style="background: rgba(255, 255, 255, 0.1); padding: 12px; border-radius: 6px; text-align: center;">
                                <div class="number" style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">${data.holidays.length}</div>
                                <div class="label" style="font-size: 11px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px;">Total Holidays</div>
                            </div>
                            <div class="stat-box" style="background: rgba(255, 255, 255, 0.1); padding: 12px; border-radius: 6px; text-align: center;">
                                <div class="number" style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">${data.upcoming ? data.upcoming.length : 0}</div>
                                <div class="label" style="font-size: 11px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px;">Coming Up</div>
                            </div>
                            <div class="stat-box" style="background: rgba(255, 255, 255, 0.1); padding: 12px; border-radius: 6px; text-align: center;">
                                <div class="number" style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">${daysUntilNext !== 'N/A' ? daysUntilNext : '-'}</div>
                                <div class="label" style="font-size: 11px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px;">Days Left</div>
                            </div>
                        </div>
                        ${nextHoliday ? `
                            <div class="next-holiday-block" style="background: rgba(255, 255, 255, 0.15); padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid rgba(255, 255, 255, 0.5);">
                                <div class="label" style="font-size: 12px; opacity: 0.9; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">Next Holiday</div>
                                <div class="holiday-name" style="font-size: 20px; font-weight: 600; margin-bottom: 10px;">${nextHoliday.name}</div>
                                <div class="countdown" style="font-size: 36px; font-weight: 700; margin-bottom: 5px; color: #fff;">${daysUntilNext}</div>
                                <div class="countdown-label" style="font-size: 12px; opacity: 0.85;">${daysUntilNext == 0 ? 'Today!' : daysUntilNext == 1 ? 'Tomorrow' : 'days remaining'}</div>
                            </div>
                        ` : ''}
                        <div class="upcoming-holidays">
                            <h4 style="font-size: 14px; margin-bottom: 12px; opacity: 0.95; text-transform: uppercase; letter-spacing: 0.5px;">Upcoming Holidays</h4>
                            ${upcomingHtml || '<p style="font-size: 12px; opacity: 0.8;">No upcoming holidays</p>'}
                        </div>
                        <div class="sync-info" style="font-size: 11px; opacity: 0.8; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                            <div style="margin-bottom: 10px;">
                                <strong>Last Updated:</strong><br>
                                <span id="lastSyncTime">${new Date().toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</span>
                            </div>
                            <button class="sync-button" style="display: inline-block; background: rgba(255, 255, 255, 0.25); color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; margin-top: 10px; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;" onclick="syncHolidays()">
                                <i class="fas fa-sync-alt"></i> Refresh Holidays
                            </button>
                        </div>
                    </div>
                    <div class="calendar-container glass-panel" style="padding: 20px; border-radius: 18px;">
                        <h3 style="margin: 0 0 20px 0; color: #333; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-calendar-days"></i> Holiday Calendar
                        </h3>
                        <div id="holidayCalendar" style="height: 550px;"></div>
                    </div>
                </div>
            `;

            // Initialize calendar after content is rendered
            initializeCalendar(data.holidays);
        }

        // Initialize FullCalendar
        function initializeCalendar(holidays) {
            const calendarEl = document.getElementById('holidayCalendar');
            if (!calendarEl) {
                releaseHolidayPreloader(1200);
                return;
            }

            try {
                const events = holidays.map(h => ({
                    title: h.name,
                    start: h.holiday_date,
                    backgroundColor: '#005ba8',
                    borderColor: '#005ba8',
                    extendedProps: {
                        category: h.category || 'Holiday'
                    }
                }));

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listMonth'
                    },
                    events: events,
                    eventClick: function(info) {
                        const event = info.event;
                        alert(
                            event.title + '\n' +
                            'Date: ' + event.start.toLocaleDateString() + '\n' +
                            'Category: ' + (event.extendedProps.category || 'Holiday')
                        );
                    },
                    editable: false,
                    selectable: false
                });

                calendar.render();
            } catch (err) {
                console.error('Calendar initialization error:', err);
            } finally {
                releaseHolidayPreloader(1200);
            }
        }

        // Show error message
        function showError(message) {
            const container = document.getElementById('holidayContent');
            container.innerHTML = `
                <div class="glass-panel" style="background: rgba(248, 215, 218, 0.9); border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 18px; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 32px; margin-bottom: 15px;"></i>
                    <h3>${message}</h3>
                    <p style="margin-bottom: 0;">Please try reloading the page or contact support if the problem persists.</p>
                </div>
            `;
            releaseHolidayPreloader(1200);
        }

        // Show setup needed
        function showSetupNeeded() {
            const container = document.getElementById('holidayContent');
            container.innerHTML = `
                <div class="glass-panel" style="padding: 40px; border-radius: 18px; text-align: center;">
                    <i class="fas fa-wrench" style="font-size: 48px; color: #ffc107; margin-bottom: 20px;"></i>
                    <h3 style="color: #666;">Holiday System Setup Required</h3>
                    <p style="color: #999; margin-bottom: 20px;">The holiday system needs to be initialized. Please visit the setup page to sync holidays from the API.</p>
                    <p style="font-size: 12px; color: #999; margin-bottom: 20px;">
                        <strong>Setup Steps:</strong><br>
                        1. Visit <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px;">/app/setup/holiday_setup.php</code><br>
                        2. Click "Sync Holidays from API"<br>
                        3. Return to this page
                    </p>
                    <a href="../app/setup/holiday_setup.php" style="display: inline-block; background: #005ba8; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; text-decoration: none;">
                        <i class="fas fa-cog"></i> Go to Setup
                    </a>
                </div>
            `;
            releaseHolidayPreloader(1200);
        }

        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };

        // Sync holidays from API
        function syncHolidays() {
            const btn = event.target.closest('.sync-button');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
            btn.disabled = true;

            fetch('../app/api/holiday_api.php?action=sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Holidays synced successfully!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(data.message || 'Failed to sync holidays');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error syncing holidays');
            })
            .finally(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }
    </script>

<?php require_once __DIR__ . '/../layout/content_footer.php'; ?>
<?php require_once __DIR__ . '/../layout/page_end.php'; ?>
