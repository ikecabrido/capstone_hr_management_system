# Absence & Late Management System - Complete Implementation Guide

## Overview

A comprehensive system for managing employee absences and late arrivals with features for:
- ✅ View all absences and late arrivals by employee/date range
- ✅ Mark as excused/unexcused
- ✅ Add notes/reasons
- ✅ Generate reports
- ✅ Employee appeal system
- ✅ HR review workflow

---

## System Architecture

### Database Tables

#### 1. `ta_absence_late_records`
Stores individual absence/late records with excuse tracking.

```sql
record_id          INT PRIMARY KEY
attendance_id      INT (FK to ta_attendance)
employee_id        INT (FK to employees)
absence_date       DATE
type               ENUM('ABSENT', 'LATE')
is_excused         BOOLEAN (default: false)
excuse_status      ENUM('PENDING', 'APPROVED', 'REJECTED', 'AWAITING_DOCUMENTS')
reason             TEXT
notes              TEXT (HR notes)
supporting_document_url VARCHAR(255)
submitted_by       INT (FK to users)
submitted_date     DATETIME
reviewed_by        INT (FK to users)
reviewed_date      DATETIME
approval_notes     TEXT
created_at         TIMESTAMP
updated_at         TIMESTAMP
```

#### 2. `ta_absence_late_thresholds`
Tracks monthly absence/late counts and warning levels.

```sql
threshold_id       INT PRIMARY KEY
employee_id        INT (FK to employees)
month_year         VARCHAR(7) (format: YYYY-MM)
absent_count       INT
late_count         INT
excused_absent_count   INT
excused_late_count     INT
warning_level      ENUM('NONE', 'LEVEL_1', 'LEVEL_2', 'LEVEL_3')
warning_date       DATETIME
last_action_taken  VARCHAR(100)
```

#### 3. `ta_absence_late_policies`
Company policies for absence/late management.

```sql
policy_id          INT PRIMARY KEY
policy_name        VARCHAR(100)
max_late_per_month INT (default: 3)
max_absent_per_month INT (default: 2)
max_excused_absent_per_month INT (default: 2)
max_excused_late_per_month INT (default: 5)
warning_after_late_count INT (default: 5)
warning_after_absent_count INT (default: 3)
late_threshold_minutes INT (default: 15)
is_active          BOOLEAN
```

---

## Component Files

### 1. Database Migration
**Location:** `time_attendance/migrations/002_add_absence_late_management.sql`

Installs all three tables and adds columns to `ta_attendance`.

**Installation:**
```bash
# In phpMyAdmin, run the SQL file to create tables
```

### 2. Model Class
**Location:** `time_attendance/app/models/AbsenceLateMgmt.php`

**Key Methods:**
```php
// Get records with filters
getRecords($filters = [])

// Get single record
getRecord($record_id)

// Create new record
createRecord($attendance_id, $employee_id, $absence_date, $type)

// Employee submits excuse
submitExcuse($record_id, $reason, $supporting_document, $employee_id)

// HR reviews excuse (approve/reject)
reviewExcuse($record_id, $status, $approval_notes, $reviewed_by)

// Add HR notes
addNotes($record_id, $notes)

// Get employee monthly summary
getEmployeeSummary($employee_id, $month_year)

// Update monthly thresholds
updateThresholds($employee_id, $date)

// Generate report
getReport($filters = [])

// Get dashboard statistics
getSummaryStats($filters = [])

// Get pending approvals
getPendingApprovals($limit = 20)
```

### 3. API Endpoints
**Location:** `time_attendance/app/api/absence_late_management.php`

**Actions:**

| Action | Method | Permission | Description |
|--------|--------|-----------|-------------|
| `get_records` | GET | TIME/HR | Get filtered absence/late records |
| `get_record` | GET | AUTH | Get single record details |
| `submit_excuse` | POST | EMPLOYEE | Submit absence/late excuse |
| `review_excuse` | POST | TIME/HR | Approve/reject excuse |
| `add_notes` | POST | TIME/HR | Add HR notes to record |
| `get_employee_summary` | GET | AUTH | Get employee monthly summary |
| `get_report` | GET | TIME/HR | Generate report with filters |
| `get_summary_stats` | GET | TIME/HR | Get dashboard statistics |
| `get_pending` | GET | TIME/HR | Get pending approvals |

### 4. HR Management Interface
**Location:** `time_attendance/public/absence_late_management.php`

