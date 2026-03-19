<?php

require_once __DIR__ . '/../models/legalComplianceModel.php';

class LegalComplianceController
{
    private LegalComplianceModel $model;

    public function __construct($db)
    {
        $this->model = new LegalComplianceModel($db);
    }

    public function getStats()
    {
        return $this->model->getStats();
    }

    public function getComplianceChecks($employeeId = null)
    {
        return $this->model->getComplianceChecks($employeeId);
    }

    public function updateComplianceStatus($employeeId, $lawType, $status, $remarks = '')
    {
        return $this->model->updateComplianceStatus($employeeId, $lawType, $status, $remarks);
    }

    public function getPolicies()
    {
        return $this->model->getPolicies();
    }

    public function getPolicyById($id)
    {
        return $this->model->getPolicyById($id);
    }

    public function addPolicy($data)
    {
        return $this->model->addPolicy($data);
    }

    public function acknowledgePolicy($employeeId, $policyId)
    {
        return $this->model->acknowledgePolicy($employeeId, $policyId);
    }

    public function getPolicyAcknowledgments($policyId)
    {
        return $this->model->getPolicyAcknowledgments($policyId);
    }

    public function getIncidents($status = null)
    {
        return $this->model->getIncidents($status);
    }

    public function addIncident($data)
    {
        return $this->model->addIncident($data);
    }

    public function updateIncidentStatus($id, $status)
    {
        return $this->model->updateIncidentStatus($id, $status);
    }

    public function getRisks($status = null)
    {
        return $this->model->getRisks($status);
    }

    public function addRisk($data)
    {
        return $this->model->addRisk($data);
    }

    public function updateRiskStatus($id, $status)
    {
        return $this->model->updateRiskStatus($id, $status);
    }

    public function getAuditLogs($limit = 50)
    {
        return $this->model->getAuditLogs($limit);
    }

    public function logAction($userId, $action, $module, $details = '')
    {
        return $this->model->logAction($userId, $action, $module, $details);
    }

    public function getPhilippineLaws()
    {
        return $this->model->getPhilippineLaws();
    }

    public function getComplianceByLaw($lawType)
    {
        return $this->model->getComplianceByLaw($lawType);
    }

    public function getHighRiskEmployees()
    {
        return $this->model->getHighRiskEmployees();
    }

    public function getPendingAcknowledgments()
    {
        return $this->model->getPendingAcknowledgments();
    }

    // New Methods for Enhanced Compliance System
    
    public function getCategories()
    {
        return $this->model->getCategories();
    }

    public function getRules($categoryId = null)
    {
        return $this->model->getRules($categoryId);
    }

    public function getComplianceSummary($employeeId = null)
    {
        return $this->model->getComplianceSummary($employeeId);
    }

    public function getRiskFlags($resolved = false)
    {
        return $this->model->getRiskFlags($resolved);
    }

    public function getRiskFlagById($id)
    {
        return $this->model->getRiskFlagById($id);
    }

    public function resolveRiskFlag($id)
    {
        return $this->model->resolveRiskFlag($id);
    }

    public function escalateRiskFlag($id, $notes)
    {
        return $this->model->escalateRiskFlag($id, $notes);
    }

    public function getEmployeeDetailedCompliance($employeeId)
    {
        return $this->model->getEmployeeDetailedCompliance($employeeId);
    }

    public function sendReminder($employeeId, $message, $subject = null)
    {
        return $this->model->sendReminder($employeeId, $message, $subject);
    }

    public function getDashboardStats()
    {
        return $this->model->getDashboardStats();
    }

    public function runComplianceCheck($employeeId = null)
    {
        return $this->model->runComplianceCheck($employeeId);
    }

    public function getEmployeesWithScores()
    {
        return $this->model->getEmployeesWithScores();
    }

    public function getLawById($id)
    {
        return $this->model->getLawById($id);
    }

    public function getEmployeeComplianceDetails($employeeId)
    {
        return $this->model->getEmployeeComplianceDetails($employeeId);
    }

    public function getLawsWithStats()
    {
        return $this->model->getLawsWithStats();
    }
}
