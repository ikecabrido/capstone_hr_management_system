<!-- TAB 2: ATTRITION & TURNOVER -->
    <div class="filter-section">
        <label>Select Year:</label>
        <select id="attrition-year" onchange="loadAttritionData()">
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026" selected>2026</option>
        </select>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
            </div>
            <div class="metric-content">
                <h3>Attrition Rate</h3>
                <p class="metric-value" id="attrition-rate">-</p>
            </div>
        </div>
    </div>

    <div class="charts-section">
        <div class="chart-container">
            <h3>Monthly Attrition Trends</h3>
            <canvas id="attritionChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Performance Distribution</h3>
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <div class="table-section">
        <h3>Recently Separated Employees</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Separation Date</th>
                </tr>
            </thead>
            <tbody id="separated-employees-table">
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
