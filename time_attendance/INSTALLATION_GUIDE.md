# Installation & Deployment Guide

## Overview
Two main issues have been fixed in the Time & Attendance module:
1. **Icon disappearance on sidebar menu items** - CSS fix applied
2. **Real-time dashboard updates** - New feature added with API, JS, and CSS

---

## Installation Steps

### Step 1: Backup Current Files
```bash
# Backup existing files before deployment
cp app/components/Sidebar.php app/components/Sidebar.php.backup
cp public/dashboard.php public/dashboard.php.backup
```

### Step 2: Deploy Updated Files

All necessary files have been created/updated:

#### ✅ Modified Files (Already Updated)
- `app/components/Sidebar.php` - CSS icon fix added
- `public/dashboard.php` - Real-time widget integrated

#### ✅ New Files (Already Created)
- `app/api/realtime_updates.php` - API endpoint for real-time events
- `assets/realtime-dashboard.js` - JavaScript polling and UI logic
- `assets/realtime-dashboard.css` - Styling for real-time components

### Step 3: Verify Database Tables

The API uses the following tables (should already exist):
- `users` - For login information
- `employees` - Employee details
- `attendance` - Time in/out records
- `activity_logs` (optional) - For login event tracking

No database migrations are required. The API gracefully handles missing `activity_logs` table.

### Step 4: Clear Browser Cache

Users should clear their browser cache to ensure:
- Updated CSS and JavaScript files are loaded
- Icons display correctly
- Real-time updates function properly

```
Ctrl+Shift+Delete (Windows)
Cmd+Shift+Delete (Mac)
```

### Step 5: Test Deployment

1. Login as HR Admin
2. Navigate to Dashboard
3. Verify:
   - Real-time widget appears below quick stats
   - Sidebar icons are visible
   - Real-time updates polling (check console)
   - No JavaScript errors

---

## File Structure

```
time_attendance/
├── app/
│   ├── api/
│   │   ├── get_day_records.php
│   │   └── realtime_updates.php (NEW)
│   ├── components/
│   │   └── Sidebar.php (MODIFIED - icon CSS fix)
│   ├── config/
│   ├── controllers/
│   ├── models/
│   └── ...
├── assets/
│   ├── style.css
│   ├── dashboard.css
│   ├── realtime-dashboard.js (NEW)
│   ├── realtime-dashboard.css (NEW)
│   └── ...
├── public/
│   ├── dashboard.php (MODIFIED - added widget)
│   ├── approve_attendance.php
│   ├── leave_approvals.php
│   └── ...
└── TESTING_GUIDE.md (NEW)
```

---

## Configuration

### Real-time Update Settings

To customize polling behavior, edit `assets/realtime-dashboard.js`:

```javascript
// Line 15-17: Change polling interval
window.realtimeDashboard = new RealtimeDashboard({
    pollInterval: 10000,  // Change to desired milliseconds
    eventsLimit: 30       // Change max events to show
});
```

**Recommended Values:**
- `pollInterval: 5000` - Very frequent updates (5 seconds)
- `pollInterval: 10000` - Balanced (10 seconds) **[DEFAULT]**
- `pollInterval: 30000` - Infrequent updates (30 seconds)

---

## API Configuration

### Rate Limiting

The API `realtime_updates.php` has a built-in limit of 500 maximum results:

```php
// Line 31-32
$limit = (int)($_GET['limit'] ?? 50);
$limit = min($limit, 500); // Cap at 500 to prevent overload
```

To change this, edit the cap:
```php
$limit = min($limit, 1000); // Increase to 1000
```

### Event Time Range

Currently shows events from the **past 1 hour**:

```php
// Line 40 and 49
WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
```

To change to 24 hours:
```php
WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
```

---

## Security Considerations

### Authentication
- ✅ API requires valid session
- ✅ Role-based access (HR_ADMIN, SYSTEM_ADMIN only)
- ✅ Proper HTTP status codes (401 Unauthorized, 403 Forbidden)

### Data Protection
- ✅ JSON escaping on all output
- ✅ PDO prepared statements to prevent SQL injection
- ✅ No sensitive data exposed in API response

