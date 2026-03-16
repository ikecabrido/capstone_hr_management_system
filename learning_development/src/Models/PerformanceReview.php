<?php

namespace HRManagement\Models;

/**
 * Performance Review Model
 */
class PerformanceReview extends BaseModel
{
    private int $employeeId = 0;
    private int $reviewerId = 0;
    private ?string $reviewPeriodStart = null;
    private ?string $reviewPeriodEnd = null;
    private ?float $rating = null;
    private string $comments = '';
    private ?string $reviewedDate = null;
    private string $status = 'draft';

    public function getEmployeeId(): int { return $this->employeeId; }
    public function setEmployeeId(int $id): self { $this->employeeId = $id; return $this; }

    public function getReviewerId(): int { return $this->reviewerId; }
    public function setReviewerId(int $id): self { $this->reviewerId = $id; return $this; }

    public function getReviewPeriodStart(): ?string { return $this->reviewPeriodStart; }
    public function setReviewPeriodStart(?string $date): self { $this->reviewPeriodStart = $date; return $this; }

    public function getReviewPeriodEnd(): ?string { return $this->reviewPeriodEnd; }
    public function setReviewPeriodEnd(?string $date): self { $this->reviewPeriodEnd = $date; return $this; }

    public function getRating(): ?float { return $this->rating; }
    public function setRating(?float $rating): self { $this->rating = $rating; return $this; }

    public function getComments(): string { return $this->comments; }
    public function setComments(string $comments): self { $this->comments = $comments; return $this; }

    public function getReviewedDate(): ?string { return $this->reviewedDate; }
    public function setReviewedDate(?string $date): self { $this->reviewedDate = $date; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
