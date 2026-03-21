# WFA System - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Verify API Endpoints
All 5 endpoints should be in `/api/wfa/`:
```
✓ dashboard_metrics.php
✓ at_risk_employees.php
✓ attrition_metrics.php
✓ department_analytics.php
✓ diversity_metrics.php
```

Test one endpoint:
```
http://localhost/capstone_hr_management_system/api/wfa/dashboard_metrics.php
```
Expected: JSON response with metrics data

---

### Step 2: Run Data Population Script (First Time)
```bash
php workforce/scripts/populate_wfa_daily.php
```

Expected output:
```
[2026-03-21 XX:XX:XX] [INFO] Starting WFA daily population process...
[2026-03-21 XX:XX:XX] [INFO] ✓ Employee metrics updated
[2026-03-21 XX:XX:XX] [INFO] ✓ Department analytics updated
[2026-03-21 XX:XX:XX] [INFO] ✓ Risk assessments updated (150 employees)
[2026-03-21 XX:XX:XX] [INFO] ✓ Monthly attrition summary updated
[2026-03-21 XX:XX:XX] [INFO] ✅ WFA daily population completed successfully!
```

---

### Step 3: Access Analytics Page
```
http://localhost/capstone_hr_management_system/workforce/analytics.php
```

Should display:
- 6 metric cards (total employees, new hires, at-risk, performance, departments, salary)
- 4 interactive charts (department, gender, attrition, separation types)
- High-risk employees table
- Department statistics table

---

### Step 4: Test API Endpoints

#### Dashboard Metrics:
```
GET /api/wfa/dashboard_metrics.php?date=2026-03-21
```

#### At-Risk Employees:
```
GET /api/wfa/at_risk_employees.php?limit=10&risk_level=high
```

#### Attrition Metrics:
```
GET /api/wfa/attrition_metrics.php?year=2026&month=3
```

#### Department Analytics:
```
GET /api/wfa/department_analytics.php?date=2026-03-21
```

#### Diversity Metrics:
```
GET /api/wfa/diversity_metrics.php?category=gender
```

---

### Step 5: Add Widget to Dashboard (Optional)

In any dashboard file, add:
```php
<?php include 'workforce/public/wfa_widgets.php'; ?>
```

This will display:
- At-risk count
- Attrition summary
- Average tenure
- Top 5 at-risk employees
- Link to full analytics

---

## 📋 Cron Job Setup

### Linux/Unix:
```bash
crontab -e

# Add this line:
0 23 * * * /usr/bin/php /var/www/html/capstone_hr_management_system/workforce/scripts/populate_wfa_daily.php >> /var/www/html/capstone_hr_management_system/logs/wfa_population.log 2>&1
```

Runs daily at 11:59 PM

### Windows Task Scheduler:
1. Open Task Scheduler
2. Create Basic Task
3. Set:
   - Name: "WFA Daily Population"
   - Trigger: Daily at 23:59
   - Action: Start Program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\capstone_hr_management_system\workforce\scripts\populate_wfa_daily.php`

---

## 📊 What Gets Calculated Daily

✅ **Employee Metrics**
- Total active employees
- Teachers vs Staff count
- New hires this year
- Average salary
- Average performance score
- Department count

✅ **Department Analytics**
- Employee count per department
- Average salary by department
- Average performance by department
- Average tenure by department
- Vacancy tracking

✅ **Risk Assessment** (Per Employee)
- Risk Level: High / Medium / Low
- Risk Score: 0-100
- Risk Factors: low_performance, high_absence, low_tenure
- Performance score tracking
- Absence days tracking
- Tenure months tracking

✅ **Monthly Attrition Summary**
- Total separations this month
- Voluntary vs involuntary separations
- Attrition rate percentage
- Separation types breakdown

---

## 🔍 Monitoring

### Check Log File:
```bash
tail -f logs/wfa_population.log
```

### Verify Data in Database:
```sql
-- Check last update
SELECT MAX(updated_at) FROM wfa_employee_metrics;

-- Count at-risk employees
SELECT COUNT(*) FROM wfa_risk_assessment WHERE risk_level = 'high';

-- View recent attrition
SELECT * FROM wfa_attrition_tracking ORDER BY separation_date DESC LIMIT 5;
```

---

## 📱 Features Overview

### Dashboard Metrics API
- Real-time KPI data
- Employee count breakdown
- Performance averages
- Attrition summary

### At-Risk Employees API
- Employee risk scores
- Risk factors breakdown
- Performance tracking
- Absence monitoring
- Pagination support

### Attrition Metrics API
- Monthly trends
- Separation types
- Department breakdowns
- Recent separations (last 30 days)

### Department Analytics API
- Department statistics
- Vacancy rates
- Salary analysis
- Performance distribution
- Optional single-department view

### Diversity Metrics API
- Gender distribution
- Age group breakdown
- Tenure distribution
- Percentage-based metrics
- Category filtering

---

## 🎯 Performance Targets

✅ API Response Time: < 1 second
✅ Data Population Time: < 2 minutes
✅ Chart Load Time: < 500ms
✅ Database Query Performance: Indexed columns

---

## 🆘 Troubleshooting

**Issue**: Analytics page shows no charts
- [ ] Check if populate_wfa_daily.php has been run
- [ ] Verify API endpoints return data in browser
- [ ] Check browser console for JavaScript errors
- [ ] Ensure Chart.js library is loaded

**Issue**: API returns empty data
- [ ] Check database has data: `SELECT COUNT(*) FROM wfa_employee_metrics;`
- [ ] Verify auth/database.php connection works
- [ ] Check file permissions on API files (should be 644)
- [ ] Review logs/wfa_population.log for errors

**Issue**: Cron job not running
- [ ] Check cron service is running: `sudo systemctl status cron`
- [ ] Verify permissions on script file
- [ ] Check logs: `grep CRON /var/log/syslog`
- [ ] Test manually: `php workforce/scripts/populate_wfa_daily.php`

**Issue**: Risk scores not calculating
- [ ] Verify employees table has data
- [ ] Check performance_reviews table has data
- [ ] Review calculation logic in populate_wfa_daily.php
- [ ] Check database query results manually

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| WFA_IMPLEMENTATION_COMPLETE.md | Full implementation guide |
| WFA_QUICK_REFERENCE.md | Quick reference checklist |
| This file | Quick start guide |
| api/wfa/dashboard_metrics.php | API endpoint code |
| workforce/analytics.php | Analytics dashboard page |
| workforce/scripts/populate_wfa_daily.php | Daily data population script |
| workforce/public/wfa_widgets.php | Dashboard widget component |

---

## ✅ Completion Checklist

- [x] Create WFA database schema (17 tables)
- [x] Create API endpoints (5 files)
- [x] Create data population script
- [x] Create analytics page with charts
- [x] Create dashboard widgets
- [ ] Setup cron job for daily refresh
- [ ] Test all components with live data
- [ ] Add widgets to main dashboard
- [ ] Verify data refresh is working
- [ ] Deploy to production

---

## 🎉 You're All Set!

The WFA system is ready to use. Just:
1. Run the population script once to populate initial data
2. Access `/workforce/analytics.php` to view analytics
3. Setup cron job for daily automatic refresh
4. Optionally add widgets to your main dashboard

For detailed documentation, see **WFA_IMPLEMENTATION_COMPLETE.md**
