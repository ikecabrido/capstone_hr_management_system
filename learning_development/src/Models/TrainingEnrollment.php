<?php

namespace HRManagement\Models;

/**
 * Training Enrollment Model
 */
class TrainingEnrollment extends BaseModel
{
    private int $userId = 0;
    private int $programId = 0;
    private ?string $enrollmentDate = null;
    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?string $completionDate = null;
    private string $status = 'pending';
    private int $progressPercentage = 0;
    private ?float $score = null;
    private bool $certificateIssued = false;

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): self { $this->userId = $userId; return $this; }

    public function getProgramId(): int { return $this->programId; }
    public function setProgramId(int $programId): self { $this->programId = $programId; return $this; }

    public function getEnrollmentDate(): ?string { return $this->enrollmentDate; }
    public function setEnrollmentDate(?string $date): self { $this->enrollmentDate = $date; return $this; }

    public function getStartDate(): ?string { return $this->startDate; }
    public function setStartDate(?string $date): self { $this->startDate = $date; return $this; }

    public function getEndDate(): ?string { return $this->endDate; }
    public function setEndDate(?string $date): self { $this->endDate = $date; return $this; }

    public function getCompletionDate(): ?string { return $this->completionDate; }
    public function setCompletionDate(?string $date): self { $this->completionDate = $date; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getProgressPercentage(): int { return $this->progressPercentage; }
    public function setProgressPercentage(int $percentage): self { $this->progressPercentage = $percentage; return $this; }

    public function getScore(): ?float { return $this->score; }
    public function setScore(?float $score): self { $this->score = $score; return $this; }

    public function isCertificateIssued(): bool { return $this->certificateIssued; }
    public function setCertificateIssued(bool $issued): self { $this->certificateIssued = $issued; return $this; }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
