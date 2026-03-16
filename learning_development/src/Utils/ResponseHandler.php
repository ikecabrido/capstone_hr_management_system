<?php

namespace HRManagement\Utils;

/**
 * Response Handler
 * 
 * Standardized API response format
 */
class ResponseHandler
{
    private int $statusCode = 200;
    private string $message = '';
    private $data = null;
    private array $errors = [];

    /**
     * Success response
     */
    public static function success($data = null, string $message = 'Success'): self
    {
        $response = new self();
        $response->statusCode = 200;
        $response->message = $message;
        $response->data = $data;
        return $response;
    }

    /**
     * Created response
     */
    public static function created($data = null, string $message = 'Resource created'): self
    {
        $response = new self();
        $response->statusCode = 201;
        $response->message = $message;
        $response->data = $data;
        return $response;
    }

    /**
     * Error response
     */
    public static function error(string $message = 'An error occurred', int $status = 400): self
    {
        $response = new self();
        $response->statusCode = $status;
        $response->message = $message;
        return $response;
    }

    /**
     * Validation error response
     */
    public static function validationError(array $errors = []): self
    {
        $response = new self();
        $response->statusCode = 422;
        $response->message = 'Validation failed';
        $response->errors = $errors;
        return $response;
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return self::error($message, 404);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    /**
     * Set custom data
     */
    public function withData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set errors
     */
    public function withErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get response as array
     */
    public function toArray(): array
    {
        $response = [
            'success' => $this->statusCode >= 200 && $this->statusCode < 300,
            'status' => $this->statusCode,
            'message' => $this->message,
        ];

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    /**
     * Get response as JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Send JSON response
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo $this->toJson();
        exit;
    }

    /**
     * Get status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Check if response is successful
     */
    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