**Features:**
- Dashboard with 6 key statistics
- Advanced filtering by date, employee, type, status
- Real-time record table with inline actions
- Modal for viewing full record details
- Approve/reject decisions with notes
- CSV report generation
- Responsive design for mobile

**Required Permissions:** `time` or `hr` role

### 5. Employee Appeal Interface
**Location:** `time_attendance/public/my_absence_appeals.php`

**Features:**
- View personal absence/late records
- Monthly summary statistics
- Submit excuse with reason
- Upload supporting documents
- Track approval status
- View HR review notes
- Edit pending excuses
- Tab-based filtering

**Required Permissions:** Authenticated user

---

## User Workflows

### Workflow 1: HR Views and Manages Absences

1. **Access Management Page**
   ```
   time_attendance/public/absence_late_management.php
   ```

2. **Filter Records**
   - Set date range
   - Filter by employee/department
   - Filter by type (ABSENT/LATE)
   - Filter by status (PENDING/APPROVED/REJECTED)

3. **Review Pending Excuses**
   - Click "View" to see full details
   - See reason and supporting documents
   - Click "Approve" or "Reject"
   - Add review notes

4. **Generate Reports**
   - Set filters
   - Click "Generate Report"
   - Download CSV file with data

### Workflow 2: Employee Submits Excuse

1. **Navigate to Appeals Page**
   ```
   time_attendance/public/my_absence_appeals.php
   ```

2. **Find Record**
   - View absence/late in list
   - See current status

3. **Submit Excuse**
   - Click "Edit Excuse" (for pending)
   - Enter reason explaining absence/lateness
   - Optionally upload supporting document
   - Click "Submit Excuse"

4. **Track Status**
   - View "Pending" tab to see submitted excuses
   - Receive notification when HR reviews
   - Check "Approved" or "Rejected" tab

### Workflow 3: System Auto-creates Records

When attendance is recorded:

1. **Time In recorded but after 9:00 AM**
   - Automatically marked as LATE
   - Record created in `ta_absence_late_records`

2. **No Time In for workday**
   - Marked as ABSENT
   - Record created for employee to appeal

3. **Employee can then submit excuse**

---

## API Usage Examples

### Get Pending Approvals
```bash
curl "http://localhost/time_attendance/app/api/absence_late_management.php?action=get_pending&limit=20" \
  -H "Cookie: PHPSESSID=xxx"
```

### Get Employee Summary
```bash
curl "http://localhost/time_attendance/app/api/absence_late_management.php?action=get_employee_summary&employee_id=5&month_year=2026-03" \
  -H "Cookie: PHPSESSID=xxx"
```

### Get Records with Filters
```bash
curl "http://localhost/time_attendance/app/api/absence_late_management.php?action=get_records&start_date=2026-03-01&end_date=2026-03-31&excuse_status=PENDING&type=ABSENT" \
  -H "Cookie: PHPSESSID=xxx"
```

### Submit Excuse
```bash
curl -X POST "http://localhost/time_attendance/app/api/absence_late_management.php?action=submit_excuse" \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=xxx" \
  -d '{
    "record_id": 1,
    "reason": "Doctor appointment",
    "document": "url/to/medical/cert.pdf"
  }'
```

### Review Excuse (Approve)
```bash
curl -X POST "http://localhost/time_attendance/app/api/absence_late_management.php?action=review_excuse" \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=xxx" \
  -d '{
    "record_id": 1,
    "status": "APPROVED",
    "notes": "Medical certificate verified"
  }'
```

---

## Features Implemented

### ✅ Feature 1: View All Absences/Lates by Date Range

**Location:** `absence_late_management.php`

- Filter by start and end date
- Filter by employee ID
- Filter by type (ABSENT/LATE)
- Filter by excuse status
- Pagination support
- Sort by date descending

### ✅ Feature 2: Mark as Excused/Unexcused

**Workflow:**
1. HR views absence/late record
2. Employee submits excuse OR HR manually marks as excused
3. System updates `is_excused` flag
4. Thresholds recalculated

**Data Fields:**
- `is_excused` (boolean)
- `excuse_status` (PENDING/APPROVED/REJECTED)

### ✅ Feature 3: Add Notes/Reasons

**Employee-submitted:**
- `reason` - Why they were absent/late

**HR-added:**
- `notes` - Internal HR notes
- `approval_notes` - Decision rationale

**Supporting Documents:**
- `supporting_document_url` - File path to certificate/proof

### ✅ Feature 4: Generate Reports

**Location:** `absence_late_management.php` → "Generate Report"

