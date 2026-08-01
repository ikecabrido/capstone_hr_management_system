    <?php
/**
 * File Upload Handler
 * Handles file uploads for incident evidence
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../models/FileUpload.php';
require_once __DIR__ . '/../models/Incident.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

try {
    // Get incident ID
    $incidentId = (int) ($_POST['incident_id'] ?? 0);
    if (!$incidentId) {
        echo json_encode([
            'success' => false,
            'message' => 'Incident ID is required'
        ]);
        exit;
    }

    // Verify incident exists
    $incidentModel = new Incident();
    $incident = $incidentModel->getById($incidentId);
    if (!$incident) {
        echo json_encode([
            'success' => false,
            'message' => 'Incident not found'
        ]);
        exit;
    }

    // Check if files were uploaded
    if (empty($_FILES['evidence'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No files uploaded'
        ]);
        exit;
    }

    // Initialize file upload model
    $fileUpload = new FileUpload();
    $uploadedBy = $_SESSION['user']['id'];
    $results = [];
    $successCount = 0;
    $errorCount = 0;

    // Handle multiple file uploads
    $files = $_FILES['evidence'];
    $fileCount = count($files['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        // Upload file
        $result = $fileUpload->upload($file, $uploadedBy);

        if ($result['success']) {
            // Save to database
            $evidenceId = $incidentModel->addEvidence($incidentId, [
                'file_name' => $result['file_name'],
                'file_path' => $result['file_path'],
                'file_type' => $result['file_type'],
                'file_size' => $result['file_size'],
                'uploaded_by' => $uploadedBy,
                'description' => $_POST['description'] ?? null
            ]);

            $results[] = [
                'success' => true,
                'file_name' => $result['file_name'],
                'evidence_id' => $evidenceId
            ];
            $successCount++;
        } else {
            $results[] = [
                'success' => false,
                'file_name' => $file['name'],
                'message' => $result['message']
            ];
            $errorCount++;
        }
    }

    // Prepare response
    $response = [
        'success' => $successCount > 0,
        'message' => '',
        'results' => $results,
        'success_count' => $successCount,
        'error_count' => $errorCount
    ];

    if ($successCount > 0 && $errorCount === 0) {
        $response['message'] = "All {$successCount} file(s) uploaded successfully";
    } elseif ($successCount > 0 && $errorCount > 0) {
        $response['message'] = "{$successCount} file(s) uploaded, {$errorCount} file(s) failed";
    } else {
        $response['message'] = "All {$errorCount} file(s) failed to upload";
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("File upload error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during file upload'
    ]);
}
