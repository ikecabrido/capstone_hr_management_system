# Fullscreen & Theme Toggle Button Testing Guide

## Changes Made

### 1. **CSS Improvements** (`payroll/custom.css`)

- Added `cursor: pointer` to navbar buttons to make them visually clickable
- Added `pointer-events: auto` to prevent accidental blocking
- Added hover opacity effect for better user feedback

### 2. **Theme Toggle Fix** (`payroll/custom.js`)

- Wrapped initialization in `DOMContentLoaded` event
- Added console logging for debugging
- Added `tabindex` and cursor style to button element
- Added database persistence via `saveTheme.php` endpoint
- Checks for element existence before accessing

### 3. **Fullscreen Button Fix** (All three payroll pages)

- Enhanced event handler with detailed console logging
- Added error handling with `.catch()` for Promise-based fullscreen API
- Added state checking before attempting fullscreen
- Applied CSS styles programmatically via jQuery

## How to Test

### Step 1: Open Browser Console

1. Press **F12** to open Developer Tools
2. Go to the **Console** tab
3. Keep the console visible while testing

### Step 2: Test Theme Toggle Button (Moon/Sun Icon)

1. **Click the moon icon** in the top-right navbar
2. **Check the Console** for these messages:
   ```
   [Theme Toggle] DOMContentLoaded fired
   [Theme Toggle] Elements found: { toggleBtn: true, icon: true }
   [Theme Toggle] Button clicked!
   [Theme Toggle] Setting theme to: dark animate: true
   [Theme Toggle] Theme applied: dark
   ```
3. **Visual feedback:**
   - Icon should **rotate 180° and scale down** (smooth animation)
   - Icon should **change from moon (🌙) to sun (☀️)**
   - Page background should **change to dark theme**
4. **Click again** to toggle back to light mode
5. **Refresh the page** - theme should persist from `localStorage`

### Step 3: Test Fullscreen Button (Expand Icon)

1. **Click the expand icon** in the top-right navbar
2. **Check the Console** for these messages:
   ```
   [AdminLTE Init] Document ready
   [Fullscreen] Button elements found: 1
   [Fullscreen] Button clicked!
   [Fullscreen] Current state - fullscreen: false
   [Fullscreen] Requesting fullscreen
   ```
3. **Visual feedback:**
   - Page should **enter fullscreen mode**
   - Press **ESC** or **click fullscreen button again** to exit
4. **Check console** when exiting:
   ```
   [Fullscreen] Current state - fullscreen: true
   [Fullscreen] Exiting fullscreen
   ```

## Troubleshooting

### Button Click Not Registered

1. **Check console** for JavaScript errors (red messages)
2. **Verify** the buttons are visible in the navbar
3. **Check browser compatibility:**
   - Chrome/Edge: Should work natively
   - Firefox: Should work natively
   - Safari: May require webkit prefix (already handled)

### Theme Not Changing

1. **Check localStorage:** Open Console and run:
   ```javascript
   console.log("Saved theme:", localStorage.getItem("theme"));
   ```
2. **Verify database persistence:** Check if `saveTheme.php` exists and is callable
3. **Look for CORS errors** if saveTheme.php returns errors (check Network tab)

### Fullscreen Not Working

1. **Browser restrictions:** Fullscreen requires user gesture (click) - cannot be automated
2. **Security context:** Some browsers restrict fullscreen in embedded pages
3. **Check permissions:** Some systems may block fullscreen mode
4. **Try different browser** to test if browser-specific issue

## Console Message Summary

| Message                                                          | Means                                 |
| ---------------------------------------------------------------- | ------------------------------------- |
| `[Theme Toggle] DOMContentLoaded fired`                          | Script loaded successfully            |
| `[Theme Toggle] Elements found: { toggleBtn: true, icon: true }` | Buttons found in DOM                  |
| `[Theme Toggle] Button clicked!`                                 | User clicked the theme toggle button  |
| `[Fullscreen] Button elements found: 1`                          | Fullscreen button detected            |
| `[Fullscreen] Button clicked!`                                   | User clicked the fullscreen button    |
| `ERROR: Theme toggle elements not found`                         | Problem with HTML structure           |
| `[Fullscreen] Request error:`                                    | Browser blocked fullscreen (security) |

## Files Modified

1. **`payroll/custom.css`** - Added button styling
2. **`payroll/custom.js`** - Enhanced theme toggle with logging & persistence
3. **`payroll/payroll.php`** - Enhanced fullscreen handler
4. **`payroll/views/payrollProcess.php`** - Enhanced fullscreen handler
5. **`payroll/views/payrollClearance.php`** - Enhanced fullscreen handler

## Next Steps If Still Not Working

If buttons still don't work after these changes:

1. **Save this test data to check:**

   ```javascript
   // Run in browser console
   console.log("jQuery loaded:", typeof jQuery !== "undefined");
   console.log("AdminLTE loaded:", typeof $.AdminLTE !== "undefined");
   console.log("toggleBtn element:", document.getElementById("darkToggle"));
   console.log(
     "fullscreen btn element:",
     document.querySelector('[data-widget="fullscreen"]'),
   );
   console.log("User agent:", navigator.userAgent);
   ```

2. **Check Network tab** for any failed resource loads (red entries)
3. **Check Sources tab** for JavaScript breakpoints that may be pausing execution
4. **Try a different browser** to isolate browser-specific issues
5. **Clear cache** (Ctrl+Shift+Delete) and reload the page
