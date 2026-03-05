<?php

namespace BDCConecta\Exceptions;

use BDCConecta\Http\Response;
use BDCConecta\ErrorCodes;

class ApiException extends BDCConectaException
{
    public function __construct(
        string $message,
        private readonly int $httpStatusCode,
        private readonly ?int $apiStatusCode = null,
        private readonly ?array $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $apiStatusCode ?? $httpStatusCode,
            context: [
                'httpStatusCode' => $httpStatusCode,
                'apiStatusCode' => $apiStatusCode,
                'responseBody' => $responseBody
            ],
            previous: $previous
        );
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $apiStatusCode = $response->getApiStatusCode();

        // Get error message from response or use error code mapping
        $message = $response->getErrorMessage() ?? 'API request failed';

        return new self(
            message: $message,
            httpStatusCode: $response->getHttpStatusCode(),
            apiStatusCode: $apiStatusCode,
            responseBody: $body
        );
    }

    public function getHttpStatusCode(): int { return $this->httpStatusCode; }
    public function getApiStatusCode(): ?int { return $this->apiStatusCode; }
    public function getResponseBody(): ?array { return $this->responseBody; }

    /**
     * Check if this is an authentication error
     */
    public function isAuthError(): bool
    {
        return $this->apiStatusCode !== null && ErrorCodes::isAuthError($this->apiStatusCode);
    }

    /**
     * Check if this is a validation error
     */
    public function isValidationError(): bool
    {
        return $this->apiStatusCode !== null && ErrorCodes::isValidationError($this->apiStatusCode);
    }

    /**
     * Check if this is an insufficient funds error
     */
    public function isInsufficientFundsError(): bool
    {
        return $this->apiStatusCode !== null && ErrorCodes::isInsufficientFundsError($this->apiStatusCode);
    }

    /**
     * Check if this is an account-related error
     */
    public function isAccountError(): bool
    {
        return $this->apiStatusCode !== null && ErrorCodes::isAccountError($this->apiStatusCode);
    }

    /**
     * Check if service is unavailable
     */
    public function isServiceUnavailable(): bool
    {
        return $this->apiStatusCode !== null && ErrorCodes::isServiceUnavailable($this->apiStatusCode);
    }
}
