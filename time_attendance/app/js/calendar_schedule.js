/**
 * Calendar Schedule JavaScript
 * Handles employee search, calendar rendering, and timeline editing
 */

// Global variables
let selectedEmployee = null;
let currentCalendar = null;
let timelineData = [];
let selectedDate = null;
let allSchedulesMode = true; // Show all employees by default
let targetCalendarView = 'dayGridMonth'; // Track which view should be active

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== CALENDAR SCHEDULE PAGE LOADED ===');
    console.log('jQuery available:', typeof $ !== 'undefined', '| Version:', typeof $ !== 'undefined' ? $.fn.jquery : 'N/A');
    console.log('FullCalendar available:', typeof FullCalendar !== 'undefined');
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');

    // ============ Day Navigation Handlers ============
    window.handlePrevDay = function() {
        console.log('✓ Previous day button clicked. selectedDate:', selectedDate);
        if (selectedDate) {
            const date = new Date(selectedDate);
            date.setDate(date.getDate() - 1);
            const newDateStr = date.toISOString().split('T')[0];
            console.log('Moving to previous day:', newDateStr);
            showDailyView(newDateStr);
        } else {
            console.log('⚠ selectedDate is not set');
        }
    };

    window.handleNextDay = function() {
        console.log('✓ Next day button clicked. selectedDate:', selectedDate);
        if (selectedDate) {
            const date = new Date(selectedDate);
            date.setDate(date.getDate() + 1);
            const newDateStr = date.toISOString().split('T')[0];
            console.log('Moving to next day:', newDateStr);
            showDailyView(newDateStr);
        } else {
            console.log('⚠ selectedDate is not set');
        }
    };

    // ============ Employee Search ============
    const searchInput = document.getElementById('employee-search');
    const searchResults = document.getElementById('search-results');
    const selectedEmployeeDiv = document.getElementById('selected-employee');
    const employeeName = document.getElementById('employee-name');
    const clearBtn = document.getElementById('clear-employee');
    const calendarSection = document.getElementById('calendar-section');

    // Load all employees' schedules on page load
    console.log('Starting to load all schedules...');
    loadAllSchedules();

    // Test tab switching
    const monthTab = document.querySelector('a[href="#month-view"]');
    const weekTab = document.querySelector('a[href="#week-view"]');
    const dayTab = document.querySelector('a[href="#day-view"]');
    
    console.log('Tab elements found - Month:', monthTab ? 'YES' : 'NO', 'Week:', weekTab ? 'YES' : 'NO', 'Day:', dayTab ? 'YES' : 'NO');
    
    if (weekTab) {
        console.log('Week tab classes:', weekTab.className);
        console.log('Week tab has data-toggle="tab":', weekTab.getAttribute('data-toggle') === 'tab');
    }

    // Search employees on input
    searchInput.addEventListener('keyup', debounce(function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        console.log('Searching for:', query);
        fetch(`../app/components/calendar_schedule.php?action=search_employees&q=${encodeURIComponent(query)}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="dropdown-item text-danger">Error searching employees</div>';
                searchResults.style.display = 'block';
            });
    }, 300));

    // Display search results
    function displaySearchResults(employees) {
        if (employees.length === 0) {
            searchResults.innerHTML = '<div class="dropdown-item">No employees found</div>';
            searchResults.style.display = 'block';
            return;
        }

        let html = '';
        employees.forEach(emp => {
            html += `
                <div class="dropdown-item employee-option" data-id="${emp.employee_id}" data-name="${emp.first_name} ${emp.last_name}">
                    <div class="employee-option-name">${emp.first_name} ${emp.last_name}</div>
                    <div class="employee-option-id">ID: ${emp.employee_number}</div>
                </div>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';

        // Add click handlers to options
        document.querySelectorAll('.employee-option').forEach(option => {
            option.addEventListener('click', function() {
                selectEmployee({
                    id: this.dataset.id,
                    name: this.dataset.name
                });
            });
        });
    }

    // Select employee
    function selectEmployee(employee) {
        selectedEmployee = employee;
        searchInput.value = employee.name;
        searchResults.style.display = 'none';
        
        employeeName.textContent = employee.name;
        selectedEmployeeDiv.style.display = 'block';
        
        // Load calendar
        loadCalendar();
    }

    // Clear selected employee
    clearBtn.addEventListener('click', function() {
        selectedEmployee = null;
        searchInput.value = '';
        selectedEmployeeDiv.style.display = 'none';
        allSchedulesMode = true;
        
        if (currentCalendar) {
            currentCalendar.destroy();
            currentCalendar = null;
        }
        
        // Reload all schedules
        loadAllSchedules();
    });
    // ============ TAB SWITCHING SETUP (Called once on page load) ============
    function setupTabSwitching() {
        console.log('Setting up tab switching for all buttons...');
        
        // Get side button selectors
        const sideMonthBtn = document.querySelector('button.side-month-btn');
        const sideWeekBtn = document.querySelector('button.side-week-btn');
        const sideDayBtn = document.querySelector('button.side-day-btn');
        
        console.log('Side buttons found - Month:', sideMonthBtn ? 'YES' : 'NO', 'Week:', sideWeekBtn ? 'YES' : 'NO', 'Day:', sideDayBtn ? 'YES' : 'NO');
        
        // Helper function to handle view switching
        function switchToView(view) {
            console.log('✓ Switching to', view, 'view');
            const calendarContainer = document.getElementById('calendar-container');
            const dayViewPane = document.getElementById('day-view');
            
            if (view === 'month') {
                if (calendarContainer) calendarContainer.style.display = 'block';
                if (dayViewPane) {
                    dayViewPane.classList.remove('show', 'active');
                }
                if (currentCalendar) {
                    currentCalendar.changeView('dayGridMonth');
                }
            } else if (view === 'week') {
                if (calendarContainer) calendarContainer.style.display = 'block';
                if (dayViewPane) {
                    dayViewPane.classList.remove('show', 'active');
                }
                if (currentCalendar) {
                    currentCalendar.changeView('dayGridWeek');
                }
            } else if (view === 'day') {
                if (calendarContainer) calendarContainer.style.display = 'none';
                if (dayViewPane) {
                    dayViewPane.classList.add('show', 'active');
                }
            }
        }
        
        // Side Month Button
        if (sideMonthBtn) {
            sideMonthBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('✓✓✓ SIDE MONTH BUTTON CLICKED');
                switchToView('month');
                return false;
            });
            console.log('Side month button listener attached');
        }
        
        // Side Week Button
        if (sideWeekBtn) {
            sideWeekBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('✓✓✓ SIDE WEEK BUTTON CLICKED');
                switchToView('week');
                return false;
            });
            console.log('Side week button listener attached');
        }
        
        // Side Day Button
        if (sideDayBtn) {
            sideDayBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('✓✓✓ SIDE DAY BUTTON CLICKED');
                switchToView('day');
                return false;
            });
            console.log('Side day button listener attached');
        }
    }

        // Helper function to activate a tab and its pane
        function activateTab(tabSelector) {
            // Simply hide/show the day view pane without manipulating other elements
            const dayViewPane = document.getElementById('day-view');
            const calendarContainer = document.getElementById('calendar-container');
            
            if (tabSelector === '#day-view') {
                if (dayViewPane) dayViewPane.classList.add('show', 'active');
                if (calendarContainer) calendarContainer.style.display = 'none';
            } else {
                if (dayViewPane) dayViewPane.classList.remove('show', 'active');
                if (calendarContainer) calendarContainer.style.display = 'block';
            }
            
            console.log('Activated tab:', tabSelector);
        }

    // Initialize tab switching once on page load
    setupTabSwitching();
    // ============ Load All Schedules ============
    function loadAllSchedules() {
        allSchedulesMode = true;
        selectedEmployeeDiv.style.display = 'none';
        
        // Clear calendar and use single container for both month and week views
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) calendarEl.innerHTML = '';
        
        if (currentCalendar) {
            currentCalendar.destroy();
            currentCalendar = null;
        }

        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        const startDate = formatDate(firstDay);
        const endDate = formatDate(lastDay);

        console.log('Loading all employees schedules from', startDate, 'to', endDate);
        
        // Load general attendance/shift data for display
        fetch(`../app/api/get_all_schedules.php?start_date=${startDate}&end_date=${endDate}`)
            .then(response => {
                console.log('All schedules API response status:', response.status);
                if (!response.ok) {
                    throw new Error('Failed to fetch schedules: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('All schedules data received:', data);
                if (data.success || Array.isArray(data)) {
                    const scheduleData = data.success ? data.data : data;
                    initializeCalendar(calendarEl, scheduleData);
                } else {
                    console.error('API returned error:', data.error);
                    showError(data.error || 'Failed to load schedules');
                }
            })
            .catch(error => {
                console.error('All schedules loading error:', error);
                // Fallback - show empty calendar
                initializeCalendar(calendarEl, { schedule: [] });
            });
    }

    // ============ Calendar Loading ============
    function loadCalendar() {
        if (!selectedEmployee) return;

        calendarSection.style.display = 'block';

        // Use single calendar container for both month and week views
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) calendarEl.innerHTML = '';
        
        if (currentCalendar) {
            currentCalendar.destroy();
            currentCalendar = null;
        }

        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // Fetch schedule data
        const startDate = formatDate(firstDay);
        const endDate = formatDate(lastDay);

        console.log('Loading calendar for employee:', selectedEmployee.id, 'from', startDate, 'to', endDate);
        
        fetch(`../app/api/get_employee_schedule.php?employee_id=${selectedEmployee.id}&start_date=${startDate}&end_date=${endDate}`)
            .then(response => {
                console.log('Calendar API response status:', response.status);
                if (!response.ok) {
                    throw new Error('Failed to fetch schedule: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Calendar data received:', data);
                if (data.success) {
                    initializeCalendar(calendarEl, data);
                    // Week view is now handled by FullCalendar changeView() in setupTabSwitching
                } else {
                    console.error('API returned error:', data.error);
                    showError(data.error || 'Failed to load schedule');
                }
            })
            .catch(error => {
                console.error('Calendar loading error:', error);
                showError('Error loading calendar: ' + error.message);
            });
    }

    // Initialize FullCalendar
    function initializeCalendar(element, scheduleData) {
        const events = buildCalendarEvents(scheduleData.schedule);

        currentCalendar = new FullCalendar.Calendar(element, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            height: 'auto',
            contentHeight: 'auto',
            events: events,
            dateClick: function(info) {
                // Any date in the calendar is clicked - show daily view
                console.log('✓ Date clicked:', info.dateStr);
                showDailyView(info.dateStr);
            },
            datesSet: function(info) {
                // Log calendar view changes
                const viewType = info.view.type;
                console.log('✓ Calendar datesSet fired. View type:', viewType, 'Target view:', targetCalendarView);
                
                // Add click handlers to day headers for week view switching
                const dayHeaders = document.querySelectorAll('.fc-col-header-cell');
                dayHeaders.forEach(header => {
                    header.style.cursor = 'pointer';
                });
            },
            eventClick: function(info) {
                if (scheduleData && scheduleData.schedule) {
                    openDayTimeline(info.event.startStr.split('T')[0], scheduleData);
                }
            }
        });

        currentCalendar.render();
        console.log('✓ Calendar rendered successfully');

        // Add click handlers for day-of-week headers to switch to week view
        setTimeout(() => {
            const dayHeaders = document.querySelectorAll('.fc-col-header-cell');
            console.log('Found', dayHeaders.length, 'day header cells');
            dayHeaders.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('✓✓✓ Day header (week column) clicked - switching to week view');
                    // Switch to week view
                    if (currentCalendar) {
                        currentCalendar.changeView('dayGridWeek');
                        console.log('Changed to week view');
                    }
                }, true); // Use capture phase to ensure it fires
            });
        }, 100);
    }

    // Build calendar events
    function buildCalendarEvents(schedule) {
        const events = [];

        // Handle both formats - array of events or array of day objects
        if (Array.isArray(schedule) && schedule.length > 0) {
            if (schedule[0].title !== undefined) {
                // Direct events format from API
                return schedule;
            } else {
                // Day-by-day format
                schedule.forEach(day => {
                    if (day.shift) {
                        events.push({
                            title: `${day.shift.shift_name}`,
                            start: day.date,
                            className: 'shift-event',
                            extendedProps: {
                                type: 'shift',
                                shift: day.shift
                            }
                        });
                    }

                    if (day.attendance) {
                        const timeIn = day.attendance.time_in ? new Date(day.attendance.time_in).toLocaleTimeString() : 'N/A';
                        const timeOut = day.attendance.time_out ? new Date(day.attendance.time_out).toLocaleTimeString() : 'N/A';
                        
                        events.push({
                            title: `Check-in: ${timeIn}`,
                            start: day.date,
                            className: 'attendance-event',
                            extendedProps: {
                                type: 'attendance',
                                attendance: day.attendance
                            }
                        });
                    }
                });
            }
        }

        return events;
    }

    // ============ Daily View ============
    function showDailyView(dateStr) {
        console.log('Showing daily view for:', dateStr);
        
        // Activate day view tab using the centralized function
        activateTab('#day-view');
        
        // Update the day display
        selectedDate = dateStr;
        const dateObj = new Date(dateStr + 'T00:00:00');
        const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        const dayTextElement = document.getElementById('current-day-text');
        if (dayTextElement) {
            dayTextElement.textContent = `${dayName}, ${formattedDate}`;
            console.log('✓ Updated day text to:', dayTextElement.textContent);
        } else {
            console.error('Day text element not found');
        }
        
        // Draw the timeline
        console.log('Drawing timeline for:', dateStr);
        drawDayTimeline(dateStr);
    }

    function drawDayTimeline(dateStr) {
        const canvas = document.getElementById('day-timeline-canvas');
        if (!canvas) {
            console.error('❌ Canvas not found');
            return;
        }

        const container = document.getElementById('day-timeline-container');
        if (!container) {
            console.error('❌ Timeline container not found');
            return;
        }

        console.log('✓ Canvas and container found, setting up timeline');
        canvas.width = container.offsetWidth - 40;
        canvas.height = 700;
        
        const ctx = canvas.getContext('2d');
        const dayHeight = canvas.height;
        const hourHeight = dayHeight / 24;

        // Draw timeline background
        ctx.fillStyle = '#f9f9f9';
        ctx.fillRect(0, 0, canvas.width, dayHeight);

        // Draw hour lines and labels
        ctx.strokeStyle = '#e0e0e0';
        ctx.fillStyle = '#666';
        ctx.font = '12px Arial';
        ctx.textAlign = 'right';

        for (let hour = 0; hour < 24; hour++) {
            const y = hour * hourHeight;
            
            // Hour line
            ctx.beginPath();
            ctx.moveTo(50, y);
            ctx.lineTo(canvas.width, y);
            ctx.stroke();

            // Hour label
            const timeStr = String(hour).padStart(2, '0') + ':00';
            ctx.fillText(timeStr, 45, y + 4);
        }

        // Draw events for this day from attendance data
        console.log('Fetching attendance data for:', dateStr);
        fetch(`../app/api/get_day_schedule.php?date=${dateStr}`)
            .then(response => {
                console.log('Day schedule API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('✓ Day schedule data received:', data);
                if (data.success && data.data) {
                    console.log('Drawing', data.data.length, 'events on timeline');
                    data.data.forEach(event => {
                        drawTimelineEvent(ctx, event, dateStr, hourHeight, 50);
                    });
                } else if (Array.isArray(data)) {
                    console.log('Drawing', data.length, 'events on timeline (array format)');
                    data.forEach(event => {
                        drawTimelineEvent(ctx, event, dateStr, hourHeight, 50);
                    });
                } else {
                    console.warn('No attendance data for', dateStr);
                }
            })
            .catch(error => console.error('❌ Error loading day schedule:', error));
    }

    function drawTimelineEvent(ctx, event, dateStr, hourHeight, leftPadding) {
        if (!event.time_in) return;

        try {
            const startTime = new Date(event.time_in);
            const endTime = event.time_out ? new Date(event.time_out) : new Date(event.time_in);

            const startHour = startTime.getHours() + startTime.getMinutes() / 60;
            const endHour = endTime.getHours() + endTime.getMinutes() / 60;

            const y1 = startHour * hourHeight;
            const y2 = endHour * hourHeight;
            const blockHeight = Math.max(y2 - y1, 20);

            // Determine color based on status
            let color, textColor;
            if (startTime.getHours() > 9) {
                color = '#ffc107'; // Yellow for late
                textColor = '#333';
            } else {
                color = '#28a745'; // Green for on-time
                textColor = '#fff';
            }

            // Draw background
            ctx.fillStyle = color;
            ctx.fillRect(leftPadding + 10, y1, 120, blockHeight);

            // Draw border
            ctx.strokeStyle = 'rgba(0,0,0,0.3)';
            ctx.lineWidth = 2;
            ctx.strokeRect(leftPadding + 10, y1, 120, blockHeight);

            // Draw text
            ctx.fillStyle = textColor;
            ctx.font = 'bold 12px Arial';
            ctx.textAlign = 'center';
            const startTimeStr = String(startTime.getHours()).padStart(2, '0') + ':' + String(startTime.getMinutes()).padStart(2, '0');
            const endTimeStr = String(endTime.getHours()).padStart(2, '0') + ':' + String(endTime.getMinutes()).padStart(2, '0');
            ctx.fillText(startTimeStr, leftPadding + 70, y1 + 15);
            ctx.fillText(endTimeStr, leftPadding + 70, y1 + 30);

            // Draw employee name
            ctx.font = '11px Arial';
            ctx.fillText(event.employee_name || 'Employee', leftPadding + 70, y1 + 45);
        } catch (error) {
            console.error('Error drawing timeline event:', error);
        }
    }

    // ============ Daily Timeline Modal ============
    function openDayTimeline(date, scheduleData) {
        selectedDate = date;
        const dayData = scheduleData.schedule.find(d => d.date === date);

        if (!dayData) {
            showError('No data for this date');
            return;
        }

        const dateObj = new Date(date);
        const dateTitle = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        document.getElementById('dayTimelineTitle').textContent = `Daily Schedule - ${dateTitle} - ${selectedEmployee.name}`;

        // Draw timeline
        drawTimeline(dayData, scheduleData);

        // Show modal
        const modal = new (window.bootstrap ? window.bootstrap.Modal : $.fn.modal.Constructor)(
            document.getElementById('dayTimelineModal')
        );
        modal.show();
    }

    // Draw 24-hour timeline
    function drawTimeline(dayData, scheduleData) {
        const canvas = document.getElementById('timeline-canvas');
        const container = document.getElementById('timeline-container');
        
        // Set canvas dimensions
        const width = container.offsetWidth;
        const height = 24 * 50; // 24 hours * 50px per hour
        
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');

        // Draw timeline hours
        ctx.fillStyle = '#666';
        ctx.font = '12px Arial';
        ctx.textAlign = 'right';

        for (let hour = 0; hour < 24; hour++) {
            const y = hour * 50;
            
            // Draw hour label
            const hourLabel = String(hour).padStart(2, '0') + ':00';
            ctx.fillText(hourLabel, 45, y + 20);

            // Draw hour line
            ctx.strokeStyle = '#ddd';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(50, y);
            ctx.lineTo(width, y);
            ctx.stroke();
        }

        // Draw shift blocks if any
        if (dayData.shift) {
            drawShiftBlock(ctx, dayData.shift, width);
        }

        // Draw attendance if any
        if (dayData.attendance && dayData.attendance.time_in) {
            drawAttendanceBlock(ctx, dayData.attendance, width);
        }

        // Store data for interactions
        timelineData = {
            shifts: dayData.shift ? [dayData.shift] : [],
            attendance: dayData.attendance || null
        };
    }

    // Draw shift block on timeline
    function drawShiftBlock(ctx, shift, width) {
        if (!shift.start_time || !shift.end_time) return;

        const [startHour, startMin] = shift.start_time.split(':').map(Number);
        const [endHour, endMin] = shift.end_time.split(':').map(Number);

        const startY = (startHour + startMin / 60) * 50;
        const endY = (endHour + endMin / 60) * 50;
        const blockHeight = endY - startY;

        // Draw shift block
        ctx.fillStyle = 'rgba(76, 175, 80, 0.7)';
        ctx.strokeStyle = '#2e7d32';
        ctx.lineWidth = 2;
        ctx.fillRect(50, startY, width - 60, blockHeight);
        ctx.strokeRect(50, startY, width - 60, blockHeight);

        // Draw text
        ctx.fillStyle = '#2e7d32';
        ctx.font = 'bold 12px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(shift.shift_name, 60, startY + 15);
        ctx.font = '11px Arial';
        ctx.fillText(shift.start_time + ' - ' + shift.end_time, 60, startY + 30);
    }

    // Draw attendance block on timeline
    function drawAttendanceBlock(ctx, attendance, width) {
        if (!attendance.time_in) return;

        const timeIn = new Date(attendance.time_in);
        const timeOut = attendance.time_out ? new Date(attendance.time_out) : null;

        const startHour = timeIn.getHours() + timeIn.getMinutes() / 60;
        const startY = startHour * 50;
        const blockHeight = timeOut ? ((timeOut.getHours() + timeOut.getMinutes() / 60) - startHour) * 50 : 40;

        // Draw attendance block
        ctx.fillStyle = 'rgba(23, 162, 184, 0.6)';
        ctx.strokeStyle = '#0c5460';
        ctx.lineWidth = 2;
        ctx.fillRect(50, startY, width - 60, blockHeight);
        ctx.strokeRect(50, startY, width - 60, blockHeight);

        // Draw text
        ctx.fillStyle = '#0c5460';
        ctx.font = 'bold 12px Arial';
        ctx.textAlign = 'left';
        ctx.fillText('Check-in', 60, startY + 15);
        ctx.font = '11px Arial';
        const timeInStr = timeIn.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const timeOutStr = timeOut ? timeOut.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--:--';
        ctx.fillText(timeInStr + ' - ' + timeOutStr, 60, startY + 30);
    }

    // ============ Week View ============
    function updateWeekView(scheduleData) {
        const weekCalendarDiv = document.getElementById('week-calendar');
        
        // If week-calendar element doesn't exist, this function is no longer used (FullCalendar handles it)
        if (!weekCalendarDiv) {
            console.log('✓ Week calendar element not found - using FullCalendar week view instead');
            return;
        }
        
        // Get current week
        const today = new Date();
        const firstDay = today.getDate() - today.getDay();
        const weekStart = new Date(today.setDate(firstDay));

        let html = '<div class="week-grid">';

        for (let i = 0; i < 7; i++) {
            const date = new Date(weekStart);
            date.setDate(date.getDate() + i);
            const dateStr = formatDate(date);
            
            const dayData = scheduleData.schedule.find(d => d.date === dateStr);

            html += `
                <div class="week-day" onclick="window.handleWeekDayClick('${dateStr}')">
                    <div class="week-day-header">
                        <div class="week-day-name">${date.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                        <div class="week-day-date">${date.getDate()}</div>
                    </div>
            `;

            if (dayData && dayData.shift) {
                html += `
                    <div class="week-day-shift">
                        <strong>${dayData.shift.shift_name}</strong><br>
                        ${dayData.shift.start_time} - ${dayData.shift.end_time}
                    </div>
                `;
            } else {
                html += '<div class="week-day-no-shift">No shift</div>';
            }

            html += '</div>';
        }

        html += '</div>';
        weekCalendarDiv.innerHTML = html;
    }

    // Handle week day click
    window.handleWeekDayClick = function(date) {
        // This will be handled by the calendar event click
        console.log('Clicked date:', date);
    };

    // ============ Save Timeline ============
    document.getElementById('save-timeline').addEventListener('click', function() {
        if (!selectedEmployee || !selectedDate) {
            showError('Invalid data');
            return;
        }

        // Prepare shift data to save
        const shiftsToSave = [];
        
        // For now, we'll save the current shift
        // In a more advanced implementation, you could allow editing the shifts directly on the timeline
        if (timelineData.shifts && timelineData.shifts.length > 0) {
            timelineData.shifts.forEach(shift => {
                shiftsToSave.push({
                    start_time: shift.start_time,
                    end_time: shift.end_time
                });
            });
        }

        const saveData = {
            employee_id: selectedEmployee.id,
            date: selectedDate,
            shifts: shiftsToSave
        };

        fetch('../app/api/save_employee_schedule.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(saveData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Schedule saved successfully');
                
                // Close modal after 1.5 seconds
                setTimeout(() => {
                    const modal = new (window.bootstrap ? window.bootstrap.Modal : $.fn.modal.Constructor)(
                        document.getElementById('dayTimelineModal')
                    );
                    modal.hide();
                }, 1500);
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showError('Failed to save schedule');
        });
    });

    // ============ Utility Functions ============
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showError(message) {
        // Using a simple alert; replace with toast notification if available
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        const container = document.getElementById('calendar-schedule-container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => alertDiv.remove(), 5000);
    }

    function showSuccess(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        const container = document.getElementById('calendar-schedule-container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#employee-search') && !event.target.closest('#search-results')) {
            searchResults.style.display = 'none';
        }
    });

});
