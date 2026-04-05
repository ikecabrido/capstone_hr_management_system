<?php
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/MedicalRecord.php";
require_once "lib/fpdf/fpdf.php";

if (!isset($_GET['id'])) {
    die("Record ID is required.");
}

$record_id = $_GET['id'];
$database = Database::getInstance();
$db = $database->getConnection();
$medicalModel = new MedicalRecord($db);

$record = $medicalModel->getRecordsWithPatientDetails(['record_id' => $record_id]);

if (empty($record)) {
    die("Record not found.");
}

$data = $record[0];
$vitalSigns = JSON_decode($data['vital_signs'], true);

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'BCP BULACAN CLINIC SYSTEM', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Medical Record History', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Patient Information
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'PATIENT INFORMATION', 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(40, 10, 'Patient Name:', 0);
$pdf->Cell(60, 10, $data['patient_name'], 0);
$pdf->Cell(40, 10, 'Patient Type:', 0);
$pdf->Cell(50, 10, $data['patient_type'], 0, 1);
$pdf->Cell(40, 10, 'Visit Date:', 0);
$pdf->Cell(60, 10, date('M d, Y h:i A', strtotime($data['visit_date'])), 0);
$pdf->Cell(40, 10, 'Record ID:', 0);
$pdf->Cell(50, 10, $data['record_id'], 0, 1);
$pdf->Ln(5);

// Clinical Information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'CLINICAL INFORMATION', 1, 1, 'L', true);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Chief Complaint:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['chief_complaint'], 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Diagnosis:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['diagnosis'], 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Treatment:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['treatment'], 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Medications Prescribed:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['medications_prescribed'] ?: 'None', 0, 1);
$pdf->Ln(5);

// Vital Signs
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'VITAL SIGNS', 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 10, 'Blood Pressure:', 0);
$pdf->Cell(50, 10, ($vitalSigns['bp_systolic'] ?? '-') . '/' . ($vitalSigns['bp_diastolic'] ?? '-') . ' mmHg', 0);
$pdf->Cell(45, 10, 'Heart Rate:', 0);
$pdf->Cell(50, 10, ($vitalSigns['heart_rate'] ?? '-') . ' bpm', 0, 1);
$pdf->Cell(45, 10, 'Temperature:', 0);
$pdf->Cell(50, 10, ($vitalSigns['temperature'] ?? '-') . ' C', 0);
$pdf->Cell(45, 10, 'Weight:', 0);
$pdf->Cell(50, 10, ($vitalSigns['weight'] ?? '-') . ' kg', 0, 1);
$pdf->Cell(45, 10, 'Height:', 0);
$pdf->Cell(50, 10, ($vitalSigns['height'] ?? '-') . ' cm', 0, 1);
$pdf->Ln(5);

// Sick Leave Information
if (($data['sick_leave_status'] ?? 'None') !== 'None') {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'SICK LEAVE INFORMATION', 1, 1, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(40, 10, 'Status:', 0);
    $pdf->Cell(60, 10, $data['sick_leave_status'], 0, 1);
    $pdf->Cell(40, 10, 'Start Date:', 0);
    $pdf->Cell(60, 10, $data['sick_leave_start'], 0);
    $pdf->Cell(40, 10, 'End Date:', 0);
    $pdf->Cell(60, 10, $data['sick_leave_end'], 0, 1);
    $pdf->Cell(40, 10, 'Total Days:', 0);
    $pdf->Cell(60, 10, ($data['sick_leave_days'] ?? 0) . ' day(s)', 0, 1);
    $pdf->Ln(5);
}

// Other Information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'ADDITIONAL NOTES', 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['notes'] ?: 'No additional notes.', 0, 1);
$pdf->Ln(10);

$pdf->Cell(40, 10, 'Attending Physician:', 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 10, $data['attending_physician'], 0, 1);

$pdf->Output('D', 'Medical_Record_' . $record_id . '.pdf');
?>