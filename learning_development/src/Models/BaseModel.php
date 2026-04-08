<?php

namespace HRManagement\Models;

use DateTime;

/**
 * Base Model Class
 * 
 * Provides common properties and methods for all entity models
 */
abstract class BaseModel
{
    protected int $id;
    protected DateTime $createdAt;
    protected DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt ?? new DateTime();
    }

    public function setCreatedAt($date): self
    {
        if (is_string($date)) {
            $this->createdAt = new DateTime($date);
        } elseif ($date instanceof DateTime) {
            $this->createdAt = $date;
        }
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt ?? new DateTime();
    }

    public function setUpdatedAt($date): self
    {
        if (is_string($date)) {
            $this->updatedAt = new DateTime($date);
        } elseif ($date instanceof DateTime) {
            $this->updatedAt = $date;
        }
        return $this;
    }

    /**
     * Convert model to array
     */
    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionClass($this);
        
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($this);
            
            if ($value instanceof DateTime) {
                $data[$name] = $value->format('Y-m-d H:i:s');
            } else {
                $data[$name] = $value;
            }
        }
        
        return $data;
    }

    /**
     * Populate model from array
     */
    public function fromArray(array $data): self
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }
}
