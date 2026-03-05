<?php

namespace BDCConecta\Http;

use BDCConecta\ErrorCodes;

class Response
{
    private ?array $jsonCache = null;

    public function __construct(
        private readonly int $httpStatusCode,
        private readonly array $headers,
        private readonly string $body
    ) {}

    public function getHttpStatusCode(): int { return $this->httpStatusCode; }
    public function getHeaders(): array { return $this->headers; }
    public function getBody(): string { return $this->body; }

    public function json(): array
    {
        if ($this->jsonCache === null) {
            $this->jsonCache = json_decode($this->body, true) ?? [];
        }
        return $this->jsonCache;
    }

    /**
     * Get the API status code from response body (0 = success, other = error)
     *
     * Note: Sometimes the API returns statusCode as a string instead of int.
     * In those cases, we return null and treat it as a system error.
     */
    public function getApiStatusCode(): ?int
    {
        $data = $this->json();

        if (!isset($data['statusCode'])) {
            return null;
        }

        $statusCode = $data['statusCode'];

        // If it's already an int, return it
        if (is_int($statusCode)) {
            return $statusCode;
        }

        // If it's a numeric string, convert to int
        if (is_string($statusCode) && is_numeric($statusCode)) {
            return (int) $statusCode;
        }

        // If it's a non-numeric string, it's a system error message
        // Return null to indicate this is not a standard error code
        return null;
    }

    /**
     * Check if the HTTP status code is successful (200-299)
     */
    public function isHttpSuccessful(): bool
    {
        return $this->httpStatusCode >= 200 && $this->httpStatusCode < 300;
    }

    /**
     * Check if the API status code indicates success (statusCode = 0)
     */
    public function isApiSuccessful(): bool
    {
        $apiStatusCode = $this->getApiStatusCode();
        return $apiStatusCode !== null && ErrorCodes::isSuccess($apiStatusCode);
    }

    /**
     * Check if response is fully successful (both HTTP and API status)
     */
    public function isSuccessful(): bool
    {
        return $this->isHttpSuccessful() && $this->isApiSuccessful();
    }

    public function isClientError(): bool
    {
        return $this->httpStatusCode >= 400 && $this->httpStatusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->httpStatusCode >= 500 && $this->httpStatusCode < 600;
    }

    /**
     * Get the error message from the API
     */
    public function getErrorMessage(): ?string
    {
        $data = $this->json();

        // If statusCode is 0, no error
        if (isset($data['statusCode']) && $data['statusCode'] === 0) {
            return null;
        }

        // If statusCode is a string (system error), use it as the message
        if (isset($data['statusCode']) && is_string($data['statusCode']) && !is_numeric($data['statusCode'])) {
            return $data['statusCode'];
        }

        // Try to get message from response
        if (isset($data['message'])) {
            // Message can be string or array
            if (is_array($data['message'])) {
                return implode(', ', $data['message']);
            }
            return $data['message'];
        }

        // Fallback to error code message
        $apiStatusCode = $this->getApiStatusCode();
        if ($apiStatusCode !== null) {
            return ErrorCodes::getMessage($apiStatusCode);
        }

        return 'Unknown error';
    }
}
