<?php

namespace BDCConecta\Http;

class DebugInfo
{
    private ?int $responseStatusCode = null;
    private ?array $responseHeaders = null;
    private ?string $responseBody = null;
    private ?string $error = null;

    public function __construct(
        private readonly string $curlCommand,
        private readonly string $method,
        private readonly string $url,
        private readonly array $requestHeaders,
        private readonly ?array $requestBody
    ) {}

    public function setResponse(int $statusCode, array $headers, string $body): void
    {
        $this->responseStatusCode = $statusCode;
        $this->responseHeaders = $headers;
        $this->responseBody = $body;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function getCurlCommand(): string
    {
        return $this->curlCommand;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    public function getRequestBody(): ?array
    {
        return $this->requestBody;
    }

    public function getResponseStatusCode(): ?int
    {
        return $this->responseStatusCode;
    }

    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function toString(): string
    {
        $separator = str_repeat('=', 60);
        $output = "\n{$separator}\n";
        $output .= "BDCConecta DEBUG\n";
        $output .= "{$separator}\n\n";

        $output .= ">>> CURL COMMAND:\n{$this->curlCommand}\n\n";

        if ($this->error !== null) {
            $output .= ">>> ERROR:\n{$this->error}\n";
        } elseif ($this->responseStatusCode !== null) {
            $output .= ">>> RESPONSE [HTTP {$this->responseStatusCode}]:\n";

            if (!empty($this->responseHeaders)) {
                foreach ($this->responseHeaders as $key => $value) {
                    $output .= "  {$key}: {$value}\n";
                }
                $output .= "\n";
            }

            // Pretty print JSON response if possible
            $decoded = json_decode($this->responseBody, true);
            if ($decoded !== null) {
                $output .= json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                $output .= $this->responseBody . "\n";
            }
        }

        $output .= "{$separator}\n";

        return $output;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
