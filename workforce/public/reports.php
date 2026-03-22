<!-- TAB 5: CUSTOM REPORTS -->
    <div class="filter-section">
        <h3>Generate Custom Report</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="filter-department">Department:</label>
                <select id="filter-department">
                    <option value="">All Departments</option>
                    <option value="Administration">Administration</option>
                    <option value="Academics">Academics</option>
                    <option value="Finance">Finance</option>
                    <option value="HR">HR</option>
                    <option value="IT">IT</option>
                    <option value="Support Services">Support Services</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filter-employment">Employment Type:</label>
                <select id="filter-employment">
                    <option value="">All Types</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Contract">Contract</option>
                    <option value="Temporary">Temporary</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filter-hire-from">Hire Date From:</label>
                <input type="date" id="filter-hire-from">
            </div>

            <div class="form-group">
                <label for="filter-hire-to">Hire Date To:</label>
                <input type="date" id="filter-hire-to">
            </div>
        </div>

        <div class="button-group">
            <button class="btn btn-primary" onclick="generateCustomReport()">Generate Report</button>
            <button class="btn btn-secondary" onclick="exportCustomReport()">Export as CSV</button>
            <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
        </div>
    </div>

    <div class="table-section">
        <h3 id="report-title">Custom Report</h3>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th>Salary</th>
                        <th>Performance</th>
                        <th>Absence Days</th>
                    </tr>
                </thead>
                <tbody id="custom-report-table">
                    <tr>
                        <td colspan="10" class="text-center">Use filters above to generate a report</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p id="report-count" class="text-muted"></p>
    </div>
