<?php
require_once __DIR__ . "/../models/Certification.php";

class CertificationController {
    private $model;

    public function __construct() {
        $this->model = new Certification();
    }

    public function index() {
        $certifications = $this->model->getAllCertifications();
        return $certifications;
    }

    public function getByEmployee($employeeId) {
        return $this->model->getCertificationsByEmployee($employeeId);
    }

    public function issue($data) {
        if ($this->model->issueCertification($data)) {
            return ['success' => true, 'message' => 'Certification issued successfully'];
        }
        return ['success' => false, 'message' => 'Failed to issue certification'];
    }

    public function revoke($id) {
        if ($this->model->revokeCertification($id)) {
            return ['success' => true, 'message' => 'Certification revoked successfully'];
        }
        return ['success' => false, 'message' => 'Failed to revoke certification'];
    }

    public function show($id) {
        return $this->model->getCertificationById($id);
    }

    public function getCertificationById($id) {
        return $this->model->getCertificationById($id);
    }

    public function update($data) {
        if ($this->model->updateCertification($data)) {
            return ['success' => true, 'message' => 'Certification updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update certification'];
    }
}
?>