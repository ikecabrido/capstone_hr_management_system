<?php

namespace HRManagement\Models;

/**
 * Individual Development Plan (IDP) Model
 */
class IndividualDevelopmentPlan extends BaseModel
{
    private int $userId = 0;
    private ?int $careerPathId = null;
    private ?string $startDate = null;
    private ?string $endDate = null;
    private string $objectives = '';
    private array $milestones = [];
    private string $status = 'active';
    private int $createdBy = 0;

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): self { $this->userId = $userId; return $this; }

    public function getCareerPathId(): ?int { return $this->careerPathId; }
    public function setCareerPathId(?int $pathId): self { $this->careerPathId = $pathId; return $this; }

    public function getStartDate(): ?string { return $this->startDate; }
    public function setStartDate(?string $date): self { $this->startDate = $date; return $this; }

    public function getEndDate(): ?string { return $this->endDate; }
    public function setEndDate(?string $date): self { $this->endDate = $date; return $this; }

    public function getObjectives(): string { return $this->objectives; }
    public function setObjectives(string $objectives): self { $this->objectives = $objectives; return $this; }

    public function getMilestones(): array { return $this->milestones; }
    public function setMilestones($milestones): self 
    { 
        if (is_string($milestones)) {
            $this->milestones = json_decode($milestones, true) ?? [];
        } elseif (is_array($milestones)) {
            $this->milestones = $milestones;
        }
        return $this; 
    }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): self { $this->createdBy = $createdBy; return $this; }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
