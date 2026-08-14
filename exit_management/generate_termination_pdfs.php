<?php
// Script: generate_termination_pdfs.php
// Scans exit_terminations and ensures a PDF termination letter exists and is linked in exit_documents

require_once __DIR__ . '/models/ExitManagementModel.php';
require_once __DIR__ . '/models/TerminationModel.php';
require_once __DIR__ . '/models/DocumentationModel.php';

$termModel = new TerminationModel();
$docModel = new DocumentationModel();
$db = $termModel->getConnection();

// Try to include Dompdf
$dompdfAutoload = __DIR__ . '/../payroll/vendor/autoload.php';
$hasDompdf = false;
if (file_exists($dompdfAutoload)) {
    require_once $dompdfAutoload;
    if (class_exists('\Dompdf\Dompdf')) {
        $hasDompdf = true;
    }
}

echo "Dompdf available: " . ($hasDompdf ? 'YES' : 'NO') . "\n";

$stmt = $db->query("SELECT * FROM exit_terminations ORDER BY created_at DESC");
$terms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uploadDir = __DIR__ . '/../uploads/documents/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

foreach ($terms as $t) {
    $tid = (int)$t['id'];
    echo "Processing termination id=$tid employee_id={$t['employee_id']}...\n";
    $docs = $docModel->getDocumentsByExitCase('termination', $tid);
    if (!empty($docs)) {
        echo " - Document(s) already linked (count=" . count($docs) . ")\n";
        continue;
    }

    // Build HTML same as TerminationModel
    $employeeId = $t['employee_id'];
    $employeeName = '';
    try {
        $stmt2 = $db->prepare("SELECT full_name FROM employees WHERE employee_id = ? LIMIT 1");
        $stmt2->execute([(string)$employeeId]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($row) $employeeName = $row['full_name'];
    } catch (Exception $e) {}

    $effectiveDate = $t['effective_date'] ?? '';
    $reason = $t['termination_reason'] ?? '';
    $comments = $t['comments'] ?? '';

    $html = '<!doctype html><html><head><meta charset="utf-8"><title>Termination Letter</title>' .
        '<style>body{font-family:Arial,sans-serif;margin:24px;color:#172b4d;} .school-header{display:flex;align-items:center;border-bottom:2px solid #1f5fbf;padding-bottom:14px;margin-bottom:20px;} .school-header img{width:86px;height:86px;object-fit:contain;margin-right:18px;} .school-name{font-size:20px;font-weight:700;color:#174a8b;} .school-details{font-size:12px;line-height:1.6;color:#333;margin-top:4px;} .content{font-size:14px;line-height:1.7;color:#1f2937;}</style>' .
        '</head><body>' .
        '<div class="school-header"><img src="/capstone_hr_management_system2/assets/pics/bcpLogo.png" alt="BCP logo"><div><div class="school-name">Bestlink College of the Philippines - Bulacan Campus</div><div class="school-details">Lot 1 Ipo Road Brgy. Minuyan Proper, City of San Jose Del Monte, Bulacan.<br>Tel. No.: (044)792-1992</div></div></div>' .
        '<h2>Termination Letter</h2>' .
        '<div class="content">' .
        '<p>This letter serves as formal notice that <strong>' . htmlspecialchars($employeeName, ENT_QUOTES) . '</strong> (Employee ID: ' . htmlspecialchars($employeeId, ENT_QUOTES) . ') is being terminated effective <strong>' . htmlspecialchars($effectiveDate, ENT_QUOTES) . '</strong>.</p>' .
        '<p><strong>Reason for termination:</strong> ' . nl2br(htmlspecialchars($reason, ENT_QUOTES)) . '</p>' .
        ($comments ? '<p><strong>Additional notes:</strong> ' . nl2br(htmlspecialchars($comments, ENT_QUOTES)) . '</p>' : '') .
        '<p>Issued by HR Management</p>' .
        '</div></body></html>';

    $pdfFileName = 'termination_' . time() . '_' . $tid . '.pdf';
    $filePathRelative = 'uploads/documents/' . $pdfFileName;
    $fullPath = __DIR__ . '/../' . $filePathRelative;

    $generated = false;
    if ($hasDompdf) {
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $out = $dompdf->output();
            file_put_contents($fullPath, $out);
            $generated = true;
        } catch (Exception $e) {
            echo " - Dompdf failed: " . $e->getMessage() . "\n";
            $generated = false;
        }
    }

    if (!$generated) {
        // fallback to HTML file
        $fileName = 'termination_' . time() . '_' . $tid . '.html';
        $filePathRelative = 'uploads/documents/' . $fileName;
        $fullPath = __DIR__ . '/../' . $filePathRelative;
        file_put_contents($fullPath, $html);
    }

    // Create document record
    $docData = [
        'employee_id' => $employeeId,
        'exit_case_type' => 'termination',
        'exit_case_id' => $tid,
        'document_type' => 'other',
        'title' => 'Termination Letter',
        'file_path' => $filePathRelative,
        'uploaded_by' => null
    ];

    $docId = $docModel->createDocument($docData);
    echo " - Created document id: $docId path: $filePathRelative\n";
}

echo "Done.\n";
