# Workforce Analytics & Reporting Module

A comprehensive HR analytics and reporting system for School Management Systems built with PHP OOP, MySQL, and Chart.js.

## 📋 Features

### 1. **HR Dashboard & Metrics**
- Real-time employee count (Total Employees, Teachers, Staff)
- New hires this year tracking
- Average salary and performance metrics
- Interactive charts showing employee distribution by department

### 2. **Attrition & Turnover Analysis**
- Calculate and display turnover rates
- Monthly attrition trends visualization
- Track separated employees (resigned, retired, terminated)
- Performance distribution analysis

### 3. **Diversity & Inclusion Reports**
- Gender distribution analysis
- Age group representation
- Department diversity metrics
- Salary statistics by department
- Comprehensive diversity charts

### 4. **Predictive Analytics**
- Identify employees at risk of attrition based on:
  - Low performance scores (< 3.0)
  - High absence days (> 15)
  - Tenure and performance combination
- Risk classification: High, Medium, Low
- Interactive employee risk table

### 5. **Custom HR Reports**
- Filter employees by:
  - Department
  - Employment type
  - Hire date range
- Export reports to CSV
- Printable dashboard format

## 🛠️ Technical Stack

- **Backend**: PHP 7.4+ with OOP (MVC Pattern)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Charts**: Chart.js
- **API**: RESTful JSON endpoints
- **Security**: Prepared statements (SQL injection prevention)

## 📁 Project Structure

```
work_analytics/
├── config/
│   ├── config.php              # Configuration file
│   └── Database.php            # Database connection class
├── models/
│   ├── Employee.php            # Employee model
│   └── Analytics.php           # Analytics model
├── api/
│   ├── dashboard_metrics.php
│   ├── department_distribution.php
│   ├── gender_distribution.php
│   ├── age_distribution.php
│   ├── attrition_data.php
│   ├── at_risk_employees.php
│   ├── performance_distribution.php
│   ├── salary_statistics.php
│   ├── tenure_distribution.php
│   └── custom_report.php
├── public/
│   └── index.html              # Main dashboard UI
├── assets/
│   ├── app.js                  # Main JavaScript
│   └── style.css               # Stylesheet
├── database/
│   └── schema.sql              # Database schema
└── README.md
```

## 🚀 Installation

### Step 1: Database Setup

1. Open phpMyAdmin or your MySQL client
2. Run the SQL script from `database/schema.sql`:
```sql
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;
-- Run the entire schema.sql file
```

Or import directly:
```bash
mysql -u root -p < database/schema.sql
```

### Step 2: Configure Database Connection

Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your password
define('DB_NAME', 'school_management');
```

### Step 3: Place Files

1. Copy the entire `work_analytics` folder to:
   - Windows: `C:\xampp\htdocs\work_analytics\`
   - Linux: `/var/www/html/work_analytics/`
   - macOS: `/Library/WebServer/Documents/work_analytics/`

### Step 4: Access the Application

Open your browser and navigate to:
```
http://localhost/work_analytics/public/index.html
```

## 📊 Dashboard Sections

### Dashboard Tab
- Key metrics at a glance
- Department distribution chart
- Gender distribution pie chart
- Age group distribution line chart
- Tenure distribution radar chart

### Attrition & Turnover Tab
- Attrition rate calculation
- Monthly attrition trends
- Performance distribution
- Recently separated employees table
- Year selector for analysis

### Diversity & Inclusion Tab
- Gender diversity visualization
- Age group breakdown
- Department representation
- Salary statistics by department

### Performance Tab
- Employee performance levels distribution
- Salary distribution by department
- Employees at risk of attrition
- Risk level filtering (High/Medium/Low)

### Custom Reports Tab
- Advanced filtering options:
  - Filter by department
  - Filter by employment type
  - Filter by hire date range
- CSV export functionality
- Printable report format

## 🔌 API Endpoints

All endpoints return JSON responses. Base URL: `/api/`

### Dashboard Metrics
```
GET /api/dashboard_metrics.php
Response: {
    "total_employees": 35,
    "total_teachers": 10,
    "total_staff": 25,
    "new_hires": 3,
    "avg_salary": 52500.00,
    "avg_performance": 4.1
}
```

### Department Distribution
```
GET /api/department_distribution.php
Response: [
    {"department": "Academics", "count": 10},
    {"department": "Finance", "count": 3},
    ...
]
```

### Attrition Data
```
GET /api/attrition_data.php?year=2026
Response: {
    "attrition_data": [...],
    "attrition_rate": 5.25,
    "separated_employees": [...]
}
```

### Employees at Risk
```
GET /api/at_risk_employees.php
Response: {
    "High": [...],
    "Medium": [...],
    "Low": [...]
}
```

### Custom Report
```
GET /api/custom_report.php?department=Academics&employment_type=Full-time&hire_date_from=2020-01-01
Response: [
    {employee_data}
]
```

## 🗄️ Database Schema

### Employees Table
```sql
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other'),
    age INT,
    department VARCHAR(100),
    position VARCHAR(100),
    hire_date DATE,
    employment_status ENUM('Full-time', 'Part-time', 'Contract', 'Temporary', 'Resigned', 'Terminated', 'Retired'),
    salary DECIMAL(10, 2),
    performance_score DECIMAL(3, 2),
    absence_days INT,
    separation_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

