# 📋 QUICK START - QR Redundancy Fix + Leave Balance Setup

## ⚡ 5-Minute Setup

### Issue #1: QR Redundancy - FIXED ✅

**What Changed:**
```
Dashboard "Time In/Out" Section:

BEFORE:  [📱 QR Scanner] [🟢 Generate QR]
AFTER:   [📱 Scan QR Code] [🟢 Display Kiosk]
         
         (Same blue and green colors)
         (Same HR admin role check)
         (Different purpose - clearer)
```

**Why:**
- `qr_display_kiosk.php` auto-refreshes every 30 seconds
- `qr_generate.php` was redundant manual generation
- Consolidated to single automated workflow
- Cleaner, simpler, more secure

---

### Issue #2: Leave Balance Table - CREATE IT NOW ✅

**Step 1: Copy SQL from this file**
📄 File: `create_leave_balances_table.sql`

**Step 2: Run in phpMyAdmin**
```
1. Open http://localhost/phpmyadmin
2. Select "hr_management" database
3. Click "SQL" tab
4. Copy entire CREATE TABLE statement
5. Paste and click "Go"
```

**Step 3: Populate Employee Data**
```
1. Still in SQL tab
2. Copy the bulk INSERT statement
3. Paste and click "Go"
4. All employees get default leave balances
```

**Step 4: Done!** ✅
- Table is created
- All employees have balances
- Dashboard automatically uses it

---

## 🎯 What You Get

### QR System - Clean Workflow
```
OFFICE KIOSK:
Admin clicks [🟢 Display Kiosk]
    ↓
Full-screen auto-refresh every 30 sec
    ↓
Leave on office monitor/TV
    ↓
Employees scan with phone camera

EMPLOYEE SCANNER:
Employee clicks [📱 Scan QR Code]
    ↓
Camera interface loads
    ↓
Points phone at kiosk QR
    ↓
Auto time in/out
    ↓
Confirmation modal
```

### Leave Balance - Full Tracking
```
Employees see:
├─ Vacation Leave: 15 days
├─ Sick Leave: 10 days
├─ Maternity Leave: 5 days
└─ Emergency Leave: 3 days

HR sees:
├─ Individual balances
├─ Department totals
├─ Low balance alerts
└─ Usage analytics
```

---

## 📝 SQL Quick Reference

### View all leave balances
```sql
SELECT e.employee_name, lt.leave_type_name, lb.remaining_balance
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026
ORDER BY e.employee_name;
```

### Update when leave approved
```sql
UPDATE leave_balances
SET used_balance = used_balance + 3,
    remaining_balance = opening_balance - (used_balance + 3)
WHERE employee_id = 5 AND leave_type_id = 1 AND year = 2026;
```

### Check low balances
```sql
SELECT e.employee_name, lt.leave_type_name, lb.remaining_balance
FROM leave_balances lb
JOIN employees e ON lb.employee_id = e.employee_id
JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
WHERE lb.year = 2026 AND lb.remaining_balance < 2;
```

---

## ✅ Verification Checklist

- [ ] Dashboard buttons updated (QR Scanner & Display Kiosk)
- [ ] Leave balance table created in hr_management database
- [ ] Initial employee data inserted
- [ ] Test kiosk on office monitor (30 sec auto-refresh works)
- [ ] Test employee QR scanner (camera loads)
- [ ] Test time in/out (confirmation modal shows)
- [ ] Check database for attendance records
- [ ] Check database for leave balance updates

---

## 📁 Files Reference

| File | Purpose |
|------|---------|
| `create_leave_balances_table.sql` | SQL to create table |
| `QR_REDUNDANCY_FIX_AND_LEAVE_BALANCE.md` | Detailed explanation |
| `employee_dashboard.php` | Updated dashboard buttons |

---

## 🚀 You're Done!

**QR System**: Clean, no redundancy ✅
**Leave Balance**: Table created and ready ✅
**Dashboard**: Updated with correct buttons ✅

**Status**: Ready for testing and deployment 🟢

