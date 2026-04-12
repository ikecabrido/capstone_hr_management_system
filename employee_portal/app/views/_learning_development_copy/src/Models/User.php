<?php

namespace HRManagement\Models;

/**
 * User Model
 */
class User extends BaseModel
{
    private string $username = '';
    private string $email = '';
    private string $password = '';
    private string $fullName = '';
    private string $role = 'employee';
    private string $department = '';
    private string $position = '';
    private ?int $managerId = null;
    private string $status = 'active';
    private string $theme = 'light';
    private ?string $lastLogin = null;
    private ?string $profilePic = null;

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getFullName(): string { return $this->fullName; }
    public function setFullName(string $fullName): self { $this->fullName = $fullName; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }

    public function getDepartment(): string { return $this->department; }
    public function setDepartment(string $department): self { $this->department = $department; return $this; }

    public function getPosition(): string { return $this->position; }
    public function setPosition(string $position): self { $this->position = $position; return $this; }

    public function getManagerId(): ?int { return $this->managerId; }
    public function setManagerId(?int $managerId): self { $this->managerId = $managerId; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getTheme(): string { return $this->theme; }
    public function setTheme(string $theme): self { $this->theme = $theme; return $this; }

    public function getLastLogin(): ?string { return $this->lastLogin; }
    public function setLastLogin(?string $lastLogin): self { $this->lastLogin = $lastLogin; return $this; }

    public function getProfilePic(): ?string { return $this->profilePic; }
    public function setProfilePic(?string $profilePic): self { $this->profilePic = $profilePic; return $this; }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function isLearningAdmin(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'learning']);
    }
}
