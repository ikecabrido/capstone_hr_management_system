# Modal UI Quick Testing Guide

## Access the Manage Shifts Page
1. Navigate to: `http://localhost/capstone_hr_management_system/time_attendance/public/shifts.php`
2. You must be logged in as a user with time/HR_ADMIN/payroll role
3. Page should load with **Overview** tab active (default view)

## Test Overview Tab (Default)

### Statistics Cards
- Look for 4 stat cards showing:
  1. **Total Shifts** - Count of all shifts in system
  2. **Active Shifts** - Count of active shifts only
  3. **Total Assignments** - Count of all employee-shift assignments
  4. **Flexible Schedules** - Count of flexible schedules created

### Shifts Grid
- See all shifts displayed in card format below statistics
- Each shift card shows:
  - Shift name with icon
  - Active/Inactive status indicator
  - Time range (start - end)
  - Description (if available)
  - Break duration
  - Edit and Delete buttons

## Test Tab Navigation

### Tab Buttons
Located in the navigation tabs (below header):
- **Overview** (currently selected/active)
- **All Shifts** 
- **Create Shift** (opens modal)
- **Assign Employee** (opens modal)
- **Flexible Schedule** (opens modal)

## Test All Shifts Tab

### Switch Tab
1. Click the **All Shifts** button
2. Overview tab should deactivate
3. All Shifts tab should activate and display:
   - Similar shift grid as Overview
   - **Current Assignments** table below showing:
     - Employee name
     - Assigned shift
     - Shift time range
     - Effective from date
     - Effective to date (or "Ongoing")

### Return to Overview
- Click **Overview** tab to return

## Test Create Shift Modal

### Open Modal
1. Click **Create Shift** button
2. A modal should appear with:
   - Header: "Create New Shift" (with icon)
   - Close button (X) in top right
   - Semi-transparent dark backdrop behind modal

### Form Fields
- **Shift Name** (required) - e.g., "Morning Shift"
- **Start Time** (required) - time picker
- **End Time** (required) - time picker
- **Break Duration** (optional) - number in minutes (default: 60)
- **Description** (optional) - text area
- **Active** - checkbox (checked by default)

### Form Actions
- **Cancel** button - closes modal without saving
- **Create Shift** button - submits form

### Test Modal Close Options
1. Click **Cancel** button → Modal closes
2. Re-open modal, click **X** button → Modal closes
3. Re-open modal, click dark backdrop → Modal closes

## Test Assign Employee Modal

### Open Modal
1. Click **Assign Employee** button
2. Modal should appear with header "Assign Shift to Employee"

### Form Fields
- **Employee** (required) - dropdown with list of all employees
- **Shift** (required) - dropdown showing all available shifts with times
- **Effective From** (required) - date picker (defaults to today)
- **Effective To** (optional) - date picker for end of assignment

### Test Functionality
1. Select an employee
2. Select a shift
3. Choose effective from date
4. (Optional) Set effective to date
5. Click "Assign Shift" to submit

## Test Flexible Schedule Modal

### Open Modal
1. Click **Flexible Schedule** button
2. Modal should appear with header "Create Flexible Schedule"

### Form Fields
- **Employee** (required) - dropdown
- **Date** (required) - date picker (min = today)
- **Repeat on Day of Week** (optional) - 6 checkboxes (Mon-Sat)
  - No Sunday option (intentional - Sundays excluded)
- **Start Time** (required) - time picker
- **End Time** (required) - time picker
- **Notes** (optional) - text area
- **Set Repeat End Date** - checkbox to enable repeat until date
- **Repeat Until** (hidden until checked) - date picker

### Test Recurring Schedule
1. Select an employee
2. Select a date
3. Check multiple day boxes (e.g., Monday, Wednesday, Friday)
4. Set start/end times
5. Check "Set Repeat End Date" checkbox
6. Select a repeat until date
7. Click "Create Schedule"

### Test One-Time Schedule
1. Select an employee
2. Select a date
3. Leave all day boxes unchecked
4. Set start/end times
5. Leave "Set Repeat End Date" unchecked
6. Click "Create Schedule"

## Test Modal Styling

### Visual Confirmation
- [ ] Modal background is white with shadow
- [ ] Modal header has blue gradient background
- [ ] Modal header text is white
- [ ] Close button (X) is white and clickable
- [ ] Form elements are properly spaced
- [ ] Input fields have visible borders
- [ ] Buttons have appropriate styling

### Interactive Feedback
- [ ] Buttons change color on hover
- [ ] Input fields show focus state (blue border/shadow)
- [ ] Modal slide-in animation plays smoothly
- [ ] Backdrop fades in/out properly

## Test Form Submission

### Create Shift Test
1. Open Create Shift modal
2. Fill in all required fields
3. Submit
4. Check browser console for errors
5. Modal should close
6. See success message
7. New shift should appear in Overview/All Shifts tabs

### Assign Employee Test
1. Open Assign Employee modal
2. Fill in all required fields
3. Submit
4. Modal should close
5. See success message
6. New assignment should appear in Current Assignments table

### Flexible Schedule Test
1. Open Flexible Schedule modal
2. Fill in all required fields
3. Submit
4. Modal should close
5. See success message
6. New flexible schedule should appear in overview

## Test Responsive Design

### Desktop (Full Width)
- Open page on desktop browser
- Modals should be 600px wide and centered
- All content visible without scrolling (unless form is very long)

### Tablet/Mobile
1. Open page on tablet or mobile device (or resize browser to < 768px)
2. Check modals adapt:
   - Modal width: ~95% of screen
   - Modal height: max 95vh with scrolling
   - Form buttons stack vertically
   - Text remains readable

## Test Dark Mode (if enabled)

- [ ] Modal background adapts to dark mode
- [ ] Modal header maintains visibility
- [ ] Form elements visible in dark mode
- [ ] Buttons styled appropriately for dark mode

## Common Issues & Fixes

### Modal doesn't open
- Check browser console for JavaScript errors
- Verify modal ID matches button onclick
- Clear browser cache and reload

### Form not submitting
- Check browser console for JavaScript errors
- Verify all required fields filled
- Check PHP error logs for server errors

### Modal won't close
- Try clicking the X button
- Try clicking the backdrop
- Try refreshing the page

### Styling looks broken
- Clear browser cache (Ctrl+Shift+Delete)
- Hard reload (Ctrl+Shift+R)
- Check if dark mode CSS is conflicting

## Database Verification

After testing form submissions:

1. Check MySQL database:
   ```sql
   -- View all shifts
   SELECT * FROM shifts;
   
   -- View all assignments
   SELECT * FROM employee_shift_assignments;
   
   -- View flexible schedules
   SELECT * FROM flexible_schedules;
   ```

2. Verify data was inserted correctly
3. Confirm no duplicate entries
4. Check dates are properly formatted

## Performance Notes

- Modals load instantly (no AJAX needed for now)
- All form data submitted via standard POST
- Page reloads after form submission (refresh feedback)
- Statistics update automatically on page load
