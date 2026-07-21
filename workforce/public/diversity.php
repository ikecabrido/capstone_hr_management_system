<!-- TAB: DIVERSITY & INCLUSION REPORTS -->
<div class="wfa-container" id="diversityContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Diversity & Inclusion Report...
    </div>
</div>

<script>
async function loadDiversityTab() {
    const container = document.getElementById('diversityContainer');
    if (!container) return;

    try {
        const basePath = '/capstone_hr_management_system/workforce';
        const response = await fetch(`${basePath}/api/wfa/employees_data.php`);
        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }

        const payload = await response.json();
        const employees = payload.data && payload.data.employees ? payload.data.employees : [];
        const totalEmployees = employees.length;

        const genderDist = buildDistribution(employees, emp => emp.gender || 'Not Specified');
        const ageDist = buildDistribution(employees, emp => emp.age_group || 'Unknown');
        const deptDist = buildDistribution(employees, emp => emp.department || 'Unassigned');

        const genderSummaryText = summarizeGender(genderDist, totalEmployees);
        const ageSummaryText = summarizeAge(ageDist, totalEmployees);
        const topDepartments = Object.entries(deptDist)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 6);

        // Generate recommendations
        const recommendations = generateDiversityRecommendations(employees, genderDist, ageDist, totalEmployees);
        const severityConfig = {
            critical: { color: '#dc3545', emoji: '⚠️', bgColor: '#fff5f5' },
            high: { color: '#ff9800', emoji: '🔍', bgColor: '#fff8f0' },
            medium: { color: '#2196f3', emoji: '💡', bgColor: '#f0f7ff' },
            info: { color: '#17a2b8', emoji: '✅', bgColor: '#f0f9ff' }
        };
        
        const recommendationsHtml = recommendations.map(rec => {
            const cfg = severityConfig[rec.severity] || severityConfig.info;
            return `
                <div style="margin-bottom: 15px; padding: 15px; background: ${cfg.bgColor}; border-left: 4px solid ${cfg.color}; border-radius: 8px; display: flex; gap: 12px;">
                    <div style="font-size: 22px; flex-shrink: 0;">${cfg.emoji}</div>
                    <div style="flex-grow: 1; font-size: 14px;">
                        <strong style="color: ${cfg.color};">Insight:</strong> ${rec.insight}<br>
                        <strong style="color: #495057;">Action:</strong> ${rec.action}
                    </div>
                </div>
            `;
        }).join('');
        
        let html = `
            <div class="wfa-section-header" style="margin-bottom: 24px;">
                <h2 style="margin:0; font-size: 24px; color: #2e3a59;">Diversity & Inclusion Reports</h2>
                <p style="margin: 10px 0 0; color: #556080; max-width: 820px; line-height: 1.6;">A focused diversity report for HR leadership, summarizing employee demographics, gender balance, age cohorts, and department representation across the organization.</p>
            </div>
            
            <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #2e3a59; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 24px;">📋</span> HR Recommendations
                </h3>
                <div>${recommendationsHtml}</div>
            </div>

            <div class="wfa-metrics-grid">
                <div class="wfa-metric-card">
                    <div class="wfa-metric-label">Total Employees</div>
                    <div class="wfa-metric-value">${totalEmployees}</div>
                    <div class="wfa-metric-change">Current headcount</div>
                </div>

                <div class="wfa-metric-card info">
                    <div class="wfa-metric-label">Gender Balance</div>
                    <div class="wfa-metric-value">${Object.keys(genderDist).length} categories</div>
                    <div class="wfa-metric-change">${genderSummaryText}</div>
                </div>

                <div class="wfa-metric-card success">
                    <div class="wfa-metric-label">Age Cohorts</div>
                    <div class="wfa-metric-value">${Object.keys(ageDist).length} groups</div>
                    <div class="wfa-metric-change">${ageSummaryText}</div>
                </div>

                <div class="wfa-metric-card warning">
                    <div class="wfa-metric-label">Department Reach</div>
                    <div class="wfa-metric-value">${Object.keys(deptDist).length}</div>
                    <div class="wfa-metric-change">Unique departments</div>
                </div>
            </div>

            <div class="wfa-section" style="margin-top: 32px; display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px;">
                <div class="wfa-card" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 24px rgba(15, 23, 42, 0.05);">
                    <h3 style="margin-top: 0; color: #2e3a59;">Key Inclusion Insights</h3>
                    <ul style="margin: 0; padding-left: 20px; color: #4b5563; line-height: 1.7;">
                        <li>${genderSummaryText}</li>
                        <li>${ageSummaryText}</li>
                        <li>Top departments represent the majority of the workforce and should be prioritized for inclusion initiatives.</li>
                    </ul>
                </div>
                <div class="wfa-card" style="padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 24px rgba(15, 23, 42, 0.05);">
                    <h3 style="margin-top: 0; color: #2e3a59;">Top Departments by Headcount</h3>
                    <ol style="margin: 0; padding-left: 20px; color: #4b5563; line-height: 1.7;">
                        ${topDepartments.map(([dept, count]) => `<li><strong>${dept}</strong>: ${count} employees</li>`).join('')}
                    </ol>
                </div>
            </div>

            <div class="wfa-section" style="margin-top: 32px;">
                <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <h3 style="margin: 0; color: #2e3a59;">Diversity Breakdown Tables</h3>
                    <button class="wfa-btn wfa-btn-info" onclick="printDiversity()"><i class="fas fa-print"></i> Print Report</button>
                </div>

                <div class="wfa-table-container" style="margin-top: 12px;">
                    <h4 style="margin-bottom: 12px;">Gender Distribution</h4>
                    <table class="wfa-table">
                        <thead>
                            <tr>
                                <th>Gender</th>
                                <th>Count</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderTableRows(genderDist, totalEmployees)}
                        </tbody>
                    </table>
                </div>

                <div class="wfa-table-container" style="margin-top: 24px;">
                    <h4 style="margin-bottom: 12px;">Age Group Distribution</h4>
                    <table class="wfa-table">
                        <thead>
                            <tr>
                                <th>Age Group</th>
                                <th>Count</th>
                                <th>Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderTableRows(ageDist, totalEmployees)}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        container.innerHTML = html;
    } catch (error) {
        console.error('Error loading diversity data:', error);
        container.innerHTML = '<div style="padding: 20px; color: #d32f2f;">Error loading diversity data: ' + error.message + '</div>';
    }
}

function generateDiversityRecommendations(employees, genderDist, ageDist, totalEmployees) {
    const recommendations = [];
    
    // Gender diversity check
    const genderEntries = Object.entries(genderDist);
    if (genderEntries.length > 0) {
        const sortedByCount = genderEntries.sort((a, b) => b[1] - a[1]);
        if (sortedByCount.length >= 2) {
            const largest = sortedByCount[0][1];
            const largestPercent = (largest / totalEmployees) * 100;
            if (largestPercent > 70) {
                const category = sortedByCount[0][0];
                recommendations.push({
                    severity: 'high',
                    insight: `Gender imbalance: ${largestPercent.toFixed(1)}% ${category}`,
                    action: 'Implement inclusive hiring initiatives and review recruitment strategies'
                });
            }
        }
    }
    
    // Age diversity check
    const ageEntries = Object.entries(ageDist).filter(([k]) => k !== 'Unknown');
    if (ageEntries.length > 0) {
        const dominantAge = ageEntries.sort((a, b) => b[1] - a[1])[0];
        if (dominantAge) {
            const dominantPercent = (dominantAge[1] / totalEmployees) * 100;
            if (dominantPercent > 60) {
                recommendations.push({
                    severity: 'medium',
                    insight: `Age concentration: ${dominantPercent.toFixed(1)}% in ${dominantAge[0]} age group`,
                    action: 'Diversify recruitment to attract candidates from different age groups'
                });
            }
        }
    }
    
    if (recommendations.length === 0) {
        recommendations.push({
            severity: 'info',
            insight: 'Diversity metrics show reasonable balance',
            action: 'Continue monitoring and maintaining inclusive hiring and workplace culture'
        });
    }
    
    return recommendations;
}

function buildDistribution(items, keyFn) {
    return items.reduce((acc, item) => {
        const key = keyFn(item) || 'Unknown';
        acc[key] = (acc[key] || 0) + 1;
        return acc;
    }, {});
}

function formatPercent(count, total) {
    return total > 0 ? `${((count / total) * 100).toFixed(1)}%` : '0%';
}

function summarizeGender(dist, total) {
    const categories = Object.entries(dist)
        .map(([key, value]) => `${key}: ${formatPercent(value, total)}`)
        .join(' • ');
    return categories || 'No demographic data available';
}

function summarizeAge(dist, total) {
    const ordered = ['18-24', '25-34', '35-44', '45-54', '55+', 'Unknown'];
    const parts = ordered
        .filter(key => dist[key])
        .map(key => `${key}: ${formatPercent(dist[key], total)}`);
    return parts.length ? parts.join(' • ') : 'No age data available';
}

function renderTableRows(dist, total) {
    return Object.entries(dist)
        .sort((a, b) => b[1] - a[1])
        .map(([key, count]) => `<tr><td><strong>${key}</strong></td><td>${count}</td><td>${formatPercent(count, total)}</td></tr>`)
        .join('');
}

function printDiversity() {
    window.print();
}
</script>
