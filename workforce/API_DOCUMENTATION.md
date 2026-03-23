# API Documentation

Complete API reference for Workforce Analytics Module

---

## Base URL
```
http://localhost/work_analytics/api/
```

All responses are in JSON format with error handling.

---

## 1. Dashboard Metrics

### Endpoint
```
GET /dashboard_metrics.php
```

### Description
Returns key metrics for the HR dashboard including total employees, teachers, staff, new hires, and averages.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": {
    "total_employees": 28,
    "total_teachers": 8,
    "total_staff": 20,
    "new_hires": 3,
    "avg_salary": 52345.67,
    "avg_performance": 4.15
  }
}
```

### Status Codes
- `200 OK` - Success
- `500 Internal Server Error` - Database error

### Notes
- Excludes resigned and terminated employees from totals
- Performance score is on 1-5 scale
- Salary is average of all active employees
- New hires are calculated for current year

---

## 2. Department Distribution

### Endpoint
```
GET /department_distribution.php
```

### Description
Returns employee count grouped by department.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "department": "Academics",
      "count": 8
    },
    {
      "department": "Administration",
      "count": 2
    },
    {
      "department": "Finance",
      "count": 3
    },
    {
      "department": "HR",
      "count": 3
    },
    {
      "department": "IT",
      "count": 4
    },
    {
      "department": "Support Services",
      "count": 4
    }
  ]
}
```

### Usage Example
```javascript
fetch('/api/department_distribution.php')
  .then(r => r.json())
  .then(data => console.log(data.data));
```

---

## 3. Gender Distribution

### Endpoint
```
GET /gender_distribution.php
```

### Description
Returns employee count by gender.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "gender": "Male",
      "count": 16
    },
    {
      "gender": "Female",
      "count": 12
    }
  ]
}
```

### Chart Type
- Pie Chart (recommended)
- Doughnut Chart
- Bar Chart

---

## 4. Age Group Distribution

### Endpoint
```
GET /age_distribution.php
```

### Description
Returns employee count grouped by age ranges.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "age_group": "18-24",
      "count": 3
    },
    {
      "age_group": "25-34",
      "count": 10
    },
    {
      "age_group": "35-44",
      "count": 8
    },
    {
      "age_group": "45-54",
      "count": 5
    },
    {
      "age_group": "55+",
      "count": 2
    }
  ]
}
```

### Age Ranges
- 18-24: Under 25
- 25-34: 25 to 34
- 35-44: 35 to 44
- 45-54: 45 to 54
- 55+: 55 and above

---

## 5. Attrition Data

### Endpoint
```
GET /attrition_data.php?year=2026
```

### Description
Returns attrition rate and separated employee data for specified year.

### Parameters
- `year` (optional): Year for analysis (default: current year)

### Response
```json
{
  "success": true,
  "data": {
    "attrition_data": [
      {
        "month": "2026-01",
        "employment_status": "Resigned",
        "count": 1
      },
      {
        "month": "2026-06",
        "employment_status": "Terminated",
        "count": 1
      }
    ],
    "attrition_rate": 5.25,
    "separated_employees": [
      {
        "id": 22,
        "name": "Robert Martinez",
        "position": "Teacher",
        "department": "Academics",
        "separation_date": "2025-11-15",
        "employment_status": "Resigned"
      }
    ]
  }
}
```

### Query Examples
```
?year=2024
?year=2025
?year=2026
```

### Separated Status Types
- `Resigned`: Employee voluntarily left
- `Terminated`: Employee was terminated
- `Retired`: Employee retired

---

## 6. Employees at Risk

### Endpoint
```
GET /at_risk_employees.php
```

### Description
Returns employees at risk of attrition based on performance and absence.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": {
    "High": [
      {
        "id": 26,
        "name": "Brandon Hall",
        "department": "Academics",
        "position": "Teacher",
        "performance_score": 2.2,
        "absence_days": 18,
        "tenure_years": 2,
        "risk_level": "High"
      }
    ],
    "Medium": [...],
    "Low": [...]
  },
  "total_at_risk": 4
}
```

### Risk Classification
- **High Risk**: Performance < 3 AND Absence > 15
- **Medium Risk**: Performance < 3 OR (Absence > 15 AND Tenure > 3) OR (Tenure > 5 AND Performance < 3.5)
- **Low Risk**: All others

### Risk Factors
- Low performance score (< 3.0)
- High absence days (> 15)
- Long tenure without promotion/growth
- Combination of factors

---

## 7. Performance Distribution

### Endpoint
```
GET /performance_distribution.php
```

### Description
Returns count of employees by performance level.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "performance_level": "Excellent (4.5+)",
      "count": 8
    },
    {
      "performance_level": "Very Good (4.0-4.5)",
      "count": 10
    },
    {
      "performance_level": "Good (3.0-3.9)",
      "count": 6
    },
    {
      "performance_level": "Fair (2.0-2.9)",
      "count": 3
    },
    {
      "performance_level": "Poor (<2.0)",
      "count": 1
    }
  ]
}
```

### Performance Levels
- Excellent: 4.5 to 5.0
- Very Good: 4.0 to 4.49
- Good: 3.0 to 3.99
- Fair: 2.0 to 2.99
- Poor: < 2.0

---

## 8. Salary Statistics

### Endpoint
```
GET /salary_statistics.php
```

