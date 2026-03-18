<?php
/**
 * Form validation utilities
 */

/**
 * Validate required field
 */
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return "$fieldName is required.";
    }
    return null;
}

/**
 * Validate email
 */
function validateEmail($value) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }
    return null;
}

/**
 * Validate URL
 */
function validateUrl($value) {
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return "Please enter a valid URL.";
    }
    return null;
}

/**
 * Validate date format (Y-m-d)
 */
function validateDate($value, $fieldName = 'Date') {
    $date = DateTime::createFromFormat('Y-m-d', $value);
    if (!$date) {
        return "$fieldName must be in YYYY-MM-DD format.";
    }
    return null;
}

/**
 * Validate integer
 */
function validateInteger($value, $fieldName = 'Value', $min = null, $max = null) {
    if (!is_numeric($value) || intval($value) != $value) {
        return "$fieldName must be an integer.";
    }
    if ($min !== null && intval($value) < $min) {
        return "$fieldName must be at least $min.";
    }
    if ($max !== null && intval($value) > $max) {
        return "$fieldName must be no more than $max.";
    }
    return null;
}

/**
 * Validate min length
 */
function validateMinLength($value, $length, $fieldName = 'Field') {
    if (strlen(trim($value)) < $length) {
        return "$fieldName must be at least $length characters.";
    }
    return null;
}

/**
 * Validate max length
 */
function validateMaxLength($value, $length, $fieldName = 'Field') {
    if (strlen(trim($value)) > $length) {
        return "$fieldName must not exceed $length characters.";
    }
    return null;
}

/**
 * Run multiple validations
 * @param array $rules - ['fieldName' => [validation1, validation2, ...]]
 * @return array Errors ['fieldName' => 'error message']
 */
function validateForm($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $fieldRules) {
        $value = $data[$field] ?? '';
        
        foreach ($fieldRules as $rule) {
            $error = null;
            
            if (is_callable($rule)) {
                $error = $rule($value);
            }
            
            if ($error) {
                $errors[$field] = $error;
                break; // Stop at first error
            }
        }
    }
    
    return $errors;
}

/**
 * Sanitize string input
 */
function sanitizeString($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize integer input
 */
function sanitizeInteger($value) {
    return intval($value);
}

/**
 * Sanitize array of values
 */
function sanitizeArray($values) {
    return array_map(function($v) {
        return sanitizeString($v);
    }, (array)$values);
}
?>
