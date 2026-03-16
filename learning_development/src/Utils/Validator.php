<?php

namespace HRManagement\Utils;

/**
 * Validator
 * 
 * Validation utilities for forms and data
 */
class Validator
{
    private array $errors = [];

    /**
     * Validate email format
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public static function isValidPassword(string $password): bool
    {
        return strlen($password) >= 8;
    }

    /**
     * Validate username format
     */
    public static function isValidUsername(string $username): bool
    {
        return preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username) === 1;
    }

    /**
     * Validate date format
     */
    public static function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $dateObj = \DateTime::createFromFormat($format, $date);
        return $dateObj && $dateObj->format($format) === $date;
    }

    /**
     * Validate integer
     */
    public static function isValidInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate float
     */
    public static function isValidFloat($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Validate URL
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Instance method: validate registration data
     */
    public function validateRegistration(array $data): bool
    {
        $this->errors = [];

        if (empty($data['username'])) {
            $this->errors['username'] = 'Username is required';
        } elseif (!self::isValidUsername($data['username'])) {
            $this->errors['username'] = 'Username must be 3-20 characters, alphanumeric with - or _';
        }

        if (empty($data['email'])) {
            $this->errors['email'] = 'Email is required';
        } elseif (!self::isValidEmail($data['email'])) {
            $this->errors['email'] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $this->errors['password'] = 'Password is required';
        } elseif (!self::isValidPassword($data['password'])) {
            $this->errors['password'] = 'Password must be at least 8 characters';
        }

        if (($data['password_confirm'] ?? '') !== ($data['password'] ?? '')) {
            $this->errors['password_confirm'] = 'Passwords do not match';
        }

        if (empty($data['full_name'])) {
            $this->errors['full_name'] = 'Full name is required';
        }

        return empty($this->errors);
    }

    /**
     * Validate training program data
     */
    public function validateTrainingProgram(array $data): bool
    {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = 'Program name is required';
        }

        if (empty($data['category'])) {
            $this->errors['category'] = 'Category is required';
        }

        if (isset($data['duration']) && !self::isValidInteger($data['duration'])) {
            $this->errors['duration'] = 'Duration must be a valid number';
        }

        return empty($this->errors);
    }

    /**
     * Validate performance review data
     */
    public function validatePerformanceReview(array $data): bool
    {
        $this->errors = [];

        if (empty($data['employee_id'])) {
            $this->errors['employee_id'] = 'Employee is required';
        }

        if (empty($data['review_period_start'])) {
            $this->errors['review_period_start'] = 'Review period start is required';
        } elseif (!self::isValidDate($data['review_period_start'])) {
            $this->errors['review_period_start'] = 'Invalid date format';
        }

        if (empty($data['review_period_end'])) {
            $this->errors['review_period_end'] = 'Review period end is required';
        } elseif (!self::isValidDate($data['review_period_end'])) {
            $this->errors['review_period_end'] = 'Invalid date format';
        }

        if (isset($data['rating']) && $data['rating'] !== '' && !self::isValidFloat($data['rating'])) {
            $this->errors['rating'] = 'Rating must be a valid number';
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }

    /**
     * Check if there are errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
