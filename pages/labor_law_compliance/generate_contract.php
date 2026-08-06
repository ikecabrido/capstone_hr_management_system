<?php
/**
 * pages/labor_law_compliance/generate_contract.php
 *
 * Public-facing redirect shim.
 *
 * The canonical contract generator lives under the legal_compliance module at
 * legal_compliance/pages/labor_law_compliance/generate_contract.php. This thin
 * shim lets the short URL
 *   /capstone_hr_management_system/pages/labor_law_compliance/generate_contract.php
 * resolve to the real endpoint without duplicating any generation logic.
 * All query parameters (?contract_id=20, &download=1, &email=1, ...) are
 * forwarded intact so PDF / email / download flows behave identically.
 */

$realPath = __DIR__ . '/../../legal_compliance/pages/labor_law_compliance/generate_contract.php';

if (!is_file($realPath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Contract generator not found.';
    exit;
}

// Query parameters (?contract_id=18, &download=1, &email=1, ...) are read by the
// real generator from $_GET, which is already populated for this request, so we
// just include the file. Do NOT append the query string to the path — require()
// treats a path with "?..." as a literal filename and fails to open it.
require $realPath;
