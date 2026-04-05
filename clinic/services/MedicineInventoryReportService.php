<?php
require_once __DIR__ . '/../models/MedicineInventory.php';

class MedicineInventoryReportService {
    private $db;
    private $medicine_inventory;

    public function __construct($db) {
        $this->db = $db;
        $this->medicine_inventory = new MedicineInventory($this->db);
    }

    public function generateReport() {
        return $this->medicine_inventory->getAllMedicines();
    }
}
?>