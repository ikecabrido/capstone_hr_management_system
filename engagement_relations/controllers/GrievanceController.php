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

    public function fileGrievance($employee_id, $subject, $description, $category = 'Workplace Conflict', $anonymous = 0, $attachment_path = null)
    {
        try {
            return $this->grievance->fileGrievance($employee_id, $subject, $description, $category, $anonymous, $attachment_path);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateStatus($id, $status)
    {
        return $this->grievance->updateStatus($id, $status);
    }

    public function assignTo($id, $assigned_to)
    {
        return $this->grievance->assignTo($id, $assigned_to);
    }

    public function updateResolution($id, $resolution_notes, $action_taken)
    {
        return $this->grievance->updateResolution($id, $resolution_notes, $action_taken);
    }

    public function submitSatisfaction($id, $rating, $comment)
    {
        return $this->grievance->submitSatisfaction($id, $rating, $comment);
    }

    public function addUpdate($grievance_id, $update_text, $updated_by)
    {
        return $this->update->create(['grievance_id' => $grievance_id, 'update_text' => $update_text, 'updated_by' => $updated_by]);
    }

    public function history($id)
    {
        return $this->update->getByGrievance($id);
    }

    public function getGrievanceReport($startDate, $endDate)
    {
        return $this->grievance->generateReport($startDate, $endDate);
    }

    public function addInvestigationNotes($id, $notes, $hrPersonnelId)
    {
        return $this->grievance->addInvestigationNotes($id, $notes, $hrPersonnelId);
    }

    public function markConfidential($id, $isConfidential)
    {
        return $this->grievance->updateConfidentialFlag($id, $isConfidential);
    }
}
