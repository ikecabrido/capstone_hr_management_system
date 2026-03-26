# Testing Guide - Time & Attendance Module Updates

## ✅ Issues Fixed & How to Test

### Issue 1: Disappearing Icons on Sidebar Menu Items

**What was fixed:**
- Icons in "Approve Manual Time" and "Approve Leave Requests" menu items now stay visible when hovering or in active state

**How to test:**
1. Navigate to the Time & Attendance module as HR Admin
2. Look at the sidebar menu items:
   - "Approve Manual Time" (check-circle icon)
   - "Approve Leave Requests" (file-alt icon)
3. Hover over these menu items
4. Click on them to navigate
5. **Expected:** Icons remain visible at all times, never disappear

**Technical details:** CSS enhancement in `Sidebar.php` with `!important` flags for icon visibility and z-index layer management.

---

### Issue 2: Real-time Dashboard Updates

**What was implemented:**
- Live activity feed on the HR Dashboard showing recent logins and time in/out events
- Automatic polling every 10 seconds for new events
- Metrics display for login, time-in, and time-out counts
- Toast notifications when new events occur
- Browser notifications (if enabled)

**How to test:**

#### Step 1: Access the Dashboard
1. Log in as HR Admin/System Admin
2. Navigate to the Time & Attendance Dashboard
3. Scroll down to see the "Live Activity Feed" widget

#### Step 2: Verify Real-time Updates
1. Open the dashboard in one browser window
2. In another browser window, have an employee:
   - Log in to the system
   - Click "Time In"
   - Click "Time Out"
3. **Expected:** Events appear in the Live Activity Feed within 10 seconds
   - Event shows employee name
   - Shows event type (LOGIN, Time In, Time Out)
   - Displays timestamp
   - Metrics update accordingly
   - Green pulse indicator shows activity

#### Step 3: Test Manual Refresh
1. Click the "Refresh" button on the widget
2. **Expected:** Events reload immediately (don't wait for next 10-second interval)

#### Step 4: Test Notifications
1. Enable browser notifications (if prompted)
2. Trigger new events from another user
3. **Expected:** 
   - Toast notification appears in bottom-right corner
   - Browser notification may appear (if browser supports it)
   - Notification auto-dismisses after 5 seconds

#### Step 5: Test with Dark Mode
1. Toggle dark mode on/off
2. **Expected:** Real-time widget maintains proper styling and readability

---

## File Locations & Access

### For Development:

**API Endpoint:**
```
/time_attendance/app/api/realtime_updates.php
```
- Access: `GET /app/api/realtime_updates.php?limit=20`
- Requires: HR_ADMIN or SYSTEM_ADMIN role
- Returns: JSON with events from past hour

**JavaScript Logic:**
```
/time_attendance/assets/realtime-dashboard.js
```
- Auto-initializes on dashboard page
- 10-second polling interval
- Can be manually controlled

**Styling:**
```
/time_attendance/assets/realtime-dashboard.css
```
- Responsive design
- Dark mode support
- Animation effects

**Dashboard Integration:**
```
/time_attendance/public/dashboard.php
```
- Real-time widget added after quick stats
- Line ~105-140

---

## Browser Console Testing

You can test the real-time system from browser console:

```javascript
// Check if system is running
console.log(window.realtimeDashboard);

// Manual refresh
window.realtimeDashboard.refresh();

// Stop polling
window.realtimeDashboard.stopPolling();

// Start polling
window.realtimeDashboard.startPolling();

// Get current events
console.log(window.realtimeDashboard.getEvents());

// Request notification permission
RealtimeDashboard.requestNotificationPermission();
```

---

## Troubleshooting

### Real-time updates not showing:
1. Check browser console for errors (F12 > Console tab)
2. Verify API endpoint is accessible: `/time_attendance/app/api/realtime_updates.php`
3. Confirm user is logged in as HR_ADMIN or SYSTEM_ADMIN
4. Check that JavaScript files are loaded (F12 > Network tab)

### Icons still disappearing:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh page (Ctrl+F5)
3. Check Sidebar.php has the CSS updates

### Notifications not appearing:
1. Check browser notification permissions
2. Verify Notification API is supported in your browser
3. Check browser console for permission errors

---

## Performance Notes

- **Polling Interval:** 10 seconds (customizable in realtime-dashboard.js)
- **Event Limit:** 20 shown in widget, 500 max from API
- **Data Retention:** Events from past hour only
- **Polling Stops:** When browser tab is hidden (saves resources)
- **Battery:** Minimal impact on battery life due to smart polling

---

## Dark Mode Testing

The real-time widget supports both light and dark modes:

1. Toggle dark mode via sidebar theme switcher
2. **Expected:**
   - Background colors adjust
   - Text colors remain readable
   - Event cards maintain visibility
   - Icons and indicators visible
   - Toast notifications styled appropriately

---

## API Response Format

When accessing the API directly, response looks like:

```json
{
  "success": true,
  "timestamp": "2024-03-14 15:30:45",
  "events": [
    {
      "type": "TIME_IN",
      "user_name": "John Doe",
      "employee_number": "EMP001",
      "time": "15:25:30",
      "date_time": "2024-03-14 15:25:30",
      "readable_time": "2024-03-14 15:25:30"
    },
    {
      "type": "LOGIN",
      "user_name": "Jane Smith",
      "employee_number": "EMP002",
      "time": "15:24:15",
      "date_time": "2024-03-14 15:24:15",
      "readable_time": "2024-03-14 15:24:15"
    }
  ],
  "total_events": 2
}
```

---

## Success Criteria ✅

- [ ] Icons visible in sidebar "Approve Manual Time" and "Approve Leave Requests" at all times
- [ ] Icons don't disappear on hover or active states
- [ ] Real-time widget appears on dashboard
- [ ] Events update automatically every 10 seconds
- [ ] Manual refresh button works
- [ ] Notifications appear for new events
- [ ] Dark mode styling works correctly
- [ ] Responsive design works on mobile
- [ ] No console errors appear
