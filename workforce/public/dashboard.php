<!-- TAB 1: DASHBOARD -->
    <!-- Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Total Employees</h3>
                <p class="metric-value" id="total-employees">-</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Total Teachers</h3>
                <p class="metric-value" id="total-teachers">-</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 7v-2a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Total Staff</h3>
                <p class="metric-value" id="total-staff">-</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="13" r="8"></circle>
                    <path d="M12 9v8m-4-4h8"></path>
                </svg>
            </div>
            <div class="metric-content">
                <h3>New Hires (This Year)</h3>
                <p class="metric-value" id="new-hires">-</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Average Salary</h3>
                <p class="metric-value" id="avg-salary">-</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Average Performance</h3>
                <p class="metric-value" id="avg-performance">-</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <h3>Employee Distribution by Department</h3>
            <canvas id="departmentChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Gender Distribution</h3>
            <canvas id="genderChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Age Group Distribution</h3>
            <canvas id="ageChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Tenure Distribution</h3>
            <canvas id="tenureChart"></canvas>
        </div>
    </div>
