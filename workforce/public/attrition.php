<!-- TAB 2: ATTRITION & TURNOVER -->
<div class="wfa-container" id="attritionContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Attrition Data...
    </div>
</div>

<script>
async function loadAttritionTab() {
    const container = document.getElementById('attritionContainer');
    
    try {
        const basePath = '/capstone_hr_management_system';
        const response = await fetch(`${basePath}/api/wfa/attrition_metrics.php`);
        const data = await response.json();
        
        const monthly = data.data?.monthly_summary || [];
        const byType = data.data?.by_separation_type || [];
        
        let html = `
            <!-- Attrition Metrics -->
            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card danger">
                    <div class="wfa-metric-label">Total Separations (YTD)</div>
                    <div class="wfa-metric-value">${monthly.reduce((sum, m) => sum + (m.total_separations || 0), 0)}</div>
                    <div class="wfa-metric-change">This year</div>
                </div>
                
                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Voluntary Separations</div>
                    <div class="wfa-metric-value">${monthly.reduce((sum, m) => sum + (m.voluntary_separations || 0), 0)}</div>
                    <div class="wfa-metric-change">Resignations</div>
                </div>
                
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Involuntary Separations</div>
                    <div class="wfa-metric-value">${monthly.reduce((sum, m) => sum + (m.involuntary_separations || 0), 0)}</div>
                    <div class="wfa-metric-change">Terminations</div>
                </div>
                
                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Avg Attrition Rate</div>
                    <div class="wfa-metric-value">${monthly.length > 0 ? (monthly.reduce((sum, m) => sum + (m.attrition_rate_percent || 0), 0) / monthly.length).toFixed(1) : 0}%</div>
                    <div class="wfa-metric-change">Monthly average</div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="wfa-charts-grid">
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Monthly Attrition Rate</div>
                    <canvas id="attritionTrendChart"></canvas>
                </div>
                
                <div class="wfa-chart-container">
                    <div class="wfa-chart-title">Separation Types</div>
                    <canvas id="separationTypeChart"></canvas>
                </div>
            </div>
            
            <!-- Attrition Summary Table -->
            <div class="wfa-table-container">
                <h3 style="margin-bottom: 15px;">Monthly Attrition Summary</h3>
                <table class="wfa-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Separations</th>
                            <th>Voluntary</th>
                            <th>Involuntary</th>
                            <th>Attrition Rate (%)</th>
                            <th>Avg Tenure (Years)</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        if (monthly.length > 0) {
            monthly.slice(0, 12).forEach(m => {
                html += `
                    <tr>
                        <td>${m.year_month || 'N/A'}</td>
                        <td>${m.total_separations || 0}</td>
                        <td>${m.voluntary_separations || 0}</td>
                        <td>${m.involuntary_separations || 0}</td>
                        <td>${(m.attrition_rate_percent || 0).toFixed(2)}%</td>
                        <td>${(m.average_tenure_departing || 0).toFixed(1)}</td>
                    </tr>
                `;
            });
        } else {
            html += '<tr><td colspan="6" style="text-align: center; padding: 20px;">No attrition data available</td></tr>';
        }
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Initialize charts
        initAttritionCharts(monthly, byType);
        
    } catch (error) {
        console.error('Error loading attrition data:', error);
        container.innerHTML = '<div class="wfa-error">Error loading attrition data</div>';
    }
}

function initAttritionCharts(monthly, byType) {
    // Attrition Trend Chart
    if (monthly.length > 0) {
        const trendCtx = document.getElementById('attritionTrendChart')?.getContext('2d');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthly.map(m => m.year_month),
                    datasets: [{
                        label: 'Attrition Rate (%)',
                        data: monthly.map(m => m.attrition_rate_percent || 0),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
    
    // Separation Type Chart
    if (byType.length > 0) {
        const typeCtx = document.getElementById('separationTypeChart')?.getContext('2d');
        if (typeCtx) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: byType.map(t => t.separation_type),
                    datasets: [{
                        data: byType.map(t => t.count),
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }
}

// Load on tab click
document.addEventListener('DOMContentLoaded', function() {
    const attritionTab = document.querySelector('a[href="#attrition"]');
    if (attritionTab) {
        attritionTab.addEventListener('click', function() {
            loadAttritionTab();
        });
    }
});
</script>
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
