<!-- TAB 4: PERFORMANCE -->
    <div class="charts-section">
        <div class="chart-container">
            <h3>Employee Performance Levels</h3>
            <canvas id="performanceDistChart"></canvas>
        </div>

        <div class="chart-container">
            <h3>Salary Distribution</h3>
            <canvas id="salaryChart"></canvas>
        </div>
    </div>

    <div class="table-section">
        <h3>Employees at Risk of Attrition</h3>
        <div class="risk-filters">
            <button class="filter-btn active" data-risk="all" onclick="filterRiskLevel(this)">All</button>
            <button class="filter-btn danger" data-risk="High" onclick="filterRiskLevel(this)">High Risk</button>
            <button class="filter-btn warning" data-risk="Medium" onclick="filterRiskLevel(this)">Medium Risk</button>
            <button class="filter-btn success" data-risk="Low" onclick="filterRiskLevel(this)">Low Risk</button>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Performance</th>
                    <th>Absence Days</th>
                    <th>Tenure (Years)</th>
                    <th>Risk Level</th>
                </tr>
            </thead>
            <tbody id="at-risk-table">
                <tr>
                    <td colspan="7" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
