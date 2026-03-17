// ============================================
// DASHBOARD DATA LOADING & RENDERING
// Handles dashboard API calls and UI rendering
// ============================================

// Fetch dashboard data from API without caching
async function loadDashboard() {
    try {
        // Add timestamp to prevent caching
        const apiUrl = `api/dashboard.php?t=${Date.now()}`;

        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache'
            },
            cache: 'no-store'
        });

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.error) {
            showError(data.error);
            return;
        }

        // Update user info
        updateUserInfo(data.user || {});

        // Render dashboard based on role
        const role = (data.user?.role || 'employee').toLowerCase();
        renderDashboard(data.dashboard, role);

    } catch (error) {
        console.error('Error loading dashboard:', error);
        showError('Failed to load dashboard data: ' + error.message);
    }
}

function updateUserInfo(user) {
    const nameEl = document.getElementById('userName');
    const roleEl = document.getElementById('userRole');
    const avatarEl = document.getElementById('userAvatar');

    if (nameEl) {
        nameEl.textContent = user.name || 'User';
    }
    if (roleEl) {
        roleEl.textContent = user.role || 'Employee';
    }
    if (avatarEl) {
        avatarEl.textContent = (user.name || 'U').charAt(0).toUpperCase();
    }
}

function renderDashboard(dashboard, role) {
    dashboard = dashboard || {};
    const content = document.getElementById('content');
    if (!content) return;
    content.innerHTML = '';

    let html = '';

    // If employee, show Personal Performance
    if (role === 'employee') {
        const performance = dashboard.personal_performance || {};
    } else if (dashboard.dashboard_type === 'DEFAULT') {
        // Default route for non-specified roles (e.g., engagement_relations)
        const overview = dashboard.system_overview || {
            total_employees: dashboard.total_employees || 0,
            active_users_today: dashboard.active_users_today || 0,
            total_departments: dashboard.total_departments || 0,
        };

        html += `
            <h2 class="section-title">System Overview</h2>
            <div class="dashboard-grid">
                <div class="card employees">
                    <div class="card-title">Total Employees</div>
                    <div class="card-value">${overview.total_employees || 0}</div>
                    <div class="card-subtitle">Active staff members</div>
                </div>
                <div class="card active-users">
                    <div class="card-title">Active Users Today</div>
                    <div class="card-value">${overview.active_users_today || 0}</div>
                    <div class="card-subtitle">Users online</div>
                </div>
                <div class="card departments">
                    <div class="card-title">Total Departments</div>
                    <div class="card-value">${overview.total_departments || 0}</div>
                    <div class="card-subtitle">Organization units</div>
                </div>
            </div>
        `;

        // Add critical KPI block for default roles
        const critical = dashboard.critical_metrics || {};
        html += `
            <h2 class="section-title">Critical Metrics (Default)</h2>
            <div class="metrics-grid">
                <div class="metric-card"><div class="metric-label">Pending Grievances</div><div class="metric-value">${critical.pending_grievances || 0}</div></div>
                <div class="metric-card"><div class="metric-label">Open Grievances</div><div class="metric-value">${critical.open_grievances || 0}</div></div>
                <div class="metric-card"><div class="metric-label">Under Investigation</div><div class="metric-value">${critical.under_investigation || 0}</div></div>
                <div class="metric-card"><div class="metric-label">Pending Feedback</div><div class="metric-value">${critical.pending_feedback || 0}</div></div>
            </div>
        `;
    } else {
        // System Overview for Admin and HR Manager
        const overview = dashboard.system_overview || {};        
        html += `
            <h2 class="section-title">Personal Performance</h2>
            <div class="dashboard-grid">
                <div class="card employees">
                    <div class="card-title">Total Recognitions</div>
                    <div class="card-value">${performance.total_recognitions_received || 0}</div>
                    <div class="card-subtitle">Achievements</div>
                </div>
                <div class="card active-users">
                    <div class="card-title">This Year</div>
                    <div class="card-value">${performance.recognitions_this_year || 0}</div>
                    <div class="card-subtitle">Annual recognition</div>
                </div>
                <div class="card departments">
                    <div class="card-title">This Month</div>
                    <div class="card-value">${performance.recognitions_this_month || 0}</div>
                    <div class="card-subtitle">Recent recognition</div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card employees">
                    <div class="card-title">Engagement Score</div>
                    <div class="card-value">${performance.engagement_score || 0}</div>
                    <div class="card-subtitle">Personal engagement</div>
                </div>
                <div class="card active-users">
                    <div class="card-title">Participation Rate</div>
                    <div class="card-value">${performance.participation_rate || 0}</div>
                    <div class="card-subtitle">Activity participation</div>
                </div>
                <div class="card departments">
                    <div class="card-title">Surveys Completed</div>
                    <div class="card-value">${performance.surveys_completed || 0}</div>
                    <div class="card-subtitle">Completed surveys</div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card employees">
                    <div class="card-title">Events Attended</div>
                    <div class="card-value">${performance.events_attended || 0}</div>
                    <div class="card-subtitle">Attended events</div>
                </div>
                <div class="card active-users">
                    <div class="card-title">Feedback Submitted</div>
                    <div class="card-value">${performance.feedback_submitted || 0}</div>
                    <div class="card-subtitle">Contributions</div>
                </div>
            </div>
        `;
    } else {
        // System Overview for Admin and HR Manager
        const overview = dashboard.system_overview || {};
        
        html += `
            <h2 class="section-title">System Overview</h2>
            <div class="dashboard-grid">
                <div class="card employees">
                    <div class="card-title">Total Employees</div>
                    <div class="card-value">${overview.total_employees || 0}</div>
                    <div class="card-subtitle">Active staff members</div>
                </div>
                <div class="card active-users">
                    <div class="card-title">Active Users Today</div>
                    <div class="card-value">${overview.active_users_today || 0}</div>
                    <div class="card-subtitle">Users online</div>
                </div>
                <div class="card departments">
                    <div class="card-title">Total Departments</div>
                    <div class="card-value">${overview.total_departments || 0}</div>
                    <div class="card-subtitle">Organization units</div>
                </div>
            </div>
        `;
    }

    // Critical Metrics
    if (dashboard.critical_metrics) {
        html += `
            <h2 class="section-title">Critical Metrics</h2>
            <div class="metrics-grid">
        `;
        
        for (const [key, value] of Object.entries(dashboard.critical_metrics)) {
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `
                <div class="metric-card">
                    <div class="metric-label">${label}</div>
                    <div class="metric-value">${value || 0}</div>
                </div>
            `;
        }
        
        html += `</div>`;
    }

    // Content Summary
    if (dashboard.content_summary) {
        html += `
            <h2 class="section-title">Content Summary</h2>
            <div class="metrics-grid">
        `;
        
        for (const [key, value] of Object.entries(dashboard.content_summary)) {
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `
                <div class="metric-card">
                    <div class="metric-label">${label}</div>
                    <div class="metric-value">${value || 0}</div>
                </div>
            `;
        }
        
        html += `</div>`;
    }

    // Quick Actions (new)
    const quickActions = [
        { title: 'File a Grievance', href: 'grievances.php', icon: 'fas fa-exclamation-triangle' },
        { title: 'Submit Feedback', href: 'feedback-suggestions.php', icon: 'fas fa-comment-dots' },
        { title: 'Register Event', href: 'events.php', icon: 'fas fa-calendar-check' },
        { title: 'View Recognition', href: 'recognition-rewards.php', icon: 'fas fa-award' },
    ];

    html += `
        <h2 class="section-title">Quick Actions</h2>
        <div class="quick-actions-grid">
    `;

    quickActions.forEach((action) => {
        html += `
            <a href="${action.href}" class="quick-action-card">
                <div class="quick-action-icon"><i class="${action.icon}"></i></div>
                <div class="quick-action-title">${action.title}</div>
            </a>
        `;
    });

    html += `</div>`;

    content.innerHTML = html;
}

function showError(message) {
    const content = document.getElementById('content');
    content.innerHTML = `
        <div class="error-message">
            <strong>Error:</strong> ${message}
        </div>
    `;
}

function logout() {
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');
    window.location.href = 'logout.php';
}

// Load dashboard when page loads
window.addEventListener('load', loadDashboard);

// Refresh data every 30 seconds
setInterval(loadDashboard, 30000);
