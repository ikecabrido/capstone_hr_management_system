<?php
namespace App\Controllers;

use App\Models\Grievance;
use App\Models\GrievanceUpdate;

class GrievanceController
{
    private $grievance;
    private $update;

    public function __construct()
    {
        $this->grievance = new Grievance();
        $this->update = new GrievanceUpdate();
    }

    public function getGrievances()
    {
        return $this->grievance->getGrievances();
    }

    public function fileGrievance($employee_id, $subject, $description)
    {
        return $this->grievance->fileGrievance($employee_id, $subject, $description);
    }

    public function updateStatus($id, $status)
    {
        return $this->grievance->updateStatus($id, $status);
    }

    public function addUpdate($grievance_id, $update_text, $updated_by)
    {
        return $this->update->create(['grievance_id' => $grievance_id, 'update_text' => $update_text, 'updated_by' => $updated_by]);
    }

    public function history($id)
    {
        return $this->update->getByGrievance($id);
    }
}
