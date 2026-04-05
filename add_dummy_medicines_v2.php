<?php
require_once "auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

try {
    echo "Adding dummy medicines to inventory...\n";

    $medicines = [
        [
            'medicine_id' => '6',
            'medicine_name' => 'Paracetamol',
            'generic_name' => 'Biogesic',
            'category' => 'Analgesic',
            'current_stock' => 200,
            'reorder_level' => 50,
            'expiry_date' => '2026-12-31',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '7',
            'medicine_name' => 'Ibuprofen',
            'generic_name' => 'Advil',
            'category' => 'Analgesic',
            'current_stock' => 120,
            'reorder_level' => 30,
            'expiry_date' => '2026-10-15',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '8',
            'medicine_name' => 'Loperamide',
            'generic_name' => 'Imodium',
            'category' => 'Antidiarrheal',
            'current_stock' => 80,
            'reorder_level' => 20,
            'expiry_date' => '2026-09-20',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '9',
            'medicine_name' => 'Salbutamol',
            'generic_name' => 'Ventolin',
            'category' => 'Bronchodilator',
            'current_stock' => 25,
            'reorder_level' => 5,
            'expiry_date' => '2027-01-10',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '10',
            'medicine_name' => 'Amoxicillin',
            'generic_name' => 'Amoxil',
            'category' => 'Antibiotic',
            'current_stock' => 150,
            'reorder_level' => 40,
            'expiry_date' => '2026-08-05',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '11',
            'medicine_name' => 'Losartan',
            'generic_name' => 'Cozaar',
            'category' => 'Antihypertensive',
            'current_stock' => 100,
            'reorder_level' => 25,
            'expiry_date' => '2027-03-22',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '12',
            'medicine_name' => 'Metformin',
            'generic_name' => 'Glucophage',
            'category' => 'Antidiabetic',
            'current_stock' => 200,
            'reorder_level' => 50,
            'expiry_date' => '2026-11-30',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '13',
            'medicine_name' => 'Carbocisteine',
            'generic_name' => 'Solmux',
            'category' => 'Mucolytic',
            'current_stock' => 150,
            'reorder_level' => 30,
            'expiry_date' => '2026-12-15',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '14',
            'medicine_name' => 'Guaifenesin',
            'generic_name' => 'Robitussin',
            'category' => 'Expectorant',
            'current_stock' => 40,
            'reorder_level' => 10,
            'expiry_date' => '2026-07-20',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '15',
            'medicine_name' => 'Neomycin + Bacitracin',
            'generic_name' => 'Poly-C',
            'category' => 'Antibacterial',
            'current_stock' => 60,
            'reorder_level' => 15,
            'expiry_date' => '2026-10-10',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '16',
            'medicine_name' => 'Multivitamins',
            'generic_name' => 'Enervon',
            'category' => 'Vitamin',
            'current_stock' => 300,
            'reorder_level' => 50,
            'expiry_date' => '2027-05-15',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '17',
            'medicine_name' => 'Calcium Carbonate',
            'generic_name' => 'Caltrate',
            'category' => 'Supplement',
            'current_stock' => 100,
            'reorder_level' => 20,
            'expiry_date' => '2027-02-28',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '18',
            'medicine_name' => 'Oral Rehydration Salts',
            'generic_name' => 'Hydrite',
            'category' => 'Electrolyte',
            'current_stock' => 100,
            'reorder_level' => 25,
            'expiry_date' => '2026-12-31',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '19',
            'medicine_name' => 'Phenylpropanolamine',
            'generic_name' => 'Decolgen',
            'category' => 'Decongestant',
            'current_stock' => 200,
            'reorder_level' => 40,
            'expiry_date' => '2026-11-30',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '20',
            'medicine_name' => 'Omeprazole',
            'generic_name' => 'Prilosec',
            'category' => 'Antacid',
            'current_stock' => 120,
            'reorder_level' => 30,
            'expiry_date' => '2027-01-15',
            'created_by' => 'Admin'
        ]
    ];

    $sql = "INSERT INTO cm_medicine_inventory 
            (medicine_id, medicine_name, generic_name, category, current_stock, reorder_level, expiry_date, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            medicine_name = VALUES(medicine_name),
            generic_name = VALUES(generic_name),
            category = VALUES(category),
            current_stock = VALUES(current_stock),
            reorder_level = VALUES(reorder_level),
            expiry_date = VALUES(expiry_date)";

    $stmt = $db->prepare($sql);

    foreach ($medicines as $med) {
        $stmt->execute([
            $med['medicine_id'],
            $med['medicine_name'],
            $med['generic_name'],
            $med['category'],
            $med['current_stock'],
            $med['reorder_level'],
            $med['expiry_date'],
            $med['created_by']
        ]);
        echo "Added: {$med['medicine_name']} (ID: {$med['medicine_id']})\n";
    }

    echo "Done! Dummy medicines added successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
