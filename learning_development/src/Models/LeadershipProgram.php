<?php

namespace HRManagement\Models;

/**
 * Leadership Program Model
 */
class LeadershipProgram extends BaseModel
{
    private string $name = '';
    private string $description = '';
    private string $level = '';
    private string $focusArea = '';
    private int $durationWeeks = 0;
    private string $targetAudience = '';
    private array $outcomes = [];
    private int $createdBy = 0;
    private string $status = 'active';

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getLevel(): string { return $this->level; }
    public function setLevel(string $level): self { $this->level = $level; return $this; }

    public function getFocusArea(): string { return $this->focusArea; }
    public function setFocusArea(string $focusArea): self { $this->focusArea = $focusArea; return $this; }

    public function getDurationWeeks(): int { return $this->durationWeeks; }
    public function setDurationWeeks(int $weeks): self { $this->durationWeeks = $weeks; return $this; }

    public function getTargetAudience(): string { return $this->targetAudience; }
    public function setTargetAudience(string $audience): self { $this->targetAudience = $audience; return $this; }

    public function getOutcomes(): array { return $this->outcomes; }
    public function setOutcomes($outcomes): self 
    { 
        if (is_string($outcomes)) {
            $this->outcomes = json_decode($outcomes, true) ?? [];
        } elseif (is_array($outcomes)) {
            $this->outcomes = $outcomes;
        }
        return $this; 
    }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): self { $this->createdBy = $createdBy; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
