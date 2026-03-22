# Complete Database Analysis - All Tables in HR Management System

## Database: `hr_management`
**Source**: hr_management (10).sql  
**Date Generated**: Mar 19, 2026  
**Total Tables**: 41

---

## All 41 Tables in Database

### 1. activity_logs
- Logging and audit trails

### 2. admins
- Admin user credentials

### 3. applications
- Job applications (Recruitment module)

### 4. **attendance** ← TIME & ATTENDANCE
- Attendance records (clock in/out)

### 5. **attendance_tokens** ← TIME & ATTENDANCE
- QR token verification

### 6. career_paths
- Career development paths

### 7. competencies
- Employee competencies

### 8. compliance_assignments
- Compliance training assignments

### 9. compliance_trainings
- Compliance training programs

### 10. education
- Employee education records

### 11. employees
- Core employee information

### 12. employees1
- Duplicate/alternative employee table

### 13. employee_settlements
- Employee settlement records

### 14. **employee_shifts** ← TIME & ATTENDANCE
- Employee shift assignments

### 15. exit_documents
- Exit/separation documentation

### 16. exit_interviews
- Exit interview records

### 17. exit_surveys
- Employee exit surveys

### 18. feedback_360
- 360-degree feedback

### 19. **flexible_schedules** ← TIME & ATTENDANCE
- Flexible schedule overrides

### 20. **holidays** ← TIME & ATTENDANCE
- Holiday calendar

### 21. individual_development_plans
- Employee development plans

### 22. knowledge_transfer_items
- Knowledge transfer items

### 23. knowledge_transfer_plans
- Knowledge transfer plans

### 24. leadership_enrollments
- Leadership program enrollments

### 25. leadership_programs
- Leadership development programs

### 26. **leave_balances** ← TIME & ATTENDANCE
- Leave balance tracking

### 27. **leave_requests** ← TIME & ATTENDANCE
- Employee leave requests

### 28. **leave_types** ← TIME & ATTENDANCE
- Leave type definitions

### 29. lms_courses
- Learning Management System courses

### 30. lms_enrollments
- LMS course enrollments

### 31. overtime_requests
- Overtime request records

### 32. performance_reviews
- Performance review records

### 33. requests
- General requests (possibly admin requests)

### 34. request_types
- Request type definitions

### 35. resignations
- Resignation/separation records

### 36. **shifts** ← TIME & ATTENDANCE
- Shift definitions

### 37. succession_plans
- Succession planning records

### 38. survey_answers
- Survey answer responses

### 39. survey_questions
- Survey questions

### 40. survey_responses
- Complete survey responses

### 41. users
- System user accounts

---

## TIME & ATTENDANCE MODULE TABLES (Actual)

### Core Tables Found: 8 Tables

1. **attendance** - Attendance/clock records
2. **attendance_tokens** - QR token system
3. **employee_shifts** - Employee shift assignments
4. **shifts** - Shift definitions
5. **flexible_schedules** - Flexible schedule overrides
6. **holidays** - Holiday calendar
7. **leave_balances** - Leave balance tracking
8. **leave_types** - Leave type definitions
9. **leave_requests** - Leave request records

### Status: NOT INCLUDED IN ACTUAL DATABASE

The following tables we identified earlier are **NOT present** in your actual database:

- ❌ custom_shifts
- ❌ custom_shift_times
- ❌ department_heads
- ❌ notifications

---

## Corrected Table Rename List

Based on your **actual database**, here are the tables that should be renamed:

### Tables to Rename (9 total):

```sql
-- Core Attendance & Time Tracking (5 tables)
RENAME TABLE attendance TO ta_attendance;
RENAME TABLE attendance_tokens TO ta_attendance_tokens;
RENAME TABLE employee_shifts TO ta_employee_shifts;
RENAME TABLE shifts TO ta_shifts;
RENAME TABLE flexible_schedules TO ta_flexible_schedules;

-- Leave Management (4 tables)
RENAME TABLE holidays TO ta_holidays;
RENAME TABLE leave_balances TO ta_leave_balances;
RENAME TABLE leave_types TO ta_leave_types;
RENAME TABLE leave_requests TO ta_leave_requests;
```

### Tables NOT to Rename (NOT PRESENT):
- ❌ custom_shifts (Does NOT exist)
- ❌ custom_shift_times (Does NOT exist)
- ❌ department_heads (Does NOT exist)
- ❌ notifications (Does NOT exist)

---

## Summary

| Category | Count | Status |
|----------|-------|--------|
| **Total Tables in Database** | 41 | ✓ All present |
| **Time & Attendance Tables** | 9 | ✓ To be renamed |
| **Other Module Tables** | 32 | Leave as-is |
| **Tables We Listed But NOT Present** | 4 | Remove from plan |

---

## Recommendation

### Update Your Migration Script

The current [MIGRATE_TABLE_NAMES.sql](MIGRATE_TABLE_NAMES.sql) includes tables that **don't exist**:

**REMOVE these lines:**
```sql
RENAME TABLE custom_shifts TO ta_custom_shifts;
RENAME TABLE custom_shift_times TO ta_custom_shift_times;
RENAME TABLE department_heads TO ta_department_heads;
RENAME TABLE notifications TO ta_notifications;
```

**KEEP only these 9 tables:**
```sql
RENAME TABLE attendance TO ta_attendance;
RENAME TABLE attendance_tokens TO ta_attendance_tokens;
RENAME TABLE employee_shifts TO ta_employee_shifts;
RENAME TABLE shifts TO ta_shifts;
RENAME TABLE flexible_schedules TO ta_flexible_schedules;
RENAME TABLE holidays TO ta_holidays;
RENAME TABLE leave_balances TO ta_leave_balances;
RENAME TABLE leave_types TO ta_leave_types;
RENAME TABLE leave_requests TO ta_leave_requests;
```

---

## Next Steps

1. Review and confirm the 9 tables above are the correct ones
2. Update MIGRATE_TABLE_NAMES.sql to include **only these 9 tables**
3. Update MIGRATE_TABLE_NAMES.sql to **remove the 4 non-existent tables**
4. Run the corrected migration script
5. Re-verify all PHP files reference only the 9 actual tables

Would you like me to update the migration script and documentation based on these 9 actual tables?