**Report Includes:**
- Employee name and department
- Absence/late date
- Type and status
- Excuse status
- Reason
- Reviewed by and date

**Export Format:** CSV

**Filters:**
- Date range
- Department
- Type
- Excuse status

---

## Dashboard Statistics

HR Dashboard shows:
1. **Total Records** - All absence/late records in date range
2. **Total Absences** - Count of ABSENT type
3. **Total Late Arrivals** - Count of LATE type
4. **Pending Reviews** - Excuses waiting for HR approval
5. **Approved Excuses** - Approved by HR
6. **Rejected Excuses** - Rejected by HR

---

## Integration with Existing System

### Links to Sidebar
Add to `time_attendance/app/components/Sidebar.php`:

```php
<li class="nav-item">
    <a href="absence_late_management.php" class="nav-link">
        <i class="fas fa-calendar-times"></i> <span>Absence & Late</span>
    </a>
</li>
```

### Integration with Employee Dashboard
The employee dashboard can link to:
```
time_attendance/public/my_absence_appeals.php
```

### Integration with Attendance Recording
When creating attendance record, also create absence/late record:

```php
// In attendance recording code
if ($time_in > shift_end_time) {
    $absenceLateMgmt->createRecord($attendance_id, $employee_id, $attendance_date, 'LATE');
} elseif (no_time_in_for_workday) {
    $absenceLateMgmt->createRecord($attendance_id, $employee_id, $attendance_date, 'ABSENT');
}
```

---

## Security Features

- ✅ Role-based access control (TIME/HR only)
- ✅ Employee can only view own records
- ✅ Audit trail via `submitted_by`, `reviewed_by`, timestamps
- ✅ PDO prepared statements for SQL injection prevention
- ✅ Session validation on all endpoints
- ✅ CSRF protection recommended

---

## Performance Considerations

### Indexes
```sql
-- Already created in migration:
KEY `idx_employee_date` (`employee_id`, `absence_date`)
KEY `idx_excuse_status` (`excuse_status`)
KEY `idx_type` (`type`)
```

### Pagination
- Default 50 records per page
- Configurable via `limit` parameter

### Caching
- Consider caching monthly summary data

---

## Customization Options

### Change Late Threshold
Currently: 9:00 AM (in `analytics.php` and dashboard.php)

To change, update:
1. `dashboard.php` - Line with `getHours() > 9`
2. `analytics.php` - Same condition
3. `ta_absence_late_policies` - `late_threshold_minutes` field

### Change Policy Thresholds
Edit `ta_absence_late_policies` table:
```sql
UPDATE ta_absence_late_policies 
SET max_late_per_month = 5,
    max_absent_per_month = 3
WHERE policy_id = 1;
```

### Add Email Notifications
Hook into:
- `submitExcuse()` - Notify HR of new excuse
- `reviewExcuse()` - Notify employee of decision

---

## Testing Checklist

- [ ] Create test absence/late record
- [ ] Submit excuse as employee
- [ ] Approve excuse as HR
- [ ] Verify `is_excused` updated
- [ ] Generate report with filters
- [ ] Verify monthly summary calculates correctly
- [ ] Test CSV export
- [ ] Test on mobile view
- [ ] Verify role-based access control

---

## Troubleshooting

### Records not appearing
- Verify `ta_attendance` has records
- Check `attendance_date` format (YYYY-MM-DD)
- Confirm user has TIME or HR role

### Filters not working
- Check URL parameters in browser
- Verify database connection
- Check for JavaScript errors in console

### Report generation fails
- Verify CSV headers are correct
- Check file permissions
- Verify data encoding (UTF-8)

---

## Next Steps

1. **Install migration:** Run `002_add_absence_late_management.sql`
2. **Update sidebar:** Add menu links
3. **Test workflows:** Create test records and test both interfaces
4. **Configure policies:** Update thresholds in `ta_absence_late_policies`
5. **Train HR staff:** On approval process
6. **Communicate to employees:** On appeals process
7. **Monitor:** Track usage and adjust policies as needed

---

## Files Created

1. ✅ `migrations/002_add_absence_late_management.sql` - Database tables
2. ✅ `app/models/AbsenceLateMgmt.php` - Core business logic
3. ✅ `app/api/absence_late_management.php` - API endpoints
4. ✅ `public/absence_late_management.php` - HR management UI
5. ✅ `public/my_absence_appeals.php` - Employee appeal UI
6. ✅ `ABSENCE_LATE_MANAGEMENT_GUIDE.md` - This guide

---

**Status:** ✅ Complete and Ready for Implementation
