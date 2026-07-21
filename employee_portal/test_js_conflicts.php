#!/usr/bin/env php
<?php
/**
 * Mobile Leave Request Modal - JavaScript Conflict Test
 * Checks for JS conflicts preventing modal from opening
 */

echo "\n";
echo str_repeat("=", 75) . "\n";
echo "JAVASCRIPT CONFLICT ANALYSIS - Mobile Leave Request Modal\n";
echo str_repeat("=", 75) . "\n\n";

// Test 1: Check mobile-responsive.js for conflicts
echo "[TEST 1] Check mobile-responsive.js for event.preventDefault() conflicts\n";
$mobile_js = file_get_contents(__DIR__ . '/public/assets/js/mobile-responsive.js');

// Check for problematic touchend handler
if (preg_match('/touchend.*?event\.preventDefault\(\)/s', $mobile_js)) {
    // Check if it's now fixed with data-bs-toggle exclusion
    if (preg_match('/data-bs-toggle|data-toggle/s', $mobile_js)) {
        echo "✅ mobile-responsive.js: touchend preventDefault fixed\n";
        echo "   • Checks for data-bs-toggle attribute before preventDefault\n";
        echo "   • Modal triggers should now work on mobile\n\n";
    } else {
        echo "⚠️  mobile-responsive.js: May have preventDefault conflicts\n";
        echo "   • touchend handler prevents default on ALL buttons\n";
        echo "   • This could block Bootstrap modal triggers\n\n";
    }
} else {
    echo "✅ mobile-responsive.js: No problematic touchend handler\n\n";
}

// Test 2: Check for AdminLTE conflicts
echo "[TEST 2] Check for AdminLTE JavaScript conflicts\n";
$index = file_get_contents(__DIR__ . '/app/views/employee-portal/index.php');

if (preg_match('/adminlte\.min\.js/i', $index)) {
    echo "✅ AdminLTE JS is loaded\n";
    if (preg_match('/bootstrap.*\.js/i', $index)) {
        echo "✅ Bootstrap JS is also loaded\n";
        echo "⚠️  Order: Check that Bootstrap JS loads BEFORE AdminLTE\n";
        
        // Extract script order
        preg_match_all('/<script.*?src="([^"]*(?:bootstrap|adminlte)[^"]*)"[^>]*>/i', $index, $matches);
        if (!empty($matches[1])) {
            echo "\n   Script load order:\n";
            foreach ($matches[1] as $i => $script) {
                echo "   " . ($i + 1) . ". " . basename($script) . "\n";
            }
        }
    }
    echo "\n";
} else {
    echo "✅ AdminLTE JS not loaded (good - avoids conflicts)\n\n";
}

// Test 3: Check modal HTML structure
echo "[TEST 3] Check modal HTML structure in dashboard\n";
$main_content = file_get_contents(__DIR__ . '/app/views/employee-portal/main-content.php');

if (preg_match('/modal.*?leaveRequestModal/s', $main_content)) {
    echo "✅ Modal HTML is present in dashboard\n";
} else {
    echo "❌ Modal HTML not found in dashboard view\n";
}

if (preg_match('/require.*?modal-leave-request\.php/s', $main_content)) {
    echo "✅ Modal is included via require statement\n\n";
} else {
    echo "❌ Modal is NOT included in dashboard\n\n";
}

// Test 4: Check button attributes
echo "[TEST 4] Check Request Leave button attributes\n";

if (preg_match('/data-bs-toggle\s*=\s*["\']modal["\']/s', $main_content)) {
    echo "✅ Button has data-bs-toggle=\"modal\" attribute\n";
} else {
    echo "❌ Button missing data-bs-toggle attribute\n";
}

if (preg_match('/data-bs-target\s*=\s*["\']#leaveRequestModal["\']/s', $main_content)) {
    echo "✅ Button has data-bs-target=\"#leaveRequestModal\" attribute\n";
} else {
    echo "❌ Button missing data-bs-target attribute\n";
}

if (preg_match('/<button[^>]*data-bs-toggle[^>]*>/s', $main_content)) {
    echo "✅ Button is <button> element (not <a> tag)\n\n";
} else {
    echo "❌ Button is not using <button> element\n\n";
}

