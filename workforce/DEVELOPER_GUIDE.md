# Developer Guidelines & Best Practices

## Code Standards & Architecture

This document outlines the development practices for the Workforce Analytics module.

---

## 📐 Architecture Overview

### MVC-like Pattern
```
Request → API Endpoint → Model → Database
           ↓
        Analytics/Employee Model
           ↓
        Prepared Statements → MySQL
           ↓
        JSON Response → AJAX
           ↓
        JavaScript → Chart.js/DOM Update
```

### Separation of Concerns
- **Models** (`models/`): Business logic and database operations
- **API** (`api/`): Request handling and data serialization
- **Frontend** (`public/`, `assets/`): User interface
- **Config** (`config/`): Environment and connection setup

---

## PHP Coding Standards

### Class Structure
```php
class ClassName {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Method description
     * 
     * @param Type $parameter Description
     * @return Type Description
     */
    public function methodName($parameter) {
        // Implementation
    }
}
```

### Database Operations
```php
// ✅ CORRECT - Using prepared statements
$query = "SELECT * FROM employees WHERE id = ?";
$result = $this->db->fetchOne($query, [$id], 'i');

// ❌ WRONG - SQL injection vulnerability
$query = "SELECT * FROM employees WHERE id = " . $id;
```

### Error Handling
```php
// ✅ CORRECT
try {
    $data = $this->model->getData();
    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// ❌ WRONG - No error handling
echo json_encode($model->getData());
```

---

## JavaScript Best Practices

### Async/Await Pattern
```javascript
// ✅ CORRECT - Using async/await
async function loadData() {
    try {
        const response = await fetch(url);
        const data = await response.json();
        console.log(data);
    } catch (error) {
        console.error('Error:', error);
    }
}

// ✅ ACCEPTABLE - Using .then()
fetch(url)
    .then(r => r.json())
    .then(data => console.log(data))
    .catch(error => console.error(error));
```

### Chart Management
```javascript
// ✅ CORRECT - Destroying old chart before creating new one
if (chartInstances.department) {
    chartInstances.department.destroy();
}

chartInstances.department = new Chart(ctx, {
    // Configuration
});

// ❌ WRONG - Memory leak from multiple instances
new Chart(ctx, config);
new Chart(ctx, config); // Second chart not cleaned up
```

### DOM Manipulation
```javascript
// ✅ CORRECT - Modern approach
const tbody = document.getElementById('table-body');
tbody.innerHTML = '';
data.forEach(item => {
    const row = document.createElement('tr');
    row.innerHTML = `<td>${item.name}</td>`;
    tbody.appendChild(row);
});

// ✅ ACCEPTABLE - Using template literals
const html = data.map(item => `
    <tr>
        <td>${item.name}</td>
    </tr>
`).join('');
tbody.innerHTML = html;
```

---

## CSS Best Practices

### Variable Usage
```css
/* ✅ CORRECT - Using CSS variables */
:root {
    --primary-color: #2c3e50;
    --text-color: #2c3e50;
}

.element {
    color: var(--text-color);
    background: var(--primary-color);
}

/* ✅ CORRECT - Descriptive variable names */
--shadow-subtle: 0 2px 8px rgba(0, 0, 0, 0.1);
--shadow-strong: 0 8px 20px rgba(0, 0, 0, 0.2);
```

### Responsive Design
```css
/* ✅ CORRECT - Mobile-first approach */
.grid {
    display: grid;
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (min-width: 1024px) {
    .grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

---

## Adding New Features

### Adding a New Dashboard Metric

**Step 1: Create Model Method**
```php
// models/Analytics.php
public function getNewMetric() {
    $query = "SELECT calculation FROM employees WHERE condition";
    $result = $this->db->fetchOne($query);
    return $result['calculation'];
}
```

**Step 2: Create API Endpoint**
```php
// api/new_metric.php
<?php
require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../models/Analytics.php';

