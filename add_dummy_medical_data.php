<?php
require_once __DIR__ . "/auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    die("Database connection failed.");
}

try {
    echo "Starting dummy data injection with images...\n";

    // 1. Create/Update dummy patients with avatars
    $dummy_patients = [
        ['patient_id' => 'EMP001', 'employee_id' => 101, 'first_name' => 'John', 'last_name' => 'Doe', 'avatar' => 'user1-128x128.jpg', 'patient_type' => 'Staff', 'gender' => 'Male', 'birth_date' => '1990-05-15'],
        ['patient_id' => 'EMP002', 'employee_id' => 102, 'first_name' => 'Jane', 'last_name' => 'Smith', 'avatar' => 'user2-160x160.jpg', 'patient_type' => 'Faculty', 'gender' => 'Female', 'birth_date' => '1985-10-20'],
        ['patient_id' => 'EMP003', 'employee_id' => 103, 'first_name' => 'Robert', 'last_name' => 'Johnson', 'avatar' => 'user3-128x128.jpg', 'patient_type' => 'Staff', 'gender' => 'Male', 'birth_date' => '1992-02-10'],
        ['patient_id' => 'EMP004', 'employee_id' => 104, 'first_name' => 'Emily', 'last_name' => 'Davis', 'avatar' => 'user4-128x128.jpg', 'patient_type' => 'Faculty', 'gender' => 'Female', 'birth_date' => '1988-08-05'],
        ['patient_id' => 'EMP005', 'employee_id' => 105, 'first_name' => 'Michael', 'last_name' => 'Wilson', 'avatar' => 'user5-128x128.jpg', 'patient_type' => 'Staff', 'gender' => 'Male', 'birth_date' => '1995-12-12'],
        ['patient_id' => 'EMP006', 'employee_id' => 106, 'first_name' => 'Sarah', 'last_name' => 'Miller', 'avatar' => 'user6-128x128.jpg', 'patient_type' => 'Faculty', 'gender' => 'Female', 'birth_date' => '1991-04-22'],
        ['patient_id' => 'EMP007', 'employee_id' => 107, 'first_name' => 'David', 'last_name' => 'Brown', 'avatar' => 'user7-128x128.jpg', 'patient_type' => 'Staff', 'gender' => 'Male', 'birth_date' => '1989-11-30'],
        ['patient_id' => 'EMP008', 'employee_id' => 108, 'first_name' => 'Jessica', 'last_name' => 'Taylor', 'avatar' => 'user8-128x128.jpg', 'patient_type' => 'Staff', 'gender' => 'Female', 'birth_date' => '1993-07-15']
    ];

    $patient_insert = $db->prepare("INSERT INTO cm_patients (patient_id, employee_id, first_name, last_name, avatar, patient_type, gender, birth_date, created_at) 
                                   VALUES (:patient_id, :employee_id, :first_name, :last_name, :avatar, :patient_type, :gender, :birth_date, NOW()) 
                                   ON DUPLICATE KEY UPDATE first_name=VALUES(first_name), avatar=VALUES(avatar)");
    
    foreach ($dummy_patients as $p) {
        $patient_insert->execute($p);
        echo "Ensured patient: {$p['first_name']} {$p['last_name']} with avatar {$p['avatar']}\n";
    }

    // 2. Add Medical Records for the last 14 days to have better trends
    $illnesses = ['Headache', 'Fever', 'Cough', 'Stomach Ache', 'Muscle Pain', 'Allergy', 'Flu', 'Back Pain', 'Fatigue', 'Sore Throat'];
    $medicines = ['MED001', 'MED002', 'MED003', 'MED004', 'MED005'];
    $physicians = ['Dr. Juan Dela Cruz', 'Dr. Maria Clara', 'Dr. Jose Rizal', 'Dr. Andres Bonifacio'];

    $record_insert = $db->prepare("INSERT INTO cm_medical_records 
        (record_id, patient_id, visit_date, chief_complaint, diagnosis, treatment, attending_physician, status, consultation_type, vital_signs, created_by) 
        VALUES (:record_id, :patient_id, :visit_date, :chief_complaint, :diagnosis, :treatment, :physician, 'Completed', :type, :vitals, 'System')");

    $usage_insert = $db->prepare("INSERT INTO cm_medicine_usage_logs (log_id, medicine_id, record_id, quantity_used, usage_date, purpose, used_by, remaining_stock) 
                                 VALUES (:log_id, :medicine_id, :record_id, :qty, :usage_date, :purpose, 'System', 100)");

    for ($i = 0; $i < 14; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $num_records = rand(2, 10); // Random visits per day to make the chart look interesting
        
        for ($j = 0; $j < $num_records; $j++) {
            $timestamp = strtotime("-$i days " . rand(8, 16) . ":" . rand(10, 59) . ":" . rand(10, 59));
            $rec_id = 'REC' . date('YmdHis', $timestamp) . $j;
            $p_idx = rand(0, count($dummy_patients) - 1);
            $illness = $illnesses[rand(0, count($illnesses) - 1)];
            $physician = $physicians[rand(0, count($physicians) - 1)];
            $type = ['Walk-in', 'Appointment', 'Emergency', 'Follow-up'][rand(0, 3)];
            
            $vitals = json_encode([
                'bp_systolic' => rand(110, 130),
                'bp_diastolic' => rand(70, 90),
                'heart_rate' => rand(60, 100),
                'temperature' => number_format(36.5 + (rand(0, 20) / 10), 1),
                'weight' => rand(50, 90),
                'height' => rand(150, 185)
            ]);

            $record_insert->execute([
                'record_id' => $rec_id,
                'patient_id' => $dummy_patients[$p_idx]['patient_id'],
                'visit_date' => date('Y-m-d H:i:s', $timestamp),
                'chief_complaint' => "Feeling $illness",
                'diagnosis' => $illness,
                'treatment' => 'Prescribed medicine and rest',
                'physician' => $physician,
                'type' => $type,
                'vitals' => $vitals
            ]);

            // Add dummy medicine usage
            $num_meds = rand(1, 2);
            for ($k = 0; $k < $num_meds; $k++) {
                $m_idx = rand(0, 4);
                $qty = rand(1, 3);
                $usage_insert->execute([
                    'log_id' => 'LOG' . date('His', $timestamp) . $j . $k,
                    'medicine_id' => $medicines[$m_idx],
                    'record_id' => $rec_id,
                    'qty' => $qty,
                    'usage_date' => date('Y-m-d H:i:s', $timestamp),
                    'purpose' => "Prescribed for $rec_id"
                ]);
            }
        }
        echo "Added $num_records records for $date\n";
    }

    echo "Dummy data injection completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