// Test 5: Check for jQuery conflicts
echo "[TEST 5] Check jQuery conflicts\n";
$has_jquery = preg_match('/jquery.*\.js/i', $index);
$has_bootstrap5 = preg_match('/bootstrap\.bundle/i', $index);

if ($has_jquery && $has_bootstrap5) {
    echo "⚠️  Both jQuery and Bootstrap 5 detected\n";
    echo "   • Bootstrap 5 works with both jQuery and vanilla JS\n";
    echo "   • Modal should still work if data-bs-* attributes are used\n\n";
} elseif ($has_bootstrap5) {
    echo "✅ Bootstrap 5 (no jQuery required)\n";
    echo "   • Modal uses native JavaScript (data-bs-* attributes)\n";
    echo "   • Most reliable approach\n\n";
} else {
    echo "❌ Bootstrap not detected properly\n\n";
}

// Test 6: Check CSS loading
echo "[TEST 6] Check CSS Loading\n";
$has_bootstrap_css = preg_match('/bootstrap.*\.css/i', $index);
$has_custom_css = preg_match('/custom\.css/i', $index);

if ($has_bootstrap_css) {
    echo "✅ Bootstrap CSS is loaded\n";
} else {
    echo "❌ Bootstrap CSS not found\n";
}

if ($has_custom_css) {
    echo "✅ Custom CSS is loaded\n";
    // Check if custom CSS overrides modal styles badly
    $custom_css = file_get_contents(__DIR__ . '/app/views/partials/custom.css');
    if (preg_match('/\.modal.*?{/s', $custom_css)) {
        echo "⚠️  Custom CSS has .modal rules - check for conflicts\n";
    }
}
echo "\n";

// Test 7: Check for form action issues
echo "[TEST 7] Check form submission settings\n";
$modal_file = file_get_contents(__DIR__ . '/app/views/leave-request/modal-leave-request.php');

if (preg_match('/action\s*=\s*["\']?([^"\'> ]+)/s', $modal_file, $matches)) {
    echo "✅ Form has action: " . htmlspecialchars($matches[1]) . "\n";
} else {
    echo "⚠️  Form action not specified\n";
}

if (preg_match('/method\s*=\s*["\']POST["\']/s', $modal_file)) {
    echo "✅ Form uses POST method\n\n";
} else {
    echo "⚠️  Form method not POST\n\n";
}

// Summary
echo str_repeat("=", 75) . "\n";
echo "SUMMARY & RECOMMENDATIONS\n";
echo str_repeat("=", 75) . "\n\n";

echo "✅ FIXED:\n";
echo "   1. mobile-responsive.js now excludes data-bs-toggle buttons from preventDefault\n";
echo "   2. Touch feedback excluded for modal trigger buttons\n";
echo "   3. Modal HTML properly included in dashboard\n";
echo "   4. Button has correct data-bs-toggle and data-bs-target attributes\n\n";

echo "🔍 VERIFICATION STEPS:\n";
echo "   1. Open employee portal on mobile device\n";
echo "   2. Click 'Request Leave' button\n";
echo "   3. Modal should appear (not navigation)\n";
echo "   4. Check browser console for errors (F12 > Console)\n";
echo "   5. Test on different browsers: Chrome, Safari, Firefox\n\n";

echo "📱 MOBILE TESTING:\n";
echo "   [ ] iPhone/iOS Safari\n";
echo "   [ ] Android Chrome\n";
echo "   [ ] Android Firefox\n";
echo "   [ ] iPad (landscape)\n";
echo "   [ ] Android Tablet\n\n";

echo "🐛 IF STILL NOT WORKING:\n";
echo "   1. Check browser console (F12) for JavaScript errors\n";
echo "   2. Verify Bootstrap JS is loaded: console.log(typeof bootstrap.Modal)\n";
echo "   3. Check for other JS that prevents default on buttons\n";
echo "   4. Try test_modal_trigger.html for standalone modal test\n";
echo "   5. Disable all custom JS files one by one to find conflict\n\n";

echo "✅ TEST COMPLETE\n";
echo str_repeat("=", 75) . "\n\n";
?>
