<?php

class Validator
{
    public static function validate($data, $rules)
    {
        if (!is_array($data)) {
            throw new Exception('Invalid data payload.');
        }

        foreach ($rules as $field => $rule) {
            $parts = explode('|', $rule);

            foreach ($parts as $part) {
                if ($part === 'required') {
                    if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                        throw new Exception("Validation failed: $field is required.");
                    }
                } elseif (strpos($part, 'min:') === 0) {
                    $len = (int) substr($part, strlen('min:'));
                    if (isset($data[$field]) && strlen((string) $data[$field]) < $len) {
                        throw new Exception("Validation failed: $field must be at least $len characters.");
                    }
                } elseif (strpos($part, 'max:') === 0) {
                    $len = (int) substr($part, strlen('max:'));
                    if (isset($data[$field]) && strlen((string) $data[$field]) > $len) {
                        throw new Exception("Validation failed: $field must be at most $len characters.");
                    }
                } elseif (strpos($part, 'enum:') === 0) {
                    $options = explode(',', substr($part, strlen('enum:')));
                    if (isset($data[$field]) && !in_array($data[$field], $options)) {
                        throw new Exception("Validation failed: $field must be one of " . implode(', ', $options));
                    }
                }
            }
        }

        return true;
    }
}