### Description
Returns salary statistics grouped by department.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "department": "Administration",
      "count": 2,
      "min_salary": 55000.00,
      "max_salary": 85000.00,
      "avg_salary": 70000.00
    },
    {
      "department": "Academics",
      "count": 8,
      "min_salary": 46000.00,
      "max_salary": 70000.00,
      "avg_salary": 59250.00
    }
  ]
}
```

### Data Fields
- `department`: Department name
- `count`: Number of employees in department
- `min_salary`: Minimum salary in department
- `max_salary`: Maximum salary in department
- `avg_salary`: Average salary in department

---

## 9. Tenure Distribution

### Endpoint
```
GET /tenure_distribution.php
```

### Description
Returns employee count by tenure range.

### Parameters
None

### Response
```json
{
  "success": true,
  "data": [
    {
      "tenure_range": "< 1 year",
      "count": 5
    },
    {
      "tenure_range": "1-3 years",
      "count": 8
    },
    {
      "tenure_range": "4-7 years",
      "count": 10
    },
    {
      "tenure_range": "8+ years",
      "count": 5
    }
  ]
}
```

### Tenure Ranges
- Less than 1 year: Newly hired
- 1-3 years: Early career
- 4-7 years: Mid-career
- 8+ years: Long-term

---

## 10. Custom Report

### Endpoint
```
GET /custom_report.php
```

### Description
Generates filtered HR reports based on multiple criteria.

### Parameters
All parameters are optional:
- `department` (string): Filter by department
- `employment_type` (string): Filter by employment status
- `hire_date_from` (date): Filter by hire date range (from)
- `hire_date_to` (date): Filter by hire date range (to)

### Query Examples
```
?department=Academics

?employment_type=Full-time

?hire_date_from=2020-01-01&hire_date_to=2022-12-31

?department=Academics&employment_type=Full-time&hire_date_from=2018-01-01
```

### Response
```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "name": "Emma Wilson",
      "gender": "Female",
      "department": "Academics",
      "position": "Senior Teacher",
      "hire_date": "2014-09-01",
      "employment_status": "Full-time",
      "salary": 65000.00,
      "performance_score": 4.7,
      "absence_days": 1,
      "age": 35
    }
  ],
  "filters": {
    "department": "Academics"
  },
  "total_records": 8
}
```

### Field Descriptions
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Employee ID |
| name | string | Employee name |
| gender | string | Male, Female, Other |
| department | string | Department name |
| position | string | Job position |
| hire_date | date | YYYY-MM-DD format |
| employment_status | string | Employment type |
| salary | decimal | Annual salary |
| performance_score | decimal | 1-5 scale |
| absence_days | integer | Days absent |
| age | integer | Employee age |

---

## Error Responses

### Database Connection Error
```json
{
  "success": false,
  "message": "Database connection failed: [error details]"
}
```

### Invalid Parameters
```json
{
  "success": false,
  "message": "Invalid parameter: [parameter name]"
}
```

### No Data Found
```json
{
  "success": true,
  "data": []
}
```

---

## Response Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 400 | Bad Request - Invalid parameters |
| 500 | Internal Server Error - Database error |

---

## Rate Limiting
No rate limiting applied. Safe for production use with reasonable request volumes.

---

## Authentication
No authentication required (modify as needed for security).

---

## CORS Support
All endpoints support CORS:
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Content-Type
```

---

## JavaScript Examples

### Fetch Dashboard Metrics
```javascript
async function loadMetrics() {
  try {
    const response = await fetch('../api/dashboard_metrics.php');
    const json = await response.json();
    
    if (json.success) {
      console.log('Employees:', json.data.total_employees);
      console.log('Teachers:', json.data.total_teachers);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}
```

### Fetch Custom Report
```javascript
async function loadReport(filters) {
  const params = new URLSearchParams();
  
  if (filters.department) {
    params.append('department', filters.department);
  }
  if (filters.hireFrom) {
    params.append('hire_date_from', filters.hireFrom);
  }
  
  const response = await fetch('../api/custom_report.php?' + params);
  const data = await response.json();
  
  console.log(`Found ${data.total_records} records`);
  data.data.forEach(emp => {
    console.log(emp.name, emp.position);
  });
}
```

### Error Handling
```javascript
async function safeAPICall(endpoint) {
  try {
    const response = await fetch(`../api/${endpoint}`);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    
    const data = await response.json();
    
    if (!data.success) {
      throw new Error(data.message);
    }
    
    return data.data;
  } catch (error) {
    console.error('API Error:', error);
    return null;
  }
}
```

---

## Testing with cURL

### Test Dashboard Metrics
```bash
curl http://localhost/work_analytics/api/dashboard_metrics.php
```

### Test Custom Report
```bash
curl "http://localhost/work_analytics/api/custom_report.php?department=Academics"
```

### Pretty Print JSON
```bash
curl http://localhost/work_analytics/api/dashboard_metrics.php | jq .
```

---

## Pagination (Future Enhancement)

Future version will support pagination:
```
?page=1&limit=10
?offset=0&count=20
```

---

## Filtering (Future Enhancement)

Advanced filtering options planned:
```
?status=active
?performance>=4.0
?salary>50000
?absences<10
```

---

## Performance Tips

1. **Cache Results**: Store API responses for 5-10 minutes
2. **Lazy Load**: Load non-critical charts after page load
3. **Batch Requests**: Combine related endpoints when possible
4. **Database Indexes**: Already optimized in schema

---

## Support

For API issues:
1. Check JSON response format
2. Verify all parameters are URL-encoded
3. Test endpoints in browser directly
4. Check browser console for errors
5. Review database data with sample queries

---

**Last Updated**: 2026  
**Version**: 1.0.0  
**Status**: Complete Documentation