## 🎨 Styling Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Color Scheme**: Professional blue/dark color palette
- **Interactive Charts**: Hover effects and tooltips
- **Print-Friendly**: Special styling for printing
- **Accessibility**: Semantic HTML and proper contrast

## 📱 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🔒 Security Features

- **SQL Injection Prevention**: Prepared statements
- **Data Validation**: Server-side filtering
- **CORS Headers**: API access control
- **UTF-8 Encoding**: Proper character handling

## 📈 Performance Optimization

- Database indexing on frequently queried columns
- Singleton pattern for database connection
- Chart.js optimization
- Efficient query structure

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/config.php`
- Ensure database `school_management` exists

### API Endpoints Not Working
- Check if PHP error reporting is enabled
- Verify file paths are correct
- Check browser console for CORS errors

### Charts Not Displaying
- Verify Chart.js CDN is accessible
- Check browser console for JavaScript errors
- Ensure API endpoints return valid JSON

## 📝 Sample Data

The schema includes 30+ sample employees with:
- Different departments (Administration, Academics, Finance, HR, IT, Support Services)
- Various positions and salary ranges
- Performance scores
- Absence records
- Some separated employees (resigned, terminated, retired)
- High-risk employees for analysis

## 🔧 Customization

### Add New Department
1. Modify the filter dropdown in `public/index.html`
2. Update department options in `api/custom_report.php`
3. Insert employees with new department in database

### Change Color Scheme
Edit CSS variables in `assets/style.css`:
```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --danger-color: #e74c3c;
    /* ... more colors ... */
}
```

### Add New Charts
1. Create new data fetching function in `assets/app.js`
2. Initialize Chart.js instance
3. Add HTML canvas element in `public/index.html`

## 📚 Code Examples

### Create Employee Record
```php
$employee = new Employee();
$data = [
    'name' => 'John Doe',
    'gender' => 'Male',
    'department' => 'Academics',
    'position' => 'Teacher',
    'hire_date' => '2024-01-15',
    'employment_status' => 'Full-time',
    'salary' => 50000.00,
    'performance_score' => 4.5,
    'absence_days' => 2,
    'age' => 35
];
$employee->createEmployee($data);
```

### Get At-Risk Employees
```php
$analytics = new Analytics();
$atRisk = $analytics->getEmployeesAtRisk();
```

### Fetch Data via AJAX
```javascript
fetch('../api/dashboard_metrics.php')
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
    .catch(error => console.error('Error:', error));
```

## 📄 License

This project is for educational and organizational use.

## 👨‍💼 Support

For issues or questions:
1. Check the troubleshooting section
2. Review the API documentation
3. Check browser console for errors
4. Verify database connection

## 🎯 Future Enhancements

- Multi-user authentication
- Role-based access control
- Email notifications
- Advanced predictive analytics
- Machine learning integration
- Real-time dashboards
- Data export to PDF with formatting
- Employee feedback system
- Performance review tracking

---

**Version**: 1.0.0  
**Last Updated**: 2026  
**Status**: Production Ready
