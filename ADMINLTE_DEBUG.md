# AdminLTE Loading Issue - Debug Steps

## Problem

JavaScript error: "Cannot read properties of undefined (reading 'layout')" when trying to access `$.AdminLTE.layout.fix()`

## Root Cause Analysis

The error indicates that `$.AdminLTE` is undefined, meaning the AdminLTE JavaScript library is not properly loaded or attached to jQuery.

## Changes Made

### 1. **Script Source Change**

- **Before:** `adminlte.min.js` (minified version)
- **After:** `adminlte.js` (non-minified version)
- **Reason:** Minified version might have loading issues; non-minified is easier to debug

### 2. **Enhanced Debugging**

- Added console logging to check:
  - jQuery version
  - AdminLTE availability
  - Component initialization status
- Added error handling to prevent crashes if AdminLTE fails to load

### 3. **Consistency Across Files**

- Updated all three payroll pages to use `adminlte.js` instead of `adminlte.min.js`
- **Files changed:**
  - `payroll/payroll.php` ✅ (already correct)
  - `payroll/views/payrollProcess.php` ✅ (changed from minified)
  - `payroll/views/payrollClearance.php` ✅ (changed from minified)

## Expected Console Output (After Fix)

When you load the payroll page, you should see in the browser console:

```
[AdminLTE Init] Document ready
[AdminLTE Init] jQuery version: 3.x.x
[AdminLTE Init] AdminLTE available: true
[AdminLTE Init] AdminLTE components initialized
[Fullscreen] Button elements found: 1
[Theme Toggle] DOMContentLoaded fired
[Theme Toggle] Elements found: { toggleBtn: true, icon: true }
[Theme Toggle] Saved theme from localStorage: light
```

## Troubleshooting Steps

### Step 1: Check Browser Console

1. Press **F12** to open Developer Tools
2. Go to **Console** tab
3. Reload the payroll page
4. Look for the messages above

### Step 2: Check Network Tab

1. Go to **Network** tab in Developer Tools
2. Reload the page
3. Look for `adminlte.js` - it should load with status 200
4. If it shows 404, there's a path issue

### Step 3: Check for JavaScript Errors

- Look for any red error messages in console
- If you see "AdminLTE not loaded!", the script failed to attach to jQuery

### Step 4: Manual Script Test

Open browser console and run:

```javascript
// Test jQuery
console.log("jQuery loaded:", typeof jQuery !== "undefined");
console.log("jQuery version:", $.fn.jquery);

// Test AdminLTE
console.log("AdminLTE loaded:", typeof $.AdminLTE !== "undefined");
if (typeof $.AdminLTE !== "undefined") {
  console.log("AdminLTE methods:", Object.keys($.AdminLTE));
}
```

## If Still Not Working

### Option 1: Clear Cache

1. Press **Ctrl+Shift+Delete** (or Cmd+Shift+Delete on Mac)
2. Clear browsing data for "Cached images and files"
3. Reload the page

### Option 2: Check File Permissions

The `adminlte.js` file should be readable. Check if the file exists:

- Path: `c:\xampp\htdocs\capstone_hr_management_system\assets\dist\js\adminlte.js`
- Should be ~500KB in size

### Option 3: Alternative Loading

If the issue persists, we can try loading AdminLTE differently:

```html
<!-- Try this instead of the current script tag -->
<script>
  // Load AdminLTE manually
  $.getScript("../../assets/dist/js/adminlte.js", function () {
    console.log("AdminLTE loaded manually");
    // Then initialize
    $.AdminLTE.layout.fix();
    $.AdminLTE.pushMenu.activate();
  });
</script>
```

## Files Modified

1. **`payroll/views/payrollProcess.php`** - Changed script source, added debugging
2. **`payroll/views/payrollClearance.php`** - Changed script source
3. **`payroll/payroll.php`** - Already correct

## Next Steps

1. Test the page and check console output
2. If AdminLTE still doesn't load, try the manual loading approach
3. Report back with console output for further diagnosis</content>
   <parameter name="filePath">c:\xampp\htdocs\capstone_hr_management_system\ADMINLTE_DEBUG.md
