<?php
require_once "../auth/auth_check.php";
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/MedicalRecord.php";
require_once "models/Employee.php";
require_once "models/Patient.php";
require_once "models/MedicineInventory.php";
require_once "models/EmergencyCase.php";
require_once "lib/fpdf/fpdf.php";

if (!isset($_GET['type'])) {
    die("Type is required.");
}

$type = $_GET['type'];
$id = $_GET['id'] ?? null;

// Validate that ID is provided for non-list types
if (!str_ends_with($type, '_list') && $id === null) {
    die("ID is required for this report type.");
}

$database = Database::getInstance();
$db = $database->getConnection();

class ClinicPDF extends FPDF {
    protected $reportTitle;

    function setReportTitle($title) {
        $this->reportTitle = $title;
    }

    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'BCP BULACAN CLINIC SYSTEM', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->reportTitle, 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 0, 'R');
    }

    function SectionHeader($label) {
        $this->SetFillColor(230, 230, 230);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $label, 1, 1, 'L', true);
        $this->Ln(2);
    }

    function DataRow($label, $value) {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(50, 8, $label . ':', 0);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 8, $value, 0, 1);
    }
}

$pdf = new ClinicPDF();
$pdf->AliasNbPages();

switch ($type) {
    case 'medical_record':
        $model = new MedicalRecord($db);
        $records = $model->getRecordsWithPatientDetails(['record_id' => $id]);
        if (empty($records)) die("Record not found.");
        $data = $records[0];
        $vitalSigns = json_decode($data['vital_signs'], true);

        $pdf->setReportTitle('Medical Record History');
        $pdf->AddPage();
        
        $pdf->SectionHeader('PATIENT INFORMATION');
        $pdf->DataRow('Patient Name', $data['patient_name']);
        $pdf->DataRow('Patient Type', $data['patient_type']);
        $pdf->DataRow('Visit Date', date('M d, Y h:i A', strtotime($data['visit_date'])));
        $pdf->DataRow('Record ID', $data['record_id']);
        $pdf->Ln(5);

        $pdf->SectionHeader('CLINICAL INFORMATION');
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Chief Complaint:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $data['chief_complaint'], 0, 1); $pdf->Ln(2);
        
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Diagnosis:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $data['diagnosis'], 0, 1); $pdf->Ln(2);
        
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Treatment:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $data['treatment'], 0, 1); $pdf->Ln(5);

        $pdf->SectionHeader('VITAL SIGNS');
        $pdf->DataRow('Blood Pressure', ($vitalSigns['bp_systolic'] ?? '-') . '/' . ($vitalSigns['bp_diastolic'] ?? '-') . ' mmHg');
        $pdf->DataRow('Heart Rate', ($vitalSigns['heart_rate'] ?? '-') . ' bpm');
        $pdf->DataRow('Temperature', ($vitalSigns['temperature'] ?? '-') . ' C');
        $pdf->DataRow('Weight', ($vitalSigns['weight'] ?? '-') . ' kg');
        $pdf->DataRow('Height', ($vitalSigns['height'] ?? '-') . ' cm');
        $pdf->Ln(5);

        if (($data['sick_leave_status'] ?? 'None') !== 'None') {
            $pdf->SectionHeader('SICK LEAVE INFORMATION');
            $pdf->DataRow('Status', $data['sick_leave_status']);
            $pdf->DataRow('Start Date', $data['sick_leave_start']);
            $pdf->DataRow('End Date', $data['sick_leave_end']);
            $pdf->DataRow('Total Days', ($data['sick_leave_days'] ?? 0) . ' day(s)');
            $pdf->Ln(5);
        }

        $pdf->SectionHeader('ADDITIONAL NOTES');
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $data['notes'] ?: 'No additional notes.', 0, 1);
        $pdf->Ln(10);

        $pdf->DataRow('Attending Physician', $data['attending_physician']);
        $filename = 'Medical_Record_' . $id . '.pdf';
        break;

    case 'employee':
        $model = new Patient($db);
        $data = $model->read(['patient_id' => $id]);
        if (empty($data)) die("Employee not found.");
        $emp = $data[0];

        $pdf->setReportTitle('Employee Medical Profile');
        $pdf->AddPage();

        $pdf->SectionHeader('PERSONAL INFORMATION');
        $pdf->DataRow('Employee ID', $emp['employee_id']);
        $pdf->DataRow('Name', $emp['first_name'] . ' ' . ($emp['middle_name'] ? $emp['middle_name'] . ' ' : '') . $emp['last_name']);
        $pdf->DataRow('Email', $emp['email'] ?: 'N/A');
        $pdf->DataRow('Phone', $emp['phone'] ?: 'N/A');
        $pdf->DataRow('Type', $emp['patient_type']);
        $pdf->Ln(5);

        $pdf->SectionHeader('MEDICAL PROFILE');
        $pdf->DataRow('Blood Type', $emp['blood_type'] ?: 'Unknown');
        $pdf->DataRow('Allergies', $emp['allergies'] ?: 'None');
        $pdf->DataRow('Medical Conditions', $emp['medical_conditions'] ?: 'None');
        $pdf->DataRow('Current Medications', $emp['current_medications'] ?: 'None');
        $pdf->Ln(5);

        $pdf->SectionHeader('EMERGENCY CONTACT');
        $pdf->DataRow('Contact Person', $emp['emergency_contact_name'] ?? 'N/A');
        $pdf->DataRow('Contact Number', $emp['emergency_contact_phone'] ?? 'N/A');
        
        $filename = 'Employee_Profile_' . $emp['employee_id'] . '.pdf';
        break;

    case 'medicine':
        $model = new MedicineInventory($db);
        $data = $model->read(['medicine_id' => $id]);
        if (empty($data)) die("Medicine not found.");
        $med = $data[0];

        $pdf->setReportTitle('Medicine Information Sheet');
        $pdf->AddPage();

        $pdf->SectionHeader('MEDICINE DETAILS');
        $pdf->DataRow('Medicine ID', $med['medicine_id']);
        $pdf->DataRow('Name', $med['medicine_name']);
        $pdf->DataRow('Generic Name', $med['generic_name'] ?: 'N/A');
        $pdf->DataRow('Category', $med['category']);
        $pdf->DataRow('Expiry Date', $med['expiry_date'] ? date('M d, Y', strtotime($med['expiry_date'])) : 'N/A');
        $pdf->Ln(5);

        $pdf->SectionHeader('INVENTORY STATUS');
        $pdf->DataRow('Current Stock', number_format($med['current_stock']));
        $pdf->DataRow('Reorder Level', number_format($med['reorder_level'] ?? 10));
        
        $status = 'Available';
        if (strtotime($med['expiry_date']) < time()) $status = 'Expired';
        elseif ($med['current_stock'] == 0) $status = 'Out of Stock';
        elseif ($med['current_stock'] <= ($med['reorder_level'] ?? 10)) $status = 'Low Stock';
        
        $pdf->DataRow('Current Status', $status);
        
        $filename = 'Medicine_' . $id . '.pdf';
        break;

    case 'emergency':
        $model = new EmergencyCase($db);
        $records = $model->read(['case_id' => $id]);
        if (empty($records)) die("Emergency case not found.");
        $case = $records[0];
        
        // Get patient name
        $patientModel = new Patient($db);
        $patientData = $patientModel->read(['patient_id' => $case['patient_id']]);
        $patientName = !empty($patientData) ? ($patientData[0]['first_name'] . ' ' . $patientData[0]['last_name']) : 'Unknown';

        $pdf->setReportTitle('Emergency Incident Report');
        $pdf->AddPage();

        $pdf->SectionHeader('CASE SUMMARY');
        $pdf->DataRow('Case ID', $case['case_id']);
        $pdf->DataRow('Patient', $patientName);
        $pdf->DataRow('Incident Type', $case['incident_type']);
        $pdf->DataRow('Date/Time', date('M d, Y H:i', strtotime($case['incident_date'])));
        $pdf->DataRow('Severity', $case['severity_level']);
        $pdf->DataRow('Status', $case['case_status']);
        $pdf->Ln(5);

        $pdf->SectionHeader('INCIDENT DETAILS');
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Chief Complaint:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $case['chief_complaint'], 0, 1); $pdf->Ln(2);
        
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Initial Assessment:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $case['initial_assessment'], 0, 1); $pdf->Ln(2);
        
        $pdf->SetFont('Arial', 'B', 11); $pdf->Cell(0, 8, 'Treatment Provided:', 0, 1);
        $pdf->SetFont('Arial', '', 11); $pdf->MultiCell(0, 6, $case['treatment_provided'], 0, 1); $pdf->Ln(5);

        $pdf->SectionHeader('OTHER INFORMATION');
        $pdf->DataRow('Attending Staff', $case['attending_staff']);
        $pdf->DataRow('Ambulance Called', $case['ambulance_called'] ? 'Yes' : 'No');
        if ($case['ambulance_called']) {
            $pdf->DataRow('Arrival Time', $case['ambulance_arrival_time'] ? date('H:i', strtotime($case['ambulance_arrival_time'])) : 'N/A');
        }
        
        $filename = 'Emergency_Case_' . $id . '.pdf';
        break;

    case 'employee_list':
        $model = new Employee($db);
        $employees = $model->read([], 'last_name ASC, first_name ASC');
        
        $pdf->setReportTitle('Employee Medical Records List');
        $pdf->AddPage('L'); // Landscape
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'ID', 1);
        $pdf->Cell(50, 8, 'Name', 1);
        $pdf->Cell(20, 8, 'Blood', 1);
        $pdf->Cell(50, 8, 'Allergies', 1);
        $pdf->Cell(50, 8, 'Medical Conditions', 1);
        $pdf->Cell(40, 8, 'Emergency Contact', 1);
        $pdf->Cell(40, 8, 'Phone', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 9);
        foreach ($employees as $emp) {
            $pdf->Cell(20, 8, $emp['employee_id'], 1);
            $pdf->Cell(50, 8, substr($emp['first_name'] . ' ' . $emp['last_name'], 0, 25), 1);
            $pdf->Cell(20, 8, $emp['blood_type'] ?: 'N/A', 1);
            $pdf->Cell(50, 8, substr($emp['allergies'] ?: 'None', 0, 25), 1);
            $pdf->Cell(50, 8, substr($emp['medical_conditions'] ?: 'None', 0, 25), 1);
            $pdf->Cell(40, 8, substr($emp['emergency_contact_name'] ?? 'N/A', 0, 20), 1);
            $pdf->Cell(40, 8, $emp['emergency_contact_phone'] ?? 'N/A', 1);
            $pdf->Ln();
        }
        $filename = 'Employee_List_' . date('Ymd') . '.pdf';
        break;

    case 'medicine_list':
        $model = new MedicineInventory($db);
        $medicines = $model->read([], 'medicine_name ASC');
        
        $pdf->setReportTitle('Medicine Inventory List');
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Medicine Name', 1);
        $pdf->Cell(40, 8, 'Category', 1);
        $pdf->Cell(20, 8, 'Stock', 1);
        $pdf->Cell(40, 8, 'Expiry Date', 1);
        $pdf->Cell(40, 8, 'Status', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 10);
        foreach ($medicines as $med) {
            $status = 'Available';
            if (strtotime($med['expiry_date']) < time()) $status = 'Expired';
            elseif ($med['current_stock'] == 0) $status = 'Out of Stock';
            elseif ($med['current_stock'] <= ($med['reorder_level'] ?? 10)) $status = 'Low Stock';

            $pdf->Cell(50, 8, substr($med['medicine_name'], 0, 25), 1);
            $pdf->Cell(40, 8, $med['category'], 1);
            $pdf->Cell(20, 8, number_format($med['current_stock']), 1);
            $pdf->Cell(40, 8, $med['expiry_date'] ? date('M d, Y', strtotime($med['expiry_date'])) : 'N/A', 1);
            $pdf->Cell(40, 8, $status, 1);
            $pdf->Ln();
        }
        $filename = 'Medicine_Inventory_' . date('Ymd') . '.pdf';
         break;

    case 'medical_record_list':
        $model = new MedicalRecord($db);
        $records = $model->getRecordsWithPatientDetails();
        
        $pdf->setReportTitle('Medical Records History List');
        $pdf->AddPage('L');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 8, 'Record ID', 1);
        $pdf->Cell(35, 8, 'Date', 1);
        $pdf->Cell(45, 8, 'Patient', 1);
        $pdf->Cell(45, 8, 'Chief Complaint', 1);
        $pdf->Cell(45, 8, 'Diagnosis', 1);
        $pdf->Cell(40, 8, 'Physician', 1);
        $pdf->Cell(25, 8, 'Status', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 9);
        foreach ($records as $record) {
            $pdf->Cell(30, 8, $record['record_id'], 1);
            $pdf->Cell(35, 8, date('M d, Y H:i', strtotime($record['visit_date'])), 1);
            $pdf->Cell(45, 8, substr($record['patient_name'], 0, 22), 1);
            $pdf->Cell(45, 8, substr($record['chief_complaint'], 0, 25), 1);
            $pdf->Cell(45, 8, substr($record['diagnosis'], 0, 25), 1);
            $pdf->Cell(40, 8, substr($record['attending_physician'], 0, 20), 1);
            $pdf->Cell(25, 8, $record['status'] ?? 'Pending', 1);
            $pdf->Ln();
        }
        $filename = 'Medical_Records_' . date('Ymd') . '.pdf';
        break;

    case 'emergency_list':
        $model = new EmergencyCase($db);
        $all_cases = $model->read([], 'incident_date DESC');
        
        $pdf->setReportTitle('Emergency Cases List');
        $pdf->AddPage('L');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 8, 'Case ID', 1);
        $pdf->Cell(45, 8, 'Patient', 1);
        $pdf->Cell(35, 8, 'Incident Type', 1);
        $pdf->Cell(35, 8, 'Date/Time', 1);
        $pdf->Cell(25, 8, 'Severity', 1);
        $pdf->Cell(30, 8, 'Status', 1);
        $pdf->Cell(60, 8, 'Treatment Provided', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 9);
        foreach ($all_cases as $case) {
            // Get patient name
            $patientModel = new Patient($db);
            $patientData = $patientModel->read(['patient_id' => $case['patient_id']]);
            $patientName = !empty($patientData) ? ($patientData[0]['first_name'] . ' ' . $patientData[0]['last_name']) : 'Unknown';

            $pdf->Cell(25, 8, $case['case_id'], 1);
            $pdf->Cell(45, 8, substr($patientName, 0, 22), 1);
            $pdf->Cell(35, 8, $case['incident_type'], 1);
            $pdf->Cell(35, 8, date('M d, H:i', strtotime($case['incident_date'])), 1);
            $pdf->Cell(25, 8, $case['severity_level'], 1);
            $pdf->Cell(30, 8, $case['case_status'], 1);
            $pdf->Cell(60, 8, substr($case['treatment_provided'], 0, 35), 1);
            $pdf->Ln();
        }
        $filename = 'Emergency_Cases_' . date('Ymd') . '.pdf';
        break;

     default:
        die("Invalid type.");
}

$pdf->Output('D', $filename);
?>