try {
    $analytics = new Analytics();
    $metric = $analytics->getNewMetric();
    echo json_encode(['success' => true, 'data' => $metric]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
```

**Step 3: Update HTML**
```html
<!-- public/index.html -->
<div class="metric-card">
    <div class="metric-icon">🎯</div>
    <div class="metric-content">
        <h3>New Metric</h3>
        <p class="metric-value" id="new-metric">-</p>
    </div>
</div>
```

**Step 4: Update JavaScript**
```javascript
// assets/app.js
function loadNewMetric() {
    fetch('../api/new_metric.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('new-metric').textContent = data.data;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Add to loadDashboard()
function loadDashboard() {
    loadDashboardMetrics();
    loadNewMetric();  // Add this line
    // ... other calls
}
```

---

### Adding a New Chart

**Step 1: Add Canvas Element**
```html
<div class="chart-container">
    <h3>Chart Title</h3>
    <canvas id="newChart"></canvas>
</div>
```

**Step 2: Add to Chart Instances**
```javascript
const chartInstances = {
    // ... existing charts
    newChart: null
};
```

**Step 3: Create Load Function**
```javascript
function loadNewChart() {
    fetch('../api/new_data.php')
        .then(r => r.json())
        .then(data => {
            const labels = data.data.map(item => item.label);
            const values = data.data.map(item => item.value);
            
            const ctx = document.getElementById('newChart').getContext('2d');
            
            if (chartInstances.newChart) {
                chartInstances.newChart.destroy();
            }
            
            chartInstances.newChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Label',
                        data: values,
                        backgroundColor: '#3498db'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        });
}
```

---

### Adding New Filters

**Step 1: Update HTML**
```html
<div class="form-group">
    <label for="filter-new">New Filter:</label>
    <select id="filter-new">
        <option value="">All</option>
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
    </select>
</div>
```

**Step 2: Update API**
```php
// api/custom_report.php
if (!empty($filters['new_filter'])) {
    $query .= " AND column = ?";
    $params[] = $filters['new_filter'];
    $types .= 's';
}
```

**Step 3: Update JavaScript**
```javascript
function generateCustomReport() {
    const newFilter = document.getElementById('filter-new').value;
    
    let params = new URLSearchParams();
    if (newFilter) {
        params.append('new_filter', newFilter);
    }
    
    // ... fetch and display
}
```

---

## Database Query Optimization

### Use Indexes
```sql
-- ✅ GOOD - Indexed columns
SELECT * FROM employees WHERE department = 'Academics';
SELECT * FROM employees WHERE hire_date > '2020-01-01';
SELECT * FROM employees WHERE performance_score > 4.0;

-- ❌ SLOW - Unindexed calculation
SELECT * FROM employees WHERE YEAR(hire_date) = 2020;
```

### Optimize Complex Queries
```php
// ✅ CORRECT - Single optimized query
$query = "SELECT department, COUNT(*) as count 
          FROM employees 
          WHERE employment_status NOT IN ('Resigned', 'Terminated')
          GROUP BY department
          ORDER BY count DESC";

// ❌ INEFFICIENT - Multiple queries in loop
foreach ($departments as $dept) {
    $count = "SELECT COUNT(*) FROM employees WHERE department = ?";
    // Run for each department
}
```

---

## Testing Guidelines

### Testing an API Endpoint
```bash
# Basic test
curl http://localhost/work_analytics/api/dashboard_metrics.php

# Test with parameters
curl "http://localhost/work_analytics/api/custom_report.php?department=Academics"

# Pretty print JSON
curl http://localhost/work_analytics/api/dashboard_metrics.php | jq .
```

### Testing JavaScript Functions
```javascript
// In browser console (F12)
loadDashboard();
loadAtRiskEmployees();
generateCustomReport();

// Check variables
console.log(chartInstances);
console.log(allAtRiskEmployees);
console.log(customReportData);
```

### Testing Database
```sql
-- Verify data integrity
SELECT COUNT(*) FROM employees;
SELECT COUNT(DISTINCT department) FROM employees;
SELECT AVG(performance_score) FROM employees;
SELECT MIN(hire_date) FROM employees;
```

---

## Performance Optimization

### Frontend Performance
```javascript
// ✅ Lazy load charts
if (tabName === 'attrition') {
    loadAttritionData(); // Only load when tab is selected
}

// ✅ Debounce filter changes
let filterTimeout;
function onFilterChange() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        generateCustomReport();
    }, 300);
}

// ✅ Use requestAnimationFrame for DOM updates
requestAnimationFrame(() => {
    displayResults(data);
});
```

### Database Performance
```php
// ✅ Limit results
$query = "SELECT * FROM employees LIMIT 50";

// ✅ Select only needed columns
$query = "SELECT id, name, department FROM employees";
// Not: SELECT * FROM employees;

// ✅ Use COUNT(*) for counting
$query = "SELECT COUNT(*) FROM employees";
// Not: SELECT * then count in PHP
```

---

## Security Checklist

### Input Validation
```php
// ✅ CORRECT - Validate and sanitize
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// ❌ WRONG - No validation
$year = $_GET['year'];
```

### SQL Injection Prevention
```php
// ✅ CORRECT - Prepared statements
$stmt = $db->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// ❌ WRONG - String concatenation
$query = "SELECT * FROM employees WHERE id = " . $_GET['id'];
```

### Error Messages
```php
// ✅ CORRECT - Generic error messages
echo json_encode(['success' => false, 'message' => 'Query failed']);

// ❌ WRONG - Reveal database structure
echo json_encode(['success' => false, 'message' => $mysqli->error]);
```

---

## Naming Conventions

### Database Tables & Columns
```sql
-- ✅ CORRECT
- employees (plural, lowercase)
- performance_score (snake_case)
- employment_status (descriptive)

-- ❌ WRONG
- employee (singular)
- perfScore (camelCase)
- status (ambiguous)
```

### PHP Classes & Methods
```php
// ✅ CORRECT
class AnalyticsController
class getEmployeeCount()
class calculateAttritionRate()

// ❌ WRONG
class Analytics_Controller
class get_employee_count()
class calcAttrition()
```

### JavaScript Functions & Variables
```javascript
// ✅ CORRECT
function loadDashboardMetrics()
const chartInstances = {}
let isLoading = false

// ❌ WRONG
function LoadDashboardMetrics()
const CHART_INSTANCES = {}
var isloading;
```

### HTML IDs & Classes
```html
<!-- ✅ CORRECT -->
<div id="dashboard-metrics">
<div class="metric-card">

<!-- ❌ WRONG -->
<div id="Dashboard_Metrics">
<div class="MetricCard">
```

---

## Documentation Standards

### PHP Comments
```php
/**
 * Brief description of function
 * 
 * Longer description if needed.
 * Multiple lines supported.
 * 
 * @param Type $paramName Description
 * @param Type $paramName2 Description
 * @return Type Description
 * @throws Exception When something goes wrong
 */
public function methodName($paramName, $paramName2) {
    // Implementation
}
```

### JavaScript Comments
```javascript
/**
 * Brief description of function
 * 
 * @param {Type} paramName - Description
 * @returns {Type} Description
 */
function functionName(paramName) {
    // Implementation
}
```

---

## Debugging Tips

### Enable Query Logging
```php
// config/Database.php - Add logging
private function executeQuery($query, $params = []) {
    error_log("Query: " . $query);
    error_log("Params: " . json_encode($params));
    // ... execute
}
```

### Browser DevTools
```javascript
// Check network requests
// DevTools → Network tab

// Check console errors
// DevTools → Console tab

// Check stored data
// DevTools → Application → Local Storage

// Profile performance
// DevTools → Performance tab
```

### MySQL Slow Query Log
```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
-- Queries taking > 1 second are logged
```

---

## Version Control Best Practices

### Commit Messages
```
✅ CORRECT
git commit -m "Add employee at-risk prediction feature"
git commit -m "Fix: resolve chart memory leak in refresh"
git commit -m "Refactor: optimize salary query with indexes"

❌ WRONG
git commit -m "update"
git commit -m "fixed bugs"
git commit -m "changes"
```

### Branching Strategy
```
main                          (production)
└── feature/analytics         (new features)
└── fix/performance-issue     (bug fixes)
└── refactor/chart-update     (code improvements)
```

---

## Common Pitfalls to Avoid

1. **Memory Leaks in Charts**
   - Always destroy old chart instances before creating new ones

2. **Race Conditions**
   - Use async/await or .then() properly to handle timing

3. **N+1 Queries**
   - Don't loop queries; fetch all data in one query

4. **Hardcoded Values**
   - Use configuration files and constants

5. **Missing Error Handling**
   - Always wrap try-catch blocks around API calls

6. **Unescaped HTML**
   - Use textContent instead of innerHTML when possible

7. **Blocking Operations**
   - Don't use synchronous fetch or blocking PHP operations

8. **Missing Input Validation**
   - Always validate and sanitize user input

---

## Deployment Checklist

- [ ] Database backed up
- [ ] config/config.php has production credentials
- [ ] Error reporting disabled
- [ ] HTTPS enabled (if public)
- [ ] All API endpoints tested
- [ ] Charts render correctly
- [ ] Tables display data
- [ ] Filters work properly
- [ ] Export features tested
- [ ] Mobile responsiveness verified
- [ ] Performance acceptable
- [ ] Security headers configured
- [ ] Database maintenance scheduled

---

## Future Enhancements

1. **Authentication & Authorization**
2. **User Roles & Permissions**
3. **Advanced Predictive Analytics**
4. **PDF Export with Formatting**
5. **Real-time Dashboards**
6. **Email Notifications**
7. **Data Import/Export**
8. **Custom Dashboards**
9. **API Rate Limiting**
10. **Activity Logging**

---

## Support & Resources

- **PHP Documentation**: https://www.php.net/docs.php
- **Chart.js Docs**: https://www.chartjs.org/docs/latest/
- **MySQL Docs**: https://dev.mysql.com/doc/
- **MDN Web Docs**: https://developer.mozilla.org/

---

**Last Updated**: 2026  
**Version**: 1.0.0  
**Status**: Complete Guidelines
