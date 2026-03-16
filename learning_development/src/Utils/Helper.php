<?php

namespace HRManagement\Utils;

/**
 * Helper Functions
 * 
 * Common utility functions
 */
class Helper
{
    /**
     * Sanitize string input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize array
     */
    public static function sanitizeArray(array $input): array
    {
        return array_map(function($value) {
            if (is_array($value)) {
                return self::sanitizeArray($value);
            }
            return is_string($value) ? self::sanitize($value) : $value;
        }, $input);
    }

    /**
     * Format date
     */
    public static function formatDate(string $date, string $format = 'M d, Y'): string
    {
        try {
            $dateObj = new \DateTime($date);
            return $dateObj->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Format datetime
     */
    public static function formatDateTime(string $datetime, string $format = 'M d, Y H:i'): string
    {
        try {
            $dateObj = new \DateTime($datetime);
            return $dateObj->format($format);
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    /**
     * Get time ago string
     */
    public static function timeAgo(string $datetime): string
    {
        try {
            $dateObj = new \DateTime($datetime);
            $now = new \DateTime();
            $interval = $now->diff($dateObj);

            if ($interval->y > 0) {
                return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
            } elseif ($interval->m > 0) {
                return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
            } elseif ($interval->d > 0) {
                return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
            } elseif ($interval->h > 0) {
                return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
            } elseif ($interval->i > 0) {
                return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
            } else {
                return 'just now';
            }
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    /**
     * Generate random string
     */
    public static function randomString(int $length = 16): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Truncate string
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Convert percentage to status badge
     */
    public static function getProgressBadge(int $percentage): string
    {
        if ($percentage === 100) {
            return '<span class="badge badge-success">Completed</span>';
        } elseif ($percentage >= 75) {
            return '<span class="badge badge-info">In Progress</span>';
        } elseif ($percentage >= 50) {
            return '<span class="badge badge-warning">In Progress</span>';
        } else {
            return '<span class="badge badge-secondary">Started</span>';
        }
    }

    /**
     * Get status color
     */
    public static function getStatusColor(string $status): string
    {
        $colors = [
            'active' => 'success',
            'draft' => 'secondary',
            'completed' => 'success',
            'pending' => 'warning',
            'in_progress' => 'info',
            'inactive' => 'danger',
            'dropped' => 'danger',
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Convert string to slug
     */
    public static function toSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = preg_replace('~-+~', '-', $text);
        $text = trim($text, '-');
        return $text;
    }

    /**
     * Check if value is empty
     */
    public static function isEmpty($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_array($value)) {
            return empty($value);
        }
        return false;
    }

    /**
     * Get first value that is not empty
     */
    public static function coalesce(...$values): mixed
    {
        foreach ($values as $value) {
            if (!self::isEmpty($value)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get average of array values
     */
    public static function average(array $values): float
    {
        if (empty($values)) {
            return 0;
        }
        return array_sum($values) / count($values);
    }

    /**
     * Group array by key
     */
    public static function groupBy(array $data, string $key): array
    {
        $grouped = [];
        foreach ($data as $item) {
            $groupKey = $item[$key] ?? null;
            if ($groupKey !== null) {
                $grouped[$groupKey][] = $item;
            }
        }
        return $grouped;
    }
}
