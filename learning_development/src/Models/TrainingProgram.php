<?php

namespace HRManagement\Models;

/**
 * Training Program Model
 */
class TrainingProgram extends BaseModel
{
    private string $name = '';
    private string $description = '';
    private string $category = 'General';
    private string $type = '';
    private int $duration = 0;
    private int $createdBy = 0;
    private string $status = 'active';

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getCategory(): string { return $this->category; }
    public function setCategory(string $category): self { $this->category = $category; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getDuration(): int { return $this->duration; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): self { $this->createdBy = $createdBy; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
