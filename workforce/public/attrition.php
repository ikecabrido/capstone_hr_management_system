<!-- TAB: ATTRITION & TURNOVER -->
<div class="wfa-container" id="attritionContainer">
    <div class="wfa-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading Attrition Data...
    </div>
</div>

<script>
async function loadAttritionTab() {
    const container = document.getElementById('attritionContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="wfa-loading">
            <i class="fas fa-spinner fa-spin"></i> Loading Attrition Data...
        </div>
    `;

    try {
        const currentYear = new Date().getFullYear();
        const selectedYear = document.getElementById('attritionYearSelect')?.value || currentYear;
        const apiUrl = `/capstone_hr_management_system/workforce/api/attrition_data.php?year=${selectedYear}`;

        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error(`Attrition API error: ${response.status}`);

        const result = await response.json();
        if (!result.success) throw new Error(result.message || 'Unable to load attrition data');

        renderAttritionPage(result.data, parseInt(selectedYear, 10));
    } catch (error) {
        container.innerHTML = `
            <div style="padding: 20px; color: #d32f2f; background: #fff1f0; border: 1px solid #f8d7da; border-radius: 10px;">
                <strong>Error loading attrition metrics:</strong> ${error.message}
            </div>
        `;
        console.error('Attrition load error:', error);
    }
}

function generateAttritionRecommendations(data) {
    const recommendations = [];
    const topReasons = data.top_reasons || [];
    const attritionRate = data.attrition_rate || 0;
    
    if (attritionRate > 20) {
        recommendations.push({
            severity: 'critical',
            insight: `High attrition rate of ${attritionRate.toFixed(1)}%`,
            action: 'Conduct immediate employee retention audit and exit interviews'
        });
    } else if (attritionRate > 15) {
        recommendations.push({
            severity: 'warning',
            insight: `Elevated attrition rate of ${attritionRate.toFixed(1)}%`,
            action: 'Review retention strategies and employee engagement programs'
        });
    }
    
    if (topReasons.length > 0 && topReasons[0].reason) {
        const topReason = topReasons[0];
        if (topReason.reason.toLowerCase().includes('salary')) {
            recommendations.push({
                severity: 'critical',
                insight: `Top resignation reason: ${topReason.reason} (${topReason.count} cases)`,
                action: 'Conduct market salary analysis and review compensation package'
            });
        } else if (topReason.reason.toLowerCase().includes('career')) {
            recommendations.push({
                severity: 'high',
                insight: `Key concern: ${topReason.reason} (${topReason.count} cases)`,
                action: 'Strengthen career development programs and promotion pathways'
            });
        }
    }
    
    if (recommendations.length === 0) {
        recommendations.push({
            severity: 'info',
            insight: 'Attrition metrics within acceptable range',
            action: 'Continue current retention initiatives and monitor trends'
        });
    }
    
    return recommendations;
}

function generateRecommendationsHtml(recommendations) {
    const severityConfig = {
        critical: { color: '#dc3545', emoji: '⚠️', bgColor: '#fff5f5' },
        warning: { color: '#ffc107', emoji: '⚡', bgColor: '#fffbf0' },
        high: { color: '#ff9800', emoji: '🔍', bgColor: '#fff8f0' },
        medium: { color: '#2196f3', emoji: '💡', bgColor: '#f0f7ff' },
        info: { color: '#17a2b8', emoji: '✅', bgColor: '#f0f9ff' }
    };
    
    const recHtml = recommendations.map(rec => {
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
    
    return `
        <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
            <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #2e3a59; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 24px;">📋</span> HR Recommendations
            </h3>
            <div>${recHtml}</div>
        </div>
    `;
}

function generateAttritionRecommendations(data) {
    const recommendations = [];
    const topReasons = data.top_reasons || [];
    const attritionRate = data.attrition_rate || 0;
    
    if (attritionRate > 20) {
        recommendations.push({
            severity: 'critical',
            insight: `High attrition rate of ${attritionRate.toFixed(1)}%`,
            action: 'Conduct immediate employee retention audit and exit interviews'
        });
    } else if (attritionRate > 15) {
        recommendations.push({
            severity: 'warning',
            insight: `Elevated attrition rate of ${attritionRate.toFixed(1)}%`,
            action: 'Review retention strategies and employee engagement programs'
        });
    }
    
    if (topReasons.length > 0 && topReasons[0].reason) {
        const topReason = topReasons[0];
        if (topReason.reason.toLowerCase().includes('salary')) {
            recommendations.push({
                severity: 'critical',
                insight: `Top resignation reason: ${topReason.reason} (${topReason.count} cases)`,
                action: 'Conduct market salary analysis and review compensation package'
            });
        } else if (topReason.reason.toLowerCase().includes('career')) {
            recommendations.push({
                severity: 'high',
                insight: `Key concern: ${topReason.reason} (${topReason.count} cases)`,
                action: 'Strengthen career development programs and promotion pathways'
            });
        }
    }
    
    if (recommendations.length === 0) {
        recommendations.push({
            severity: 'info',
            insight: 'Attrition metrics within acceptable range',
            action: 'Continue current retention initiatives and monitor trends'
        });
    }
    
    return recommendations;
}

function renderAttritionPage(data, year) {
    const container = document.getElementById('attritionContainer');
    const attritionData = data.attrition_data || [];
    const separatedEmployees = data.separated_employees || [];
    const topReasons = data.top_reasons || [];
    const totalSeparated = data.total_separated || separatedEmployees.length;
    const attritionRate = data.attrition_rate || 0;

    const resignationTypeTotals = attritionData.reduce((acc, item) => {
        const type = item.resignation_type || 'Unknown';
        acc[type] = (acc[type] || 0) + (parseInt(item.count, 10) || 0);
        return acc;
    }, {});

    const months = [...new Set(attritionData.map(item => item.month))].sort();
    const monthlyTotals = months.map(month => {
        return attritionData
            .filter(item => item.month === month)
            .reduce((sum, item) => sum + (parseInt(item.count, 10) || 0), 0);
    });

    container.innerHTML = `
        <div class="wfa-dashboard-hero" style="margin-bottom: 24px; padding: 24px; background: #f5f7ff; border-radius: 16px; box-shadow: 0 12px 24px rgba(33, 40, 82, 0.06);">
            <h2 style="margin: 0 0 10px; font-size: 28px; color: #253858;">Attrition & Turnover Analysis</h2>
            <p style="margin: 0; color: #566278; max-width: 760px; line-height: 1.6;">Tracks employee exits and identifies why people leave. Attrition is employees who leave and are not replaced; turnover is the broader replacement cycle.</p>
        </div>

        ${generateRecommendationsHtml(generateAttritionRecommendations(data))}

        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; align-items: center;">
            <div>
                <p style="margin: 0 0 6px 0; font-weight: 600; color: #333;">Year</p>
                <select id="attritionYearSelect" style="padding: 10px 12px; border: 1px solid #ccd0d8; border-radius: 8px; min-width: 120px;">
                    ${generateYearOptions(year)}
                </select>
            </div>
            <button class="wfa-btn wfa-btn-info" onclick="printAttrition()" style="padding: 12px 18px; border-radius: 8px;">
                <i class="fas fa-print"></i> Print Attrition Report
            </button>
        </div>

        <div class="wfa-metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="wfa-metric-card success" style="padding: 20px; background: linear-gradient(135deg, #009688 0%, #26a69a 100%); color: white; border-radius: 14px;">
                <div style="font-size: 18px; opacity: 0.85; margin-bottom: 8px;">Total Separated</div>
                <div style="font-size: 34px; font-weight: 700;">${totalSeparated}</div>
                <div style="opacity: 0.85; margin-top: 8px;">Employees who left in ${year}</div>
            </div>
            <div class="wfa-metric-card danger" style="padding: 20px; background: linear-gradient(135deg, #f44336 0%, #ff6f61 100%); color: white; border-radius: 14px;">
                <div style="font-size: 18px; opacity: 0.85; margin-bottom: 8px;">Attrition Rate</div>
                <div style="font-size: 34px; font-weight: 700;">${attritionRate}%</div>
                <div style="opacity: 0.85; margin-top: 8px;">Left vs active workforce</div>
            </div>
            <div class="wfa-metric-card info" style="padding: 20px; background: linear-gradient(135deg, #3f51b5 0%, #5c6bc0 100%); color: white; border-radius: 14px;">
                <div style="font-size: 18px; opacity: 0.85; margin-bottom: 8px;">Top Reason</div>
                <div style="font-size: 24px; font-weight: 700; line-height: 1.2;">${topReasons[0] ? topReasons[0].reason : 'No data'}</div>
                <div style="opacity: 0.85; margin-top: 8px;">Most frequent exit reason</div>
            </div>
            <div class="wfa-metric-card warning" style="padding: 20px; background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%); color: white; border-radius: 14px;">
                <div style="font-size: 18px; opacity: 0.85; margin-bottom: 8px;">Turnover Types</div>
                <div style="font-size: 24px; font-weight: 700;">${Object.keys(resignationTypeTotals).length}</div>
                <div style="opacity: 0.85; margin-top: 8px;">Resignation categories</div>
            </div>
        </div>

        <div class="wfa-table-container" style="background: white; padding: 24px; border-radius: 14px; box-shadow: 0 10px 20px rgba(16, 24, 40, 0.06);">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 18px;">
                <h3 style="margin: 0; font-size: 20px;">Recent Separated Employees</h3>
                <p style="margin: 0; color: #667085;">Showing up to 50 most recent exits.</p>
            </div>
            <table class="wfa-table" style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                <thead>
                    <tr style="background: #f8fafc; color: #102a43; text-align: left;">
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Name</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Department</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Position</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Left On</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Last Working Day</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    ${separatedEmployees.length > 0 ? separatedEmployees.map(emp => {
                        const separationDate = formatDate(emp.separation_date);
                        const lastWorking = emp.last_working_date ? formatDate(emp.last_working_date) : 'N/A';
                        const reasonText = emp.reason ? emp.reason : 'Not provided';
                        const employeeName = emp.name || (emp.employee_id ? `ID: ${emp.employee_id}` : 'Unknown');
                        return `
                            <tr>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${employeeName}</td>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${emp.department || 'N/A'}</td>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${emp.position || 'N/A'}</td>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${separationDate}</td>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${lastWorking}</td>
                                <td style="padding: 14px 16px; border-bottom: 1px solid #e2e8f0;">${reasonText}</td>
                            </tr>
                        `;
                    }).join('') : `
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">No separation records available for ${year}.</td>
                        </tr>
                    `}
                </tbody>
            </table>
        </div>
    `;

    const yearSelectElement = document.getElementById('attritionYearSelect');
    if (yearSelectElement) {
        yearSelectElement.onchange = loadAttritionTab;
    }
}

function generateYearOptions(selectedYear) {
    const currentYear = new Date().getFullYear();
    const years = [currentYear, currentYear - 1, currentYear - 2, currentYear - 3];
    return years.map(year => `<option value="${year}" ${year === selectedYear ? 'selected' : ''}>${year}</option>`).join('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function printAttrition() {
    window.print();
}

if (typeof loadAttritionTab === 'function') {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('attritionContainer')) {
            loadAttritionTab();
        }
    });
}
</script>