### Recommendations
1. Consider adding rate limiting at web server level
2. Log API access for audit trails
3. Monitor API performance for unusual usage
4. Consider CORS policies if consuming from different domain

---

## Troubleshooting

### Issue: API returns 401 Unauthorized
**Solution:** 
- Ensure user is logged in
- Check session is properly started

### Issue: API returns 403 Forbidden
**Solution:**
- Confirm user has HR_ADMIN or SYSTEM_ADMIN role
- Check role in database `users.role` field

### Issue: Real-time widget shows "Loading" forever
**Solution:**
1. Check browser console for errors (F12 > Console)
2. Verify API file exists at `app/api/realtime_updates.php`
3. Test API directly: `/app/api/realtime_updates.php`
4. Check browser network tab for failed requests

### Issue: No events displayed
**Solution:**
- Verify there are recent login/time in/out events in database
- Check time range (past hour by default)
- Test with multiple employees logging in

### Issue: Icons disappearing
**Solution:**
- Hard refresh browser (Ctrl+F5)
- Clear cache and cookies
- Check that Sidebar.php modification was applied correctly

---

## Database Queries

### Check Recent Events (for manual testing)

```sql
-- Recent time in/out events
SELECT 
    'TIME_IN' as event_type,
    CONCAT(e.first_name, ' ', e.last_name) as user_name,
    e.employee_number,
    a.time_in as event_time
FROM attendance a
JOIN employees e ON a.employee_id = e.employee_id
WHERE a.time_in >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY a.time_in DESC
LIMIT 20;

-- Check attendance table exists
DESCRIBE attendance;

-- Check employees table
DESCRIBE employees;
```

---

## Performance Metrics

### Expected Performance
- **API Response Time:** < 200ms (for 20 events)
- **JavaScript Overhead:** < 5MB total size
- **Polling Impact:** Minimal network usage (~2KB per poll)
- **CPU Usage:** Negligible

### Optimization Tips
1. Reduce `pollInterval` for real-time feel
2. Increase `pollInterval` for lower bandwidth usage
3. Limit visible events with `eventsLimit` parameter
4. Archive old attendance records (older than 6 months)

---

## Rollback Instructions

If issues occur, revert changes:

```bash
# Restore original Sidebar.php
cp app/components/Sidebar.php.backup app/components/Sidebar.php

# Restore original dashboard.php
cp public/dashboard.php.backup public/dashboard.php

# Remove new files
rm app/api/realtime_updates.php
rm assets/realtime-dashboard.js
rm assets/realtime-dashboard.css
```

Then clear browser cache and refresh.

---

## Monitoring & Maintenance

### Monitor API Usage
Add logging to `realtime_updates.php` if needed:

```php
// Log API access
error_log("API Access: User {$_SESSION['user_id']}, Role: {$current_role}, Time: " . date('Y-m-d H:i:s'));
```

### Check System Health
1. Monitor API response times in browser Network tab
2. Check database connection logs
3. Monitor server CPU/memory during peak usage
4. Review PHP error logs for warnings

---

## Support & Maintenance

### Known Limitations
- ⚠️ Polling-based (not true WebSockets)
- ⚠️ 10-second delay between updates (configurable)
- ⚠️ Events from past hour only (configurable)
- ⚠️ Works best with <500 concurrent events

### Future Improvements
- [ ] WebSocket implementation for true real-time
- [ ] Event filtering by user/department
- [ ] Export event logs
- [ ] Email alerts
- [ ] Custom notification rules

---

## Contact & Support

For issues or questions about the implementation:
1. Check TESTING_GUIDE.md for detailed testing steps
2. Review browser console for error messages
3. Check database for data availability
4. Verify file permissions and folder structure
5. Test API endpoint directly via URL

---

## Change Log

### Version 1.0 (2024-03-14)
- ✅ Fixed disappearing icons on sidebar menu items
- ✅ Added real-time dashboard updates
- ✅ Added live event feed
- ✅ Added event metrics display
- ✅ Added toast notifications
- ✅ Dark mode support

---

## License & Attribution

This update maintains the same license as the parent project (Time & Attendance Management System).
