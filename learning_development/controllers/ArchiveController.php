<?php
require_once __DIR__ . '/../models/Archive.php';

class ArchiveController {
    private $archiveModel;

    public function __construct() {
        $this->archiveModel = new Archive();
    }

    /**
     * Get all archives
     */
    public function index() {
        return $this->archiveModel->getAllArchives();
    }

    /**
     * Get archives by type (course or program)
     */
    public function getByType($type) {
        return $this->archiveModel->getArchivesByType($type);
    }

    /**
     * Archive a course
     */
    public function archiveCourse($courseId, $userId) {
        return $this->archiveModel->archiveItem($courseId, 'course', $userId);
    }

    /**
     * Archive a training program
     */
    public function archiveProgram($programId, $userId) {
        return $this->archiveModel->archiveItem($programId, 'program', $userId);
    }

    /**
     * Archive a certification
     */
    public function archiveCertification($certificationId, $userId) {
        return $this->archiveModel->archiveItem($certificationId, 'certification', $userId);
    }

    /**
     * Restore an archived item
     */
    public function restore($archiveId, $archivedBy) {
        return $this->archiveModel->restoreArchive($archiveId, $archivedBy);
    }

    /**
     * Get archive by ID
     */
    public function show($archiveId) {
        return $this->archiveModel->getArchiveById($archiveId);
    }

    /**
     * Delete an archive permanently
     */
    public function delete($archiveId) {
        return $this->archiveModel->deleteArchive($archiveId);
    }

    /**
     * Get archives for a specific user
     */
    public function getByCreator($userId) {
        return $this->archiveModel->getArchivesByCreator($userId);
    }
}
?>
