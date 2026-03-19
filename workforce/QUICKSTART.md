# 🚀 Quick Start Guide - Workforce Analytics Module

## ⚡ 5-Minute Setup

### Prerequisites
- XAMPP, WAMP, or LAMP stack installed
- MySQL running
- PHP 7.4 or higher

### Step 1: Import Database (1 minute)
```bash
# Open MySQL command line or phpMyAdmin
# Copy and paste entire content from: database/schema.sql
# Or run from terminal:
mysql -u root -p < database/schema.sql
```

**Expected Output:**
```
Database created successfully
Table employees created with 30+ sample records
```

### Step 2: Update Configuration (30 seconds)
Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');  // Change this
define('DB_NAME', 'school_management');
```

### Step 3: Access Application (30 seconds)
```
Open browser: http://localhost/work_analytics/public/index.html
```

✅ You should now see the dashboard with all charts and metrics!

---

## 🎯 What You Can Do

### View Dashboard
1. **Metrics**: See total employees, teachers, staff, new hires
2. **Charts**: Department, gender, age, tenure distribution
3. **Real-time Data**: All metrics update from database

### Analyze Attrition
1. Go to "Attrition & Turnover" tab
2. Select year from dropdown
3. See:
   - Attrition rate percentage
   - Monthly trends
   - Separated employees list
   - Performance distribution

### Check Diversity
1. Go to "Diversity & Inclusion" tab
2. See:
   - Gender representation
   - Age groups breakdown
   - Department diversity
   - Salary statistics

### Find At-Risk Employees
1. Go to "Performance" tab
2. See employees at risk of leaving
3. Filter by: High Risk, Medium Risk, Low Risk
4. View details: performance, absence days, tenure

### Generate Custom Reports
1. Go to "Custom Reports" tab
2. Apply filters:
   - Department
   - Employment type
   - Hire date range
3. Click "Generate Report"
4. Export as CSV or Print

---

## 📊 Sample Data Included

The database comes with:
- **35+ employees** across 6 departments
- **10 teachers** in Academics
- **25 support staff** in various departments
- **3 separated employees** (for attrition analysis)
- **4 at-risk employees** (low performance/high absence)
- **Salary range**: $28,000 - $85,000
- **Performance scores**: 2.2 - 4.8 / 5.0

---

## 🔍 API Endpoints Reference

All endpoints return JSON:

| Endpoint | Purpose |
|----------|---------|
| `/api/dashboard_metrics.php` | Get key metrics |
| `/api/department_distribution.php` | Department breakdown |
| `/api/gender_distribution.php` | Gender stats |
| `/api/age_distribution.php` | Age groups |
| `/api/attrition_data.php?year=2026` | Attrition data |
| `/api/at_risk_employees.php` | At-risk employees |
| `/api/performance_distribution.php` | Performance levels |
| `/api/salary_statistics.php` | Salary data |
| `/api/tenure_distribution.php` | Tenure breakdown |
| `/api/custom_report.php?dept=X&type=Y` | Custom reports |

---

## 🎨 Key Features

✅ **Responsive Design** - Works on mobile, tablet, desktop  
✅ **Interactive Charts** - Hover effects, zoom capabilities  
✅ **Real-time Data** - Updates from live database  
✅ **Export Options** - CSV export, printable format  
✅ **Advanced Filtering** - Department, date, type filters  
✅ **At-Risk Detection** - Predictive analytics  
✅ **Multiple Views** - 5 different dashboard tabs  
✅ **Professional UI** - Modern color scheme, smooth animations  

---

## 🛠️ File Locations

```
work_analytics/
├── public/index.html              ← Open this in browser
├── api/                           ← API endpoints
├── models/                        ← Employee, Analytics classes
├── config/                        ← Database connection
├── assets/
│   ├── app.js                     ← Main JavaScript
│   └── style.css                  ← Styling
└── database/schema.sql            ← Database setup
```

---

## 🐛 Common Issues & Solutions

### Issue: "Cannot connect to database"
```
Solution: 
1. Check MySQL is running
2. Verify credentials in config/config.php
3. Ensure database 'school_management' exists
```

### Issue: "Charts not showing"
```
Solution:
1. Check browser console (F12)
2. Verify Chart.js CDN is accessible
3. Ensure API endpoints return valid data
```

### Issue: "404 API errors"
```
Solution:
1. Verify file paths in api/ folder
2. Check browser console for exact error
3. Ensure all PHP files exist
```

### Issue: "No data displaying"
```
Solution:
1. Import schema.sql again
2. Verify employee records exist: 
   SELECT COUNT(*) FROM employees;
3. Check database credentials
```

---

## 📈 Understanding Metrics

| Metric | Meaning |
|--------|---------|
| **Total Employees** | Active employees excluding resigned/terminated |
| **Total Teachers** | Positions containing "Teacher" |
| **Total Staff** | Non-teaching positions |
| **New Hires** | Employees hired in current year |
| **Attrition Rate** | % of employees who left during year |
| **Performance Score** | 1-5 scale, 4+ is excellent |
| **Absence Days** | Days absent from work |
| **Risk Level** | High/Medium/Low based on performance + absence |

---

## 🎓 Learning Path

1. **Start**: View Dashboard tab - understand basic metrics
2. **Explore**: Switch to each tab to see different analyses
3. **Analyze**: Go to Performance tab - identify at-risk employees
4. **Report**: Use Custom Reports to filter and export data
5. **Export**: Download CSV or print for presentation

---

## 🔐 Security

- ✅ SQL injection prevention (prepared statements)
- ✅ Input validation and sanitization
- ✅ CORS headers for API security
- ✅ UTF-8 encoding for special characters

---

## 📞 Need Help?

1. Check README.md for detailed documentation
2. Review API endpoints in public/index.html
3. Check browser console (F12) for errors
4. Verify database has sample data: `SELECT COUNT(*) FROM employees;`
5. Test API directly: Visit `http://localhost/work_analytics/api/dashboard_metrics.php`

---

## ✨ Next Steps

After setup:
1. Add your own employee data
2. Customize department names
3. Modify CSS for your brand colors
4. Add more analytics as needed
5. Integrate with your HR system

---

**Status**: ✅ Ready to use  
**Version**: 1.0.0  
**Created**: 2026
