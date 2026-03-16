<?php

namespace HRManagement\Models;

/**
 * Career Path Model
 */
class CareerPath extends BaseModel
{
    private string $name = '';
    private string $description = '';
    private string $targetPosition = '';
    private string $prerequisites = '';
    private array $skillsRequired = [];
    private int $durationMonths = 12;
    private string $status = 'active';
    private int $createdBy = 0;

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getTargetPosition(): string { return $this->targetPosition; }
    public function setTargetPosition(string $position): self { $this->targetPosition = $position; return $this; }

    public function getPrerequisites(): string { return $this->prerequisites; }
    public function setPrerequisites(string $prerequisites): self { $this->prerequisites = $prerequisites; return $this; }

    public function getSkillsRequired(): array { return $this->skillsRequired; }
    public function setSkillsRequired($skills): self 
    { 
        if (is_string($skills)) {
            $this->skillsRequired = json_decode($skills, true) ?? [];
        } elseif (is_array($skills)) {
            $this->skillsRequired = $skills;
        }
        return $this; 
    }

    public function getDurationMonths(): int { return $this->durationMonths; }
    public function setDurationMonths(int $months): self { $this->durationMonths = $months; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): self { $this->createdBy = $createdBy; return $this; }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
