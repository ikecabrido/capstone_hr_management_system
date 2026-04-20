<?php
require_once __DIR__ . '/../models/EnrollmentProgram.php';

class EnrollmentProgramController
{
    private $enrollmentModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentProgram();
    }

    public function enroll()
    {
        session_start();

        try {
            $userId = $_SESSION['user_id'] ?? 0;
            $programId = intval($_POST['id'] ?? 0);

            if (!$userId || !$programId) {
                throw new Exception("Invalid enrollment request.");
            }

            $result = $this->enrollmentModel->enroll($userId, $programId);

            $_SESSION[$result['status'] ? 'success' : 'error'] = $result['message'];
        } catch (Exception $e) {
            $_SESSION['error'] = "Enrollment failed: " . $e->getMessage();
        }

        header("Location: index.php?url=training-program-index");
        exit;
    }
    public function unenroll()
    {
        session_start();

        try {
            $userId = $_SESSION['user_id'] ?? 0;
            $programId = intval($_POST['id'] ?? 0);

            if (!$userId || !$programId) {
                throw new Exception("Invalid unenrollment request.");
            }

            $result = $this->enrollmentModel->unenroll($userId, $programId);

            $_SESSION[$result['status'] ? 'success' : 'error'] = $result['message'];
        } catch (Exception $e) {
            $_SESSION['error'] = "Unenrollment failed: " . $e->getMessage();
        }

        header("Location: index.php?url=training-program-index");
        exit;
    }
}
